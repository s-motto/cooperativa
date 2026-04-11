# Gestionale Cooperativa

Applicazione web per la gestione interna della **Cooperativa di Comunità Progetto Appennino** (Zeri, MS).

Costruita con Laravel 13, MySQL e Tailwind CSS.

---

## Funzionalità

- **Prima Nota** — registro entrate e uscite suddivise per cassa e banca, con filtri per tipo, conto, categoria e periodo. Export in Excel.
- **Soci** — anagrafica soci con storico quote annuali. La registrazione di una quota genera automaticamente un movimento nella prima nota.
- **Verbali** — archivio verbali di assemblea e consiglio, con possibilità di allegare PDF.
- **Dashboard** — riepilogo saldi cassa/banca, ultimi movimenti, soci attivi e insoluti.
- **Gestione utenti** — creazione utenti con ruoli admin/membro. Registrazione pubblica disabilitata.

---

## Stack tecnologico

- **Backend:** PHP 8.3, Laravel 13
- **Frontend:** Blade, Tailwind CSS, Alpine.js
- **Database:** MySQL 8/9
- **Ambiente locale:** Laragon
- **Export:** Laravel Excel (maatwebsite/excel)
- **Autenticazione:** Laravel Breeze

---

## Installazione locale

### Prerequisiti

- Laragon (include PHP 8.3, MySQL, Composer, Node.js)

### Setup

```bash
# Clona il repository
git clone https://github.com/s-motto/cooperativa.git
cd cooperativa

# Installa dipendenze PHP
composer install

# Installa dipendenze JS
npm install && npm run build

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
# Crea il database e popola con i dati di esempio
php artisan migrate --seed

# Crea il link per i file caricati
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
│ ├── Controllers/ # DashboardController, MovimentoController, SocioController...
│ └── Middleware/ # IsAdmin
├── Models/ # Movimento, Socio, Categoria, QuotaSociale, Verbale
└── Exports/ # MovimentiExport
database/
├── migrations/ # Struttura del database
└── seeders/ # Dati di esempio
resources/views/
├── dashboard.blade.php
├── movimenti/ # index, create, edit
├── soci/ # index, create, edit
├── verbali/ # index, create, show
└── utenti/ # create

---

## Utilizzo quotidiano

1. Avvia **Laragon** → Start All
2. Vai su `http://cooperativa.test`
3. Accedi con le tue credenziali

Se hai modificato il codice, svuota la cache:

```bash
php artisan cache:clear && php artisan config:clear && php artisan route:clear && php artisan view:clear
```

---

## Autore

Sviluppato da **Sabrina Motto** — [sabrina-motto.vercel.app](https://sabrina-motto.vercel.app)
