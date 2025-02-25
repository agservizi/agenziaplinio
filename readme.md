# AG SERVIZI VIA PLINIO 72 DI CAVALIERE CARMINE

Questo progetto è un sito web per un'agenzia di servizi che offre consulenza, marketing, supporto tecnico e altre soluzioni su misura. Di seguito una panoramica delle funzionalità e delle modifiche implementate.

## Struttura del Progetto

- **Index.html**: Pagina home con sezioni quali Hero, Chi Siamo, Servizi, Soluzioni, Testimonianze, Contatti e FAQ.
- **Blog.html**: Sezione blog con articoli demo, paginazione e sezione dedicata alle notizie.
- **Privacy-policy.html** e **Cookie-policy.html**: Pagine dedicate alla privacy e ai cookie.
- **Assets**
  - `assets/css/style.css`: Contiene tutte le regole di stile, compresi i reset, le variabili CSS, le media query per il responsive design e gli stili specifici per le sezioni (testimonianze, card, navbar, ecc.).
  - `assets/js/main.js`: Script di inizializzazione per Swiper, Rellax, AOS, GSAP, gestione del menu, cookie banner, newsletter e pulsante "Torna su".

## Modifiche Principali

- **Testimonianze**: La sezione "Cosa dicono i nostri clienti" è stata trasformata in un layout a schede, con foto circolari, citazioni del cliente, nome e valutazione con icone a stella.
- **Responsive Design**: Sono state applicate media query per dispositivi piccoli, tablet, desktop e laptop per garantire che il layout si adatti correttamente a diverse risoluzioni.
- **Interattività**:
  - Navigazione fluida (scrolling smooth) per i link interni.
  - Gestione del menu burger per la versione mobile.
  - Animazioni con GSAP e AOS per rendere lo scroll e le transizioni più dinamiche.
- **Cookie e Newsletter**: Implementazione di un cookie banner con opzioni di accettazione/rifiuto e form per l'iscrizione alla newsletter.
- **Altri dettagli**:
  - Pagine dedicate a Privacy Policy e Cookie Policy con contenuti aggiornati.
  - Integrazione di Chart.js per visualizzare grafici (ad es. grafico dei clienti soddisfatti).

## Istruzioni per lo Sviluppo e la Manutenzione

- **Stili**: Tutte le regole CSS sono concentrate in `assets/css/style.css`. Per modificare l’aspetto di una sezione, cercare la relativa area (utilizzando i commenti `/* ...existing css... */` per ignorare il codice inalterato).
- **Script**: Tutti gli script JavaScript si trovano in `assets/js/main.js`. Le funzionalità (menu, newsletter, transizioni, ecc.) sono modulari e facilmente estendibili.
- **Contenuti**: I contenuti testuali sono presenti all’interno dei file HTML. Per aggiornamenti di contenuti o sezioni, modificare direttamente i file HTML (es. `index.html`, `blog.html`, `privacy-policy.html`, ecc.)

## Note Finali

Il progetto è strutturato per essere facilmente scalabile e mantenibile.  
Per ulteriori modifiche o integrazioni, si consiglia di mantenere lo stesso approccio modulare già adottato.

<!-- ...existing documentation o note aggiuntive... -->
