import { renderModeSelector } from './ModeSelector.js';

export function renderPlayerInput() {
  const app = document.getElementById('app');
  app.innerHTML = `
    <h2>Legg til spillere</h2>
    <ul id="playerList"></ul>
    <input id="playerName" placeholder="Spillernavn" />
    <button id="addPlayer">Legg til</button>
    <p id="playerMessage" style="color: red;"></p>
    <button id="continue">Fortsett</button>
  `;

  const playerList = document.getElementById('playerList');
  const players = [];

  const message = document.getElementById('playerMessage');

  document.getElementById('addPlayer').addEventListener('click', () => {
    const input = document.getElementById('playerName');
    const name = input.value.trim();
    if (!name) {
      message.textContent = 'Skriv inn et navn.';
      return;
    }
    if (players.includes(name)) {
      message.textContent = 'Navnet er allerede lagt til.';
      return;
    }
    players.push(name);
    updateList();
    input.value = '';
    message.textContent = '';
  });

  document.getElementById('continue').addEventListener('click', () => {
    if (players.length < 2) {
      message.textContent = 'Legg til minst to spillere!';
      return;
    }
    localStorage.setItem('players', JSON.stringify(players));
    renderModeSelector();
  });

  function updateList() {
    playerList.innerHTML = '';
    players.forEach((p, index) => {
      const li = document.createElement('li');
      li.textContent = p;
      const del = document.createElement('button');
      del.textContent = 'Slett';
      del.addEventListener('click', () => {
        players.splice(index, 1);
        updateList();
        if (players.length >= 2) {
          message.textContent = '';
        }
      });
      li.appendChild(del);
      playerList.appendChild(li);
    });
  }
}
