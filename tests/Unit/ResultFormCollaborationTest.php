<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Process;

class ResultFormCollaborationTest extends TestCase
{
    public function test_a_later_editor_with_a_lower_user_id_gets_the_second_colour(): void
    {
        $this->assertScenario(<<<'JS'
            const editor = makeEditor(41, 'first');
            editor.initEditors();
            assert.equal(editor.collaboratorsUi[0].color, '#64748b');
            editor.syncCollaboratorsUi([first]);
            editor.collaboratorJoinedUi(second);
            assert.equal(editor.collaboratorColorAssignments[41], blue);
            assert.equal(editor.collaboratorColorAssignments[4], purple);
            assert.equal(editor.collaboratorColor, blue);
            JS);
    }

    public function test_devices_agree_on_colours_despite_different_roster_orders(): void
    {
        $this->assertScenario(<<<'JS'
            const existing = makeEditor(41, 'first');
            existing.syncCollaboratorsUi([first]);
            existing.collaboratorJoinedUi(second);
            const joining = makeEditor(4, 'second');
            joining.syncCollaboratorsUi([second, first]);
            assert.deepEqual(existing.collaboratorColorAssignments, joining.collaboratorColorAssignments);
            assert.equal(joining.collaboratorColor, purple);
            joining.syncCollaboratorsUi([first, second]);
            assert.equal(joining.collaboratorColor, purple);
            JS);
    }

    public function test_delayed_lock_colours_cannot_override_the_presence_roster(): void
    {
        $this->assertScenario(<<<'JS'
            const editor = makeEditor(4, 'second');
            editor.syncCollaboratorsUi([second, first]);
            editor.receiveResultFieldLock(lockEvent({color: purple}));
            assert.equal(editor.fieldLocks[field].color, blue);
            assert.ok(editor.resultFieldActivityStyle(field).includes(blue));
            assert.ok(editor.collaboratorActivityStyle(editor.collaboratorsUi.find(member => member.id === 41)).includes(blue));
            assert.equal(editor.isResultFieldDisabled(field), true);
            assert.equal(editor.resultFieldLockLabel(field), 'First is editing this field');
            JS);
    }

    public function test_locks_received_before_the_roster_are_recoloured_without_losing_ownership(): void
    {
        $this->assertScenario(<<<'JS'
            const editor = makeEditor(4, 'second');
            editor.receiveResultFieldLock(lockEvent({color: purple}));
            editor.syncCollaboratorsUi([second, first]);
            assert.equal(editor.fieldLocks[field].color, blue);
            assert.equal(editor.fieldLocks[field].client_id, 'first');
            assert.equal(editor.fieldLocks[field].claimed_at, 100);
            editor.receiveResultFieldLock(lockEvent({action: 'renewed', color: purple}));
            assert.equal(editor.fieldLocks[field].color, blue);
            editor.receiveResultFieldLock(lockEvent({action: 'released', client_id: 'unrelated'}));
            assert.equal(editor.isResultFieldDisabled(field), true);
            editor.receiveResultFieldLock(lockEvent({action: 'released'}));
            assert.equal(editor.isResultFieldDisabled(field), false);
            JS);
    }

    public function test_rejoining_before_the_leave_animation_finishes_preserves_the_editor(): void
    {
        $this->assertScenario(<<<'JS'
            const editor = makeEditor(4, 'second');
            editor.syncCollaboratorsUi([first, second]);
            editor.collaboratorLeftUi(first);
            editor.collaboratorJoinedUi(first);
            flushTimers();
            assert.equal(editor.collaboratorsUi.length, 2);
            assert.equal(editor.collaboratorColorAssignments[41], blue);
            assert.equal(editor.collaboratorColorAssignments[4], purple);
            JS);
    }

    public function test_a_join_during_a_leave_animation_excludes_the_departing_editor(): void
    {
        $this->assertScenario(<<<'JS'
            const editor = makeEditor(4, 'second');
            editor.syncCollaboratorsUi([first, second]);
            editor.collaboratorLeftUi(first);
            editor.collaboratorJoinedUi({id: 2, joined_at: 300, name: 'Third'});
            assert.equal(editor.collaboratorColorAssignments[41], undefined);
            assert.equal(editor.collaboratorColorAssignments[4], blue);
            assert.equal(editor.collaboratorColorAssignments[2], purple);
            flushTimers();
            assert.equal(editor.collaboratorsUi.length, 2);
            JS);
    }

    public function test_equal_join_times_and_rosters_larger_than_the_palette_are_deterministic(): void
    {
        $this->assertScenario(<<<'JS'
            const members = Array.from({length: 14}, (_, index) => ({id: index + 1, joined_at: 100}));
            const left = makeEditor(1, 'left');
            const right = makeEditor(2, 'right');
            left.syncCollaboratorsUi(members);
            right.syncCollaboratorsUi([...members].reverse());
            assert.deepEqual(left.collaboratorColorAssignments, right.collaboratorColorAssignments);
            assert.equal(new Set(Object.values(left.collaboratorColorAssignments)).size, 14);
            JS);
    }

    private function assertScenario(string $scenario): void
    {
        $harness = <<<'JS'
            const assert = require('node:assert/strict');
            const fs = require('node:fs');
            const vm = require('node:vm');
            const timers = new Map();
            let timerId = 0;
            const window = {
                setTimeout(callback) { timers.set(++timerId, callback); return timerId; },
                clearTimeout(id) { timers.delete(id); },
            };
            const context = vm.createContext({window, document: {head: {querySelector: () => null}}, console});
            const source = fs.readFileSync('resources/js/bootstrap.js', 'utf8').replaceAll('import.meta.env', '({})');
            vm.runInContext(source, context);
            const blue = '#2563eb';
            const purple = '#7c3aed';
            const first = {id: 41, joined_at: 100, name: 'First'};
            const second = {id: 4, joined_at: 200, name: 'Second'};
            const field = 'frames.0.home_player_id';
            const makeEditor = (id, clientId) => ({
                ...window.resultFormCollaboration({collaboratorId: id, clientId}),
                ...window.resultFormEditors([id === 41 ? first : second], [blue, purple]),
                $nextTick: callback => callback(),
            });
            const lockEvent = (overrides = {}) => ({
                action: 'acquired', field_key: field, client_id: 'first', user_id: 41,
                user_name: 'First', claimed_at: 100, expires_at: Date.now() + 12000,
                color: blue, ...overrides,
            });
            const flushTimers = () => {
                const callbacks = [...timers.values()];
                timers.clear();
                callbacks.forEach(callback => callback());
            };
            JS;

        $process = new Process(['node', '-e', $harness."\n".$scenario], dirname(__DIR__, 2));
        $process->run();

        $this->assertSame(0, $process->getExitCode(), $process->getErrorOutput().$process->getOutput());
    }
}
