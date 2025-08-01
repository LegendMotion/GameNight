import initSection from './section.js';

initSection('email', [
  { type: 'text', name: 'email_host', label: 'SMTP Host' },
  { type: 'text', name: 'email_port', label: 'SMTP Port' },
  { type: 'text', name: 'email_user', label: 'SMTP Username' },
  { type: 'text', name: 'email_pass', label: 'SMTP Password' }
]);
