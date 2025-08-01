const CACHE_VERSION = 'v2';
const CACHE_NAME = `gamenight-cache-${CACHE_VERSION}`;
const PRECACHE_URLS = [
  '/',
  '/index.html',
  '/manifest.json',
  '/styles/main.css',
  '/main.js',
  '/components/ChallengeCard.js',
  '/components/ModeSelector.js',
  '/components/PlayerInput.js',
  '/components/init.js',
  '/GameNight-logo-small.webp',
  '/gamenight_ikon.png',
  '/backgrounds/challenge.jpg',
  '/backgrounds/jegharaldri.jpg',
  '/backgrounds/main.jpg',
  '/backgrounds/spillthetea.jpg',
  '/backgrounds/yayornay.jpg',
  '/titles/challenge.png',
  '/titles/jegharaldri.png',
  '/titles/spillthetea.png',
  '/titles/yayornay.png'
];

self.addEventListener('install', event => {
  console.log('Service Worker installing...');
  event.waitUntil(
    caches.open(CACHE_NAME).then(cache => cache.addAll(PRECACHE_URLS))
  );
});

self.addEventListener('activate', event => {
  event.waitUntil(
    caches.keys().then(keys =>
      Promise.all(keys.filter(key => key !== CACHE_NAME).map(key => caches.delete(key)))
    )
  );
});

self.addEventListener('fetch', event => {
  if (event.request.mode === 'navigate') {
    event.respondWith(
      fetch(event.request).catch(() => caches.match('/index.html'))
    );
    return;
  }

  event.respondWith(
    caches.match(event.request).then(response => response || fetch(event.request))
  );
});
