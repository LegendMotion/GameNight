import { jest } from '@jest/globals';
import { JSDOM } from 'jsdom';

describe('main.js integration', () => {
  it('calls registerServiceWorker on DOMContentLoaded', async () => {
    const dom = new JSDOM(`<!DOCTYPE html><div id="app"></div>`);
    global.window = dom.window;
    global.document = dom.window.document;

    const mockRender = jest.fn();
    const mockRegister = jest.fn();

    jest.unstable_mockModule('../src/components/PlayerInput.js', () => ({
      renderPlayerInput: mockRender
    }));

    jest.unstable_mockModule('../src/registerServiceWorker.js', () => ({
      registerServiceWorker: mockRegister
    }));

    await import('../src/main.js');

    document.dispatchEvent(new dom.window.Event('DOMContentLoaded'));

    expect(mockRender).toHaveBeenCalled();
    expect(mockRegister).toHaveBeenCalled();
  });
});
