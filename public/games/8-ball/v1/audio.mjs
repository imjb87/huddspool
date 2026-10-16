export class PoolAudio {
    constructor() { this.context = null; this.muted = false; this.volume = 0.45; this.lastCollision = 0; }
    async unlock() {
        const Audio = globalThis.AudioContext || globalThis.webkitAudioContext;
        if (!Audio) return;
        this.context ??= new Audio();
        if (this.context.state === 'suspended') await this.context.resume().catch(() => {});
    }
    play(type, intensity = 0.5) {
        if (!this.context || this.muted || this.context.state !== 'running') return;
        const time = this.context.currentTime;
        if (['collision','cushion'].includes(type)) {
            if (time - this.lastCollision < 0.025) return;
            this.lastCollision = time;
        }
        const frequencies = { strike: 130, collision: 620, cushion: 170, pocket: 280, turn: 540, group: 740, foul: 160, low: 880, win: 1046, loss: 196 };
        const oscillator = this.context.createOscillator(), gain = this.context.createGain();
        oscillator.type = ['foul','loss'].includes(type) ? 'triangle' : 'sine';
        oscillator.frequency.setValueAtTime(frequencies[type] || 440, time);
        oscillator.frequency.exponentialRampToValueAtTime((frequencies[type] || 440)*.6,time+.12);
        gain.gain.setValueAtTime(Math.min(.22,Math.max(.015,intensity*.18))*this.volume,time);
        gain.gain.exponentialRampToValueAtTime(.001,time+.18);
        oscillator.connect(gain);gain.connect(this.context.destination);oscillator.start(time);oscillator.stop(time+.2);
        oscillator.onended=()=>{ oscillator.disconnect(); gain.disconnect(); };
    }
    dispose() { this.context?.close(); this.context = null; }
}
