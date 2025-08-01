import { jest } from '@jest/globals';
import { loadCollection } from '../src/data/loadCollection.js';

describe('loadCollection', () => {
  beforeEach(() => {
    global.fetch = jest.fn();
    console.error = jest.fn();
  });

  it('fetches collection and returns data', async () => {
    const mockData = { name: 'test' };
    fetch.mockResolvedValue({ ok: true, json: () => Promise.resolve(mockData) });

    const data = await loadCollection('ABC123');
    expect(fetch).toHaveBeenCalledWith('/api/collection.php?gamecode=ABC123');
    expect(data).toEqual(mockData);
  });

  it('returns null on fetch failure', async () => {
    fetch.mockResolvedValue({ ok: false });
    const data = await loadCollection('ABC123');
    expect(console.error).toHaveBeenCalled();
    expect(data).toBeNull();
  });
});
