import test from 'node:test';
import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import { setMaxListeners } from 'node:events';

// Exercise the actual UI controller, not a second implementation of its state machine.
// This DOM fixture does not replace device/browser rendering verification.
function environment(storage = new Map()) {
    const nodes = new Map();
    class Element extends EventTarget {
        constructor(id = '') {
            super(); this.id = id; this.children = []; this.value = ''; this.style = {setProperty(){}};
            this.selectors = new Map(); this.attributes = new Map(); this.open = false; this.capture = new Set();
            const classes = new Set();
            this.classList = {add:v=>classes.add(v),remove:v=>classes.delete(v),contains:v=>classes.has(v),toggle(v,force){const add=force??!classes.has(v);if(add)classes.add(v);else classes.delete(v);return add;}};
        }
        set innerHTML(html) {
            for (const match of html.matchAll(/id="([^"]+)"/g)) nodes.set(match[1],new Element(match[1]));
        }
        querySelector(selector) {
            if (!this.selectors.has(selector)) this.selectors.set(selector,new Element(selector));
            return this.selectors.get(selector);
        }
        replaceChildren(...children) {this.children=children;}
        append(child) {this.children.push(child);}
        setAttribute(name,value) {this.attributes.set(name,String(value));}
        getAttribute(name) {return this.attributes.get(name);}
        getBoundingClientRect() {return this.rect || {left:0,top:0,width:1120,height:620};}
        showModal() {this.open=true;}
        close() {this.open=false;this.dispatchEvent(new Event('close'));}
        setPointerCapture(id) {this.capture.add(id);}
        hasPointerCapture(id) {return this.capture.has(id);}
        releasePointerCapture(id) {this.capture.delete(id);this.dispatchEvent(Object.assign(new Event('lostpointercapture'),{pointerId:id}));}
        getContext() {return new Proxy({}, {get(target,key){if(key.startsWith('create'))return()=>({addColorStop(){}});return target[key]??(()=>{});},set(target,key,value){target[key]=value;return true;}});}
    }
    const html=readFileSync(new URL('../../public/games/8-ball/index.html',import.meta.url),'utf8');
    for(const match of html.matchAll(/id="([^"]+)"/g))nodes.set(match[1],new Element(match[1]));
    const doc=new Element('document');doc.hidden=false;
    doc.getElementById=id=>nodes.get(id);
    doc.querySelector=selector=>selector==='[data-pool-host]'?nodes.get('pool'):doc.querySelectorBase(selector);
    doc.querySelectorBase=Element.prototype.querySelector.bind(doc);
    doc.querySelectorAll=()=>[new Element(),new Element()];
    doc.createElement=tag=>new Element(tag);doc.createTextNode=text=>({textContent:text});
    const win=new EventTarget();win.parent=win;win.top=win;win.location={origin:'https://huddspool.test'};
    Object.assign(globalThis, {document:doc,window:win,location:win.location,devicePixelRatio:1,
        matchMedia:()=>({matches:false}),ResizeObserver:class {observe(){}disconnect(){}},
        requestAnimationFrame:()=>1,cancelAnimationFrame:()=>{},
        AbortController:class extends AbortController {constructor(){super();setMaxListeners(0,this.signal);}},
    });
    Object.defineProperty(globalThis,'localStorage',{configurable:true,value:{getItem:key=>storage.get(key)??null,setItem:(key,value)=>storage.set(key,value)}});
    return {nodes,storage,click:id=>nodes.get(id).dispatchEvent(new Event('click'))};
}
const config={players:[{id:'p1',name:'Player 1'},{id:'p2',name:'Player 2'}],storageId:'p1',returnUrl:'/'};

test('actual UI boots, confirms placement and commits a keyboard-powered shot', async()=>{
    const e=environment();const {bootGame}=await import('../../public/games/8-ball/v1/game.mjs');
    const game=bootGame(config);
    assert.equal(e.nodes.get('modal').open,true);e.click('start');
    assert.equal(game.match.paused,false);assert.equal(e.nodes.get('confirm').disabled,false);
    e.click('confirm');assert.equal(game.match.state.phase,'AIMING');
    e.nodes.get('power').dispatchEvent(Object.assign(new Event('keydown',{cancelable:true}),{key:'ArrowDown'}));
    assert.equal(e.nodes.get('shoot').disabled,false);e.click('shoot');
    assert.equal(game.match.state.phase,'SHOT_IN_PROGRESS');assert.equal(game.match.state.shotNumber,1);
    game.frame(performance.now());game.dispose();
});
test('actual controller renders the rotated portrait table and resets gestures on resize',async()=>{
    const e=environment();const {bootGame}=await import('../../public/games/8-ball/v1/game.mjs');
    const game=bootGame(config);e.click('start');e.click('confirm');
    e.nodes.get('table').rect={left:0,top:0,width:310,height:560};game.renderer.resize();
    assert.equal(game.renderer.portrait,true);game.frame(performance.now());
    const event=Object.assign(new Event('pointerdown'),{pointerId:1,button:0,clientX:150,clientY:250});
    e.nodes.get('table').dispatchEvent(event);assert.equal(game.tableInput.active,true);
    game.cancelGesture();assert.equal(game.tableInput.active,false);game.dispose();
});
test('saved match restores through the actual UI controller and can resume',async()=>{
    const e=environment();const {bootGame}=await import('../../public/games/8-ball/v1/game.mjs');
    const first=bootGame(config);e.click('start');e.click('confirm');first.save();first.dispose();
    const restored=bootGame(config);assert.equal(restored.match.state.phase,'AIMING');assert.equal(e.nodes.get('modal').open,true);
    e.click('resume-match');assert.equal(restored.match.paused,false);assert.equal(restored.canAim(),true);restored.dispose();
});
