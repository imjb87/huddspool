import test from 'node:test';
import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import vm from 'node:vm';

const source = readFileSync(new URL('../../public/serviceworker.js', import.meta.url), 'utf8');
function workerFixture() {
    const origin = 'https://huddspool.test';
    const stores = new Map(), handlers = new Map();
    let offline = false, skipped = false, claimed = false;
    const key = value => new URL(typeof value === 'string' ? value : value.url, origin).pathname;
    const response = body => ({ body, ok: true, type: 'basic', headers: { get: () => 'public' }, clone() { return this; }, async json() { return {}; } });
    const open = async name => {
        if (!stores.has(name)) stores.set(name, new Map());
        const entries = stores.get(name);
        return {
            async match(request) { return entries.get(key(request)); },
            async put(request, value) { entries.set(key(request), value); },
            async keys() { return [...entries.keys()]; },
            async delete(request) { return entries.delete(key(request)); },
        };
    };
    const context = vm.createContext({
        URL, console,
        self: { location: { origin }, addEventListener(type, fn) { handlers.set(type, fn); },
            async skipWaiting() { skipped = true; }, clients: { async claim() { claimed = true; } } },
        caches: { open, async keys() { return [...stores.keys()]; }, async delete(name) { return stores.delete(name); },
            async match() { throw new Error('Cache lookups must be scoped to this worker version'); } },
        async fetch(request) {
            if (offline) throw new Error('Offline');
            return response(`new:${key(request)}`);
        },
    });
    vm.runInContext(source, context);
    const dispatch = async type => {
        let pending;
        handlers.get(type)({ waitUntil(promise) { pending = promise; } });
        await pending;
    };
    return { stores, open, response, dispatch, context, setOffline() { offline = true; }, get skipped() { return skipped; }, get claimed() { return claimed; } };
}

test('waiting worker installs into v4 without overwriting or deleting the active v3 cache', async () => {
    const w = workerFixture(), active = await w.open('pwa-static-v3');
    await active.put('/offline', w.response('old offline'));
    await active.put('/games/8-ball/index.html', w.response('old game'));
    await w.dispatch('install');
    assert.equal((await active.match('/offline')).body, 'old offline');
    assert.equal((await active.match('/games/8-ball/index.html')).body, 'old game');
    assert.equal((await (await w.open('pwa-static-v4')).match('/games/8-ball/index.html')).body, 'new:/games/8-ball/index.html');
    assert.equal(w.stores.has('pwa-static-v3'), true);
    assert.equal(w.skipped, false); assert.equal(w.claimed, false);
});
test('worker reads only its own static/runtime caches, including offline navigation', async () => {
    const w = workerFixture();
    await (await w.open('pwa-static-v3')).put('/games/8-ball/index.html', w.response('old game'));
    await w.dispatch('install');
    await (await w.open('pwa-static-v5')).put('/future.mjs', w.response('future deployment'));
    assert.equal((await w.context.handleAsset({url:'https://huddspool.test/games/8-ball/index.html'})).body, 'new:/games/8-ball/index.html');
    assert.equal((await w.context.handleAsset({url:'https://huddspool.test/future.mjs',destination:'script'})).body, 'new:/future.mjs');
    await (await w.open('pwa-runtime-v4')).put('/news', w.response('current cached news'));
    w.setOffline();
    assert.equal((await w.context.handleNavigation({url:'https://huddspool.test/news'})).body, 'current cached news');
    assert.equal((await w.context.handleNavigation({url:'https://huddspool.test/not-cached'})).body, 'new:/offline');
});
test('previous caches are deleted only on activation and unrelated caches survive', async () => {
    const w = workerFixture();
    await w.open('pwa-static-v3'); await w.open('pwa-runtime-v3'); await w.open('other-app');
    await w.dispatch('install');
    assert.equal(w.stores.has('pwa-static-v3'), true);
    await w.dispatch('activate');
    assert.equal(w.stores.has('pwa-static-v3'), false); assert.equal(w.stores.has('pwa-runtime-v3'), false);
    assert.equal(w.stores.has('pwa-static-v4'), true); assert.equal(w.stores.has('other-app'), true);
    assert.equal(w.claimed, true);
});
