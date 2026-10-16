import { PHYSICS as P, POCKETS, CUSHIONS, clamp } from './config.mjs';

export function seededRandom(seed) {
    let n = seed >>> 0;
    return () => {
        n += 0x6D2B79F5;
        let t = Math.imul(n ^ n >>> 15, 1 | n);
        t ^= t + Math.imul(t ^ t >>> 7, 61 | t);
        return ((t ^ t >>> 14) >>> 0) / 4294967296;
    };
}

export function rack(seed) {
    const random = seededRandom(seed);
    const shuffle = items => {
        for (let i = items.length - 1; i > 0; i--) {
            const j = Math.floor(random() * (i + 1));
            [items[i], items[j]] = [items[j], items[i]];
        }
        return items;
    };
    const solid = 1 + Math.floor(random() * 7);
    const stripe = 9 + Math.floor(random() * 7);
    const rest = shuffle(Array.from({ length: 15 }, (_, i) => i + 1).filter(id => ![solid, stripe, 8].includes(id)));
    const corners = random() < 0.5 ? [solid, stripe] : [stripe, solid];
    const positions = [{ id: 0, x: 240, y: 250 }];
    for (let row = 0; row < 5; row++) {
        for (let col = 0; col <= row; col++) {
            const id = row === 2 && col === 1 ? 8 : row === 4 && col === 0 ? corners[0] : row === 4 && col === 4 ? corners[1] : rest.pop();
            positions.push({ id, x: 750 + row * Math.sqrt(3) * (P.radius + 0.015), y: 250 + (col - row / 2) * (2 * P.radius + 0.03) });
        }
    }
    return positions.sort((a, b) => a.id - b.id).map(b => ({ ...b, vx: 0, vy: 0, wx: 0, wy: 0, wz: 0, rotation: 0, pocketed: false }));
}

export function validPlacement(balls, x, y, behindHead = false) {
    const r = P.radius;
    return Number.isFinite(x) && Number.isFinite(y)
        && x >= r && x <= (behindHead ? 250 : P.width - r) && y >= r && y <= P.height - r
        && POCKETS.every(p => Math.hypot(x - p.x, y - p.y) > P.pocketRadius + r * 0.5)
        && balls.every(b => b.id === 0 || b.pocketed || Math.hypot(x - b.x, y - b.y) >= 2 * r + 0.02);
}

export function respot(balls, id = 8) {
    const ball = balls.find(b => b.id === id);
    // Closest legal point on the longitudinal spotting line: endpoints of forbidden intervals.
    const candidates = [750];
    for (const b of balls) {
        if (b.id === id || b.pocketed || Math.abs(b.y - 250) >= P.radius * 2 + 0.03) continue;
        const dx = Math.sqrt((P.radius * 2 + 0.03) ** 2 - (b.y - 250) ** 2);
        candidates.push(b.x - dx - 0.001, b.x + dx + 0.001);
    }
    const x = candidates.filter(x => x >= P.radius && x <= 1000 - P.radius)
        .filter(x => balls.every(b => b.id === id || b.pocketed || Math.hypot(b.x - x, b.y - 250) >= 2 * P.radius + 0.02))
        .sort((a, b) => Math.abs(a - 750) - Math.abs(b - 750) || b - a)[0];
    if (x === undefined) throw new Error('No legal spotting position');
    Object.assign(ball, { x, y: 250, vx: 0, vy: 0, wx: 0, wy: 0, wz: 0, pocketed: false });
}

export class Simulation {
    constructor(balls, onEvent = () => {}) {
        this.balls = balls;
        this.onEvent = onEvent;
        this.tick = 0;
        this.sequence = 0;
        this.quiet = 0;
        this.events = [];
    }
    emit(type, data, time = 0) {
        const event = { type, tick: this.tick, time, sequence: this.sequence++, ...data };
        this.events.push(event);
        this.onEvent(event);
    }
    strike(angle, power, spin) {
        const cue = this.balls[0];
        const speed = P.maxSpeed * power ** P.powerExponent;
        cue.vx = Math.cos(angle) * speed;
        cue.vy = Math.sin(angle) * speed;
        // wx/wy store the cloth-contact rolling velocity, in world units per second.
        cue.wx = cue.vx * spin.y * P.spinStrength;
        cue.wy = cue.vy * spin.y * P.spinStrength;
        cue.wz = spin.x * speed;
    }
    step() {
        const speed = Math.max(...this.balls.filter(b => !b.pocketed).map(b => Math.hypot(b.vx, b.vy)), 0);
        const substeps = Math.max(1, Math.ceil(speed * P.step / (P.radius * P.substepRadius)));
        for (let sub = 0; sub < substeps; sub++) this.integrate(P.step / substeps, sub / substeps);
        this.tick++;
        const moving = this.balls.some(b => !b.pocketed && Math.max(Math.hypot(b.vx, b.vy), Math.hypot(b.wx, b.wy), Math.abs(b.wz) * P.sideSettleScale) > P.settleSpeed);
        this.quiet = moving ? 0 : this.quiet + P.step;
        if (this.quiet >= P.settleSeconds) {
            for (const b of this.balls) Object.assign(b, { vx: 0, vy: 0, wx: 0, wy: 0, wz: 0 });
            return true;
        }
        return false;
    }
    integrate(dt, time) {
        const balls = this.balls.filter(b => !b.pocketed);
        for (const b of balls) {
            b.x += b.vx * dt;
            b.y += b.vy * dt;
            b.rotation += Math.hypot(b.vx, b.vy) * dt / P.radius;
            const slipX = b.vx - b.wx, slipY = b.vy - b.wy;
            const slip = Math.hypot(slipX, slipY);
            if (slip > 0.5) {
                const impulse = Math.min(P.slidingDeceleration * dt, slip / (1 + P.slidingRotationFactor));
                b.vx -= slipX / slip * impulse;
                b.vy -= slipY / slip * impulse;
                b.wx += slipX / slip * impulse * P.slidingRotationFactor;
                b.wy += slipY / slip * impulse * P.slidingRotationFactor;
            } else {
                const speed = Math.hypot(b.vx, b.vy);
                const scale = speed > 0 ? Math.max(0, 1 - P.rollingDeceleration * dt / speed) : 0;
                b.vx *= scale; b.vy *= scale;
                b.wx = b.vx; b.wy = b.vy;
            }
            b.wz *= Math.exp(-P.spinDecay * dt);
            const pocket = POCKETS.findIndex(p => Math.hypot(b.x - p.x, b.y - p.y) < P.pocketRadius);
            if (pocket !== -1) {
                b.pocketed = true;
                Object.assign(b, { vx: 0, vy: 0, wx: 0, wy: 0, wz: 0 });
                this.emit('pocket', { ball: b.id, pocket, x: b.x, y: b.y }, time);
                continue;
            }
            if (b.x < -50 || b.x > 1050 || b.y < -50 || b.y > 550) {
                b.pocketed = true;
                Object.assign(b, { vx: 0, vy: 0, wx: 0, wy: 0, wz: 0 });
                this.emit('off-table', { ball: b.id }, time);
                continue;
            }
            for (let rail = 0; rail < CUSHIONS.length; rail++) {
                const [ax, ay, bx, by] = CUSHIONS[rail];
                const t = clamp(((b.x - ax) * (bx - ax) + (b.y - ay) * (by - ay)) / ((bx - ax) ** 2 + (by - ay) ** 2), 0, 1);
                const dx = b.x - ax - t * (bx - ax), dy = b.y - ay - t * (by - ay);
                const dist = Math.hypot(dx, dy);
                if (dist >= P.radius || dist < 1e-9) continue;
                const nx = dx / dist, ny = dy / dist;
                b.x += nx * (P.radius - dist + 0.001); b.y += ny * (P.radius - dist + 0.001);
                const normal = b.vx * nx + b.vy * ny;
                if (normal >= -0.05) continue;
                const side = b.wz * P.sideTransfer;
                b.vx -= (1 + P.cushionRestitution) * normal * nx + side * ny;
                b.vy -= (1 + P.cushionRestitution) * normal * ny - side * nx;
                b.wx *= P.cushionRollRetention; b.wy *= P.cushionRollRetention; b.wz *= P.cushionSpinRetention;
                this.emit('cushion', { ball: b.id, rail, intensity: -normal }, time);
            }
        }
        // Stable numeric ID pair order is the final simultaneous-event tie-breaker.
        for (let i = 0; i < balls.length; i++) for (let j = i + 1; j < balls.length; j++) {
            const a = balls[i], b = balls[j];
            if (a.pocketed || b.pocketed) continue;
            const dx = b.x - a.x, dy = b.y - a.y, dist = Math.hypot(dx, dy);
            if (dist >= P.radius * 2) continue;
            const nx = dist > 1e-9 ? dx / dist : 1, ny = dist > 1e-9 ? dy / dist : 0;
            const overlap = (P.radius * 2 - dist + 0.001) / 2;
            a.x -= nx * overlap; a.y -= ny * overlap; b.x += nx * overlap; b.y += ny * overlap;
            const normal = (b.vx - a.vx) * nx + (b.vy - a.vy) * ny;
            if (normal >= -0.05) continue;
            const impulse = -(1 + P.restitution) * normal / 2;
            a.vx -= impulse * nx; a.vy -= impulse * ny; b.vx += impulse * nx; b.vy += impulse * ny;
            const throwImpulse = clamp((a.wz - b.wz) * P.ballSpinThrow, -impulse * P.maxThrowRatio, impulse * P.maxThrowRatio);
            a.vx += throwImpulse * ny; a.vy -= throwImpulse * nx;
            b.vx -= throwImpulse * ny; b.vy += throwImpulse * nx;
            this.emit('collision', { a: a.id, b: b.id, intensity: impulse }, time);
        }
    }
}

export function predict(balls, angle) {
    const cue = balls[0], dx = Math.cos(angle), dy = Math.sin(angle);
    let distance = 1500, target = null;
    // Ray against expanded ball circles and capsule cushions, including rounded jaws.
    for (const ball of balls) {
        if (!ball.id || ball.pocketed) continue;
        const x = cue.x - ball.x, y = cue.y - ball.y;
        const projection = x * dx + y * dy;
        const disc = projection ** 2 - (x * x + y * y - (P.radius * 2) ** 2);
        const t = disc >= 0 ? -projection - Math.sqrt(disc) : -1;
        if (t >= 0 && t < distance) { distance = t; target = ball; }
    }
    for (const [ax, ay, bx, by] of CUSHIONS) {
        const length = Math.hypot(bx - ax, by - ay), tx = (bx - ax) / length, ty = (by - ay) / length;
        const nx = -ty, ny = tx, denominator = dx * nx + dy * ny;
        for (const side of [-1, 1]) {
            const t = Math.abs(denominator) > 1e-9 ? (side * P.radius - (cue.x - ax) * nx - (cue.y - ay) * ny) / denominator : -1;
            const along = (cue.x + dx * t - ax) * tx + (cue.y + dy * t - ay) * ty;
            if (t >= 0 && along >= 0 && along <= length && t < distance) { distance = t; target = null; }
        }
        for (const [x, y] of [[ax, ay], [bx, by]]) {
            const rx = cue.x - x, ry = cue.y - y, projection = rx * dx + ry * dy;
            const disc = projection ** 2 - (rx * rx + ry * ry - P.radius ** 2);
            const t = disc >= 0 ? -projection - Math.sqrt(disc) : -1;
            if (t >= 0 && t < distance) { distance = t; target = null; }
        }
    }
    return { x: cue.x + dx * distance, y: cue.y + dy * distance, distance, target };
}
