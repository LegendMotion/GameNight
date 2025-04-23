# GameNight 🎉

**GameNight** er en nettbasert drikkelek du kan spille med venner – selv uten internett.

## Funksjoner

- 📲 PWA: Installerbar på telefon og kan brukes offline
- 🔄 Rediger JSON-spillfiler direkte
- 🔍 Søk etter spill via navn eller ID
- 🔒 Offentlige og private spill
- 📸 Spill med bilder og beskrivelse
- 💬 Støtte for flere språk (standard: norsk)
- 🧠 Nye spilltyper kan enkelt legges til
- 💸 Fremtidig støtte for reklame via AdMob
- 📺 Planlagt Chromecast-støtte
- 🛍️ Lenker til merch og drinkoppskrifter i fremtiden

## Strukturen i et spill (JSON)

```json
{
  "id": "1234",
  "title": "Kveldens Mix",
  "description": "...",
  "image": "games/assets/kveldens-mix.jpg",
  "public": true,
  "language": "no",
  "prompts": [
    { "type": "challenge", "value": "NAVN må ..." },
    { "type": "neverhaveiever", "value": "spist ..." },
    { "type": "yayornay", "question": "...", "options": ["...", "..."] },
    { "type": "spillthetea", "value": "..." }
  ]
}
```

For utviklerinfo og konvensjoner, se `DEVELOPER_GUIDE.md`.
