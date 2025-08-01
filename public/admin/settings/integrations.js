import initSection from './section.js';

initSection('integrations', [
  { type: 'text', name: 'integrations_google_analytics', label: 'Google Analytics ID' },
  { type: 'text', name: 'integrations_discord_webhook', label: 'Discord Webhook URL' },
  { type: 'select', name: 'integrations_payment_provider', label: 'Payment Provider', options: ['', 'stripe', 'paypal'] }
]);
