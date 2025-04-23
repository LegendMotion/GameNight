# GameNight 🎉

GameNight er en mobiloptimalisert webapp og PWA for drikkeleker. Den fungerer offline, krever ingen innlogging og kjøres på statisk Apache-server.

## 🚀 Funksjoner
- Spill starter med spillerinput og tilfeldig utfordringer
- Støtter ulike spilltyper via JSON-filer
- Spillene kan være private eller offentlige
- Redigeringsverktøy for JSON-spill
- Tema-støtte (f.eks. jul, halloween)
- SEO-optimalisering for artikler og offentlige spill
- Offline-støtte via PWA/service worker
- Reklame via Google AdSense
- Mulig fremtidig støtte for Chromecast og butikk

## 📁 Prosjektstruktur
- `public/` – ikoner, logo, bilder
- `src/games/` – JSON-spill
- `src/articles/` – artikler som Ring of Fire
- `src/pages/` – views, inkludert spillvisning
- `themes/` – CSS-temaer (f.eks. christmas.css)
- `theme-config.json` – dato-konfig for temaer
- `service-worker.js` – cache og offline
- `manifest.json` – installasjon på mobil
- `seo/` – SEO-snutter og funksjoner

## 🧠 Hvordan bruke
- Legg inn spillere
- Velg spill fra Collections eller søk
- Spillet laster utfordringer og viser én etter én
- “NAVN” i tekst byttes med tilfeldig spiller
- Ingen spørsmål gjentas i samme spillrunde

## 🛠️ Dev-hjelp
Se `DEVELOPER_GUIDE.md` og `GPT_REFERENCE.md` for teknisk dokumentasjon.