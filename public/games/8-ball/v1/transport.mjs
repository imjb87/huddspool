// Contract, intentionally not enabled as an online lobby until an authority is deployed.
export class PoolTransport {
    async connect({ matchId, lastRevision }) { throw new Error('Implement authenticated session join and snapshot recovery'); }
    async submit(command) { throw new Error('Implement server receipt, validation, simulation and durable command deduplication'); }
    subscribe(listener) { throw new Error('Implement authoritative snapshot/event subscription and connection status'); }
    async disconnect() {}
}

// Suitable for an authenticated HTTP endpoint plus the existing private Echo channel.
// send() must obtain server time/deadlines; it must never accept client pocket/winner claims.
export class AuthoritativeTransport extends PoolTransport {
    constructor({ join, send, listen, leave }) { super(); Object.assign(this, { join, send, listen, leave }); }
    async connect(context) { return this.join(context); }
    async submit(command) { return this.send(command); }
    subscribe(listener) { return this.listen(listener); }
    async disconnect() { await this.leave?.(); }
}
