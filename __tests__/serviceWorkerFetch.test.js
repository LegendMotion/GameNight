import { jest } from '@jest/globals';

describe('service worker fetch handling', () => {
  it('bypasses cache for admin routes', async () => {
    const listeners = {};
    global.self = { addEventListener: (type, cb) => { listeners[type] = cb; } };
    global.caches = { match: jest.fn(), open: jest.fn(), keys: jest.fn() };
    global.fetch = jest.fn().mockResolvedValue('network');

    await import('../public/service-worker.js');

    const event = {
      request: { url: 'https://example.com/admin/dashboard', mode: 'navigate' },
      respondWith: jest.fn()
    };

    listeners.fetch(event);

    expect(fetch).toHaveBeenCalledWith(event.request);
    expect(caches.match).not.toHaveBeenCalled();
    expect(event.respondWith).toHaveBeenCalled();
  });
});

