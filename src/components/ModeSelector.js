import { loadCollection } from '../data/loadCollection.js';
import { showChallenge } from './ChallengeCard.js';
import Swal from 'sweetalert2';

export function renderModeSelector() {
  const app = document.getElementById('app');
  app.innerHTML = `
    <div class="screen card-transition fade-in">
      <h2>Velg spillmodus</h2>
      <input id="gamecodeInput" placeholder="FEST123" />
      <button id="loadBtn">Start spill</button>
    </div>
  `;
  const screen = app.querySelector('.screen');
  requestAnimationFrame(() => screen.classList.remove('fade-in'));

  document.getElementById('loadBtn').addEventListener('click', async () => {
    const code = document.getElementById('gamecodeInput').value.trim();
    const collection = await loadCollection(code);
    if (collection) {
      localStorage.setItem('activeCollection', JSON.stringify(collection));
      showChallenge(collection);
    } else {
      Swal.fire({
        icon: 'error',
        title: 'Oops... ',
        text: 'Fant ikke spillmodus med kode ' + code
      });
    }
  });
}
