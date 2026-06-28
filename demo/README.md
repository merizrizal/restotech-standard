# Restotech Standard Demo App

This demo spins up an isolated Laravel app that installs the `restotech/standard` package from the local repository and boots a seeded database.

## Run

```bash
rtk docker compose -f docker-compose.demo.yml up --build
```

Then open:

- http://localhost:8080
- http://localhost:8080/login
- http://localhost:8080/restotech/admin
- http://localhost:8080/restotech/pos

## Demo credentials

- Email: `demo@restotech.test`
- Password: `password`

## Seeded data

- One open transaction day
- One open cashier balance
- One dining area and dining table
- One menu category, unit, item, recipe item, and stock balance
