export function showChallenge(collection, opts = {}) {
  const {
    containerId = 'app',
    nextBtnId = 'nextBtn',
    applyBackground = true
  } = opts;

  const app = document.getElementById(containerId);
  const shownIds = new Set();
  const players = JSON.parse(localStorage.getItem('players') || '[]');

  const typeAssets = {
    challenge: {
      background: '/backgrounds/challenge.jpg',
      title: '/titles/challenge.png'
    },
    never: {
      background: '/backgrounds/jegharaldri.jpg',
      title: '/titles/jegharaldri.png'
    },
    spillthetea: {
      background: '/backgrounds/spillthetea.jpg',
      title: '/titles/spillthetea.png'
    },
    yayornay: {
      background: '/backgrounds/yayornay.jpg',
      title: '/titles/yayornay.png'
    }
  };

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

  function replacePlaceholders(text) {
    if (players.length === 0) return text;
    return text.replace(/{{player}}/gi, () => {
      const index = Math.floor(Math.random() * players.length);
      return players[index];
    });
  }

  function renderChallenge(challenge) {
    const assets = typeAssets[challenge.type] || typeAssets.challenge;
    if (applyBackground) {
      document.body.style.backgroundImage = `url('${assets.background}')`;
    }
    app.innerHTML = `
      <div class="challenge-card">
        <img src="${assets.title}" alt="${challenge.type}" />
        <h3>${replacePlaceholders(challenge.title)}</h3>
        <button id="${nextBtnId}">Neste</button>
      </div>
    `;
    document.getElementById(nextBtnId).addEventListener('click', getNextChallenge);
  }

  getNextChallenge();
}

