# Prompt Templates for GameNight Collections

This guide describes how to ask ChatGPT for new challenge collections that match the project's JSON schemas. Use it together with [`docs/game-schema.json`](./game-schema.json) and [`docs/collection-schema.json`](./collection-schema.json).

## Schema Summary

### Collection (`collection-schema.json`)
| Field | Type | Required | Notes |
| --- | --- | --- | --- |
| `name` | string | Yes | Machine-friendly identifier, e.g. `classic_party` |
| `friendlyName` | string | No | Human readable title |
| `gamecode` | string | No | Short code players can enter (auto-generated when omitted) |
| `public` | boolean | Yes | `true` for public collections |
| `language` | string | No | ISO language code, e.g. `en`, `no` |
| `challenges` | array | Yes | List of challenge objects (see below) |

### Challenge (`game-schema.json`)
| Field | Type | Required | Notes |
| --- | --- | --- | --- |
| `id` | string | Yes | Unique per challenge |
| `type` | string enum | Yes | One of `never`, `challenge`, `truth`, `custom`, `spillthetea`, `yayornay` |
| `title` | string | Yes | Challenge text |
| `description` | string | No | Extra details |
| `tags` | string[] | No | E.g. `"fun"`, `"physical"` |
| `language` | string | No | ISO language code |
| `players` | integer | No | Number of players involved |

For a full reference, see the [example collection](./examples/example-collection.json).

## Ready-to-Copy Prompt

```
You are helping me build a GameNight challenge collection.
Return **only** valid JSON following the collection and game schemas.

Collection settings:
- theme: <THEME>
- language: <LANGUAGE_CODE>
- collection name: <NAME>
- friendly name: <FRIENDLY_NAME>
- number of challenges: <COUNT>
- public: true

Rules:
- Do not include the `gamecode` field; it will be assigned automatically.
- Use placeholders like {{player}} when needed.
- Each challenge must include id, type, and title.
- Do not add commentary or markdown, only JSON.
```

## Validation

Before importing generated JSON, validate it with [AJV](https://ajv.js.org/):

```bash
npm install -g ajv-cli # once
ajv validate -s docs/collection-schema.json -d my-collection.json
```

This checks that `my-collection.json` follows the required structure.
