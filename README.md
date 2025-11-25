# Agenzia Plinio - Sito E-commerce

Sito web completo per Agenzia Plinio, multiservizi professionali con e-commerce integrato.

## Caratteristiche

- **E-commerce Completo**: Shop, carrello, checkout con pagamenti Stripe/Klarna reali
- **Gestione Prodotti**: Custom fields per prodotti digitali, categorie, stock
- **Sistema Utenti**: Registrazione, login, profilo, ordini storici
- **Admin Panel**: Gestione prodotti, ordini, utenti, FAQ, coupon
- **Sicurezza**: CSRF protection, input validation, audit logs
- **Responsive**: Design mobile-first con Bootstrap
- **SEO Ottimizzato**: Meta tags, sitemap, schema.org
- **Performance**: Cache, lazy loading, minificazione assets

## Requisiti

- PHP 8.1+
- MySQL 5.7+
- Composer (per dipendenze future)
- Node.js (per build assets)

## Installazione

1. Clona il repository:
   ```bash
   git clone https://github.com/agservizi/agenziaplinio.git
   cd agenziaplinio
   ```

2. Configura ambiente:
   - Copia `.env.example` in `.env`
   - Modifica le variabili database, email, pagamenti

3. Installa dipendenze:
   ```bash
   composer install  # se presente
   npm install       # per minificazione
   ```

4. Setup database:
   - Importa `agenziaplinio.sql`
   - Verifica connessione in `includes/database.php`

5. Avvia server:
   ```bash
   php -S localhost:8000
   ```

## Struttura

- `index.php`: Entry point
- `sections/`: Pagine del sito
- `includes/`: Logica backend
- `components/`: Componenti riutilizzabili
- `assets/`: CSS, JS, immagini
- `admin/`: Pannello amministratore

## Deployment

1. Carica files su server
2. Configura dominio e SSL
3. Setup cron per backup: `0 2 * * * php /path/to/backup.php`
4. Monitora logs in `storage/logs/`

## Sicurezza

- Usa HTTPS sempre
- Aggiorna password regolarmente
- Monitora accessi admin
- Backup giornaliero automatico

## Supporto

Per problemi: info@agenziaplinio.it

## Licenza

Proprietario - Agenzia Plinio