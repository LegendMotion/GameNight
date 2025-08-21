const CACHE_VERSION = 'v2';
const CACHE_NAME = `gamenight-cache-${CACHE_VERSION}`;
const RUNTIME_CACHE = 'runtime-cache-v1';
const PRECACHE_URLS = [
  '/',
  '/index.html',
  '/manifest.json',
  '/styles/main.css',
  '/main.js',
  '/components/ChallengeCard.js',
  '/components/ModeSelector.js',
  '/components/PlayerInput.js',
  '/components/Ads.js',
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
  '/titles/yayornay.png',
  '/offline.html'
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
  const url = new URL(event.request.url);

  const networkOnlyPaths = ['/admin'];
  if (networkOnlyPaths.some(path => url.pathname.startsWith(path))) {
    event.respondWith(fetch(event.request));
    return;
  }

  if (url.pathname.startsWith('/api/')) {
    event.respondWith(
      caches.open(RUNTIME_CACHE).then(async cache => {
        try {
          const response = await fetch(event.request);
          if (response.ok) {
            cache.put(event.request, response.clone());
            limitCache(cache, 50);
          }
          return response;
        } catch (err) {
          const cached = await cache.match(event.request);
          return cached || caches.match('/offline.html');
        }
      })
    );
    return;
  }

  if (event.request.mode === 'navigate') {
    event.respondWith(
      fetch(event.request).catch(async () => {
        const cached = (await caches.match(event.request)) || (await caches.match('/index.html'));
        return cached || caches.match('/offline.html');
      })
    );
    return;
  }

  const { request } = event;
  if (request.method !== 'GET') {
    event.respondWith(fetch(request));
    return;
  }

  event.respondWith(
    caches.match(request).then(cached => {
      const fetchPromise = fetch(request)
        .then(response => {
          if (response.ok) {
            caches.open(RUNTIME_CACHE).then(cache => {
              cache.put(request, response.clone());
              limitCache(cache, 50);
            });
          }
          return response;
        })
        .catch(() => cached || caches.match('/offline.html'));
      return cached || fetchPromise;
    })
  );
});

function limitCache(cache, maxItems) {
  cache.keys().then(keys => {
    if (keys.length > maxItems) {
      cache.delete(keys[0]).then(() => limitCache(cache, maxItems));
    }
  });
}
