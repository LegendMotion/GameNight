# 🎉 GameNight – Den ultimate PWA-drikkespillplattformen

Velkommen til **GameNight** – en rask, offline-kapabel og sosialt engasjerende webapp for drikkespill, inspirert av Picolo. Appen er laget for bruk på vors, fester og spillkvelder – både på mobil og TV-skjerm.

🔗 Offisiell side: [https://gamenight.no](https://gamenight.no)

## 🚀 Hva er GameNight?

- En **Progressive Web App (PWA)** som kjører 100 % i nettleseren for spillere – ingen installasjon eller innlogging kreves.
- Spillere legger inn **navnene på deltakerne**, velger en spillmodus (collection), og appen viser **én tilfeldig utfordring av gangen**.
- Fungerer **offline** takket være caching og lokal lagring.
- Spillmoduser, artikler og innstillinger administreres via et PHP-basert **admin-grensesnitt** med MySQL som database.

## ✅ Ferdige funksjoner
- Spillmoduser lastes via GameCode og JSON-baserte filer
- Challenges vises i tilfeldig rekkefølge uten gjentakelser
- Spillernavn lagres i `localStorage`, og placeholderen `{{player}}` erstattes automatisk
- PWA-funksjoner med manifest og service worker gir offline-støtte
- Sample-modus `FEST123` tilgjengelig for demo
- **Administrasjonsverktøy**
  - Innlogging med passord og TOTP-basert MFA
  - Brukerhåndtering med oppretting, redigering og reset av MFA
  - Spilladministrasjon med synlighet, bildeopplasting og eksterne redigeringstokens
  - Artikkel- og bloggadministrasjon med filtrering på type
  - Innstillinger med revisjonslogg

## 🚧 Gjenstående arbeid før lansering
- Flere placeholders som `{{next}}` og `{{oldest}}`
- UI-komponenter med animasjoner og bedre design
- Editor for å lage og dele egne spillmoduser
- Offline fallback ved nettverksfeil
- Tidsstyrte temaer og flere visuelle temaer
- Flere artikler og innhold for SEO
- Automatiserte tester og validering av collections
- Forbedret admin-grensesnitt og tilgangskontroll

## 🛠️ Installasjon på server

GameNight består av statiske filer, et PHP-API og et administrasjonsgrensesnitt. Du kan hoste prosjektet på en vanlig Apache- eller Nginx-server.

**Forutsetninger:** PHP 8 med PDO MySQL og enten GD- eller Imagick-utvidelsen for bildeoptimalisering.

1. **Klone repoet**
   ```bash
   git clone https://github.com/USERNAME/GameNight.git
   cd GameNight
   ```
2. **Bygg prosjektet**
   ```bash
   npm install
   npm run build
   ```
3. **Kopier `public/` til webserverens rot**
   Dette er den prod-klare koden, inkludert `index.html`, service worker, API og admin-grensesnitt (`/admin`).
4. **Sett opp database**
   - Importer `sql/schema.sql` i MySQL.
   - Konfigurer DB-tilkobling via miljøvariabler (`DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`) eller rediger `public/api/db.php`.
5. **Aktiver HTTPS** for full PWA-støtte og sørg for at `manifest.json` og `service-worker.js` serveres med riktige MIME-typer.

Se `docs/deployment.md` for mer detaljert veiledning.

## 📂 Mappestruktur

```
GameNight/
├── public/        # Prod-klar kode, API og statiske ressurser
├── src/           # Arbeidsmappe for fremtidig kildekode
├── sql/           # Databaseskjema
├── docs/          # All dokumentasjon
└── README.md
```

## 📖 Viktig dokumentasjon

| Fil                          | Beskrivelse |
|------------------------------|-------------|
| `docs/overview.md`           | Hva prosjektet er, inspirasjon, mål |
| `docs/features.md`           | Komplett funksjonsoversikt |
| `docs/game-schema.json`      | Struktur på én challenge |
| `docs/collection-schema.json`| Struktur på én spillmodus |
| `docs/structure.md`          | Hvordan filene er organisert |
| `docs/user-flow.md`          | Hvordan en bruker bruker appen steg-for-steg |
| `docs/theming.md`            | Hvordan visuelle tema aktiveres basert på dato |
| `docs/analytics.md`          | GDPR-vennlig bruk av Google Analytics |
| `docs/content-strategy.md`   | Hvordan vi tenker rundt SEO, AdSense og artikler |
| `docs/editor.md`             | Krav og mål for editor-verktøyet |
| `docs/gpt-guidance.md`       | En forklaring for GPT/agenter som skal forstå prosjektet |
| `docs/admin-interface.md`    | Detaljer om admin-grensesnittet |
| `docs/changelog.md`          | Logg over hva som er bygget når |
| `docs/languages.md`          | Hvordan språkutvidelse fungerer |
| `docs/tests.md`              | Hva som må testes og hvordan |
| `docs/components.md`         | Plan for UI-komponenter |
| `docs/deployment.md`         | Detaljert guide for serverinstallasjon og databaseoppsett |
| `docs/examples/`             | Eksempeldata: ferdige JSON-filer til testing |
| `docs/articles/`             | Artikler med regler for klassiske drikkespill (SEO-innhold) |

## 🔒 Sikkerhet og personvern

- Spillere har ingen innlogging; all spillerdata lagres lokalt i nettleseren.
- Admin-grensesnittet krever innlogging med MFA og logger endringer for revisjon.
- Analyse skjer anonymt med Google Analytics.
- Fullt i tråd med GDPR- og AdSense-retningslinjer.

## 🧠 For utviklere og GPT-brukere

Du trenger ikke snakke med prosjektets eier for å forstå GameNight.

👉 **Start her:**
1. `docs/overview.md`
2. `docs/structure.md`
3. `docs/gpt-guidance.md`

## 🖼 Eksisterende innhold

- Mockups og UI-design i `public/mockups/`
- Logoer i `public/logos/`
- Challenge-titler og bilder i `public/challenges/`

God utvikling – og husk å drikke ansvarlig! 🍻
