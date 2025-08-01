import { jest } from '@jest/globals';
import { registerServiceWorker } from '../src/registerServiceWorker.js';

describe('registerServiceWorker', () => {
  beforeEach(() => {
    console.log = jest.fn();
    console.error = jest.fn();
  });

  it('registers service worker when supported', async () => {
    global.navigator = {
      serviceWorker: {
        register: jest.fn().mockResolvedValue({ scope: '/' })
      }
    };

    await registerServiceWorker();

    expect(navigator.serviceWorker.register).toHaveBeenCalledWith('/service-worker.js');
    expect(console.log).toHaveBeenCalled();
  });

  it('logs error when registration fails', async () => {
    global.navigator = {
      serviceWorker: {
        register: jest.fn().mockRejectedValue(new Error('fail'))
      }
    };

    await registerServiceWorker();

    expect(console.error).toHaveBeenCalled();
  });

  it('skips registration when unsupported', async () => {
    global.navigator = {};
    await registerServiceWorker();
    expect(console.log).not.toHaveBeenCalled();
    expect(console.error).not.toHaveBeenCalled();
  });
});
