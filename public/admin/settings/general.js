import initSection from './section.js';

initSection('general', [
  { type: 'text', name: 'general_site_name', label: 'Site Name' },
  { type: 'text', name: 'general_tagline', label: 'Tagline' },
  { type: 'select', name: 'general_timezone', label: 'Timezone', options: ['UTC', 'Europe/Oslo'] },
  { type: 'toggle', name: 'general_maintenance', label: 'Maintenance Mode' }
]);
