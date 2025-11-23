<?php
require_once __DIR__ . '/../components/service-card.php';

$services = [
    [
        'title' => 'Pagamenti smart',
        'description' => 'Bollettini, pagoPA, F24 e tributi gestiti in sicurezza con ricevuta digitale immediata.',
        'icon' => '💳',
        'key' => 'payments'
    ],
    [
        'title' => 'Ricariche evolute',
        'description' => 'Telefonia, carte prepagate e ricariche tech con monitoraggio in tempo reale.',
        'icon' => '📱',
        'key' => 'topups'
    ],
    [
        'title' => 'Servizi digitali',
        'description' => 'SPID, PEC, firme digitali e consulenza per identità elettronica certificata.',
        'icon' => '💻',
        'key' => 'digital'
    ],
    [
        'title' => 'Telefonia partner',
        'description' => 'WindTre, Fastweb, Iliad: attivazioni rapide, portabilità e assistenza post vendita.',
        'icon' => '📞',
        'key' => 'telco'
    ],
    [
        'title' => 'Spedizioni premium',
        'description' => 'Pacchi e corrispondenza con tracciamento live e coperture assicurative dedicate.',
        'icon' => '📦',
        'key' => 'shipping'
    ],
    [
        'title' => 'Consulenza su misura',
        'description' => 'Soluzioni integrate per aziende e professionisti con account manager dedicato.',
        'icon' => '⚙️',
        'key' => 'advisory'
    ],
];
?>
<section id="servizi" class="services-section section-padding">
    <div class="container">
        <div class="section-heading text-center" data-reveal="fade-up">
            <p class="eyebrow">Cosa facciamo</p>
            <h2 class="mb-3">Servizi che semplificano la tua giornata</h2>
            <p class="text-muted">Processi chiari, supporto umano e tecnologia proprietaria per gestire qualsiasi esigenza nel modo più veloce.</p>
        </div>
        <div class="row g-4 mt-4">
            <?php foreach ($services as $service): ?>
                <div class="col-md-6 col-lg-4">
                    <?php echo renderServiceCard($service); ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
