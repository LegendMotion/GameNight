# GPT Developer Reference – GameNight (Extended)

## 🔍 Mål
GameNight skal være et fullverdig spillunivers som kjører 100 % statisk. Ingen backend. Alt lagres som filer, og systemet må være skalerbart, sikkert og lett å utvide.

## 🧱 Grunnleggende teknisk arkitektur
- Ingen innlogging eller database
- Hostes på Apache med støtte for HTML/CSS/JS/PHP (men helst unngås PHP)
- Spill og artikler er statiske filer
- Alle spilldata defineres i JSON
- Bruker PWA-teknologi for offline-støtte
- Lokal lagring brukes for spillere og spillprogresjon

## 🎮 Spillstruktur
- Hvert spill ligger i `src/games/` som `.json`
- Struktur:
```json
{
  "id": "1234",
  "title": "Kveldens Mix",
  "description": "Mix av utfordringer",
  "image": "games/assets/kveldens-mix.jpg",
  "public": true,
  "language": "no",
  "prompts": [
    { "type": "challenge", "value": "NAVN må danse" },
    { "type": "yayornay", "question": "...", "options": ["A", "B"] }
  ]
}
```
- Ingen rating, ingen serverlagring

## 📚 Artikkel-spill
- Ligger i `src/articles/*.html`
- For eksempel: Ring of Fire, Kongen Befaler
- Bruk JSON-LD for SEO i `<head>`

## 🧩 Tema-støtte
- Ligger i `themes/`
- Bruker CSS-variabler og `theme-config.json` for datoaktivering
- JS laster riktig tema basert på dato eller brukerens valg (lagret i localStorage)

## 📱 PWA-funksjonalitet
- manifest.json definerer app-info og ikoner
- service-worker.js cacher alle nødvendige filer
- Alt fungerer offline etter første last

## 🔍 SEO
- Bruk JSON-LD (`Game` og `CreativeWork`) i `<script type="application/ld+json">`
- Spill med `public: true` får JSON-LD injisert via `inject-game-jsonld.js`
- Artikler får hardkodet JSON-LD i head
- `robots.txt` blokkerer unødvendige ruter som /games/ og /settings/

## 💰 Reklame
- Google AdSense brukes i HTML som inline script
- Ingen tracking eller personalisert data samles av GameNight selv
- Personvernsiden forklarer bruken av cookies, localStorage og AdSense/Analytics

## ⚙️ Navneplassering i utfordringer
- Alle prompts med “NAVN” får satt tilfeldig spiller
- Spilleren byttes automatisk ved hver ny prompt
- Ingen prompt gjentas i samme sesjon

## 📦 Brukerens opplevelse
- Starter på /index.html → spillerskjerm → spillvisning
- Kan lagres til hjem-skjerm som app
- Tema og preferanser huskes lokalt
- Responsiv og optimalisert for små skjermer

## 🎯 Mulige oppgaver for GPT
- Generere eller oppdatere spill-JSON
- Forbedre spillvisning og animasjon
- Lage flere temaer (CSS + assets)
- Hjelpe med artikkelproduksjon (tekst + markup)
- Lage enklere templating-løsning for SEO/artikler