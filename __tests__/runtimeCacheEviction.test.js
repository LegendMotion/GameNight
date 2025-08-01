import { jest } from '@jest/globals';

describe('runtime cache eviction and offline fallback', () => {
  it('keeps precached assets available after eviction', async () => {
    const listeners = {};
    global.self = { addEventListener: (t, cb) => { listeners[t] = cb; } };

    const cacheStore = {};
    const createCache = () => {
      const store = new Map();
      return {
        store,
        match: jest.fn(req => Promise.resolve(store.get(req.url || req))),
        put: jest.fn((req, res) => { store.set(req.url || req, res); return Promise.resolve(); }),
        keys: jest.fn(() => Promise.resolve([...store.keys()].map(url => ({ url })))),
        delete: jest.fn(req => { store.delete(req.url || req); return Promise.resolve(true); })
      };
    };

    global.caches = {
      open: jest.fn(name => {
        cacheStore[name] = cacheStore[name] || createCache();
        return Promise.resolve(cacheStore[name]);
      }),
      match: jest.fn(req => {
        for (const cache of Object.values(cacheStore)) {
          const found = cache.store.get(req);
          if (found) return Promise.resolve(found);
        }
        return Promise.resolve(undefined);
      }),
      keys: jest.fn()
    };

    // Prepopulate precache with index.html
    cacheStore['gamenight-cache-v2'] = createCache();
    cacheStore['gamenight-cache-v2'].store.set('/index.html', 'offline-page');

    // Import service worker to set up fetch listener
    await import('../public/service-worker.js');

    // Mock successful network fetches
    global.fetch = jest.fn().mockResolvedValue({ ok: true, clone: () => 'res' });

    const responses = [];
    for (let i = 0; i < 51; i++) {
      const url = `https://example.com/asset${i}`;
      const event = {
        request: { url, method: 'GET' },
        respondWith: p => responses.push(p),
        waitUntil: jest.fn()
      };
      listeners.fetch(event);
    }

    await Promise.all(responses);
    const runtime = cacheStore['runtime-cache-v1'];
    expect(runtime.store.size).toBe(50);

    // Simulate offline navigate request
    global.fetch = jest.fn().mockRejectedValue(new Error('offline'));
    let navPromise;
    const navEvent = {
      request: { url: 'https://example.com/', mode: 'navigate', method: 'GET' },
      respondWith: jest.fn(p => { navPromise = p; })
    };
    listeners.fetch(navEvent);
    await expect(navPromise).resolves.toBe('offline-page');
  });
});
