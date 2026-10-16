import test from 'node:test';
import assert from 'node:assert/strict';
import { RULES, PHYSICS as P, groupOf } from '../../public/games/8-ball/v1/config.mjs';
import { rack, Simulation, validPlacement, respot, predict } from '../../public/games/8-ball/v1/physics.mjs';
import { PoolMatch, FixedStepper, targets, resolveShot } from '../../public/games/8-ball/v1/match.mjs';
import { bindPowerMeter, bindTableDrag } from '../../public/games/8-ball/v1/input.mjs';
import { Renderer } from '../../public/games/8-ball/v1/render.mjs';

const players = [{id:'one',name:'One'},{id:'two',name:'Two'}];
const collision = (ball=1,tick=0) => ({type:'collision',a:0,b:ball,tick,time:0,sequence:0});
const pocket = (ball=1,where=0,tick=1) => ({type:'pocket',ball,pocket:where,tick,time:0,sequence:ball});
const cushion = (ball=0,tick=1) => ({type:'cushion',ball,rail:0,tick,time:0,sequence:1});
function fixture({isBreak=false,groups=['solids','stripes'],eligibleEight=false}={}) {
    const state=new PoolMatch({players,seed:42,breaker:0}).state;
    state.phase='RESOLVING_SHOT';state.groups=groups;
    if(eligibleEight)for(const b of state.balls)if(groupOf(b.id)==='solids')b.pocketed=true;
    state.shotStart={legalTargets:targets(state),isBreak,eligibleEight,calledPocket:2,remaining:eligibleEight?[]:[1,2,3,4,5,6,7]};
    return state;
}
function resolve(state,events) {
    for(const event of events)if(event.type==='pocket'||event.type==='off-table')state.balls[event.ball].pocketed=true;
    return resolveShot(state,events);
}
const ready = (now=()=>0) => {const m=new PoolMatch({players,seed:42,breaker:0,now});m.confirmPlacement('one',240,250);return m;};
const command = (m,overrides={})=>({id:'shot-1',matchId:m.state.matchId,revision:m.state.revision,playerId:m.state.players[m.state.activePlayer].id,angle:0,power:.8,spin:{x:0,y:0},placement:{x:m.state.balls[0].x,y:m.state.balls[0].y},calledPocket:m.state.selectedPocket,...overrides});
const settle = m=>{for(let i=0;i<60000&&m.simulation;i++)m.step();assert.equal(m.simulation,null,'Shot must settle without a timeout forcing it to stop');};

test('seeded rack is legal and repeatable, with no overlap across 100 seeds',()=>{
    for(let seed=0;seed<100;seed++){
        const balls=rack(seed);assert.deepEqual(balls,rack(seed));assert.equal(balls.length,16);
        assert.equal(balls[8].x,750+2*Math.sqrt(3)*(P.radius+.015));assert.equal(balls[8].y,250);
        const rear=balls.filter(b=>b.x>825).sort((a,b)=>a.y-b.y);
        assert.notEqual(groupOf(rear[0].id),groupOf(rear.at(-1).id));
        assert.equal(Math.min(...balls.filter(b=>b.id).map(b=>b.x)),750);
        for(let i=0;i<balls.length;i++)for(let j=i+1;j<balls.length;j++)assert.ok(Math.hypot(balls[i].x-balls[j].x,balls[i].y-balls[j].y)>=P.radius*2);
    }
});
test('legal break with four distinct cushion balls passes turn and keeps table open',()=>{
    const s=fixture({isBreak:true,groups:[null,null]});const r=resolve(s,[collision(),...Array.from({length:4},(_,i)=>cushion(i+1))]);assert.equal(r.foul,null);assert.equal(r.nextPlayer,1);assert.deepEqual(s.groups,[null,null]);
});
test('break with a pocket continues but never assigns groups',()=>{const s=fixture({isBreak:true,groups:[null,null]});const r=resolve(s,[collision(),pocket(1)]);assert.equal(r.foul,null);assert.equal(r.nextPlayer,0);assert.deepEqual(s.groups,[null,null]);});
test('illegal break counts distinct object balls only after contact',()=>{
    for(const events of [[collision(),cushion(1),cushion(1),cushion(1),cushion(1)],[collision(),cushion(0),cushion(1),cushion(2),cushion(3)],[cushion(1,-1),collision(),cushion(2),cushion(3),cushion(4)]])assert.match(resolve(fixture({isBreak:true}),events).foul,/Illegal break/);
});
test('first legal post-break pocket assigns group',()=>{const s=fixture({groups:[null,null]});const r=resolve(s,[collision(9),pocket(9)]);assert.equal(r.foul,null);assert.deepEqual(s.groups,['stripes','solids']);assert.equal(r.nextPlayer,0);});
test('mixed groups use authoritative event order, not ball ID',()=>{const s=fixture({groups:[null,null]});resolve(s,[collision(1),pocket(10,0,1),pocket(1,0,2)]);assert.deepEqual(s.groups,['stripes','solids']);});
test('simultaneous pockets use tick, time and stable sequence',()=>{const s=fixture({groups:[null,null]});const a={...pocket(9),sequence:1};const b={...pocket(1),sequence:2};resolve(s,[b,collision(),a]);assert.equal(s.groups[0],'stripes');});
test('foul never assigns a group',()=>{const s=fixture({groups:[null,null]});assert.ok(resolve(s,[collision(),pocket(1),pocket(0)]).foul);assert.deepEqual(s.groups,[null,null]);});
test('8 is an illegal first target on open table',()=>assert.match(resolve(fixture({groups:[null,null]}),[collision(8),cushion()]).foul,/8-ball/));
test('legal first target and post-contact cushion produce a legal miss',()=>{const r=resolve(fixture(),[collision(1),cushion()]);assert.equal(r.foul,null);assert.equal(r.nextPlayer,1);});
test('wrong group first is a specific foul',()=>assert.match(resolve(fixture(),[collision(9),cushion()]).foul,/stripe first/));
test('missing all object balls is a foul',()=>assert.match(resolve(fixture(),[cushion()]).foul,/no object-ball/));
test('cushion before contact does not satisfy post-contact requirement',()=>assert.match(resolve(fixture(),[cushion(0,-1),collision()]).foul,/no pocket or cushion/));
test('pocket after legal contact satisfies rail requirement even for opponent ball',()=>{const s=fixture();const r=resolve(s,[collision(),pocket(9)]);assert.equal(r.foul,null);assert.equal(r.nextPlayer,1);assert.equal(s.balls[9].pocketed,true);});
test('own ball plus opponent ball on legal combination earns continuation',()=>{const r=resolve(fixture(),[collision(),pocket(9),pocket(1)]);assert.equal(r.foul,null);assert.equal(r.nextPlayer,0);});
test('scratch keeps object ball pocketed',()=>{const s=fixture();assert.match(resolve(s,[collision(),pocket(1),pocket(0)]).foul,/Scratch/);assert.equal(s.balls[1].pocketed,true);});
test('cue ball off table is a foul',()=>assert.match(resolve(fixture(),[collision(),{type:'off-table',ball:0,tick:2,time:0,sequence:2}]).foul,/left the table/));
test('early 8-ball loses including last group ball on same shot',()=>{for(const events of [[collision(),pocket(8,2)],[collision(),pocket(1),pocket(8,2)]]){const r=resolve(fixture(),events);assert.equal(r.winner,1);assert.match(r.reason,/before the group/);}});
test('legal called-pocket 8-ball wins',()=>{const r=resolve(fixture({eligibleEight:true}),[collision(8),pocket(8,2)]);assert.equal(r.winner,0);assert.equal(r.foul,null);});
test('wrong and uncalled pocket lose',()=>{for(const called of [null,1]){const s=fixture({eligibleEight:true});s.shotStart.calledPocket=called;const r=resolve(s,[collision(8),pocket(8,2)]);assert.equal(r.winner,1);assert.match(r.reason,/wrong or uncalled/);}});
test('8-ball with scratch loses',()=>{const r=resolve(fixture({eligibleEight:true}),[collision(8),pocket(8,2),pocket(0)]);assert.equal(r.winner,1);assert.match(r.reason,/foul/);});
test('scratch on 8 attempt without pocketing 8 is foul, not loss',()=>{const r=resolve(fixture({eligibleEight:true}),[collision(8),pocket(0)]);assert.equal(r.winner,null);assert.match(r.foul,/Scratch/);});
test('missing 8 without a pocket resolves normally',()=>{const r=resolve(fixture({eligibleEight:true}),[collision(8),cushion(8)]);assert.equal(r.winner,null);assert.equal(r.foul,null);assert.equal(r.nextPlayer,1);});
test('8-ball off table loses under selected profile',()=>{const r=resolve(fixture({eligibleEight:true}),[collision(8),{type:'off-table',ball:8,tick:1,time:0,sequence:1}]);assert.equal(r.winner,1);});
test('8-ball on break re-spots with no automatic win, including scratch',()=>{
    for(const scratch of [false,true]){const s=fixture({isBreak:true,groups:[null,null]});const r=resolve(s,[collision(1),pocket(8),...(scratch?[pocket(0)]:[])]);assert.equal(r.winner,null);assert.equal(s.balls[8].pocketed,false);assert.equal(s.balls[8].y,250);for(const b of s.balls)if(b.id!==8&&!b.pocketed)assert.ok(Math.hypot(s.balls[8].x-b.x,s.balls[8].y-b.y)>=22);}
});
test('occupied foot spot resolves to closest legal longitudinal position',()=>{const balls=rack(5);for(const b of balls)if(b.id!==0)b.pocketed=true;Object.assign(balls[0],{x:750,y:250});respot(balls);assert.ok(Math.abs(Math.abs(balls[8].x-750)-22.031)<.001);assert.equal(balls[8].y,250);});
test('ball-in-hand validation rejects pockets, cushion intersection, overlap and head-string crossing',()=>{const balls=rack(2);assert.equal(validPlacement(balls,100,250),true);for(const [x,y]of[[0,0],[500,11],[5,250],[750,250],[NaN,20]])assert.equal(validPlacement(balls,x,y),false);assert.equal(validPlacement(balls,300,200,true),false);assert.equal(validPlacement(balls,230,200,true),true);});
test('authority rejects out-of-turn placement and invalid position',()=>{const m=new PoolMatch({players,seed:4,breaker:0,now:()=>0});assert.throws(()=>m.confirmPlacement('two',100,100),/Out of turn/);assert.throws(()=>m.confirmPlacement('one',750,250),/Invalid/);assert.equal(m.state.phase,'BREAK_SETUP');});
test('deadline race: strictly before accepted, at or after rejected with one turn change',()=>{
    for(const offset of [-.001,0,.001]){let time=0;const m=ready(()=>time);time=m.state.deadline+offset;const c=command(m);if(offset<0){m.commit(c);assert.equal(m.state.phase,'SHOT_IN_PROGRESS');}else{assert.throws(()=>m.commit(c),/expired/);assert.equal(m.state.activePlayer,1);assert.equal(m.state.phase,'BALL_IN_HAND');assert.equal(m.expire(),false);}}
});
test('clock includes placement and pocket selection',()=>{let time=0;const m=new PoolMatch({players,seed:42,breaker:0,now:()=>time});const deadline=m.state.deadline;time=5000;m.confirmPlacement('one',200,200);assert.equal(m.state.deadline,deadline);for(const b of m.state.balls)if(groupOf(b.id)==='solids')b.pocketed=true;m.state.groups=['solids','stripes'];m.state.phase='POCKET_SELECTION';time=10000;m.selectPocket('one',2);assert.equal(m.state.deadline,deadline);});
test('duplicate shot submission never applies impulse twice',()=>{const m=ready();const c=command(m);m.commit(c,'one');const velocity=m.state.balls[0].vx;assert.equal(m.commit(c,'one').duplicate,true);assert.equal(m.state.shotNumber,1);assert.equal(m.state.balls[0].vx,velocity);});
test('duplicate IDs with altered payload or wrong authenticated identity are rejected',()=>{const m=ready();const c=command(m);m.commit(c,'one');assert.throws(()=>m.commit({...c,power:.2},'one'),/Conflicting/);assert.throws(()=>m.commit(c,'two'),/Conflicting/);});
test('stale revision and forged player identity rejected',()=>{const m=ready();assert.throws(()=>m.commit(command(m,{revision:0})),/Stale/);assert.throws(()=>m.commit(command(m),'two'),/Out of turn/);assert.throws(()=>m.commit(command(m,{playerId:'two'}),'one'),/Stale/);});
test('untrusted parameter ranges and nonfinite numbers rejected',()=>{for(const override of [{power:NaN},{power:2},{angle:Infinity},{spin:{x:1,y:1}},{spin:{x:NaN,y:0}},{placement:{x:1,y:1}}]){const m=ready();assert.throws(()=>m.commit(command(m,override)));}});
test('controls lock on commitment, shot clock is stopped, and second shot rejected',()=>{const m=ready();m.commit(command(m));assert.equal(m.state.deadline,null);assert.throws(()=>m.confirmPlacement('one',100,100),/locked/);assert.throws(()=>m.commit(command(m,{id:'second'})),/locked/);});
test('open-table shot snapshot has eligible object targets and no assigned remaining group',()=>{const m=ready();m.commit(command(m));assert.equal(m.state.shotStart.legalTargets.length,14);assert.equal(m.state.shotStart.legalTargets.includes(8),false);assert.deepEqual(m.state.shotStart.remaining,[]);});
test('called pocket phase blocks 8 attempt until selection and locks call on commitment',()=>{const m=ready();for(const b of m.state.balls)if(groupOf(b.id)==='solids')b.pocketed=true;m.state.groups=['solids','stripes'];m.state.phase='POCKET_SELECTION';assert.throws(()=>m.commit(command(m)),/selection/);m.selectPocket('one',2);m.commit(command(m));assert.equal(m.state.shotStart.calledPocket,2);assert.throws(()=>m.selectPocket('one',1),/locked/);});
test('terminal match prevents any further shots',()=>{const m=ready();m.state.phase='MATCH_OVER';m.state.winner=1;assert.throws(()=>m.commit(command(m)),/locked/);});
test('local pause preserves monotonic remaining clock and stable restoration',()=>{let time=0;const m=ready(()=>time);time=5000;m.pause();time=50000;assert.equal(m.expire(),false);const serialized=m.serialize();m.resume();assert.equal(m.state.deadline,75000);const next=ready(()=>time);next.restore(serialized);assert.equal(next.state.deadline,75000);assert.deepEqual(next.state.balls,m.state.balls);});
test('incompatible or moving saved match rejected',()=>{const m=ready();const saved=JSON.parse(m.serialize());saved.state.rulesProfile='unknown';assert.throws(()=>m.restore(JSON.stringify(saved)),/invalid/);saved.state.rulesProfile=`${RULES.id}@${RULES.version}`;saved.state.balls[0].vx=1;assert.throws(()=>m.restore(JSON.stringify(saved)),/invalid/);});
test('maximum speed cannot tunnel through an object ball',()=>{const balls=rack(2);for(const b of balls)if(b.id>1)b.pocketed=true;Object.assign(balls[0],{x:200,y:250});Object.assign(balls[1],{x:240,y:250});const sim=new Simulation(balls);sim.strike(0,1,{x:0,y:0});for(let i=0;i<10;i++)sim.step();assert.ok(sim.events.some(e=>e.type==='collision'&&e.a===0&&e.b===1));assert.ok(balls[1].vx>1000);});
test('pocket capture occurs once and is separate from animation',()=>{const balls=rack(2);for(const b of balls)if(b.id) b.pocketed=true;Object.assign(balls[0],{x:500,y:50,vx:0,vy:-1000});const sim=new Simulation(balls);for(let i=0;i<100;i++)sim.step();assert.equal(sim.events.filter(e=>e.type==='pocket'&&e.ball===0).length,1);assert.equal(balls[0].pocketed,true);});
test('prediction uses expanded circles and stops at first actual collision geometry',()=>{const balls=rack(1);const guide=predict(balls,0);assert.equal(guide.target.id,balls.find(b=>b.x===750).id);assert.ok(Math.abs(guide.x-728)<.001);const rail=predict(balls,Math.PI/2);assert.equal(rail.target,null);assert.ok(Math.abs(rail.y-489)<.001);});
test('follow and draw produce distinct post-impact cue paths',()=>{
    const positions=[];
    for(const spin of [-1,0,1]){const balls=rack(2);for(const b of balls)if(b.id>1)b.pocketed=true;Object.assign(balls[0],{x:200,y:250});Object.assign(balls[1],{x:250,y:250});const sim=new Simulation(balls);sim.strike(0,.5,{x:0,y:spin});for(let i=0;i<240;i++)sim.step();positions.push(balls[0].x);}
    assert.ok(positions[0]<positions[1]-10);assert.ok(positions[2]>positions[1]+10);
});
test('equivalent complete break outcomes at 30, 60 and 144 render fps',()=>{
    const outcomes=[];
    for(const fps of [30,60,144]){const m=ready();m.commit(command(m));const stepper=new FixedStepper(m);for(let frames=0;frames<fps*120&&m.simulation;frames++)stepper.advance(1/fps);assert.equal(m.simulation,null);outcomes.push({balls:m.state.balls,events:m.state.shotEvents,groups:m.state.groups,active:m.state.activePlayer,foul:m.state.foul});}
    assert.deepEqual(outcomes[0],outcomes[1]);assert.deepEqual(outcomes[0],outcomes[2]);
});
test('complete shot emits stable atomic outcome once and retains ordered events',()=>{const observed=[];const m=ready();m.onEvent=e=>{if(e.type==='turn-change')observed.push({phase:m.state.phase,foul:m.state.foul,active:m.state.activePlayer,deadline:m.state.deadline});};m.commit(command(m));settle(m);assert.equal(observed.length,1);assert.equal(m.state.history.length,1);assert.notEqual(observed[0].phase,'RESOLVING_SHOT');assert.ok(observed[0].deadline>0);assert.equal(m.state.balls.every(b=>b.pocketed||Math.hypot(b.vx,b.vy)===0),true);});

function powerFixture() {
    const element=new EventTarget(),capture=new Set(),abort=new AbortController();
    element.setPointerCapture=id=>capture.add(id);element.releasePointerCapture=id=>capture.delete(id);element.hasPointerCapture=id=>capture.has(id);element.getBoundingClientRect=()=>({height:200});
    let power=0,shots=0,enabled=true;
    const control=bindPowerMeter({element,signal:abort.signal,isEnabled:()=>enabled,onPower:value=>power=value,onCommit:()=>{shots++;}});
    const send=(type,y=0,id=1)=>element.dispatchEvent(Object.assign(new Event(type),{pointerId:id,clientY:y,button:0}));
    return{control,send,abort,get power(){return power;},get shots(){return shots;},disable(){enabled=false;}};
}
test('power release commits exactly once after a drag',()=>{const p=powerFixture();p.send('pointerdown',10);p.send('pointermove',110);assert.equal(p.power,.5);p.send('pointerup',110);p.send('pointerup',110);assert.equal(p.shots,1);});
test('tap without power drag never shoots',()=>{const p=powerFixture();p.send('pointerdown',10);p.send('pointerup',10);assert.equal(p.shots,0);assert.equal(p.power,0);});
test('touch cancellation, lost capture and explicit Escape/resize cancellation never shoot',()=>{for(const cancellation of ['pointercancel','lostpointercapture','explicit']){const p=powerFixture();p.send('pointerdown');p.send('pointermove',150);if(cancellation==='explicit')p.control.cancel();else p.send(cancellation);p.send('pointerup',150);assert.equal(p.shots,0);assert.equal(p.power,0);}});
test('second pointer cannot steer or release another pointer power gesture',()=>{const p=powerFixture();p.send('pointerdown');p.send('pointermove',100,2);p.send('pointerup',100,2);assert.equal(p.power,0);assert.equal(p.shots,0);p.send('pointermove',100);p.send('pointerup',100);assert.equal(p.shots,1);});
test('timeout or disabled controls prevent a pending pointer release shot',()=>{const p=powerFixture();p.send('pointerdown');p.send('pointermove',100);p.disable();p.send('pointerup',100);assert.equal(p.shots,0);assert.equal(p.power,0);});
test('disposal aborts power listeners',()=>{const p=powerFixture();p.abort.abort();p.send('pointerdown');p.send('pointermove',100);p.send('pointerup',100);assert.equal(p.shots,0);});
function tableFixture() {
    const element=new EventTarget(),capture=new Set(),abort=new AbortController();
    element.setPointerCapture=id=>capture.add(id);
    element.hasPointerCapture=id=>capture.has(id);
    element.releasePointerCapture=id=>{capture.delete(id);send('lostpointercapture',id);};
    const send=(type,id=1)=>element.dispatchEvent(Object.assign(new Event(type),{pointerId:id,button:0}));
    let owner=null,moves=0;
    const control=bindTableDrag({element,signal:abort.signal,isEnabled:()=>true,onMove:()=>moves++,onCapture:id=>{owner=id;}});
    return {send,control,get owner(){return owner;},get moves(){return moves;}};
}
test('lost table capture clears ownership and permits the next table drag',()=>{const t=tableFixture();t.send('pointerdown');assert.equal(t.owner,1);t.send('lostpointercapture');assert.equal(t.owner,null);assert.equal(t.control.active,false);t.send('pointerdown',2);t.send('pointermove',2);assert.equal(t.owner,2);assert.equal(t.moves,3);t.send('pointerup',2);assert.equal(t.owner,null);});
test('lost capture from a different pointer does not cancel the table owner',()=>{const t=tableFixture();t.send('pointerdown');t.send('lostpointercapture',2);assert.equal(t.owner,1);assert.equal(t.control.active,true);});
test('losing table capture unlocks the power meter for a subsequent shot',()=>{
    const t=tableFixture(),element=new EventTarget();let power=0,shots=0;
    element.setPointerCapture=()=>{};element.hasPointerCapture=()=>false;element.getBoundingClientRect=()=>({height:100});
    bindPowerMeter({element,signal:new AbortController().signal,isEnabled:()=>t.owner===null,onPower:p=>{power=p;},onCommit:()=>{if(power>0)shots++;}});
    const send=(type,y=0)=>element.dispatchEvent(Object.assign(new Event(type),{pointerId:2,clientY:y,button:0}));
    t.send('pointerdown');send('pointerdown');assert.equal(power,0);t.send('lostpointercapture');send('pointerdown');send('pointermove',75);send('pointerup',75);assert.equal(shots,1);
});
test('screen-to-world conversion stays correct after portrait/landscape resize and DPR change',()=>{
    let rect={left:10,top:20,width:1120,height:620};
    const canvas={getContext:()=>({}),getBoundingClientRect:()=>rect};
    globalThis.devicePixelRatio=1;const renderer=new Renderer(canvas);
    assert.deepEqual(renderer.point({clientX:570,clientY:330}),{x:500,y:250});
    rect={left:30,top:80,width:336,height:186};globalThis.devicePixelRatio=2;renderer.resize();
    assert.deepEqual(renderer.point({clientX:198,clientY:173}),{x:500,y:250});assert.equal(canvas.width,672);
});
test('full power breaks with side spin settle without live overlaps',()=>{
    for(const seed of [1,10,100]){const m=ready();m.state.balls=rack(seed);m.state.balls[0].x=240;m.commit(command(m,{power:1,angle:.02,spin:{x:.6,y:.8}}));settle(m);const live=m.state.balls.filter(b=>!b.pocketed);for(let i=0;i<live.length;i++)for(let j=i+1;j<live.length;j++)assert.ok(Math.hypot(live[i].x-live[j].x,live[i].y-live[j].y)>=P.radius*2-.05);}
});
