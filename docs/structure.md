# App-struktur

```
GameNight/
├── public/              # Prod-klar kode, API og admin
│   ├── api/             # PHP-endepunkter
│   ├── admin/           # Admin-grensesnitt
│   ├── assets/, backgrounds/, ...
│   └── challenges/, logos/, mockups/  # Statiske ressurser
├── src/                 # Frontend-kildekode
│   ├── index.html
│   ├── main.js
│   ├── styles/
│   ├── components/
│   └── data/            # Parsed collections fra JSON
├── sql/                 # Databaseskjema og migrasjoner
├── docs/                # Dokumentasjon (denne mappen)
├── tests/               # PHPUnit-tester
├── README.md
└── ...
```