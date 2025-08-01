export function textInput(name, label, value = '') {
  const wrapper = document.createElement('div');
  const labelEl = document.createElement('label');
  labelEl.textContent = label;
  const input = document.createElement('input');
  input.type = 'text';
  input.name = name;
  input.value = value;
  labelEl.appendChild(input);
  wrapper.appendChild(labelEl);
  return { wrapper, input };
}

export function toggle(name, label, checked = false) {
  const wrapper = document.createElement('div');
  const labelEl = document.createElement('label');
  const input = document.createElement('input');
  input.type = 'checkbox';
  input.name = name;
  input.checked = checked;
  labelEl.appendChild(input);
  labelEl.append(' ' + label);
  wrapper.appendChild(labelEl);
  return { wrapper, input };
}

export function selectBox(name, label, options = [], value = '') {
  const wrapper = document.createElement('div');
  const labelEl = document.createElement('label');
  labelEl.textContent = label;
  const select = document.createElement('select');
  select.name = name;
  options.forEach(opt => {
    const option = document.createElement('option');
    if (typeof opt === 'string') {
      option.value = opt;
      option.textContent = opt;
    } else {
      option.value = opt.value;
      option.textContent = opt.label;
    }
    if (option.value === value) {
      option.selected = true;
    }
    select.appendChild(option);
  });
  labelEl.appendChild(select);
  wrapper.appendChild(labelEl);
  return { wrapper, input: select };
}

export function textArea(name, label, value = '') {
  const wrapper = document.createElement('div');
  const labelEl = document.createElement('label');
  labelEl.textContent = label;
  const textarea = document.createElement('textarea');
  textarea.name = name;
  textarea.value = value;
  labelEl.appendChild(textarea);
  wrapper.appendChild(labelEl);
  return { wrapper, input: textarea };
}
