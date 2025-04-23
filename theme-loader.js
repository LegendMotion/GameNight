function loadTheme(name) {
  const link = document.getElementById("theme-style") || document.createElement("link");
  link.rel = "stylesheet";
  link.id = "theme-style";
  link.href = `/themes/${name}.css`;
  document.head.appendChild(link);
}

function initThemeFromDate(config) {
  const today = new Date();
  const key = `${(today.getMonth() + 1).toString().padStart(2, '0')}-${today.getDate().toString().padStart(2, '0')}`;
  const theme = config.themes_by_date[key] || config.default;
  loadTheme(theme);
}

// Fetch and apply theme config
fetch('/theme-config.json')
  .then(res => res.json())
  .then(config => initThemeFromDate(config));