import { renderPlayerInput } from './components/PlayerInput.js';
import { registerServiceWorker } from './registerServiceWorker.js';
import { setSEO } from './seo.js';
import { initAnalytics } from './analytics.js';

document.addEventListener('DOMContentLoaded', () => {
  renderPlayerInput();
  registerServiceWorker();
  setSEO({ title: 'GameNight', url: window.location.href });
  initAnalytics();
});
