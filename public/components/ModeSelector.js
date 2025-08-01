import { loadCollection } from '../data/loadCollection.js';
import { showChallenge } from './ChallengeCard.js';

export function renderModeSelector() {
  const app = document.getElementById('app');
  app.innerHTML = \`
    <h2>Velg spillmodus</h2>
    <input id="gamecodeInput" placeholder="FEST123" />
    <button id="loadBtn">Start spill</button>
  \`;

  document.getElementById('loadBtn').addEventListener('click', async () => {
    const code = document.getElementById('gamecodeInput').value.trim();
    const collection = await loadCollection(code);
    if (collection) {
      localStorage.setItem('activeCollection', JSON.stringify(collection));
      showChallenge(collection);
    } else {
      alert('Fant ikke spillmodus med kode ' + code);
    }
  });
}
