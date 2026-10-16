import { RULES, PHYSICS as P, POCKETS, groupOf, clamp } from './config.mjs';
import { PoolMatch, FixedStepper, targets } from './match.mjs';
import { validPlacement } from './physics.mjs';
import { Renderer, COLORS } from './render.mjs';
import { PoolAudio } from './audio.mjs';
import { bindPowerMeter, bindTableDrag } from './input.mjs';

const $ = id => document.getElementById(id);
const reducedMotion = matchMedia('(prefers-reduced-motion: reduce)').matches;

export class HuddspoolGame {
    constructor(root = $('pool')) {
        this.root = root; this.abort = new AbortController(); this.renderer = new Renderer($('table'));
        this.audio = new PoolAudio(); this.angle = 0; this.power = 0; this.spin = { x: 0, y: 0 };
        this.scores = [0, 0]; this.rackNumber = 1; this.effects = []; this.gesture = null; this.disposed = false;
        this.storageKey = 'huddspool.pool.v1.guest'; this.lastTime = performance.now(); this.lowSecond = null;
        this.on = (target, event, fn, options = {}) => target.addEventListener(event, fn, { ...options, signal: this.abort.signal });
        this.bind();
        this.resizeObserver = new ResizeObserver(() => { this.cancelGesture(); this.renderer.resize(); }); this.resizeObserver.observe($('table'));
        this.on(document, 'visibilitychange', () => {
            this.cancelGesture();
            if (document.hidden) { this.pause(); this.save(); }
            else if (!$('modal').open) this.resume();
        });
        this.on(window, 'pagehide', () => { this.pause(); this.save(); });
        this.on(window, 'pageshow', () => { if (!$('modal').open && !document.hidden) this.resume(); });
        this.frame = this.frame.bind(this);
    }
    initialize({ players = [{ id: 'local-1', name: 'Player 1' }, { id: 'local-2', name: 'Player 2' }], storageId = 'guest', returnUrl = '/' } = {}) {
        if (this.match) return;
        this.returnUrl = new URL(returnUrl, location.origin).origin === location.origin ? returnUrl : '/';
        document.querySelector('.brand').href = this.returnUrl;
        this.storageKey = `huddspool.pool.v1.${storageId}`;
        this.players = players.map(p => ({ id: String(p.id), name: String(p.name).slice(0, 32), avatar: p.avatar || null }));
        this.newRack();
        try {
            const prefs = JSON.parse(localStorage.getItem('huddspool.pool.settings') || '{}');
            this.audio.muted = !!prefs.muted; this.audio.volume = clamp(Number(prefs.volume ?? .45),0,1);
            this.root.classList.toggle('right-handed', prefs.handedness === 'right'); this.haptics = !!prefs.haptics;
            const saved = localStorage.getItem(this.storageKey);
            if (saved) {
                const checkpoint = JSON.parse(saved); this.match.restore(checkpoint.snapshot);
                this.players = this.match.state.players;
                this.scores = checkpoint.scores || [0,0]; this.rackNumber = checkpoint.rackNumber || 1;
                if (checkpoint.command) {
                    this.match.commit(checkpoint.command);
                    let steps = 0;
                    while (this.match.simulation && steps++ < 60000) this.match.step();
                    if (this.match.simulation) throw new Error('Saved shot failed to settle');
                }
                this.stepper = new FixedStepper(this.match); this.placement = { x: 240, y: 250 }; this.update();
                this.openDialog('Welcome back.', '<p>Your local match is ready to resume. The shot clock was paused while you were away.</p><div class="actions"><button id="resume-match" class="primary">Resume match</button></div>');
                this.on($('resume-match'),'click',()=>{ $('modal').close(); if(this.match.state.phase==='MATCH_OVER')this.results(); });
            } else this.playerDialog();
        } catch (error) {
            this.match.dispose(); this.newRack();
            this.playerDialog(); this.setStatus('Starting a fresh table. Saved data or browser storage was unavailable.');
        }
        $('mute').setAttribute('aria-pressed',String(this.audio.muted));
        this.raf = requestAnimationFrame(this.frame);
        this.emit('connection-status', { mode: 'local', connected: true });
    }
    configurePlayers(players) {
        if (this.match.state.shotNumber > 0 || players.length !== 2 || String(players[0].id) === String(players[1].id)) throw new Error('Configure two distinct players before the first shot');
        this.players = players.map(p => ({ id: String(p.id), name: String(p.name).trim().slice(0,32) || 'Player', avatar: p.avatar || null }));
        this.match.state.players = this.players; this.update(); this.save();
    }
    newRack(alternate = false) {
        const breaker = alternate && this.match ? 1 - this.match.state.breaker : undefined;
        this.match?.dispose();
        this.match = new PoolMatch({ players: this.players, breaker, onEvent: event => this.handle(event) });
        if (alternate) this.match.state.breakerSelection = { method: 'alternate-rematch', value: breaker };
        this.stepper = new FixedStepper(this.match); this.angle = 0; this.spin = {x:0,y:0}; this.power = 0; this.effects = [];
        this.placement = { x:240, y:250 }; this.pending = null; this.update();
    }
    emit(type, detail) {
        this.root.dispatchEvent(new CustomEvent(`huddspool:${type}`, { bubbles:true, detail }));
        if (window.parent !== window) window.parent.postMessage({ type:'huddspool:event', event:{ type,...detail } },location.origin);
    }
    handle(event) {
        if (['foul','turn-change','match-end'].includes(event.type)) this.cancelGesture();
        if (event.type === 'physics') this.audio.play(event.event.type,Math.min(1,event.event.intensity/500));
        if (event.type === 'ball-pocketing') {
            this.audio.play('pocket');
            if (!reducedMotion) this.effects.push({...event.event,started:performance.now(),progress:0});
        }
        if (event.type === 'shot-commitment') this.audio.play('strike',event.command.power);
        if (event.type === 'foul') this.audio.play('foul');
        if (event.type === 'turn-change') this.audio.play('turn');
        if (event.type === 'group-assignment') this.audio.play('group');
        if (event.type === 'match-end') {
            this.scores[event.winner]++; this.audio.play('win');
            setTimeout(() => { if (!this.disposed) this.results(); },0);
        }
        if (event.type === 'state-stable') {
            this.pending = null;
            if (this.match) { this.update(); this.save(); }
        }
        this.emit(event.type,event);
    }
    playerId() { return this.match.state.players[this.match.state.activePlayer].id; }
    canAim() { return this.match && this.match.state.phase === 'AIMING' && !this.match.paused; }
    tryAction(action) { try { action(); } catch(error) { this.setStatus(error.message); } }
    setStatus(text) { $('status').textContent = text; }
    setPower(power) {
        this.power=clamp(power,0,1);
        document.querySelector('.power-fill').style.height=`${this.power*94}%`;
        document.querySelector('.power-handle').style.top=`${this.power*92}%`;
        $('power').setAttribute('aria-valuenow',Math.round(this.power*100)); $('power-number').textContent=`${Math.round(this.power*100)}%`;
        $('shoot').disabled=!this.canAim() || this.power<.01;
    }
    commit() {
        if(!this.canAim() || this.power<.01) return;
        const state=this.match.state;
        const command={id:crypto.randomUUID(),matchId:state.matchId,revision:state.revision,playerId:this.playerId(),
            angle:this.angle,power:this.power,spin:{...this.spin},placement:{x:state.balls[0].x,y:state.balls[0].y},calledPocket:state.selectedPocket};
        this.tryAction(()=>{
            const snapshot=this.match.serialize();
            this.match.commit(command);
            this.pending={snapshot,command}; this.save();
            this.spin={x:0,y:0}; this.setPower(0); this.cancelGesture(); this.update();
            if(this.haptics) navigator.vibrate?.(12);
        });
    }
    update() {
        if(!this.match) return;
        const state=this.match.state, locked=!this.canAim();
        this.setStatus(state.status);
        for(let i=0;i<2;i++) {
            const panel=$(`player-${i}`), player=state.players[i];
            panel.classList.toggle('active',state.activePlayer===i && state.phase!=='MATCH_OVER');
            panel.querySelector('.player-name').textContent=player.name;
            panel.querySelector('.player-label').textContent=state.groups[i] ? `${state.groups[i]} · ${state.balls.filter(b=>!b.pocketed && groupOf(b.id)===state.groups[i]).length} remaining` : 'OPEN TABLE';
            const avatar=panel.querySelector('.avatar'); avatar.replaceChildren();
            if(player.avatar && /^https?:/.test(player.avatar)) { const image=document.createElement('img');image.src=player.avatar;image.alt='';avatar.append(image); }
            else avatar.textContent=player.name.split(' ').map(s=>s[0]).join('').slice(0,2).toUpperCase();
            const track=panel.querySelector('.ball-track');track.replaceChildren();
            const ids=state.groups[i]==='solids'?[1,2,3,4,5,6,7]:state.groups[i]==='stripes'?[9,10,11,12,13,14,15]:[];
            for(const id of ids){const ball=document.createElement('span');ball.className=`mini-ball ${id>8?'stripe':''} ${state.balls[id].pocketed?'potted':''}`;ball.style.setProperty('--ball',COLORS[id]);ball.textContent=id;ball.title=`Ball ${id}${state.balls[id].pocketed?' pocketed':''}`;track.append(ball);}
            if(!ids.length) {const note=document.createElement('span');note.textContent='SOLIDS OR STRIPES';note.style.cssText='font-size:7px;letter-spacing:1px;color:#6f8875';track.append(note);}
        }
        $('score').replaceChildren(document.createTextNode(`${this.scores[0]} `),Object.assign(document.createElement('span'),{textContent:'–'}),document.createTextNode(` ${this.scores[1]}`));
        $('rack-count').textContent=`RACK ${String(this.rackNumber).padStart(2,'0')}`;
        $('confirm').hidden=!['BREAK_SETUP','BALL_IN_HAND'].includes(state.phase);
        $('confirm').disabled=this.match.paused || !validPlacement(state.balls,this.placement.x,this.placement.y,state.phase==='BREAK_SETUP');
        for(const id of ['spin','fine','aim-left','aim-right']) $(id).disabled=locked;
        $('power').setAttribute('aria-disabled',String(locked)); $('power').tabIndex=locked?-1:0;this.setPower(this.power);
        const marker=$('spin').querySelector('span');marker.style.left=`calc(${50+this.spin.x*35}% - 3.5px)`;marker.style.top=`calc(${50-this.spin.y*35}% - 3.5px)`;
        $('spin-description').textContent=Math.hypot(this.spin.x,this.spin.y)<.05?'Center ball':`${this.spin.y>.15?'Follow':this.spin.y<-.15?'Draw':''} ${this.spin.x<-.15?'Left':this.spin.x>.15?'Right':''}`.trim();
        const pockets=$('pocket-buttons');pockets.replaceChildren();
        if(['AIMING','POCKET_SELECTION'].includes(state.phase) && targets(state).includes(8)) {
            POCKETS.forEach((p,i)=>{const button=document.createElement('button');button.textContent=i+1;button.className=state.selectedPocket===i?'selected':'';button.setAttribute('aria-label',`Call pocket ${i+1}`);button.setAttribute('aria-pressed',String(state.selectedPocket===i));button.style.left=`${(p.x+60)/1120*100}%`;button.style.top=`${(p.y+60)/620*100}%`;button.disabled=this.match.paused;this.on(button,'click',()=>this.tryAction(()=>{this.match.selectPocket(this.playerId(),i);this.update();}));pockets.append(button);});
        }
        this.root.classList.toggle('paused',this.match.paused);
    }
    bind() {
        this.on(this.root,'pointerdown',()=>this.audio.unlock(),{passive:true});
        this.on(this.root,'keydown',()=>this.audio.unlock(),{passive:true});
        const canvas=$('table');
        const tableMove=event=>{
            const point=this.renderer.point(event),state=this.match.state;
            if(['BREAK_SETUP','BALL_IN_HAND'].includes(state.phase)) {this.placement=point;$('confirm').disabled=!validPlacement(state.balls,point.x,point.y,state.phase==='BREAK_SETUP');}
            else if(this.canAim()) this.angle=Math.atan2(point.y-state.balls[0].y,point.x-state.balls[0].x);
        };
        this.tableInput=bindTableDrag({element:canvas,signal:this.abort.signal,
            isEnabled:()=>this.match&&!this.match.paused&&!this.powerInput.active&&['BREAK_SETUP','BALL_IN_HAND','AIMING'].includes(this.match.state.phase),
            onMove:tableMove,onCapture:id=>{this.gesture=id===null?null:{type:'table',id};},
        });
        this.powerInput=bindPowerMeter({element:$('power'),signal:this.abort.signal,isEnabled:()=>this.canAim()&&!this.gesture,onPower:power=>this.setPower(power),onCommit:()=>this.commit()});
        this.on($('power'),'keydown',event=>{if(!this.canAim())return;if(['ArrowUp','ArrowDown','ArrowLeft','ArrowRight'].includes(event.key)){event.preventDefault();this.setPower(this.power+(['ArrowDown','ArrowRight'].includes(event.key)?.05:-.05));}});
        this.on($('shoot'),'click',()=>this.commit());
        this.on($('confirm'),'click',()=>this.tryAction(()=>{this.match.confirmPlacement(this.playerId(),this.placement.x,this.placement.y);this.update();}));
        this.on($('aim-left'),'click',()=>this.adjustAim(-.002));this.on($('aim-right'),'click',()=>this.adjustAim(.002));
        let fine=0;this.on($('fine'),'input',()=>{const next=Number($('fine').value);this.adjustAim((next-fine)*.0008);fine=next;});
        this.on($('fine'),'change',()=>{fine=0;$('fine').value=0;});
        this.on(canvas,'keydown',event=>{
            if(!this.match || this.match.paused)return;
            if(event.key.startsWith('Arrow')){event.preventDefault();const delta=event.shiftKey?1:5;
                if(['BREAK_SETUP','BALL_IN_HAND'].includes(this.match.state.phase)){this.placement.x+=(event.key==='ArrowRight'?delta:event.key==='ArrowLeft'?-delta:0);this.placement.y+=(event.key==='ArrowDown'?delta:event.key==='ArrowUp'?-delta:0);this.update();}
                else this.adjustAim((event.key==='ArrowRight'||event.key==='ArrowDown'?1:-1)*(event.shiftKey?.0002:.002));}
        });
        this.on(document,'keydown',event=>{if(event.key==='Escape')this.cancelGesture();});
        this.on($('mute'),'click',()=>{this.audio.muted=!this.audio.muted;$('mute').setAttribute('aria-pressed',String(this.audio.muted));$('mute').setAttribute('aria-label',this.audio.muted?'Unmute sound':'Mute sound');this.saveSettings();});
        this.on($('settings'),'click',()=>this.settings());this.on($('help'),'click',()=>this.help());this.on($('spin'),'click',()=>this.spinDialog());
        this.on($('new-match'),'click',()=>{this.openDialog('A fresh table?', '<p>Your current local match will be replaced when you start.</p><div class="actions"><button id="fresh" class="primary">Start new match</button><button id="keep">Keep playing</button></div>');this.on($('keep'),'click',()=>$('modal').close());this.on($('fresh'),'click',()=>{this.scores=[0,0];this.rackNumber=1;this.newRack();$('modal').close();this.playerDialog();});});
        this.on($('exit'),'click',()=>{this.openDialog('Leave the clubroom?', '<p>Your match will be saved on this device. You can pick it up when you return.</p><div class="actions"><button id="leave" class="primary">Save & return</button><button id="stay">Keep playing</button></div>');this.on($('stay'),'click',()=>$('modal').close());this.on($('leave'),'click',()=>{this.save();window.top.location.href=this.returnUrl;});});
        this.on($('modal'),'close',()=>{if(!document.hidden&&!$('modal').open)this.resume();});
    }
    adjustAim(delta) {if(this.canAim())this.angle=Math.atan2(Math.sin(this.angle+delta),Math.cos(this.angle+delta));}
    cancelGesture() {
        this.tableInput?.cancel();this.gesture=null;
        this.powerInput?.cancel();this.setPower(0);
    }
    openDialog(title,html) {this.cancelGesture();this.pause();$('modal-content').innerHTML='<h2></h2>'+html;$('modal-content').querySelector('h2').textContent=title;if(!$('modal').open)$('modal').showModal();}
    playerDialog() {
        this.openDialog('Meet at the table.', '<p>Two people. One table. Pass the device when the turn changes.</p><label>Player one<input id="name-0" type="text" maxlength="32" autocomplete="off"></label><label>Player two<input id="name-1" type="text" maxlength="32" autocomplete="off"></label><div class="actions"><button id="start" class="primary">Let’s play →</button></div>');
        $('name-0').value=this.players[0].name;$('name-1').value=this.players[1].name;
        this.on($('start'),'click',()=>{this.configurePlayers(this.players.map((p,i)=>({...p,name:$(`name-${i}`).value})));$('modal').close();this.save();});
    }
    settings() {
        this.openDialog('Make yourself comfortable.', '<label>Power bar side<select id="hand"><option value="left">Left</option><option value="right">Right</option></select></label><label>Sound volume<input id="volume" type="range" min="0" max="100"></label><label><input id="haptics" type="checkbox"> Gentle haptics (supported devices)</label><p>Local shot clock: 30 seconds. Paused while this menu is open. Both players use the same aiming assistance.</p>');
        $('hand').value=this.root.classList.contains('right-handed')?'right':'left';$('volume').value=this.audio.volume*100;$('haptics').checked=this.haptics;
        this.on($('hand'),'change',()=>{this.root.classList.toggle('right-handed',$('hand').value==='right');this.saveSettings();});
        this.on($('volume'),'input',()=>{this.audio.volume=Number($('volume').value)/100;this.saveSettings();this.audio.play('turn');});
        this.on($('haptics'),'change',()=>{this.haptics=$('haptics').checked;this.saveSettings();});
    }
    saveSettings() {try{localStorage.setItem('huddspool.pool.settings',JSON.stringify({muted:this.audio.muted,volume:this.audio.volume,handedness:this.root.classList.contains('right-handed')?'right':'left',haptics:this.haptics}));}catch{}}
    spinDialog() {
        if(!this.canAim())return;
        this.openDialog('A little English.', '<p>Top for follow, bottom for draw. Move left or right to change the cushion response.</p><div id="spin-pad" role="slider" tabindex="0" aria-label="Spin contact. Arrow keys move the marker; Home centers it." aria-valuemin="-100" aria-valuemax="100" aria-valuenow="0"><span id="spin-marker"></span></div><div class="actions"><button id="center-spin">Center ball</button><button id="done-spin" class="primary">Set spin</button></div>');
        const sync=()=>{const marker=$('spin-marker');marker.style.left=`${50+this.spin.x*43}%`;marker.style.top=`${50-this.spin.y*43}%`;$('spin-pad').setAttribute('aria-valuetext',`Side ${Math.round(this.spin.x*100)}, follow ${Math.round(this.spin.y*100)}`);};
        const set=(x,y)=>{const length=Math.hypot(x,y);this.spin={x:length>1?x/length:x,y:length>1?y/length:y};sync();};
        const move=event=>{const r=$('spin-pad').getBoundingClientRect();set((event.clientX-r.left-r.width/2)/(r.width*.43),-(event.clientY-r.top-r.height/2)/(r.height*.43));};
        let spinPointer=null;
        this.on($('spin-pad'),'pointerdown',event=>{spinPointer=event.pointerId;$('spin-pad').setPointerCapture(event.pointerId);move(event);});
        this.on($('spin-pad'),'pointermove',event=>{if(event.pointerId===spinPointer)move(event);});
        this.on($('spin-pad'),'pointerup',()=>{spinPointer=null;});this.on($('spin-pad'),'pointercancel',()=>{spinPointer=null;});
        this.on($('spin-pad'),'keydown',event=>{if(event.key==='Home'){event.preventDefault();set(0,0);}else if(event.key.startsWith('Arrow')){event.preventDefault();set(this.spin.x+(event.key==='ArrowRight'?.05:event.key==='ArrowLeft'?-.05:0),this.spin.y+(event.key==='ArrowUp'?.05:event.key==='ArrowDown'?-.05:0));}});
        this.on($('center-spin'),'click',()=>set(0,0));this.on($('done-spin'),'click',()=>{$('modal').close();this.update();});sync();
    }
    help() {this.openDialog('Your next great shot.', '<p>Place and confirm the cue ball. Drag on the table to aim. Pull the power bar down and release to shoot. Escape cancels a power gesture.</p><p>Keyboard: table arrow keys aim (Shift for precision); placement arrows move the cue ball. Focus the power bar and use arrows to set power, then activate Shoot. Fine aim and spin also support a keyboard.</p><p>The break leaves an open table. Your first legal post-break pocket assigns solids or stripes. Clear your group, call a pocket, then sink the 8-ball. A scratch or foul gives your opponent ball-in-hand anywhere.</p><p>Menus and backgrounding pause local play. No three-foul loss. Huddspool default rules v1.0.0.</p>');}
    results() {
        const s=this.match.state;
        this.openDialog('Rack well played.', '<p id="winner-text"></p><p id="result-text"></p><div class="actions"><button id="rematch" class="primary">Rematch →</button><button id="result-exit">Return to Huddspool</button></div>');
        $('winner-text').textContent=`${s.players[s.winner].name} wins. ${s.players[1-s.winner].name} loses this rack.`;$('result-text').textContent=s.resultReason;
        this.on($('rematch'),'click',()=>{this.rackNumber++;this.newRack(true);$('modal').close();this.save();});
        this.on($('result-exit'),'click',()=>{this.save();window.top.location.href=this.returnUrl;});
    }
    pause() {this.match?.pause();this.cancelGesture();this.root.classList.add('paused');}
    resume() {this.match?.resume();this.lastTime=performance.now();this.update();}
    serialize() {return this.match.serialize();}
    restore(value) {this.match.restore(value);this.stepper=new FixedStepper(this.match);this.update();}
    save() {
        if(!this.match)return;
        try {const checkpoint=this.pending || {snapshot:this.match.serialize()};localStorage.setItem(this.storageKey,JSON.stringify({...checkpoint,scores:this.scores,rackNumber:this.rackNumber}));}
        catch { $('connection').textContent='Pass & play · saving unavailable'; }
    }
    frame(time) {
        if(this.disposed)return;
        const dt=(time-this.lastTime)/1000;this.lastTime=time;
        const alpha=this.stepper.advance(dt);this.match.expire();
        const seconds=this.match.state.deadline===null?null:Math.max(0,Math.ceil((this.match.state.deadline-(this.match.paused?this.match.pausedTime:performance.now()))/1000));
        $('clock').replaceChildren(document.createTextNode(seconds===null?'—':seconds),Object.assign(document.createElement('span'),{textContent:seconds===null?'':'s'}));$('clock').classList.toggle('low',seconds!==null&&seconds<=5);
        document.querySelectorAll('.clock-track>div').forEach((bar,i)=>{bar.style.width=i===this.match.state.activePlayer&&seconds!==null?`${seconds/RULES.clockSeconds*100}%`:'0%';});
        if(seconds!==null&&seconds<=5&&seconds!==this.lowSecond&&!this.match.paused){this.audio.play('low');this.lowSecond=seconds;}
        this.effects=this.effects.filter(effect=>{effect.progress=(time-effect.started)/650;return effect.progress<1;});
        this.renderer.draw(this.match.state,{angle:this.angle,power:this.power,placement:this.placement,previous:this.match.simulation?this.stepper.previous:null,alpha,effects:this.effects});
        this.raf=requestAnimationFrame(this.frame);
    }
    dispose() {this.save();this.disposed=true;cancelAnimationFrame(this.raf);this.abort.abort();this.resizeObserver.disconnect();this.match?.dispose();this.audio.dispose();}
}

const game=new HuddspoolGame();
window.huddspoolGame=game;
if(window.parent===window)game.initialize();
else {
    const receive=event=>{
        if(event.origin!==location.origin||event.source!==parent)return;
        if(event.data?.type==='huddspool:init')game.initialize(event.data.config);
        if(event.data?.type==='huddspool:host-ready')parent.postMessage({type:'huddspool:ready'},location.origin);
    };
    game.on(window,'message',receive);parent.postMessage({type:'huddspool:ready'},location.origin);
}
