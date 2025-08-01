import initSection from './section.js';

initSection('notifications', [
  { type: 'toggle', name: 'notifications_email', label: 'Enable Email Notifications' },
  { type: 'toggle', name: 'notifications_push', label: 'Enable Push Notifications' },
  { type: 'text', name: 'notifications_sender', label: 'Sender Email' }
]);
