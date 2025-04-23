# GPT Developer Reference – GameNight

Dette dokumentet inneholder all informasjon en GPT-agent trenger for å forstå og videreutvikle GameNight-prosjektet.

---

## 🎮 Hva er GameNight?
GameNight er en PWA-basert webapp for drikkeleker. Brukere kan legge inn spillere, starte spill basert på JSON-data, og spille utfordringer som "Jeg har aldri", "Spill the tea", osv. Appen fungerer offline og hostes på en enkel Apache-server uten backend.

---

## 📄 Filstruktur (kort)
```
gamenight-webapp/
├── public/                # Ikoner og statisk innhold
├── src/
│   ├── components/       # React/Vue-komponenter (hvis aktuelt)
│   ├── pages/            # App-visninger (forside, spill, editor)
│   ├── games/            # Spill (JSON-filer)
│   ├── articles/         # Ikke-interaktive spill (HTML/Markdown)
│   └── utils/            # Hjelpefunksjoner (eks. NAVN-erstatning)
├── json-editor/          # JSON-redigeringsgrensesnitt
├── styles.css
├── index.html
├── manifest.json         # PWA
├── service-worker.js     # PWA offline cache
├── README.md             # Prosjektoversikt
├── DEVELOPER_GUIDE.md    # Teknisk dokumentasjon
└── GPT_REFERENCE.md      # (denne filen)
```

---

## 🧠 Spilldata (JSON)

### Eksempel:
```json
{
  "id": "1234",
  "title": "Kveldens Mix",
  "description": "Mix av utfordringer og bekjennelser.",
  "image": "games/assets/kveldens-mix.jpg",
  "public": true,
  "language": "no",
  "prompts": [
    { "type": "challenge", "value": "NAVN må danse i 30 sekunder" },
    { "type": "neverhaveiever", "value": "stjålet noe" },
    { "type": "yayornay", "question": "NAVN, vil du helst ...?", "options": ["Fly", "Usynlig"] },
    { "type": "spillthetea", "value": "Fortell om en klein date" }
  ]
}
```

### Typer utfordringer:
- `challenge`: Tekst med "NAVN" placeholder
- `neverhaveiever`: Kun tekst, prepender "Jeg har aldri"
- `yayornay`: Spørsmål + to valg
- `spillthetea`: Spørsmål eller avsløring

---

## 🌐 Navigasjon og UI
- **Home**: Forside med featured spill
- **Collections**: Spill og kategorier
- **Search**: Søk etter spillnavn eller ID
- **Shop**: (fremtidig) merch-lenker
- **Settings**: Om oss, Kontakt, Personvern (HTML-sider)

---

## 🏠 Hosting
- Appen hostes på Apache-server
- Ingen backend, kun statisk HTML/CSS/JS
- Spill lagres som filer under `games/`

---

## 🌍 PWA & offline
- `manifest.json` gir appen installbarhet
- `service-worker.js` cacher HTML/CSS/JS/games

---

## 💰 Reklame
- Bruker **Google AdSense** (ikke AdMob)
- Reklamen legges inn som vanlig HTML-snutter (script + ins)
- Reklame kan vises på:
  - Startside
  - Mellom utfordringer (eks. hver 3.)

---

## 🔍 Teknisk oppførsel
- NAVN-placeholdere erstattes med tilfeldige spillernavn fra aktiv sesjon
- Ingen prompt vises to ganger i samme spillrunde
- Språkstøtte starter med `no`, men flere språk mulig
- Spill kan settes som `public: false` for kun å være tilgjengelig via ID

---

## 🚀 Fremtidsplaner
- Chromecast-støtte for å vise spill på TV
- Shop med merch
- Artikler og drinkoppskrifter (under `articles/`)
- Kontoer/matchingssystem (kun vurdert)

---

## 🛠 GPT-oppgaver (eksempler)
- Generer nye spill (JSON)
- Lage visninger for spesifikke spilltyper
- Lage en "random challenge" komponent
- Legge inn AdSense på relevante steder
- Lage layout for `Collections` eller `Settings`

---

## 📊 Tips
- Hold deg til ren HTML/JS, ingen rammeverk med mindre det spesifiseres
- Ingen server-side koding (PHP kun hvis absolutt nødvendig)
- Fokus: Mobiloptimalisert, offlinebruk, lav terskel for deling

---

## 🤝 Kontakt
Spørsmål? Kontakt Angel: angel.angelov@rekomgroup.com

Happy building ✨
