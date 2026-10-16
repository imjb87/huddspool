import { PHYSICS as P, POCKETS, CUSHIONS } from './config.mjs';
import { predict, validPlacement } from './physics.mjs';
import { targets } from './match.mjs';
export const COLORS = ['#eeeedd','#e9b92e','#4072b1','#cd5146','#8d569c','#e9933d','#448e79','#9a4244','#151d20','#e9b92e','#4072b1','#cd5146','#8d569c','#e9933d','#448e79','#9a4244'];
export class Renderer {
    constructor(canvas) {
        this.canvas = canvas; this.ctx = canvas.getContext('2d');
        if (!this.ctx) throw new Error('Canvas rendering is unavailable in this browser.');
        this.resize();
    }
    resize() {
        const { width, height } = this.canvas.getBoundingClientRect();
        this.portrait = height > width;
        const dpr = Math.min(devicePixelRatio || 1, 2.5);
        this.canvas.width = Math.round(width * dpr);
        this.canvas.height = Math.round(width * (this.portrait ? 1120 / 620 : 620 / 1120) * dpr);
    }
    point(event) {
        const rect = this.canvas.getBoundingClientRect();
        if (this.portrait) return { x: (event.clientY - rect.top) * 1120 / rect.height - 60, y: 560 - (event.clientX - rect.left) * 620 / rect.width };
        return { x: (event.clientX - rect.left) * 1120 / rect.width - 60, y: (event.clientY - rect.top) * 620 / rect.height - 60 };
    }
    project(x, y) { return this.portrait ? { left: (560 - y) / 620 * 100, top: (x + 60) / 1120 * 100 } : { left: (x + 60) / 1120 * 100, top: (y + 60) / 620 * 100 }; }
    circle(x, y, radius, color) { const c = this.ctx; c.beginPath(); c.arc(x, y, radius, 0, Math.PI * 2); c.fillStyle = color; c.fill(); }
    line(x1, y1, x2, y2, color, width = 1) { const c = this.ctx; c.beginPath(); c.moveTo(x1, y1); c.lineTo(x2, y2); c.strokeStyle = color; c.lineWidth = width; c.stroke(); }
    ball(ball, alpha = 1) {
        const c = this.ctx, r = P.radius, { x, y, id } = ball;
        c.save(); c.globalAlpha = alpha;
        if (this.portrait) { c.translate(x, y); c.rotate(-Math.PI / 2); c.translate(-x, -y); }
        this.circle(x + 1.5, y + 3, r + 1, '#001a1480');
        this.circle(x, y, r, id > 8 ? '#f0eee2' : COLORS[id]);
        if (id > 8) {
            c.save(); c.beginPath(); c.arc(x, y, r, 0, Math.PI * 2); c.clip();
            c.translate(x, y); c.rotate(Math.sin(ball.rotation || 0) * 0.45);
            c.fillStyle = COLORS[id]; c.fillRect(-r, -r * 0.6, r * 2, r * 1.2); c.restore();
        }
        const gradient = c.createRadialGradient(x - r * .35, y - r * .4, 1, x + r * .2, y + r * .2, r * 1.3);
        gradient.addColorStop(0, '#ffffff66'); gradient.addColorStop(.55, '#ffffff00'); gradient.addColorStop(1, '#000000aa');
        this.circle(x, y, r, gradient);
        if (id) {
            this.circle(x, y, r * .48, '#f6f2e4'); c.fillStyle = '#17201d'; c.font = 'bold 8px system-ui'; c.textAlign = 'center'; c.textBaseline = 'middle'; c.fillText(id, x, y + .3);
        }
        this.circle(x - r * .4, y - r * .45, r * .13, '#ffffff66'); c.restore();
    }
    draw(state, { angle, power, placement, previous, alpha, effects = [] }) {
        const c = this.ctx;
        if (this.portrait) c.setTransform(0, this.canvas.height / 1120, -this.canvas.width / 620, 0, this.canvas.width, 0);
        else c.setTransform(this.canvas.width / 1120, 0, 0, this.canvas.height / 620, 0, 0);
        c.clearRect(0, 0, 1120, 620); c.translate(60, 60);
        const roundRect = (x,y,w,h,r,fill) => { c.beginPath(); c.roundRect(x,y,w,h,r); c.fillStyle=fill; c.fill(); };
        roundRect(-48,-48,1096,596,42,'#080d14'); roundRect(-45,-45,1090,590,39,'#9d7950');
        const wood = c.createLinearGradient(0,-40,0,540); wood.addColorStop(0,'#a27447'); wood.addColorStop(.08,'#70492d'); wood.addColorStop(.3,'#442b1f'); wood.addColorStop(.8,'#583923'); wood.addColorStop(1,'#a5794a');
        roundRect(-40,-40,1080,580,35,wood); roundRect(-25,-25,1050,550,23,'#091b2b');
        const cloth = c.createRadialGradient(470,210,50,500,250,650); cloth.addColorStop(0,'#197eaf'); cloth.addColorStop(.6,'#116a98'); cloth.addColorStop(1,'#0b486f');
        roundRect(0,0,1000,500,8,cloth);
        // Original procedural cloth weave: no third-party artwork or network assets.
        c.save(); c.globalAlpha=.055;
        for(let y=2;y<500;y+=4) this.line(1,y,999,y,'#c4e4c9',.4);
        for(let x=2;x<1000;x+=4) this.line(x,1,x,499,'#081f19',.4);
        c.restore();
        for (const p of POCKETS) { this.circle(p.x,p.y,31,'#b29263'); this.circle(p.x,p.y,28,'#101824'); this.circle(p.x,p.y+2,23,'#030911'); }
        c.lineCap='round';
        for(const [ax,ay,bx,by] of CUSHIONS) { this.line(ax,ay,bx,by,'#092e4d',15); this.line(ax,ay,bx,by,'#266f9c',7); this.line(ax,ay,bx,by,'#499bc2',1.5); }
        for(let x=125;x<1000;x+=125) if(x!==500) for(const y of [-25,525]) { c.save(); c.translate(x,y); c.rotate(Math.PI/4); c.fillStyle='#b3ae82'; c.fillRect(-2,-2,4,4); c.restore(); }
        for(const x of [-25,1025]) for(const y of [125,250,375]) this.circle(x,y,2.2,'#b3ae82');
        c.setLineDash([4,7]); this.line(250,12,250,488,'#c5ddbd32',.8); c.setLineDash([]);
        this.circle(750,250,2,'#d0e0c455');
        c.fillStyle='#d8edff20'; c.font='700 31px system-ui'; c.textAlign='center'; c.fillText('HUDDSPOOL',500,260);
        c.font='8px system-ui'; c.fillStyle='#d8edff30'; c.fillText('C L U B   S E R I E S',500,280);
        if(['BREAK_SETUP','BALL_IN_HAND'].includes(state.phase)) {
            c.fillStyle='#c6e49c16'; c.fillRect(11,11,state.phase==='BREAK_SETUP'?239:978,478);
            if(placement) { this.ball({...state.balls[0],...placement,id:0},.65); c.beginPath(); c.arc(placement.x,placement.y,15,0,Math.PI*2); c.strokeStyle=validPlacement(state.balls,placement.x,placement.y,state.phase==='BREAK_SETUP')?'#d9f4ab':'#f19c8d'; c.lineWidth=2; c.stroke(); c.font='bold 12px system-ui'; c.fillStyle=c.strokeStyle; c.fillText(validPlacement(state.balls,placement.x,placement.y,state.phase==='BREAK_SETUP')?'✓':'×',placement.x,placement.y-22); }
        }
        const aiming = state.phase === 'AIMING';
        if(aiming && !state.balls[0].pocketed) {
            const cue=state.balls[0], guide=predict(state.balls,angle), dx=Math.cos(angle),dy=Math.sin(angle);
            const legal=!guide.target || targets(state).includes(guide.target.id), color=legal?'#e0f0d5aa':'#f6ab9ecc';
            c.save(); c.setLineDash([5,6]); this.line(cue.x,cue.y,guide.x,guide.y,color,1.3); c.restore();
            c.beginPath(); c.arc(guide.x,guide.y,P.radius,0,Math.PI*2); c.strokeStyle=color; c.lineWidth=1.2; c.stroke();
            if(guide.target){ const nx=(guide.target.x-guide.x)/(2*P.radius), ny=(guide.target.y-guide.y)/(2*P.radius), dot=dx*nx+dy*ny;
                this.line(guide.target.x,guide.target.y,guide.target.x+nx*65,guide.target.y+ny*65,color,1.5);
                this.line(guide.x,guide.y,guide.x+(dx-dot*nx)*65,guide.y+(dy-dot*ny)*65,color,1.2);
                if(!legal){c.fillStyle=color;c.font='bold 17px system-ui';c.fillText('!',guide.x,guide.y-20);}
            }
            const back=24+power*85;
            c.save(); c.translate(cue.x,cue.y); c.rotate(angle); c.lineCap='butt';
            this.line(-back,0,-back-150,0,'#dfd0a6',5); this.line(-back-150,0,-back-270,0,'#483b29',8); this.line(-back-150,0,-back-260,0,'#a59a72',2); this.line(-back,0,-back-5,0,'#96b4ad',5); c.restore();
        }
        for(const ball of state.balls) {
            if(ball.pocketed || ['BREAK_SETUP','BALL_IN_HAND'].includes(state.phase) && ball.id===0) continue;
            const before=previous?.find(b=>b.id===ball.id);
            this.ball(before && !before.pocketed ? {...ball,x:before.x+(ball.x-before.x)*alpha,y:before.y+(ball.y-before.y)*alpha}:ball);
        }
        for(const effect of effects){ const t=effect.progress; this.ball({id:effect.ball,x:effect.x+(500-effect.x)*t,y:effect.y+(-70-effect.y)*t,rotation:t*5},1-t); }
    }
}
