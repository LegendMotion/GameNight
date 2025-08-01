import fs from 'fs';
import path from 'path';
import Ajv from 'ajv';

const collectionSchema = JSON.parse(fs.readFileSync(path.join('docs', 'collection-schema.json'), 'utf-8'));
const gameSchema = JSON.parse(fs.readFileSync(path.join('docs', 'game-schema.json'), 'utf-8'));

const ajv = new Ajv({ allErrors: true });
ajv.addSchema(gameSchema, 'game-schema.json');
const validate = ajv.compile(collectionSchema);
const validateGame = ajv.compile(gameSchema);

test('FEST123 collection matches schema', () => {
  const data = JSON.parse(fs.readFileSync('public/data/collections/FEST123.json', 'utf-8'));
  const valid = validate(data);
  if (!valid) console.error(validate.errors);
  expect(valid).toBe(true);
});

test('game schema accepts new challenge types', () => {
  const base = { id: 'test', title: 'Test challenge' };
  ['spillthetea', 'yayornay'].forEach(type => {
    const valid = validateGame({ ...base, type });
    if (!valid) console.error(validateGame.errors);
    expect(valid).toBe(true);
  });
});
