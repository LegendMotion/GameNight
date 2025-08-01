import { renderAdSlot } from '../ads.js';

export function renderBannerAd(containerId = 'ad-banner') {
  renderAdSlot(containerId, 'banner');
}

export function renderSidebarAd(containerId = 'ad-sidebar') {
  renderAdSlot(containerId, 'sidebar');
}
