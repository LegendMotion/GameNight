export function showChallenge(collection) {
  const app = document.getElementById('app');
  const shownIds = new Set();

  function getNextChallenge() {
    const unused = collection.challenges.filter(c => !shownIds.has(c.id));
    if (unused.length === 0) {
      app.innerHTML = '<h2>Spillet er ferdig 🎉</h2>';
      return;
    }
    const next = unused[Math.floor(Math.random() * unused.length)];
    shownIds.add(next.id);
    renderChallenge(next);
  }

  function renderChallenge(challenge) {
    app.innerHTML = \`
      <div class="challenge-card">
        <h3>\${challenge.title}</h3>
        <button id="nextBtn">Neste</button>
      </div>
    \`;
    document.getElementById('nextBtn').addEventListener('click', getNextChallenge);
  }

  getNextChallenge();
}
