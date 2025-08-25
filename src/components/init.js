export function initApp() {
  const app = document.getElementById('app');
  app.innerHTML = '<div class="screen card-transition fade-in"><h1>Velkommen til GameNight</h1><p>Byggingen av appen starter her!</p></div>';
  const screen = app.querySelector('.screen');
  requestAnimationFrame(() => screen.classList.remove('fade-in'));
}
