# 🎉 GameNight – Den ultimate PWA-drikkespillplattformen

Velkommen til **GameNight** – en rask, offline-kapabel og sosialt engasjerende webapp for drikkespill, inspirert av Picolo. GameNight kombinerer enkelhet, stemning og fleksibilitet. Den er designet for å brukes på vors, fester og spillkvelder – både på mobil og på TV-skjerm.

🔗 Offisiell side: [https://gamenight.no](https://gamenight.no)

---

## 🚀 Hva er GameNight?

- En **Progressiv Web App (PWA)** som kjører 100 % i nettleseren – ingen installasjon eller innlogging kreves.
- Brukeren legger inn **navnene på deltakerne**, velger en spillmodus (collection), og appen viser **én tilfeldig utfordring av gangen**.
- GameNight fungerer også **offline**, takket være caching og lokal lagring.
- Spillmoduser består av JSON-filer lastet manuelt opp av administrator (ingen backend).

---

## 📂 Mappestruktur

```
GameNight/
├── public/                # Logoer, mockups, challenges (allerede lastet opp)
│   ├── mockups/
│   ├── logos/
│   └── challenges/
├── src/                   # Kommer: Kildekoden til appen
├── docs/                  # All dokumentasjon
├── README.md              # Denne fila
└── .gitignore
```

---

## 📖 Viktig dokumentasjon

| Fil                            | Beskrivelse |
|--------------------------------|-------------|
| `docs/overview.md`             | Hva prosjektet er, inspirasjon, mål |
| `docs/features.md`             | Komplett funksjonsoversikt |
| `docs/game-schema.json`        | Struktur på én challenge |
| `docs/collection-schema.json`  | Struktur på én spillmodus |
| `docs/structure.md`            | Hvordan filene er organisert |
| `docs/user-flow.md`            | Hvordan en bruker bruker appen steg-for-steg |
| `docs/theming.md`              | Hvordan visuelle tema aktiveres basert på dato |
| `docs/analytics.md`            | GDPR-vennlig bruk av Google Analytics |
| `docs/content-strategy.md`     | Hvordan vi tenker rundt SEO, AdSense og artikler |
| `docs/editor.md`               | Krav og mål for editor-verktøyet |
| `docs/gpt-guidance.md`         | En forklaring spesifikt for GPT/agenter som skal forstå prosjektet |
| `docs/changelog.md`            | Logg over hva som er bygget når |
| `docs/languages.md`            | Hvordan språkutvidelse fungerer |
| `docs/tests.md`                | Hva som må testes og hvordan |
| `docs/components.md`           | Plan for UI-komponenter |
| `docs/examples/`               | Eksempeldata: ferdige JSON-filer som kan brukes til testing |
| `docs/articles/`               | Artikler med regler for klassiske drikkespill (SEO-innhold)

---

## 🔒 Sikkerhet og personvern

- Det er **ingen innlogging**, brukere lagrer ingenting online.
- Spillmoduser lastes **kun manuelt** av prosjektets eier.
- Analyse skjer anonymt med Google Analytics.
- Fullt i tråd med GDPR og AdSense-retningslinjer.

---

## 🧠 For utviklere og GPT-brukere

Du trenger ikke snakke med prosjektets eier for å forstå GameNight.

👉 **Start her:**
1. `docs/overview.md`
2. `docs/structure.md`
3. `docs/gpt-guidance.md`

Her vil du forstå alt fra teknologi, struktur, målgruppe og strategi til hvordan utfordringer vises og hvordan temaer fungerer.

---

## 🖼 Eksisterende innhold

- Mockups og UI-design er allerede lastet opp i `public/mockups/`
- Logoer ligger i `public/logos/`
- Challenge-titler og bilder ligger i `public/challenges/`

---

God utvikling – og husk å drikke ansvarlig! 🍻
