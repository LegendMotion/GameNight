import { renderPlayerInput } from './components/PlayerInput.js';
import { registerServiceWorker } from './registerServiceWorker.js';

document.addEventListener('DOMContentLoaded', () => {
  renderPlayerInput();
  registerServiceWorker();
});
