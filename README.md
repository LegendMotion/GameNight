# 🎉 GameNight

**GameNight** er et åpent kildekodeprosjekt som gjør vorspiel og spillkvelder mer underholdende. Appen er inspirert av Picolo og er bygget som en **Progressive Web App (PWA)** slik at den fungerer lynraskt på mobil, nettbrett og TV – også uten nett.

- 🔗 Offisiell nettside: [https://gamenight.no](https://gamenight.no)
- 📱 Støtter «legg til på hjemskjerm» og fullskjermmodus
- 🛠 Adminpanel for å lage, oppdatere og moderere innhold

## 🚀 Siste nytt
- **Totrinns-MFA** i adminpanelet
- **GameCode**-baserte spillmoduser lastet fra JSON
- **Service worker** og manifest gir ekte offline-støtte
- **Revisjonslogg** på innstillinger og innhold

## 🛤️ Hva gjenstår?
- Flere placeholders som `{{next}}` og `{{oldest}}`
- Animasjoner, temaer og mer gjennomarbeidet UI
- Innebygd editor for å lage og dele egne spillmoduser
- Offline-fallback ved nettverksfeil
- Automatisk validering av collections og bedre testdekning
- Finere tilgangskontroll i adminpanelet
- Flere artikler og innhold for SEO

## 🧑‍💻 Kom i gang for utviklere
1. **Klone repoet**
   ```bash
   git clone https://github.com/USERNAME/GameNight.git
   cd GameNight
   ```
2. **Installer avhengigheter og start utviklingsserver**
   ```bash
   npm install
   npm run dev
   ```
   Dette kompilerer frontend-koden med Vite og starter en dev-server.
3. **Admin-API og database**
   - Bruk PHP 8 med PDO MySQL
   - Importer `sql/schema.sql`
   - Sett miljøvariabler `DB_HOST`, `DB_NAME`, `DB_USER` og `DB_PASS`

## 🚀 Distribusjon
1. Bygg prosjektet
   ```bash
   npm run build
   ```
2. Kopier `public/` til webserverens rot (Apache eller Nginx)
3. Sørg for HTTPS og riktige MIME-typer for `manifest.json` og `service-worker.js`

Detaljer finnes i `docs/deployment.md`.

## 🗂️ Mappestruktur
```
GameNight/
├── public/        # Prod-klar kode, API og statiske ressurser
├── src/           # Arbeidsmappe for videre utvikling
├── sql/           # Databaseskjema
├── docs/          # All dokumentasjon
├── tests/         # Jest- og PHP-tests (eksperimentelt)
└── README.md
```

## 📚 Viktige dokumenter
| Fil | Beskrivelse |
|-----|-------------|
| `docs/overview.md` | Oversikt, inspirasjon og mål |
| `docs/features.md` | Komplett funksjonsliste |
| `docs/structure.md` | Hvordan prosjektet er organisert |
| `docs/gpt-guidance.md` | Hjelp for GPT/agenter |
| `docs/deployment.md` | Serverinstallasjon og databaseoppsett |
| `docs/changelog.md` | Endringslogg |
| `docs/tests.md` | Testplan og fremtidig automatisering |
| `docs/examples/` | Eksempeldata og collections |
| `docs/articles/` | Artikler med regler for klassiske drikkespill |

## 🤝 Bidra
Pull requests og issues er velkomne! Prosjektet er fortsatt under utvikling, så alle ideer og forslag settes pris på. Se på dokumentasjonen over for å komme i gang.

## ⚖️ Ansvarsfraskrivelse
GameNight er ment for ansvarlig underholdning. Skap et trygt miljø og husk å drikke med måte. 🍻
