# Språkstøtte

GameNight skal i utgangspunktet kun støtte norsk (`no`). Men appen og datastrukturen er bygd for å kunne støtte flere språk i fremtiden.

## Teknisk struktur
- Hver spillmodus (collection) har et `language`-felt
- All UI-tekst planlegges som tekst-ID-er senere for mulig oversettelse
- Artikler kan også merkes med språk