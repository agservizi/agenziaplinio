<?php
return [
    'site' => [
        'name' => 'Agenzia Plinio',
        'tagline' => 'Multiservizi Professionali',
        'default_title' => 'Agenzia Plinio | Servizi smart, esperienze premium',
        'default_description' => 'Pagamenti certificati, servizi digitali, telefonia e spedizioni curati dal team Agenzia Plinio per privati e aziende.',
        'og_image' => 'assets/img/og-image.jpg',
        'business' => [
            'name' => 'Agenzia Plinio',
            'telephone' => '+39 081 058 45 42',
            'email' => 'info@agenziaplinio.it',
            'address' => [
                'street' => 'Via Plinio Il Vecchio, 72',
                'locality' => 'Castellammare di Stabia (NA)',
                'postal_code' => '80053',
                'country' => 'IT'
            ],
            'opening_hours' => 'Mo-Fr 08:30-19:00, Sa 09:00-13:00'
        ]
    ],
    'smtp' => [
        'host' => 'localhost',
        'port' => 1025,
        'username' => '',
        'password' => '',
        'secure' => false,
        'from' => 'no-reply@agenziaplinio.it',
        'to' => 'info@agenziaplinio.it'
    ],
    'pages' => [
        'home' => [
            'title' => 'Agenzia Plinio | Soluzioni multiservizi',
            'description' => 'Servizi di pagamento, digitali, telefonia e spedizioni in un unico hub moderno e affidabile.',
            'sections' => ['hero', 'servizi', 'chi-siamo', 'vetrina', 'contatti']
        ],
        'servizi' => [
            'title' => 'Servizi | Agenzia Plinio',
            'description' => 'Dettaglio dei servizi offerti: pagamenti, ricariche, SPID, telefonia e spedizioni premium.',
            'sections' => ['servizi', 'contatti']
        ],
        'contatti' => [
            'title' => 'Contatti | Agenzia Plinio',
            'description' => 'Richiedi una consulenza rapida: telefono, email, form dedicato e appuntamenti in sede.',
            'sections' => ['contatti']
        ],
        'shop' => [
            'title' => 'Shop | Agenzia Plinio',
            'description' => 'Acquista SIM, servizi digitali e spedizioni premium direttamente online.',
            'sections' => ['shop']
        ],
        'product' => [
            'title' => 'Prodotto | Agenzia Plinio',
            'description' => 'Scheda prodotto con descrizione completa, prezzo e disponibilità.',
            'sections' => ['product']
        ],
        'cart' => [
            'title' => 'Carrello | Agenzia Plinio',
            'description' => 'Rivedi il tuo ordine prima del checkout.',
            'sections' => ['cart']
        ],
        'account' => [
            'title' => 'Area clienti | Agenzia Plinio',
            'description' => 'Accedi allo storico ordini, aggiorna i tuoi dati e gestisci i servizi attivati.',
            'sections' => ['account']
        ],
        'pagamenti-certificati' => [
            'title' => 'Pagamenti Certificati | Agenzia Plinio',
            'description' => 'Servizi di pagamento sicuri e certificati per bollettini, pagoPA, F24 e molto altro.',
            'sections' => ['pagamenti-certificati']
        ],
        'ricariche-multicanale' => [
            'title' => 'Ricariche Multi-Canale | Agenzia Plinio',
            'description' => 'Ricariche telefoniche, carte prepagate e device tech con conferma istantanea e massima comodità.',
            'sections' => ['ricariche-multicanale']
        ],
        'identita-digitale' => [
            'title' => 'Identità Digitale, PEC e Firma Digitale | Agenzia Plinio',
            'description' => 'Servizi digitali per SPID, firma digitale e PEC con attivazione semplice e sicura.',
            'sections' => ['identita-digitale']
        ],
        'soluzioni-voce-dati' => [
            'title' => 'Soluzioni Voce e Dati | Agenzia Plinio',
            'description' => 'Linee telefoniche, connessioni internet e dispositivi tech con attivazione guidata e assistenza completa.',
            'sections' => ['soluzioni-voce-dati']
        ],
        'logistica-smart' => [
            'title' => 'Logistica Smart | Agenzia Plinio',
            'description' => 'Spedizioni pacchi e corrispondenza con tracking live, pick-up programmati e coperture assicurative dedicate.',
            'sections' => ['logistica-smart']
        ],
        'admin' => [
            'title' => 'Admin | Agenzia Plinio',
            'description' => 'Gestisci catalogo, ordini e clienti dallo spazio riservato allo staff.',
            'sections' => ['admin']
        ]
    ]
];
