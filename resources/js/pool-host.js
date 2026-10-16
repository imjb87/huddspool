import gameShell from '../../public/games/8-ball/index.html?raw';
import '../../public/games/8-ball/v1/match-screen.css';

const host = document.querySelector('[data-pool-host]');
if (host) {
    const showError = error => {
        host.dataset.gameState = 'error';
        const panel = document.createElement('section');
        panel.className = 'pool-startup-message';
        panel.innerHTML = '<h2>The table couldn’t start.</h2><p>Please reload to try again.</p><button type="button">Reload game</button><details><summary>Error details</summary><pre></pre></details>';
        panel.querySelector('pre').textContent = error instanceof Error ? error.message : String(error);
        panel.querySelector('button').addEventListener('click', () => location.reload());
        host.replaceChildren(panel);
        console.error('[huddspool:startup]', error);
    };
    (async () => {
        try {
            const documentFragment = new DOMParser().parseFromString(gameShell, 'text/html');
            const root = documentFragment.querySelector('#pool');
            if (!root) throw new Error('Game interface is missing');
            host.replaceChildren(document.importNode(root, true));
            const { bootGame } = await import('../../public/games/8-ball/v1/game.mjs');
            const game = bootGame({
                players: [
                    { id: host.dataset.playerId, name: host.dataset.playerName, avatar: host.dataset.playerAvatar },
                    { id: 'local-2', name: 'Player 2' },
                ],
                storageId: host.dataset.playerId,
                returnUrl: host.dataset.returnUrl,
            });
            host.dataset.gameState = 'ready';
            window.addEventListener('pagehide', event => {
                if (!event.persisted) game.dispose();
            }, { once: true });
        } catch (error) {
            showError(error);
        }
    })();
}
