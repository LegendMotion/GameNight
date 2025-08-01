import { renderModeSelector } from './ModeSelector.js';

export function renderPlayerInput() {
  const app = document.getElementById('app');
  app.innerHTML = `
    <h2>Legg til spillere</h2>
    <ul id="playerList"></ul>
    <input id="playerName" placeholder="Spillernavn" />
    <button id="addPlayer">Legg til</button>
    <button id="continue">Fortsett</button>
  `;

  const playerList = document.getElementById('playerList');
  const players = [];

  document.getElementById('addPlayer').addEventListener('click', () => {
    const input = document.getElementById('playerName');
    const name = input.value.trim();
    if (name) {
      players.push(name);
      updateList();
      input.value = '';
    }
  });

  document.getElementById('continue').addEventListener('click', () => {
    if (players.length < 2) {
      alert('Legg til minst to spillere!');
      return;
    }
    localStorage.setItem('players', JSON.stringify(players));
    renderModeSelector();
  });

  function updateList() {
    playerList.innerHTML = '';
    players.forEach(p => {
      const li = document.createElement('li');
      li.textContent = p;
      playerList.appendChild(li);
    });
  }
}
