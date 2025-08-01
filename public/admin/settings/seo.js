import initSection from './section.js';

initSection('seo', [
  { type: 'text', name: 'seo_meta_description', label: 'Default Meta Description' },
  { type: 'text', name: 'seo_meta_keywords', label: 'Default Meta Keywords' },
  { type: 'text', name: 'seo_google_verification', label: 'Google Verification Token' },
  { type: 'text', name: 'seo_bing_verification', label: 'Bing Verification Token' },
  { type: 'toggle', name: 'seo_indexing', label: 'Allow Search Engine Indexing' },
  { type: 'textarea', name: 'seo_robots_txt', label: 'robots.txt' }
]);
