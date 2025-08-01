import { textInput, toggle, selectBox, textArea } from './components.js';
import { showToast } from '/components/Toast.js';

export default function initSection(section, fields) {
  const container = document.getElementById('settings-form');
  const form = document.createElement('form');
  container.appendChild(form);
  const inputs = {};

  fetch(`/api/settings.php?prefix=${encodeURIComponent(section + '_')}`)
    .then(r => r.json())
    .then(data => {
      const values = data.settings || {};
      fields.forEach(field => {
        let comp;
        const value = values[field.name] || '';
        if (field.type === 'text') {
          comp = textInput(field.name, field.label, value);
        } else if (field.type === 'toggle') {
          comp = toggle(field.name, field.label, value === '1');
        } else if (field.type === 'select') {
          comp = selectBox(field.name, field.label, field.options || [], value);
        } else if (field.type === 'textarea') {
          comp = textArea(field.name, field.label, value);
        }
        if (comp) {
          inputs[field.name] = comp.input;
          form.appendChild(comp.wrapper);
        }
      });
      const submit = document.createElement('button');
      submit.type = 'submit';
      submit.textContent = 'Save';
      form.appendChild(submit);
    });

  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    const payload = { settings: {} };
    fields.forEach(field => {
      const input = inputs[field.name];
      let value = '';
      if (field.type === 'toggle') {
        value = input.checked ? '1' : '0';
      } else {
        value = input.value;
      }
      payload.settings[field.name] = value;
    });
    try {
      const res = await fetch('/api/settings.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
      });
      const data = await res.json();
      if (!res.ok || data.error) {
        throw new Error(data.error || 'Error');
      }
      showToast({ title: 'Settings saved' });
    } catch (err) {
      showToast({ icon: 'error', title: err.message });
    }
  });
}
