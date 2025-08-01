# GPT FORKLARING – TEKNISK BESKRIVELSE

Dette prosjektet heter GameNight. Frontenden er en PWA som kjører på Apache eller Nginx, mens dynamisk innhold administreres via et PHP-API og et eget admin-grensesnitt koblet til en MySQL-database. Spillmoduser (collections) kan fortsatt hentes som JSON-filer, men kan nå opprettes og vedlikeholdes gjennom admin-panelet.

## Status per 2025-05-07
- GameCode brukes for å laste spillmodus
- Appen fungerer offline etter installasjon
- Spillere legges inn og lagres lokalt
- Placeholder {{player}} erstattes i challenge-tekster
- PWA-funksjonalitet er aktiv (manifest, service worker)
- Sample-modus FEST123 demonstrerer flowen
- Full `docs/`-struktur finnes
- Admin-grensesnittet tilbyr innlogging med MFA og CRUD for spill, artikler, brukere og innstillinger
