// Huddspool defaults, not proprietary Miniclip constants. Never change a released profile.
export const RULES = Object.freeze({
    id: 'huddspool-eight-ball', version: '1.0.0', clockSeconds: 30,
    breakRails: 4, openAfterBreak: true, mixedAssignment: 'first-event',
    breakEight: 'respot', ballInHand: 'anywhere', callEight: true,
    eightOffTableLoss: true, threeFoulLoss: false, resetSpin: true,
    deadline: 'commit-strictly-before', disconnectGraceSeconds: 60,
    disconnectOutcome: 'server-forfeit-after-grace',
});
export const PHYSICS = Object.freeze({
    version: '1.0.0', width: 1000, height: 500, radius: 11, pocketRadius: 23,
    step: 1 / 240, maxSpeed: 1650, powerExponent: 1.55,
    restitution: 0.96, cushionRestitution: 0.78,
    rollingDeceleration: 36, slidingDeceleration: 170,
    spinDecay: 0.65, sideTransfer: 0.075, spinStrength: 1.8,
    slidingRotationFactor: 2.5, ballSpinThrow: 0.005, maxThrowRatio: 0.02,
    cushionRollRetention: 0.65, cushionSpinRetention: 0.55, substepRadius: 0.25,
    sideSettleScale: 0.1, settleSpeed: 2, settleSeconds: 0.3,
});
export const POCKETS = Object.freeze([
    { x: 0, y: 0 }, { x: 500, y: -4 }, { x: 1000, y: 0 },
    { x: 0, y: 500 }, { x: 500, y: 504 }, { x: 1000, y: 500 },
]);
// Rounded endpoints are the pocket jaws. Render and collide against the same geometry.
export const CUSHIONS = Object.freeze([
    [30, 0, 473, 0], [527, 0, 970, 0],
    [30, 500, 473, 500], [527, 500, 970, 500],
    [0, 30, 0, 470], [1000, 30, 1000, 470],
    [30, 0, 19, -10], [473, 0, 481, -13], [527, 0, 519, -13], [970, 0, 981, -10],
    [30, 500, 19, 510], [473, 500, 481, 513], [527, 500, 519, 513], [970, 500, 981, 510],
    [0, 30, -10, 19], [1000, 30, 1010, 19], [0, 470, -10, 481], [1000, 470, 1010, 481],
]);
export const groupOf = id => id > 0 && id < 8 ? 'solids' : id > 8 ? 'stripes' : null;
export const otherGroup = group => group === 'solids' ? 'stripes' : 'solids';
export const clamp = (value, min, max) => Math.max(min, Math.min(max, value));
