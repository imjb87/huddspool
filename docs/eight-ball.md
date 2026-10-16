# Huddspool two-player 8-ball

The local game is integrated at the named Laravel route `games.eight-ball` (`/play/8-ball`). The Blade page retains the application layout, navigation, authentication and player profile. Vite bundles the game and its scoped CSS into normal hashed `.js`/`.css` assets and mounts it directly into the page. Profile configuration comes from the server-rendered host attributes; there is no iframe handshake. Player two is a separate human sharing the device. There is no AI opponent.

No packages were added. The renderer is Canvas 2D; the engine is browser/Node ES modules. All table, ball, cloth, rail and cue artwork is original procedural drawing. Sounds are original Web Audio tones. There are no Miniclip assets or third-party image requests beyond an existing profile avatar.

## Files

| Path | Responsibility |
| --- | --- |
| `public/games/8-ball/index.html` | Standalone offline game shell |
| `public/games/8-ball/boot.js` | Loads the Vite-built game from the cached build manifest |
| `public/games/8-ball/v1/match-screen.css` | Scoped match UI, including rotated portrait presentation |
| `public/games/8-ball/v1/config.mjs` | Frozen rules, physical parameters, world geometry |
| `public/games/8-ball/v1/physics.mjs` | Seeded rack, fixed-step simulation, placement, spotting, prediction |
| `public/games/8-ball/v1/match.mjs` | State machine, authority validation, adjudication, snapshots |
| `public/games/8-ball/v1/game.mjs` | Input, UI, lifecycle, local persistence and host events |
| `public/games/8-ball/v1/render.mjs` | Responsive table, balls, cue, guides, pocket effects |
| `public/games/8-ball/v1/audio.mjs` | Gesture-unlocked, mutable-volume sound hooks |
| `public/games/8-ball/v1/input.mjs` | Captured power gesture and cancellation |
| `public/games/8-ball/v1/transport.mjs` | Transport interface and injected server/Echo bridge |
| `resources/views/games/eight-ball.blade.php` | Huddspool integration |
| `resources/js/pool-host.js` | Vite entry, direct mounting, profile initialization and startup-error reporting |
| `tests/fixtures/pool-engine.mjs` | Executable engine checks using Node's built-in test runner |
| `tests/fixtures/pool-worker.mjs` | Waiting-worker cache isolation and activation checks |
| `tests/fixtures/pool-ui.mjs` | Real controller startup, shot commitment, rendering calls and resume with a DOM fixture |
| `tests/Unit/PoolEngineTest.php` | PHPUnit wrapper and offline asset policy checks |
| `tests/Feature/PoolGamePageTest.php` | Laravel route/embedding check |

## Setup

Use the existing Laravel Sail workflow:

```sh
vendor/bin/sail up -d
vendor/bin/sail npm run build
vendor/bin/sail artisan test --compact tests/Unit/PoolEngineTest.php tests/Feature/PoolGamePageTest.php tests/Unit/PwaServiceWorkerTest.php
vendor/bin/sail node tests/fixtures/pool-engine.mjs
vendor/bin/sail node tests/fixtures/pool-worker.mjs
vendor/bin/sail node tests/fixtures/pool-ui.mjs
vendor/bin/sail bin pint --dirty
```

The game is accessible directly at `/play/8-ball`; it is not linked in the global navigation yet. Deploy with the application's normal Laravel deployment process, including `npm run build`, the generated `public/build` assets/manifest and the `public/games/8-ball` directory. No migrations, credentials or new services are required for local play. Browsers now load bundled `.js` files; production `.mjs` MIME mappings are no longer a startup dependency. Rebuilding the assets is required for this change.

For offline play, load the app online and allow its service worker to install and cache the game shell/modules. The public static URL `/games/8-ball/index.html` is the offline entry point and can be bookmarked. The normal Laravel page contains personalized navigation and is intentionally not added to the public document cache. The standalone offline entry uses the guest save slot; profile-specific saves remain accessible through the authenticated host when available. Game asset URLs are versioned; release incompatible changes under a new version directory and update both shell and service worker lists together. Do not edit a released rules profile in place.

Worker installation no longer calls `skipWaiting()`. An installed replacement waits for existing tabs to close, preventing a new worker/cache cleanup from taking control during a match. The candidate installs into isolated `static-v5`/`runtime-v5` caches; it never overwrites the previous worker's v4 caches. Cache reads are scoped to the controlling worker's own version, and obsolete caches are deleted only on activation. Bump both cache names with each release that changes precached assets. This deliberately affects update activation across the whole PWA, not just the game. Close all application tabs to activate updates. The original worker already active on a device must first be replaced before this policy governs later updates.

## Rules profile

The single selected profile is `huddspool-eight-ball@1.0.0`, physics `1.0.0`. The seed and cryptographically selected opening breaker are recorded. Rematches alternate breakers. The head-string placement restriction uses the cue ball's center at or behind x=250. The playing surface is 1000 × 500 world units.

The break requires a pocket or four distinct object balls touching cushions after first cue contact. Groups stay open on the break. A legal break with an object pocket continues. All fouls award anywhere ball-in-hand. The break 8-ball is re-spotted at the nearest legal point along y=250, with positive-x tie preference. Legal post-break pockets assign groups by first eligible authoritative event. Event order is tick, substep time, then stable sequence (numeric ball/pair processing order). Foul shots never assign groups. Opponent pockets remain down and only own-group pockets earn continuation.

The shot-start target set determines 8-ball eligibility. The final group ball and 8 in one shot loses. A called pocket is required for an eligible 8-ball shot. An early, wrong-pocket, off-table or foul-pocketed 8 loses. Scratching with the 8 still on the table is a normal foul. No three-foul loss is enabled. All outcomes are evaluated after settling, before state/outcome events are emitted.

Clock: 30 seconds, using `performance.now()` deadlines locally. A shot must arrive strictly before its deadline; equality is a timeout. Placement and calling a pocket retain the existing deadline. Commitment clears it; simulation has no shot clock. Local backgrounding, help/settings and other modal menus pause the game. Power cancellation, pointer cancellation and turn changes clear pending gestures. The keyboard Shoot button requires explicitly selected power and deliberate button activation. Arrow keys on the table adjust aim; Shift makes the increments smaller. During placement they move the ghost cue ball. Placement must be confirmed.

## Physics and calibration

The engine runs at 240 fixed steps/second, with adaptive substeps limiting maximum motion to a quarter ball radius. Balls use equal-mass impulses with positional separation; cushion segments have rounded jaw endpoints. Pocket capture is a separate, one-time authoritative event. Rendered pocket travel never delays resolution. Interpolated rendering does not affect simulation.

Spin is an approximate planar model: wx/wy are angular cloth-contact velocities, wz is side spin. Sliding friction exchanges translation and angular motion; rolling resistance decelerates balls; spin affects ball throw and cushion rebound. There are no jump or masse shots. The model is designed for visible follow/draw/English; it is not a measured reproduction of proprietary physics. A ball is settled after all live balls remain below the speed/angular thresholds in `config.mjs` for 0.3 seconds. Small residual motion is then zeroed. Normal shots have no forced duration cutoff.

Before claiming commercial-reference fidelity, calibrate speed curve, friction, restitution, throw, jaw openings, pocket acceptance, spin decay and guide lengths against matched-angle recordings. Object-ball stripe rotation and pocket-tracking flights are stylized; pocket animations currently travel toward the top tracking area, rather than a per-player ball-slot destination. Cue pullback is power-proportional. Synthetic audio is functional placeholder sound design.

## Module API and persistence

`window.huddspoolGame` exposes `initialize({ players, storageId, returnUrl })`, `configurePlayers(players)`, `serialize()`, `restore(serialized)`, `pause()`, `resume()` and `dispose()`. Initialize accepts exactly two distinct player IDs. Configure players before the first shot. The engine can also be imported without the UI through `PoolMatch`.

Listen for bubbling `huddspool:*` CustomEvents on the host: `match-start`, `turn-change`, `shot-commitment`, `ball-pocketing`, `group-assignment`, `foul`, `match-end`, `connection-status`, and `state-stable`. Events contain a match ID and revision. `match-end` includes winner, loser and reason. Local state never updates league results automatically.

Stable snapshots contain the full match, shot history, seed, profile versions, and remaining clock time. Local storage is partitioned by host player ID (guest when standalone). A committed in-flight shot is stored with its prior stable snapshot and validated command; restoration replays it to resolution, then resumes. Storage failures are shown in the footer and do not stop play. Local storage is resumable convenience, not trusted tournament evidence. Identity/profile changes do not merge existing save slots. Disposal removes all registered listeners, observers, RAF activity and audio resources.

## Multiplayer boundary and backend work

Huddspool has Echo/Reverb for fixture-result collaboration, but no authoritative pool session store, matchmaking, game command endpoint or simulation worker. Online play is therefore not presented as available. Existing broadcasting alone cannot securely adjudicate this game. `PoolTransport` and `AuthoritativeTransport` define the integration boundary; supplying the adapter alone does not enable an online UI.

Required backend work:

1. Create durable matches with exactly two authenticated users; authorize join/spectator policy and private Echo channels. Bind identity from Sanctum/session, never `command.playerId` alone.
2. Run the shared engine in a trusted Node simulation worker (or validate a separately certified deterministic engine). Store selected profile and physics versions, authoritative monotonic clock, revision and accepted command IDs durably.
3. Accept `{id, matchId, playerId, revision, angle, power, spin:{x,y}, placement:{x,y}, calledPocket}`. Validate identity, ownership, deadline, numeric ranges, saved placement, phase and revision under a per-match transaction/lock. Deduplicate before applying the impulse. Do not accept client pocket or victory reports.
4. Add authoritative placement and call-pocket commands with equivalent revision/deadline checks. Simulate fully, then persist the resolved snapshot and events atomically. Broadcast snapshots and an authoritative server-time/deadline mapping, not wall-clock values generated by clients.
5. Add command acknowledgements, stale-revision recovery, reconnect snapshot plus events since revision, and client reconciliation. Persist deduplication for the entire match lifetime.
6. Implement configurable disconnect grace (profile default 60 seconds) and explicit server forfeit after grace; publish reconnect deadlines and terminal reasons. Local pause must not pause online authority. Adapt UI to server time, disable local adjudication/save authority, and show connection state before enabling online play.

## Verification and fidelity report

58 Node engine/input checks, 3 worker lifecycle checks and 3 controller checks passed in this workspace (64 total). They cover the requested rule edge cases, legal/illegal breaks, open and mixed assignment, fouls, rail sequencing, 8-ball outcomes and re-spotting, placement, deadlines, stale/duplicate/forged commands, saved-state compatibility, pause, high-speed collisions, one-time capture, collision prediction, visible follow/draw and byte-equivalent simulated break outcomes at 30/60/144 render FPS. Input checks cover power release/cancellation, pointer ownership, disposal, and coordinate conversion after viewport/DPR changes, including rotated portrait tables. Controller checks execute the actual startup, placement confirmation, keyboard shot, render path and saved-state resume. Worker checks verify isolated installation, version-scoped reads, and cleanup only on activation. These use DOM/EventTarget and worker VM fixtures rather than a real touch browser.

Sail reported `Docker is not running`; the managed Docker socket was unavailable. The dependency-free checks and Vite production build passed using the installed Node runtime instead. Laravel route tests, PHPUnit wrapper and Pint could not run through Sail. Chromium is installed but sandbox socket restrictions prevent browser execution; network access to the live URL also failed. The live failure's exact cause therefore remains unverified. Raw-module MIME handling and iframe startup were identified as deployment risks and removed, rather than claimed as a confirmed diagnosis. Desktop/mobile visual, touch, orientation, audio and offline service-worker end-to-end QA remains required; source inspection and event fixtures are not claimed as device verification.

Verified reference interactions: Miniclip's official [How to Aim with the Cue](https://support.miniclip.com/hc/en-us/articles/203747546-How-to-Aim-with-the-Cue-8-Ball-Pool) documents aiming, the power bar, fine aiming and remaining-ball indicators. [Advanced Plays — Spins](https://support.miniclip.com/hc/en-us/articles/35451960569361-Advanced-Plays-Spins-8-Ball-Pool) documents selecting contact position for follow, draw and side-spin effects. These informed the interaction hierarchy. No supplied gameplay footage/screenshots were present. Exact layout, timing, physics, break behavior, group assignment ties and mode-dependent calling rules are not verified Miniclip parity. They use the explicit Huddspool defaults above.

Release device checklist: mouse and touch aim without firing; power release fires once; Escape/pointercancel/orientation do not fire; invalid placements cannot confirm; pocket choice locks on commit; player timeout cancels gestures; iOS/Android safe areas and 44px control targets; 360px portrait and short landscape; audio unlock/mute/volume and reduced motion; background mid-shot then return; reload mid-shot and replay; offline direct shell after caching; pending worker update during a rack; exit cleanup and rematch breaker alternation.
