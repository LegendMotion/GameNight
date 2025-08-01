import initSection from './section.js';

initSection('integrations', [
  {
    type: 'text',
    name: 'integrations_ga_measurement_id',
    label: 'GA Measurement ID',
    pattern: 'G-[A-Z0-9]{8,}'
  },
  { type: 'toggle', name: 'integrations_ga_anonymize_ip', label: 'Anonymize IP' },
  { type: 'toggle', name: 'integrations_ga_respect_dnt', label: 'Respect Do Not Track' },
  { type: 'text', name: 'integrations_discord_webhook', label: 'Discord Webhook URL' },
  { type: 'select', name: 'integrations_payment_provider', label: 'Payment Provider', options: ['', 'stripe', 'paypal'] }
]);
