# Gestionale Cooperativa

Applicazione web per la gestione interna della **Cooperativa di Comunità Progetto Appennino** (Zeri, MS).

Costruita con Laravel 13, PHP 8.3, MySQL e Tailwind CSS.

---

## Funzionalità

- **Prima Nota** — registro unificato di entrate, uscite e fatture, ordinato per data. Filtri per tipo, conto, categoria e periodo. Export in Excel con colonne separate per cassa e banca, saldo progressivo e fatture in evidenza.
- **Fatture** — gestione fatture attive (da incassare) e passive (da pagare), con allegato PDF, data di scadenza e stato (aperta/parziale/pagata). Supporto a pagamenti parziali con metodi diversi (cassa/banca): ogni acconto genera un movimento in prima nota e aggiorna automaticamente il residuo. Le fatture appaiono direttamente nella prima nota con badge visivo.
- **Saldi di apertura** — inserimento del saldo iniziale di cassa e banca, con supporto a valori negativi.
- **Soci** — anagrafica soci con storico quote annuali. La registrazione di una quota genera automaticamente un movimento nella prima nota.
- **Verbali** — archivio verbali di assemblea e consiglio, con possibilità di allegare PDF.
- **Dashboard** — riepilogo saldi cassa/banca, ultimi movimenti, soci attivi e insoluti.
- **Gestione utenti** — creazione utenti con ruoli admin/membro. Registrazione pubblica disabilitata.

---

## Stack tecnologico

- **Backend:** PHP 8.3, Laravel 13
- **Frontend:** Blade, Tailwind CSS (CDN), Bootstrap 5 (CDN), Alpine.js (CDN)
- **Database:** MySQL 8
- **Ambiente locale:** Laragon
- **Deploy:** Hetzner CX23 + Coolify + Nixpacks
- **Export:** Laravel Excel (maatwebsite/excel)
- **Autenticazione:** Laravel Breeze

---

## Installazione locale

### Prerequisiti

- Laragon (include PHP 8.3, MySQL, Composer)

### Setup

```bash
# Clona il repository
git clone https://github.com/s-motto/cooperativa.git
cd cooperativa

# Installa dipendenze PHP
composer install

# Configura l'ambiente
cp .env.example .env
php artisan key:generate
```

Modifica il file `.env` con le credenziali del tuo database:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=cooperativa
DB_USERNAME=root
DB_PASSWORD=tua_password
```

```bash
# Crea le tabelle e popola con i dati di esempio
php artisan migrate --seed

# Crea il link per i file caricati (verbali, fatture)
php artisan storage:link
```

### Avvio

```bash
php artisan serve
```

Oppure con Laragon configurato: visita `http://cooperativa.test`

### Credenziali di default

| Campo    | Valore               |
| -------- | -------------------- |
| Email    | admin@cooperativa.it |
| Password | password             |

> ⚠️ Cambia la password dopo il primo accesso.

---

## Struttura del progetto

app/
├── Http/
│ ├── Controllers/ # Dashboard, Movimento, Fattura, Socio, Verbale, Utente, SaldoApertura
│ └── Middleware/ # IsAdmin
├── Models/ # Movimento, Fattura, Socio, Categoria, QuotaSociale, Verbale, User
└── Exports/ # MovimentiExport
database/
├── migrations/ # Struttura del database
└── seeders/ # Dati di esempio
resources/views/
├── dashboard.blade.php
├── movimenti/ # index, create, edit
├── fatture/ # index, create, show, pagamento
├── soci/ # index, create, edit
├── verbali/ # index, create, show
├── utenti/ # create
└── saldi-apertura/ # create

---

## Utilizzo quotidiano

1. Avvia **Laragon** → Start All
2. Vai su `http://cooperativa.test`
3. Accedi con le tue credenziali

Per svuotare la cache dopo modifiche al codice:

```bash
php artisan cache:clear && php artisan config:clear && php artisan route:clear && php artisan view:clear
```

---

## Produzione

Il gestionale è deployato su **Hetzner CX23** tramite **Coolify**.
Nixpacks rileva automaticamente il progetto PHP/Laravel e gestisce il build.
Bootstrap e Tailwind CSS sono caricati via CDN — non è necessario alcun build frontend.

---

## Autore

Sviluppato da **Sabrina Motto** — [sabrina-motto.vercel.app](https://sabrina-motto.vercel.app)
