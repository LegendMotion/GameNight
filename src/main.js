import { renderPlayerInput } from './components/PlayerInput.js';
import { registerServiceWorker } from './registerServiceWorker.js';
import { initAnalytics } from './analytics.js';
import { initAds } from './ads.js';

document.addEventListener('DOMContentLoaded', () => {
  renderPlayerInput();
  registerServiceWorker();
  initAnalytics();
  initAds();
});
