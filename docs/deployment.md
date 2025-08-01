# Serverinstallasjon

Denne guiden beskriver hvordan du setter opp GameNight på en egen server.

## Krav
- Apache eller Nginx med støtte for HTTPS
- PHP 8 med PDO MySQL-utvidelse
- GD eller Imagick PHP-utvidelse for bildeoptimalisering
- MySQL 5.7+ for lagring av spill, artikler, brukere og innstillinger
- Git for å hente koden

## Trinn
1. **Klone repoet**
   ```bash
   git clone https://github.com/USERNAME/GameNight.git
   cd GameNight
   ```
2. **Kopier til webroot**
   - Last opp innholdet i `public/` til serverens document root.
3. **Konfigurer database**
   - Importer `sql/schema.sql` i MySQL.
   - Sett miljøvariablene `DB_HOST`, `DB_NAME`, `DB_USER` og `DB_PASS`, eller rediger `public/api/db.php` direkte.
4. **Sett admin-passord**
   - Generer et bcrypt-hash av ønsket admin-passord.
   - Sett miljøvariabelen `ADMIN_PASS_HASH` til denne verdien.
5. **Aktiver HTTPS og caching**
   - PWA-funksjonalitet krever at nettstedet kjøres over HTTPS.
   - Sørg for at `manifest.json` og `service-worker.js` serveres med korrekte MIME-typer.
6. **Test installasjonen**
   - Åpne siden i en nettleser og bekreft at utfordringer lastes og at appen fungerer offline etter første besøk.

## Oppdateringer
For å oppdatere, hent siste endringer og synkroniser `public/` på nytt. Husk å kjøre eventuell database-migrasjon ved oppdatering av databasen.
