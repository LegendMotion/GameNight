# Developer Guide – GameNight

Denne filen er ment for utviklere og GPTer som skal bidra til eller videreutvikle GameNight.

## 🎯 Mål
- Webapp for mobil/nettleser med fokus på festspill
- Spilldata defineres via JSON
- Ingen login/backend – ren statisk frontend
- Hostes på Apache (shared hosting)
- Full støtte for PWA og offline-spilling

## 🧩 Spillstruktur
- ID: fire sifre, f.eks. "1234"
- Tittel og beskrivelse (valgfritt)
- Bilde-URL (helst under `games/assets/`)
- Public/private toggle
- Språk (eks. "no" for norsk)
- Liste over prompts (utfordringer)

## 🧪 Challenge Types

### `challenge`
```json
{ "type": "challenge", "value": "NAVN må ta 5 pushups" }
```

### `neverhaveiever`
```json
{ "type": "neverhaveiever", "value": "stjålet noe" }
```

### `yayornay`
```json
{
  "type": "yayornay",
  "question": "NAVN, vil du helst ...?",
  "options": ["Valg A", "Valg B"]
}
```

### `spillthetea`
```json
{ "type": "spillthetea", "value": "Fortell en hemmelighet" }
```

## 📜 Ikke-interaktive spill (artikler)
Ligger i `src/articles/`, f.eks. `ring-of-fire.md`. Disse vises som informasjonsartikler uten prompts.

## 🔁 Spillflyt
- Bruker legger inn navn
- En tilfeldig spiller brukes når `NAVN` dukker opp
- Challenges vises én gang per sesjon

## 📦 PWA
- Bruker `manifest.json` + `service-worker.js`
- Offline-støtte via cache

## 💰 Reklame
- Primært: AdMob via WebView
- Sekundært: AdSense hvis mulig via vanlig nett

