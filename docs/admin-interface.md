# Admin-grensesnitt

Admin-panelet lar eiere og moderatorer administrere innholdet i GameNight. Det ligger under `/admin` og er bygget i PHP med enkle HTML-skjemaer og SweetAlert2 for tilbakemeldinger.

## Funksjoner

### Autentisering og sikkerhet
- Innlogging med e-post og passord
- TOTP-basert MFA eller engangskoder via e-post
- CSRF-beskyttelse og strenge sesjonskapsler (`HttpOnly`, `SameSite=Strict`)

### Brukere
- Liste, opprettelse og redigering av brukere
- Rollenivå: admin kan administrere andre brukere
- Tilbakestilling av MFA

### Spill
- Opprett, rediger og slett spill
- Synlighetsnivåer: `public`, `private`, `hidden`
- Opplasting av fremhevet bilde
- Generering av tidsbegrensede redigeringstoken for eksterne bidragsytere

### Artikler
- Opprett, rediger, søk og slett artikler
- Filtrering på type (drink, game, ...)
- Paginering av resultater

### Innstillinger
- Nøkkel/verdi-lagring av konfigurasjon
- Audit-logg som viser hvem som endret hva
- Sider for e-postserver og MFA-konfigurasjon

## Brukervennlighet
- Minimalistisk design med få distraksjoner
- Navigasjon via lenker mellom seksjoner
- Skjemaer er enkle og rett fram
- Mangler dashboard og samlet navigasjonsmeny

## Hva som mangler
- WYSIWYG-editor for rik tekst
- Søk og filtrering for spill og brukere
- Role-based access utover `admin`
- Bedre feilhåndtering og validering
- Responsivt design og generell styling

## Status
De viktigste administrasjonsfunksjonene er implementert og fungerer, men grensesnittet er grunnleggende og kan forbedres med bedre UX, flere roller og rikere editorer.
