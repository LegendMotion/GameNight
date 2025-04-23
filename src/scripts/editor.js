document.addEventListener('DOMContentLoaded', () => {
  document.getElementById('uploadFile').addEventListener('change', handleFileUpload);
  addChallenge(); // Start with one challenge
});

function addChallenge(data = {}) {
  const container = document.getElementById('challengesContainer');
  const div = document.createElement('div');
  div.className = 'border rounded p-3 mb-3 challenge-item';

  div.innerHTML = `
    <div class="mb-2">
      <label class="form-label">Type</label>
      <select class="form-select type">
        <option value="never_have_i_ever">Never Have I Ever</option>
        <option value="challenge">Challenge</option>
        <option value="vote">Vote</option>
      </select>
    </div>
    <div class="mb-2">
      <label class="form-label">Tekst</label>
      <input type="text" class="form-control text" placeholder="Skriv challenge her" />
    </div>
    <button class="btn btn-danger btn-sm" onclick="this.parentElement.remove()">🗑 Fjern</button>
  `;

  container.appendChild(div);

  if (data.type) div.querySelector('.type').value = data.type;
  if (data.text) div.querySelector('.text').value = data.text;
}

function downloadJson() {
  const title = document.getElementById('gameTitle').value.trim();
  const description = document.getElementById('gameDescription').value.trim();
  const challenges = [...document.querySelectorAll('.challenge-item')].map(item => ({
    type: item.querySelector('.type').value,
    text: item.querySelector('.text').value.trim()
  }));

  const data = { title, description, challenges };
  const blob = new Blob([JSON.stringify(data, null, 2)], { type: 'application/json' });
  const link = document.createElement('a');
  link.href = URL.createObjectURL(blob);
  link.download = `${title || 'gamenight'}.json`;
  link.click();
}

function handleFileUpload(e) {
  const file = e.target.files[0];
  if (!file) return;

  const reader = new FileReader();
  reader.onload = () => {
    try {
      const data = JSON.parse(reader.result);
      document.getElementById('gameTitle').value = data.title || '';
      document.getElementById('gameDescription').value = data.description || '';
      document.getElementById('challengesContainer').innerHTML = '';
      (data.challenges || []).forEach(addChallenge);
    } catch (err) {
      alert('Ugyldig JSON!');
    }
  };
  reader.readAsText(file);
}