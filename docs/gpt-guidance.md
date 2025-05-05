# GPT FORKLARING – TEKNISK BESKRIVELSE

Dette prosjektet heter GameNight. Det er en PWA som kjører på Apache server uten backend eller database. Alle spillmoduser (collections) er JSON-filer som lastes manuelt opp på server av prosjektets eier (Jan Helge).

Utviklingsprinsipper:
- Ingen autentisering
- Ingen ekstern API-bruk (bortsett fra analyse via Google Analytics)
- Alt kjører i nettleser (client-side rendering)
- Spillere legges inn manuelt før spillstart
- Spillmoduser inneholder utfordringer i JSON-format
- Utfordringer vises i tilfeldig rekkefølge uten gjentakelse
- Enkelte spillmoduser kan trigge visuelle tema (f.eks. "virus" gir glitchy stil)

Du kan alltid finne utfordringene i `collection-schema.json` og `game-schema.json`.