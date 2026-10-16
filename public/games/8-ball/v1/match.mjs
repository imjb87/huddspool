import { RULES, PHYSICS, groupOf, otherGroup } from './config.mjs';
import { rack, validPlacement, respot, Simulation } from './physics.mjs';

const clone = value => JSON.parse(JSON.stringify(value));
const inputPhases = ['BREAK_SETUP', 'AIMING', 'BALL_IN_HAND', 'POCKET_SELECTION'];
const shotSignature = command => JSON.stringify([command.matchId, command.revision, command.playerId, command.angle, command.power, command.spin?.x, command.spin?.y, command.placement?.x, command.placement?.y, command.calledPocket]);
export const remaining = (state, player) => state.groups[player] ? state.balls.filter(b => !b.pocketed && groupOf(b.id) === state.groups[player]).map(b => b.id) : [];
export function targets(state, player = state.activePlayer) {
    return state.groups[player] ? remaining(state, player).length ? remaining(state, player) : [8]
        : state.balls.filter(b => !b.pocketed && groupOf(b.id)).map(b => b.id);
}

export function resolveShot(state, events) {
    const shooter = state.activePlayer, opponent = 1 - shooter, start = state.shotStart;
    const ordered = [...events].sort((a, b) => a.tick - b.tick || a.time - b.time || a.sequence - b.sequence);
    const firstIndex = ordered.findIndex(e => e.type === 'collision' && (e.a === 0 || e.b === 0));
    const first = ordered[firstIndex];
    const firstBall = first ? first.a === 0 ? first.b : first.a : null;
    const pockets = ordered.filter(e => e.type === 'pocket');
    const objects = pockets.filter(e => e.ball !== 0);
    const after = firstIndex < 0 ? [] : ordered.slice(firstIndex + 1);
    const rails = new Set(after.filter(e => e.type === 'cushion' && e.ball !== 0).map(e => e.ball));
    let foul = null;
    if (pockets.some(e => e.ball === 0)) foul = 'Scratch: opponent has ball-in-hand';
    else if (ordered.some(e => e.type === 'off-table' && e.ball === 0)) foul = 'Cue ball left the table';
    else if (!first) foul = 'Foul: no object-ball contact';
    else if (!start.legalTargets.includes(firstBall)) foul = `Foul: hit ${firstBall === 8 ? 'the 8-ball' : `a ${groupOf(firstBall) === 'stripes' ? 'stripe' : 'solid'}`} first`;
    else if (start.isBreak && !objects.length && rails.size < RULES.breakRails) foul = 'Illegal break: pocket a ball or send four object balls to cushions';
    else if (!start.isBreak && !after.some(e => e.type === 'cushion' || e.type === 'pocket' && e.ball !== 0)) foul = 'Foul: no pocket or cushion after contact';
    else if (ordered.some(e => e.type === 'off-table' && e.ball !== 8)) foul = 'Foul: ball left the table';

    const eight = pockets.find(e => e.ball === 8);
    const eightOff = ordered.some(e => e.type === 'off-table' && e.ball === 8);
    let winner = null, reason = null;
    if (start.isBreak) {
        if (eight || eightOff) respot(state.balls);
    } else if (eight || eightOff && RULES.eightOffTableLoss) {
        if (eightOff) reason = '8-ball left the table';
        else if (!start.eligibleEight) reason = '8-ball pocketed before the group was cleared';
        else if (foul) reason = `8-ball pocketed on a foul. ${foul}`;
        else if (RULES.callEight && eight.pocket !== start.calledPocket) reason = '8-ball pocketed in the wrong or uncalled pocket';
        else { winner = shooter; reason = 'Legal 8-ball in the called pocket'; }
        if (winner === null) winner = opponent;
    }
    let assignment = null;
    if (!foul && winner === null && !start.isBreak && !state.groups[shooter]) {
        assignment = groupOf(objects.find(e => groupOf(e.ball))?.ball);
        if (assignment) state.groups = shooter === 0 ? [assignment, otherGroup(assignment)] : [otherGroup(assignment), assignment];
    }
    const continues = !foul && (start.isBreak ? objects.length > 0 : objects.some(e => groupOf(e.ball) === state.groups[shooter]));
    return { foul, winner, reason, assignment, nextPlayer: continues ? shooter : opponent };
}

export class PoolMatch {
    constructor({ players, seed, breaker, now = () => performance.now(), onEvent = () => {} }) {
        if (!Array.isArray(players) || players.length !== 2 || players[0].id === players[1].id) throw new Error('Exactly two distinct players required');
        this.now = now; this.onEvent = onEvent; this.simulation = null; this.paused = false; this.disposed = false;
        const random = new Uint32Array(3);
        globalThis.crypto.getRandomValues(random);
        seed ??= random[0]; breaker ??= random[1] & 1;
        if (![0, 1].includes(breaker)) throw new Error('Invalid breaker');
        this.state = {
            matchId: `hp-${random[2].toString(16)}-${seed}`, rulesProfile: `${RULES.id}@${RULES.version}`,
            physicsVersion: PHYSICS.version, players: clone(players), activePlayer: breaker, breaker,
            breakerSelection: { method: 'crypto-unbiased-bit', value: breaker }, seed,
            phase: 'INITIALIZING', shotNumber: 0, groups: [null, null], balls: rack(seed),
            selectedPocket: null, deadline: null, foul: null, winner: null, resultReason: null,
            shotStart: null, shotEvents: [], history: [], revision: 0, acceptedCommands: [],
            status: 'Place the cue ball behind the head string, then confirm.',
        };
        this.state.phase = 'BREAK_SETUP';
        this.startClock();
        this.emit('match-start', { breaker, seed });
    }
    emit(type, detail = {}) { this.onEvent({ type, matchId: this.state.matchId, revision: this.state.revision, ...detail }); }
    startClock() { this.state.deadline = this.now() + RULES.clockSeconds * 1000; }
    assertControl(playerId) {
        if (this.disposed || this.paused || !inputPhases.includes(this.state.phase)) throw new Error('Controls are locked');
        if (playerId !== this.state.players[this.state.activePlayer].id) throw new Error('Out of turn');
        if (this.now() >= this.state.deadline) { this.expire(); throw new Error('Shot clock expired'); }
    }
    confirmPlacement(playerId, x, y) {
        this.assertControl(playerId);
        const breakSetup = this.state.phase === 'BREAK_SETUP';
        if (!breakSetup && this.state.phase !== 'BALL_IN_HAND') throw new Error('Placement is not allowed');
        if (!validPlacement(this.state.balls, x, y, breakSetup)) throw new Error('Invalid cue-ball placement');
        Object.assign(this.state.balls[0], { x, y, pocketed: false, vx: 0, vy: 0, wx: 0, wy: 0, wz: 0 });
        this.state.phase = targets(this.state).includes(8) ? 'POCKET_SELECTION' : 'AIMING';
        this.state.revision++;
        this.state.status = this.state.phase === 'POCKET_SELECTION' ? 'Call a pocket for the 8-ball.' : 'Aim, pull back the power bar, and release.';
        this.emit('state-stable');
    }
    selectPocket(playerId, pocket) {
        this.assertControl(playerId);
        if (!targets(this.state).includes(8) || !Number.isInteger(pocket) || pocket < 0 || pocket > 5) throw new Error('Invalid pocket selection');
        this.state.selectedPocket = pocket; this.state.phase = 'AIMING'; this.state.revision++;
        this.state.status = `Pocket ${pocket + 1} called. Make the 8-ball cleanly.`;
        this.emit('state-stable');
    }
    commit(command, authenticatedPlayerId = command.playerId) {
        // Only a trusted server may supply authenticatedPlayerId online; command.playerId is not identity proof.
        const receipt = this.state.acceptedCommands.find(entry => entry.id === command.id);
        if (receipt) {
            if (authenticatedPlayerId !== receipt.playerId || receipt.signature !== shotSignature(command)) throw new Error('Conflicting duplicate command');
            return { duplicate: true, revision: this.state.revision };
        }
        this.assertControl(authenticatedPlayerId);
        if (command.playerId !== authenticatedPlayerId || command.matchId !== this.state.matchId || command.revision !== this.state.revision) throw new Error('Stale or invalid command');
        if (this.state.phase !== 'AIMING') throw new Error('Finish placement or pocket selection first');
        const { angle, power, spin, calledPocket, placement } = command;
        if (typeof command.id !== 'string' || command.id.length < 1 || command.id.length > 128
            || !Number.isFinite(angle) || Math.abs(angle) > Math.PI * 2 || !Number.isFinite(power) || power < 0.01 || power > 1
            || !spin || !Number.isFinite(spin.x) || !Number.isFinite(spin.y) || Math.hypot(spin.x, spin.y) > 1.000001) throw new Error('Invalid shot parameters');
        if (this.state.balls.some(b => !b.pocketed && Math.max(Math.hypot(b.vx, b.vy), Math.hypot(b.wx, b.wy)) > 0)) throw new Error('Balls have not settled');
        const cue = this.state.balls[0];
        // A settled ball at a pocket jaw can be closer to a rail/ball than a new placement allows.
        // All actual placements were already validated; never reapply placement clearance to a live lie.
        if (cue.pocketed || !Number.isFinite(cue.x) || !Number.isFinite(cue.y) || !placement || placement.x !== cue.x || placement.y !== cue.y) throw new Error('Invalid placement');
        const legalTargets = targets(this.state), eligibleEight = legalTargets.length === 1 && legalTargets[0] === 8;
        if (eligibleEight && RULES.callEight && (calledPocket !== this.state.selectedPocket || !Number.isInteger(calledPocket))) throw new Error('Call a pocket first');
        this.state.shotStart = { legalTargets, remaining: remaining(this.state, this.state.activePlayer), eligibleEight,
            isBreak: this.state.shotNumber === 0, calledPocket: eligibleEight ? calledPocket : null, shooter: this.state.activePlayer };
        this.state.shotNumber++; this.state.deadline = null; this.state.foul = null;
        this.state.phase = 'SHOT_IN_PROGRESS'; this.state.revision++;
        this.state.acceptedCommands.push({ id: command.id, playerId: authenticatedPlayerId, signature: shotSignature(command) });
        this.state.status = 'Shot in play…';
        this.state.shotEvents = [];
        this.simulation = new Simulation(this.state.balls, event => {
            this.state.shotEvents.push(event);
            this.emit(event.type === 'pocket' ? 'ball-pocketing' : 'physics', { event });
        });
        this.simulation.strike(angle, power, spin);
        this.emit('shot-commitment', { command: clone(command) });
        return { duplicate: false, revision: this.state.revision };
    }
    step() {
        if (this.paused || !this.simulation || this.disposed) return;
        if (!this.simulation.step()) return;
        this.state.phase = 'RESOLVING_SHOT';
        const outcome = resolveShot(this.state, this.state.shotEvents);
        const previous = this.state.activePlayer;
        this.state.history.push({ number: this.state.shotNumber, start: clone(this.state.shotStart), events: clone(this.state.shotEvents), outcome });
        Object.assign(this.state, { foul: outcome.foul, winner: outcome.winner, resultReason: outcome.reason, selectedPocket: null });
        this.simulation = null;
        this.state.revision++;
        if (outcome.winner !== null) {
            this.state.phase = 'MATCH_OVER'; this.state.deadline = null;
            this.state.status = `${this.state.players[outcome.winner].name} wins. ${outcome.reason}`;
        } else {
            this.state.activePlayer = outcome.nextPlayer;
            this.state.phase = outcome.foul ? 'BALL_IN_HAND' : targets(this.state).includes(8) ? 'POCKET_SELECTION' : 'AIMING';
            if (outcome.foul) {
                Object.assign(this.state.balls[0], { pocketed: true, vx: 0, vy: 0, wx: 0, wy: 0, wz: 0 });
            }
            this.startClock();
            this.state.status = outcome.foul || (outcome.assignment ? `${this.state.players[previous].name} is ${outcome.assignment}. ` : '')
                + (outcome.nextPlayer === previous ? 'Keep going — your turn.' : `${this.state.players[outcome.nextPlayer].name} to play.`);
        }
        // Every observer sees the entire resolved state before receiving any outcome notification.
        if (outcome.assignment) this.emit('group-assignment', { group: outcome.assignment, player: previous });
        if (outcome.foul) this.emit('foul', { reason: outcome.foul });
        if (outcome.winner !== null) this.emit('match-end', { winner: outcome.winner, loser: 1 - outcome.winner, reason: outcome.reason });
        else this.emit('turn-change', { player: this.state.activePlayer });
        this.emit('state-stable');
    }
    expire() {
        if (this.paused || !inputPhases.includes(this.state.phase) || this.now() < this.state.deadline) return false;
        this.state.activePlayer = 1 - this.state.activePlayer;
        this.state.phase = 'BALL_IN_HAND'; this.state.foul = 'Shot clock expired: opponent has ball-in-hand';
        this.state.status = this.state.foul; this.state.selectedPocket = null;
        this.state.balls[0].pocketed = true;
        this.state.revision++; this.startClock();
        this.emit('foul', { reason: this.state.foul }); this.emit('turn-change', { player: this.state.activePlayer }); this.emit('state-stable');
        return true;
    }
    pause() {
        if (this.paused) return;
        this.pausedTime = this.now(); this.paused = true;
    }
    resume() {
        if (!this.paused) return;
        if (this.state.deadline !== null) this.state.deadline += this.now() - this.pausedTime;
        this.paused = false;
    }
    serialize() {
        if (this.simulation) throw new Error('Save only at a stable boundary');
        return JSON.stringify({ schema: 1, state: this.state, remainingMs: this.state.deadline === null ? null : Math.max(0, this.state.deadline - (this.paused ? this.pausedTime : this.now())) });
    }
    restore(serialized) {
        const saved = JSON.parse(serialized);
        if (saved.schema !== 1 || saved.state?.rulesProfile !== `${RULES.id}@${RULES.version}` || saved.state.physicsVersion !== PHYSICS.version
            || ![...inputPhases, 'MATCH_OVER'].includes(saved.state.phase) || !Array.isArray(saved.state.balls) || saved.state.balls.length !== 16
            || !Array.isArray(saved.state.players) || saved.state.players.length !== 2 || ![0, 1].includes(saved.state.activePlayer)
            || saved.state.balls.some((b, i) => b.id !== i || !Number.isFinite(b.x) || !Number.isFinite(b.y) || [b.vx, b.vy, b.wx, b.wy, b.wz].some(v => v !== 0))
            || saved.remainingMs !== null && (!Number.isFinite(saved.remainingMs) || saved.remainingMs < 0 || saved.remainingMs > RULES.clockSeconds * 1000)) throw new Error('Incompatible or invalid saved match');
        this.state = clone(saved.state); this.simulation = null; this.paused = false;
        this.state.deadline = saved.remainingMs === null ? null : this.now() + saved.remainingMs;
        this.emit('state-stable');
    }
    dispose() { this.disposed = true; this.simulation = null; this.onEvent = () => {}; }
}

// A host must feed elapsed time to this accumulator, never a varying dt to simulation.
export class FixedStepper {
    constructor(match) { this.match = match; this.accumulator = 0; this.previous = clone(match.state.balls); }
    advance(seconds) {
        if (this.match.paused || !this.match.simulation) { this.accumulator = 0; return 1; }
        this.accumulator += Math.max(0, Math.min(seconds, 0.25));
        while (this.accumulator + 1e-12 >= PHYSICS.step && this.match.simulation) {
            this.previous = clone(this.match.state.balls);
            this.match.step(); this.accumulator -= PHYSICS.step;
        }
        return this.match.simulation ? this.accumulator / PHYSICS.step : 1;
    }
}
