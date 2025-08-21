import { createPlaceholderReplacer } from '../src/components/ChallengeCard.js';

describe('placeholder replacer', () => {
  test('cycles through players with {{next}}', () => {
    const replace = createPlaceholderReplacer(['Alice', 'Bob', 'Charlie']);
    expect(replace('Player: {{next}}')).toBe('Player: Alice');
    expect(replace('Player: {{next}}')).toBe('Player: Bob');
    expect(replace('Player: {{next}}')).toBe('Player: Charlie');
    expect(replace('Player: {{next}}')).toBe('Player: Alice');
  });

  test('selects oldest player with {{oldest}}', () => {
    const players = [
      { name: 'Anna', age: 20 },
      { name: 'Ben', age: 35 },
      { name: 'Chris', age: 30 }
    ];
    const replace = createPlaceholderReplacer(players);
    expect(replace('Oldest is {{oldest}}')).toBe('Oldest is Ben');
  });
});
