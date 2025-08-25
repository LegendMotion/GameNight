import { renderAdSlot } from '../ads.js';

export function renderBannerAd(containerId = 'ad-banner', slotId) {
  renderAdSlot(containerId, 'banner', slotId);
}

export function renderSidebarAd(containerId = 'ad-sidebar', slotId) {
  renderAdSlot(containerId, 'sidebar', slotId);
}
