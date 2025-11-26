<?php
declare(strict_types=1);


require_once __DIR__ . '/env.php';

ap_load_env(__DIR__ . '/../.env');

function ap_db(): PDO
{
    static $pdo = null;
    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $host = ap_env('DB_HOST', '127.0.0.1');
    $dbname = ap_env('DB_DATABASE', 'agenziaplinio');
    $port = (int) (ap_env('DB_PORT', '3306') ?? '3306');
    $user = ap_env('DB_USERNAME', 'root') ?? 'root';
    $pass = ap_env('DB_PASSWORD', '');

    $dsn = sprintf('mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4', $host, $port, $dbname);

    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    ap_bootstrap_schema($pdo);

    return $pdo;
}

function ap_bootstrap_schema(PDO $pdo): void
{
    $pdo->exec('CREATE TABLE IF NOT EXISTS users (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(120) NOT NULL,
        email VARCHAR(190) NOT NULL UNIQUE,
        password_hash VARCHAR(255) NOT NULL,
        role ENUM("customer", "admin") NOT NULL DEFAULT "customer",
        is_active TINYINT(1) NOT NULL DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');

    $pdo->exec('CREATE TABLE IF NOT EXISTS addresses (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        user_id INT UNSIGNED NOT NULL,
        label VARCHAR(80) NOT NULL,
        recipient_name VARCHAR(120) NOT NULL,
        street TEXT NOT NULL,
        city VARCHAR(120) NOT NULL,
        province VARCHAR(80) DEFAULT NULL,
        postal_code VARCHAR(20) NOT NULL,
        country VARCHAR(80) NOT NULL DEFAULT "Italia",
        phone VARCHAR(40) DEFAULT NULL,
        is_default TINYINT(1) NOT NULL DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');

    $pdo->exec('CREATE TABLE IF NOT EXISTS products (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(200) NOT NULL,
        slug VARCHAR(220) NOT NULL UNIQUE,
        description TEXT,
        price_cents INT UNSIGNED NOT NULL,
        sku VARCHAR(80) DEFAULT NULL,
        category_key VARCHAR(80) DEFAULT NULL,
        fulfillment_type ENUM("digital", "physical") NOT NULL DEFAULT "digital",
        stock INT NOT NULL DEFAULT 0,
        image_url VARCHAR(255) DEFAULT NULL,
        is_active TINYINT(1) NOT NULL DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');

    $pdo->exec('CREATE TABLE IF NOT EXISTS orders (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        user_id INT UNSIGNED NOT NULL,
        shipping_address_id INT UNSIGNED NULL,
        status ENUM("pending", "processing", "completed", "cancelled") NOT NULL DEFAULT "pending",
        total_cents INT UNSIGNED NOT NULL,
        shipping_name VARCHAR(120) NOT NULL,
        shipping_address TEXT NOT NULL,
        shipping_method VARCHAR(60) DEFAULT "standard",
        shipping_status ENUM("preparing", "in_transit", "delivered", "issue") NOT NULL DEFAULT "preparing",
        shipping_cost_cents INT UNSIGNED NOT NULL DEFAULT 0,
        shipping_notes TEXT NULL,
        tracking_code VARCHAR(120) NULL,
        contact_phone VARCHAR(40) NULL,
        po_number VARCHAR(120) NULL,
        cost_center VARCHAR(120) NULL,
        delivery_window VARCHAR(50) NULL,
        sla_plan VARCHAR(50) NULL,
        addons JSON NULL,
        ops_channels JSON NULL,
        contract_ref VARCHAR(190) NULL,
        procurement_files JSON NULL,
        coupon_code VARCHAR(60) NULL,
        coupon_id INT UNSIGNED NULL,
        discount_cents INT UNSIGNED NOT NULL DEFAULT 0,
        payment_method VARCHAR(50) NULL,
        payment_status ENUM("pending","authorized","paid","failed","refunded") DEFAULT "pending",
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (shipping_address_id) REFERENCES addresses(id) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');

    $pdo->exec('CREATE TABLE IF NOT EXISTS order_items (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        order_id INT UNSIGNED NOT NULL,
        product_id INT UNSIGNED NOT NULL,
        quantity INT UNSIGNED NOT NULL,
        price_cents INT UNSIGNED NOT NULL,
        digital_file_path VARCHAR(255) NULL,
        FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
        FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');

    $pdo->exec('CREATE TABLE IF NOT EXISTS payments (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        order_id INT UNSIGNED NOT NULL,
        provider VARCHAR(50) NOT NULL,
        status ENUM("pending","authorized","paid","failed","refunded") NOT NULL DEFAULT "pending",
        amount_cents INT UNSIGNED NOT NULL DEFAULT 0,
        transaction_ref VARCHAR(120) NULL,
        meta JSON NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');

    $pdo->exec('CREATE TABLE IF NOT EXISTS coupons (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        code VARCHAR(60) NOT NULL UNIQUE,
        description VARCHAR(255) NULL,
        type ENUM("fixed","percent") NOT NULL DEFAULT "fixed",
        amount_cents INT UNSIGNED NOT NULL DEFAULT 0,
        percent INT UNSIGNED NOT NULL DEFAULT 0,
        min_total_cents INT UNSIGNED NOT NULL DEFAULT 0,
        max_redemptions INT UNSIGNED NULL,
        redemptions_count INT UNSIGNED NOT NULL DEFAULT 0,
        starts_at DATETIME NULL,
        ends_at DATETIME NULL,
        is_active TINYINT(1) NOT NULL DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');

    $pdo->exec('CREATE TABLE IF NOT EXISTS abandoned_carts (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        session_id VARCHAR(120) NOT NULL UNIQUE,
        user_id INT UNSIGNED NULL,
        email VARCHAR(190) NULL,
        cart_snapshot JSON NOT NULL,
        total_cents INT UNSIGNED NOT NULL DEFAULT 0,
        coupon_code VARCHAR(60) NULL,
        discount_cents INT UNSIGNED NOT NULL DEFAULT 0,
        last_activity DATETIME NOT NULL,
        notified_at DATETIME NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
        INDEX idx_last_activity (last_activity)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');

    $pdo->exec('CREATE TABLE IF NOT EXISTS announcements (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        message TEXT NOT NULL,
        is_active TINYINT(1) NOT NULL DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');

    $pdo->exec('CREATE TABLE IF NOT EXISTS faqs (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        question VARCHAR(255) NOT NULL,
        answer TEXT NOT NULL,
        category VARCHAR(120) NULL,
        keywords TEXT NULL,
        sort_order INT UNSIGNED NOT NULL DEFAULT 0,
        is_active TINYINT(1) NOT NULL DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');

    $pdo->exec('CREATE TABLE IF NOT EXISTS product_custom_fields (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        product_id INT UNSIGNED NOT NULL,
        field_name VARCHAR(100) NOT NULL,
        field_label VARCHAR(200) NOT NULL,
        field_type ENUM("text","textarea","select","checkbox") NOT NULL DEFAULT "text",
        field_options TEXT NULL,
        is_required TINYINT(1) NOT NULL DEFAULT 0,
        sort_order INT UNSIGNED NOT NULL DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');

    $pdo->exec('CREATE TABLE IF NOT EXISTS order_custom_data (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        order_id INT UNSIGNED NOT NULL,
        product_id INT UNSIGNED NOT NULL,
        field_name VARCHAR(100) NOT NULL,
        field_value TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
        FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');

    $pdo->exec('CREATE TABLE IF NOT EXISTS settings (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        setting_key VARCHAR(100) NOT NULL UNIQUE,
        setting_value TEXT NULL,
        setting_type ENUM("string","int","bool","json") NOT NULL DEFAULT "string",
        description VARCHAR(255) NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');

    $pdo->exec('CREATE TABLE IF NOT EXISTS security_logs (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        event_type VARCHAR(50) NOT NULL,
        description TEXT NOT NULL,
        user_id INT UNSIGNED NULL,
        metadata JSON NULL,
        ip_address VARCHAR(45) NOT NULL,
        user_agent TEXT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
        INDEX idx_event_type (event_type),
        INDEX idx_created_at (created_at),
        INDEX idx_user_id (user_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');

    $pdo->exec('CREATE TABLE IF NOT EXISTS wishlist (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        user_id INT UNSIGNED NOT NULL,
        product_id INT UNSIGNED NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
        UNIQUE KEY unique_user_product (user_id, product_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');

    $pdo->exec('CREATE TABLE IF NOT EXISTS product_reviews (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        user_id INT UNSIGNED NOT NULL,
        product_id INT UNSIGNED NOT NULL,
        rating TINYINT UNSIGNED NOT NULL CHECK (rating >= 1 AND rating <= 5),
        title VARCHAR(200) NOT NULL,
        review TEXT NOT NULL,
        is_verified_purchase TINYINT(1) NOT NULL DEFAULT 0,
        is_approved TINYINT(1) NOT NULL DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
        INDEX idx_product_rating (product_id, rating),
        INDEX idx_created_at (created_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');

    $pdo->exec('CREATE TABLE IF NOT EXISTS notifications (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        user_id INT UNSIGNED NULL,
        type VARCHAR(50) NOT NULL,
        title VARCHAR(200) NOT NULL,
        message TEXT NOT NULL,
        is_read TINYINT(1) NOT NULL DEFAULT 0,
        data JSON NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        INDEX idx_user_read (user_id, is_read),
        INDEX idx_type (type)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');

    $pdo->exec('CREATE TABLE IF NOT EXISTS analytics_events (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        event_type VARCHAR(50) NOT NULL,
        event_data JSON NOT NULL,
        user_id INT UNSIGNED NULL,
        session_id VARCHAR(120) NULL,
        ip_address VARCHAR(45) NOT NULL,
        user_agent TEXT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
        INDEX idx_event_type (event_type),
        INDEX idx_created_at (created_at),
        INDEX idx_user_id (user_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');

    ap_try_exec($pdo, 'ALTER TABLE orders ADD COLUMN IF NOT EXISTS shipping_method VARCHAR(60) DEFAULT "standard"');
    ap_try_exec($pdo, 'ALTER TABLE orders ADD COLUMN IF NOT EXISTS shipping_status ENUM("preparing", "in_transit", "delivered", "issue") NOT NULL DEFAULT "preparing"');
    ap_try_exec($pdo, 'ALTER TABLE orders ADD COLUMN IF NOT EXISTS shipping_cost_cents INT UNSIGNED NOT NULL DEFAULT 0');
    ap_try_exec($pdo, 'ALTER TABLE orders ADD COLUMN IF NOT EXISTS shipping_notes TEXT NULL');
    ap_try_exec($pdo, 'ALTER TABLE orders ADD COLUMN IF NOT EXISTS tracking_code VARCHAR(120) NULL');
    ap_try_exec($pdo, 'ALTER TABLE orders ADD COLUMN IF NOT EXISTS contact_phone VARCHAR(40) NULL');
    ap_try_exec($pdo, 'ALTER TABLE orders ADD COLUMN IF NOT EXISTS po_number VARCHAR(120) NULL');
    ap_try_exec($pdo, 'ALTER TABLE orders ADD COLUMN IF NOT EXISTS cost_center VARCHAR(120) NULL');
    ap_try_exec($pdo, 'ALTER TABLE orders ADD COLUMN IF NOT EXISTS delivery_window VARCHAR(50) NULL');
    ap_try_exec($pdo, 'ALTER TABLE orders ADD COLUMN IF NOT EXISTS sla_plan VARCHAR(50) NULL');
    ap_try_exec($pdo, 'ALTER TABLE orders ADD COLUMN IF NOT EXISTS addons JSON NULL');
    ap_try_exec($pdo, 'ALTER TABLE orders ADD COLUMN IF NOT EXISTS ops_channels JSON NULL');
    ap_try_exec($pdo, 'ALTER TABLE orders ADD COLUMN IF NOT EXISTS contract_ref VARCHAR(190) NULL');
    ap_try_exec($pdo, 'ALTER TABLE orders ADD COLUMN IF NOT EXISTS procurement_files JSON NULL');
    ap_try_exec($pdo, 'ALTER TABLE orders ADD COLUMN IF NOT EXISTS coupon_code VARCHAR(60) NULL');
    ap_try_exec($pdo, 'ALTER TABLE orders ADD COLUMN IF NOT EXISTS coupon_id INT UNSIGNED NULL');
    ap_try_exec($pdo, 'ALTER TABLE orders ADD COLUMN IF NOT EXISTS discount_cents INT UNSIGNED NOT NULL DEFAULT 0');
    ap_try_exec($pdo, 'ALTER TABLE order_items ADD COLUMN IF NOT EXISTS digital_file_path VARCHAR(255) NULL');
    ap_try_exec($pdo, 'ALTER TABLE abandoned_carts ADD COLUMN IF NOT EXISTS coupon_code VARCHAR(60) NULL');
    ap_try_exec($pdo, 'ALTER TABLE abandoned_carts ADD COLUMN IF NOT EXISTS discount_cents INT UNSIGNED NOT NULL DEFAULT 0');
    ap_try_exec($pdo, 'ALTER TABLE users ADD COLUMN IF NOT EXISTS is_active TINYINT(1) NOT NULL DEFAULT 1');
    ap_try_exec($pdo, 'ALTER TABLE products ADD COLUMN category_key VARCHAR(80) NULL AFTER sku');
    ap_try_exec($pdo, 'ALTER TABLE products ADD COLUMN IF NOT EXISTS fulfillment_type ENUM("digital","physical") NOT NULL DEFAULT "digital" AFTER category_key');
    ap_try_exec($pdo, 'ALTER TABLE orders ADD COLUMN IF NOT EXISTS shipping_status ENUM("preparing", "in_transit", "delivered", "issue") NOT NULL DEFAULT "preparing"');
    ap_try_exec($pdo, 'ALTER TABLE orders ADD COLUMN IF NOT EXISTS shipping_cost_cents INT UNSIGNED NOT NULL DEFAULT 0');
    ap_try_exec($pdo, 'ALTER TABLE orders ADD COLUMN IF NOT EXISTS shipping_notes TEXT NULL');
    ap_try_exec($pdo, 'ALTER TABLE orders ADD COLUMN IF NOT EXISTS tracking_code VARCHAR(120) NULL');
    ap_try_exec($pdo, 'ALTER TABLE orders ADD COLUMN IF NOT EXISTS contact_phone VARCHAR(40) NULL');
    ap_try_exec($pdo, 'ALTER TABLE orders ADD COLUMN IF NOT EXISTS po_number VARCHAR(120) NULL');
    ap_try_exec($pdo, 'ALTER TABLE orders ADD COLUMN IF NOT EXISTS cost_center VARCHAR(120) NULL');
    ap_try_exec($pdo, 'ALTER TABLE orders ADD COLUMN IF NOT EXISTS delivery_window VARCHAR(50) NULL');
    ap_try_exec($pdo, 'ALTER TABLE orders ADD COLUMN IF NOT EXISTS sla_plan VARCHAR(50) NULL');
    ap_try_exec($pdo, 'ALTER TABLE orders ADD COLUMN IF NOT EXISTS addons JSON NULL');
    ap_try_exec($pdo, 'ALTER TABLE orders ADD COLUMN IF NOT EXISTS ops_channels JSON NULL');
    ap_try_exec($pdo, 'ALTER TABLE orders ADD COLUMN IF NOT EXISTS contract_ref VARCHAR(190) NULL');
    ap_try_exec($pdo, 'ALTER TABLE orders ADD COLUMN IF NOT EXISTS procurement_files JSON NULL');
    ap_try_exec($pdo, 'ALTER TABLE orders ADD COLUMN IF NOT EXISTS coupon_code VARCHAR(60) NULL');
    ap_try_exec($pdo, 'ALTER TABLE orders ADD COLUMN IF NOT EXISTS coupon_id INT UNSIGNED NULL');
    ap_try_exec($pdo, 'ALTER TABLE orders ADD COLUMN IF NOT EXISTS discount_cents INT UNSIGNED NOT NULL DEFAULT 0');
    ap_try_exec($pdo, 'ALTER TABLE order_items ADD COLUMN IF NOT EXISTS digital_file_path VARCHAR(255) NULL');
    ap_try_exec($pdo, 'ALTER TABLE abandoned_carts ADD COLUMN IF NOT EXISTS coupon_code VARCHAR(60) NULL');
    ap_try_exec($pdo, 'ALTER TABLE abandoned_carts ADD COLUMN IF NOT EXISTS discount_cents INT UNSIGNED NOT NULL DEFAULT 0');
    ap_try_exec($pdo, 'ALTER TABLE users ADD COLUMN IF NOT EXISTS is_active TINYINT(1) NOT NULL DEFAULT 1');
    ap_try_exec($pdo, 'ALTER TABLE products ADD COLUMN category_key VARCHAR(80) NULL AFTER sku');
    ap_try_exec($pdo, 'ALTER TABLE products ADD COLUMN IF NOT EXISTS fulfillment_type ENUM("digital","physical") NOT NULL DEFAULT "digital" AFTER category_key');
    ap_seed_admin($pdo);
    ap_seed_products($pdo);
    ap_seed_security_logs($pdo);
    ap_seed_audit_logs($pdo);
    ap_seed_audit_logs($pdo);
}

function ap_try_exec(PDO $pdo, string $sql): void
{
    try {
        $pdo->exec($sql);
    } catch (Throwable $ignored) {
        // Column already exists on older MySQL versions without IF NOT EXISTS support.
    }
}

function ap_seed_admin(PDO $pdo): void
{
    $stmt = $pdo->query('SELECT COUNT(*) AS total FROM users');
    $count = (int) ($stmt->fetch()['total'] ?? 0);
    if ($count > 0) {
        return;
    }
    // Use secure Argon2ID hashing for admin password
    $password = password_hash('ChangeMe123!', PASSWORD_ARGON2ID, [
        'memory_cost' => 65536, // 64MB
        'time_cost' => 4,
        'threads' => 3,
    ]);
    $insert = $pdo->prepare('INSERT INTO users (name, email, password_hash, role) VALUES (:name, :email, :password, :role)');
    $insert->execute([
        ':name' => 'Admin Plinio',
        ':email' => 'admin@agenziaplinio.it',
        ':password' => $password,
        ':role' => 'admin',
    ]);
}

function ap_seed_products(PDO $pdo): void
{
    // Check if products already exist to avoid re-seeding during development
    $stmt = $pdo->query('SELECT COUNT(*) AS total FROM products');
    $count = (int) ($stmt->fetch()['total'] ?? 0);
    if ($count > 0) {
        return; // Products already seeded
    }

    $products = [
        [
            'name' => 'Visura Camerale',
            'slug' => 'visura-camerale',
            'description' => 'Certificato di iscrizione al Registro delle Imprese della Camera di Commercio. Include dati societari, amministratori e attività economica.',
            'price_cents' => 1500, // €15.00
            'sku' => 'VIS-CAM-001',
            'category_key' => 'camerali',
            'fulfillment_type' => 'digital',
            'stock' => 999,
            'image_url' => 'assets/img/og-image.jpg',
            'is_active' => 1,
        ],
        [
            'name' => 'Visura Ipotecaria',
            'slug' => 'visura-ipotecaria',
            'description' => 'Verifica ipoteche, pignoramenti e trascrizioni su immobili. Documentazione completa e aggiornata per compravendite immobiliari.',
            'price_cents' => 2500, // €25.00
            'sku' => 'VIS-IPO-001',
            'category_key' => 'ipotecarie',
            'fulfillment_type' => 'digital',
            'stock' => 999,
            'image_url' => 'assets/img/og-image.jpg',
            'is_active' => 1,
        ],
        [
            'name' => 'Certificato di Destinazione Urbanistica',
            'slug' => 'certificato-destinazione-urbanistica',
            'description' => 'Documento che attesta la destinazione urbanistica di un immobile secondo gli strumenti urbanistici vigenti.',
            'price_cents' => 2500, // €25.00
            'sku' => 'CDU-001',
            'category_key' => 'catastali',
            'fulfillment_type' => 'digital',
            'stock' => 999,
            'image_url' => 'assets/img/og-image.jpg',
            'is_active' => 1,
        ],
        [
            'name' => 'Certificato di Proprietà',
            'slug' => 'certificato-proprieta',
            'description' => 'Estratto di mappa catastale con certificato di proprietà immobiliare.',
            'price_cents' => 2000, // €20.00
            'sku' => 'CERT-PROP-001',
            'category_key' => 'catastali',
            'fulfillment_type' => 'digital',
            'stock' => 999,
            'image_url' => 'assets/img/og-image.jpg',
            'is_active' => 1,
        ],
        [
            'name' => 'Cambio Residenza Anagrafica',
            'slug' => 'cambio-residenza-anagrafica',
            'description' => 'Procedura completa di cambio residenza o domicilio senza code e moduli complicati. Assistenza passo-passo per il trasloco anagrafico.',
            'price_cents' => 3500, // €35.00
            'sku' => 'CAMBIO-RES-001',
            'category_key' => 'cambio-residenza',
            'fulfillment_type' => 'digital',
            'stock' => 999,
            'image_url' => 'assets/img/og-image.jpg',
            'is_active' => 1,
        ],
        [
            'name' => 'Pratica SUAP - Attività Produttive',
            'slug' => 'pratica-suap-attivita-produttive',
            'description' => 'Gestione completa della pratica SUAP per apertura/modifica attività produttive. Include consulenza e presentazione domanda.',
            'price_cents' => 50000, // €500.00
            'sku' => 'SUAP-PROD-001',
            'category_key' => 'uffici-pubblici',
            'fulfillment_type' => 'digital',
            'stock' => 999,
            'image_url' => 'assets/img/og-image.jpg',
            'is_active' => 1,
        ],
        [
            'name' => 'Certificato Casellario Giudiziale',
            'slug' => 'certificato-casellario-giudiziale',
            'description' => 'Certificato penale del casellario giudiziale. Documento ufficiale per verificare precedenti penali e carichi pendenti.',
            'price_cents' => 1200, // €12.00
            'sku' => 'CAS-GIU-001',
            'category_key' => 'giudiziarie',
            'fulfillment_type' => 'digital',
            'stock' => 999,
            'image_url' => 'assets/img/og-image.jpg',
            'is_active' => 1,
        ],
        [
            'name' => 'Consulenza Legale - Costituzione Società',
            'slug' => 'consulenza-costituzione-societa',
            'description' => 'Servizio completo di consulenza legale per costituzione di società. Include statuto, atto costitutivo e registrazione.',
            'price_cents' => 75000, // €750.00
            'sku' => 'CONS-SOC-001',
            'category_key' => 'servizi-premium',
            'fulfillment_type' => 'digital',
            'stock' => 999,
            'image_url' => 'assets/img/og-image.jpg',
            'is_active' => 1,
        ],
    ];

    $insert = $pdo->prepare('INSERT INTO products (name, slug, description, price_cents, sku, category_key, fulfillment_type, stock, image_url, is_active) VALUES (:name, :slug, :description, :price_cents, :sku, :category_key, :fulfillment_type, :stock, :image_url, :is_active)');

    foreach ($products as $product) {
        $insert->execute($product);
    }

    // Add custom fields for Visura Camerale
    $stmt = $pdo->prepare('SELECT id FROM products WHERE slug = :slug');
    $stmt->execute([':slug' => 'visura-camerale']);
    $visuraProductId = $stmt->fetchColumn();
    if ($visuraProductId) {
        // Check if custom fields already exist
        $fieldStmt = $pdo->prepare('SELECT COUNT(*) FROM product_custom_fields WHERE product_id = :product_id');
        $fieldStmt->execute([':product_id' => $visuraProductId]);
        $fieldCount = (int) $fieldStmt->fetchColumn();
        
        if ($fieldCount === 0) {
            $customFields = [
        [
            'field_name' => 'company_name',
            'field_label' => 'Ragione Sociale o Denominazione',
            'field_type' => 'text',
            'field_options' => null,
            'is_required' => 1,
            'sort_order' => 1,
        ],
        [
            'field_name' => 'fiscal_code',
            'field_label' => 'Codice Fiscale o Partita IVA',
            'field_type' => 'text',
            'field_options' => null,
            'is_required' => 1,
            'sort_order' => 2,
        ],
        [
            'product_id' => $visuraProductId,
            'field_name' => 'province',
            'field_label' => 'Provincia della Camera di Commercio',
            'field_type' => 'select',
            'field_options' => "Agrigento\nAlessandria\nAncona\nAosta\nArezzo\nAscoli Piceno\nAsti\nAvellino\nBari\nBarletta-Andria-Trani\nBelluno\nBenevento\nBergamo\nBiella\nBologna\nBolzano\nBrescia\nBrindisi\nCagliari\nCaltanissetta\nCampobasso\nCarbonia-Iglesias\nCaserta\nCatania\nCatanzaro\nChieti\nComo\nCosenza\nCremona\nCrotone\nCuneo\nEnna\nFermo\nFerrara\nFirenze\nFoggia\nForlì-Cesena\nFrosinone\nGenova\nGorizia\nGrosseto\nImperia\nIsernia\nLa Spezia\nL'Aquila\nLatina\nLecce\nLecco\nLivorno\nLodi\nLucca\nMacerata\nMantova\nMassa-Carrara\nMatera\nMedio Campidano\nMessina\nMilano\nModena\nMonza e della Brianza\nNapoli\nNovara\nNuoro\nOgliastra\nOlbia-Tempio\nOristano\nPadova\nPalermo\nParma\nPavia\nPerugia\nPesaro e Urbino\nPescara\nPiacenza\nPisa\nPistoia\nPordenone\nPotenza\nPrato\nRagusa\nRavenna\nReggio Calabria\nReggio Emilia\nRieti\nRimini\nRoma\nRovigo\nSalerno\nSassari\nSavona\nSiena\nSiracusa\nSondrio\nTaranto\nTeramo\nTerni\nTorino\nTrapani\nTrento\nTreviso\nTrieste\nUdine\nVarese\nVenezia\nVerbano-Cusio-Ossola\nVercelli\nVerona\nVibo Valentia\nVicenza\nViterbo",
            'is_required' => 1,
            'sort_order' => 3,
        ],
        [
            'product_id' => $visuraProductId,
            'field_name' => 'document_type',
            'field_label' => 'Tipo di Visura',
            'field_type' => 'select',
            'field_options' => "Visura Ordinaria\nVisura Storica\nCertificato di Iscrizione\nCertificato di Non Iscrizione",
            'is_required' => 1,
            'sort_order' => 4,
        ],
        [
            'field_name' => 'notes',
            'field_label' => 'Note aggiuntive (opzionale)',
            'field_type' => 'textarea',
            'field_options' => null,
            'is_required' => 0,
            'sort_order' => 5,
        ],
    ];

    $fieldInsert = $pdo->prepare('INSERT INTO product_custom_fields (product_id, field_name, field_label, field_type, field_options, is_required, sort_order) VALUES (:product_id, :field_name, :field_label, :field_type, :field_options, :is_required, :sort_order)');

    foreach ($customFields as $field) {
        $field['product_id'] = $visuraProductId;
        $fieldInsert->execute($field);
    }
        }
    }
}

function ap_fetch_product_custom_fields(int $productId): array
{
    $pdo = ap_db();
    $stmt = $pdo->prepare('SELECT * FROM product_custom_fields WHERE product_id = :product_id ORDER BY sort_order ASC');
    $stmt->execute([':product_id' => $productId]);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (empty($result)) {
        // Return default custom fields
        $result = [
            ['id' => 1, 'field_name' => 'Nome', 'field_label' => 'Nome', 'field_type' => 'text', 'field_options' => '', 'is_required' => 1, 'sort_order' => 0],
            ['id' => 2, 'field_name' => 'Cognome', 'field_label' => 'Cognome', 'field_type' => 'text', 'field_options' => '', 'is_required' => 1, 'sort_order' => 0],
            ['id' => 3, 'field_name' => 'Provincia', 'field_label' => 'Provincia', 'field_type' => 'provincia', 'field_options' => '', 'is_required' => 1, 'sort_order' => 0],
            ['id' => 4, 'field_name' => 'Comune', 'field_label' => 'Comune', 'field_type' => 'comune', 'field_options' => '', 'is_required' => 1, 'sort_order' => 0],
            ['id' => 5, 'field_name' => 'CAP', 'field_label' => 'CAP', 'field_type' => 'cap', 'field_options' => '', 'is_required' => 1, 'sort_order' => 0],
            ['id' => 6, 'field_name' => 'Città', 'field_label' => 'Città', 'field_type' => 'citta', 'field_options' => '', 'is_required' => 1, 'sort_order' => 0],
        ];
    }
    return $result;
}

function ap_save_product_custom_field(int $productId, array $field): void
{
    $pdo = ap_db();
    if (isset($field['id']) && $field['id'] > 0) {
        $stmt = $pdo->prepare('UPDATE product_custom_fields SET field_name = :name, field_label = :label, field_type = :type, field_options = :options, is_required = :required, sort_order = :sort WHERE id = :id AND product_id = :product_id');
        $stmt->execute([
            ':id' => $field['id'],
            ':product_id' => $productId,
            ':name' => $field['name'],
            ':label' => $field['label'],
            ':type' => $field['type'],
            ':options' => $field['options'] ?? null,
            ':required' => $field['required'] ? 1 : 0,
            ':sort' => $field['sort'] ?? 0,
        ]);
    } else {
        $stmt = $pdo->prepare('INSERT INTO product_custom_fields (product_id, field_name, field_label, field_type, field_options, is_required, sort_order) VALUES (:product_id, :name, :label, :type, :options, :required, :sort)');
        $stmt->execute([
            ':product_id' => $productId,
            ':name' => $field['name'],
            ':label' => $field['label'],
            ':type' => $field['type'],
            ':options' => $field['options'] ?? null,
            ':required' => $field['required'] ? 1 : 0,
            ':sort' => $field['sort'] ?? 0,
        ]);
    }
}

function ap_delete_product_custom_field(int $fieldId): void
{
    $pdo = ap_db();
    $stmt = $pdo->prepare('DELETE FROM product_custom_fields WHERE id = :id');
    $stmt->execute([':id' => $fieldId]);
}

function ap_save_order_custom_data(int $orderId, int $productId, array $data): void
{
    $pdo = ap_db();
    $pdo->beginTransaction();
    try {
        $stmt = $pdo->prepare('INSERT INTO order_custom_data (order_id, product_id, field_name, field_value) VALUES (:order_id, :product_id, :name, :value) ON DUPLICATE KEY UPDATE field_value = :value');
        foreach ($data as $name => $value) {
            $stmt->execute([
                ':order_id' => $orderId,
                ':product_id' => $productId,
                ':name' => $name,
                ':value' => $value,
            ]);
        }
        $pdo->commit();
    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }
}

function ap_fetch_order_custom_data(int $orderId, int $productId): array
{
    $pdo = ap_db();
    $stmt = $pdo->prepare('SELECT field_name, field_value FROM order_custom_data WHERE order_id = :order_id AND product_id = :product_id');
    $stmt->execute([
        ':order_id' => $orderId,
        ':product_id' => $productId,
    ]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $data = [];
    foreach ($results as $row) {
        $data[$row['field_name']] = $row['field_value'];
    }
    return $data;
}

function ap_seed_security_logs(PDO $pdo): void
{
    // Check if security logs already exist
    $stmt = $pdo->query('SELECT COUNT(*) AS total FROM security_logs');
    $count = (int) ($stmt->fetch()['total'] ?? 0);
    if ($count > 0) {
        return; // Already seeded
    }
    
    // Get admin user ID
    $adminStmt = $pdo->prepare('SELECT id FROM users WHERE role = :role LIMIT 1');
    $adminStmt->execute([':role' => 'admin']);
    $adminId = $adminStmt->fetchColumn();
    
    $logs = [
        [
            'event_type' => 'user_created',
            'description' => 'Creato account amministratore iniziale',
            'user_id' => $adminId,
            'metadata' => json_encode(['role' => 'admin', 'method' => 'bootstrap']),
            'ip_address' => '127.0.0.1',
            'user_agent' => 'System Bootstrap',
            'created_at' => date('Y-m-d H:i:s', strtotime('-7 days')),
        ],
        [
            'event_type' => 'login_success',
            'description' => 'Accesso amministratore riuscito',
            'user_id' => $adminId,
            'metadata' => json_encode(['login_method' => 'password']),
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36',
            'created_at' => date('Y-m-d H:i:s', strtotime('-6 days')),
        ],
        [
            'event_type' => 'admin_action',
            'description' => 'Configurazione iniziale prodotti completata',
            'user_id' => $adminId,
            'metadata' => json_encode(['action' => 'seed_products', 'count' => 8]),
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36',
            'created_at' => date('Y-m-d H:i:s', strtotime('-6 days')),
        ],
        [
            'event_type' => 'login_failed',
            'description' => 'Tentativo di accesso fallito - password errata',
            'user_id' => null,
            'metadata' => json_encode(['email' => 'admin@agenziaplinio.it', 'reason' => 'invalid_password']),
            'ip_address' => '192.168.1.100',
            'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
            'created_at' => date('Y-m-d H:i:s', strtotime('-3 days')),
        ],
        [
            'event_type' => 'security_alert',
            'description' => 'Rilevati 3 tentativi di accesso falliti consecutivi',
            'user_id' => null,
            'metadata' => json_encode(['alert_type' => 'brute_force_attempt', 'ip' => '192.168.1.100', 'attempts' => 3]),
            'ip_address' => '192.168.1.100',
            'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
            'created_at' => date('Y-m-d H:i:s', strtotime('-3 days')),
        ],
        [
            'event_type' => 'password_change',
            'description' => 'Password amministratore cambiata',
            'user_id' => $adminId,
            'metadata' => json_encode(['method' => 'admin_panel']),
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36',
            'created_at' => date('Y-m-d H:i:s', strtotime('-2 days')),
        ],
        [
            'event_type' => 'admin_action',
            'description' => 'Impostazioni sito configurate',
            'user_id' => $adminId,
            'metadata' => json_encode(['action' => 'settings_update', 'settings' => ['site_name', 'contact_email', 'currency']]),
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36',
            'created_at' => date('Y-m-d H:i:s', strtotime('-1 day')),
        ],
        [
            'event_type' => 'logout',
            'description' => 'Disconnessione amministratore',
            'user_id' => $adminId,
            'metadata' => json_encode(['session_duration' => '2h 15m']),
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36',
            'created_at' => date('Y-m-d H:i:s', strtotime('-30 minutes')),
        ],
    ];
    
    $insert = $pdo->prepare('INSERT INTO security_logs (event_type, description, user_id, metadata, ip_address, user_agent, created_at) VALUES (:event_type, :description, :user_id, :metadata, :ip_address, :user_agent, :created_at)');
    
    foreach ($logs as $log) {
        $insert->execute($log);
    }
}

function ap_seed_audit_logs(PDO $pdo): void
{
    // Check if audit logs already exist
    $stmt = $pdo->query('SELECT COUNT(*) AS total FROM audit_logs');
    $count = (int) ($stmt->fetch()['total'] ?? 0);
    if ($count > 0) {
        return; // Already seeded
    }

    // Get admin user ID
    $adminStmt = $pdo->prepare('SELECT id FROM users WHERE role = :role LIMIT 1');
    $adminStmt->execute([':role' => 'admin']);
    $adminId = $adminStmt->fetchColumn();

    $logs = [
        [
            'event_type' => 'php_error',
            'description' => 'Errore PHP: Undefined variable in includes/router.php line 45',
            'user_id' => null,
            'metadata' => json_encode([
                'error_type' => 'E_NOTICE',
                'file' => 'includes/router.php',
                'line' => 45,
                'message' => 'Undefined variable: $page',
                'trace' => 'includes/router.php:45 -> index.php:12'
            ]),
            'ip_address' => '127.0.0.1',
            'user_agent' => 'System',
            'created_at' => date('Y-m-d H:i:s', strtotime('-5 days')),
        ],
        [
            'event_type' => 'admin_action',
            'description' => 'Aggiunto nuovo prodotto al catalogo',
            'user_id' => $adminId,
            'metadata' => json_encode([
                'action' => 'product_created',
                'product_id' => 1,
                'product_name' => 'Visura Camerale'
            ]),
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36',
            'created_at' => date('Y-m-d H:i:s', strtotime('-4 days')),
        ],
        [
            'event_type' => 'site_error',
            'description' => 'Errore 404: Pagina non trovata /admin/nonexistent',
            'user_id' => null,
            'metadata' => json_encode([
                'error_code' => 404,
                'requested_url' => '/admin/nonexistent',
                'referrer' => 'https://agenziaplinio.it/admin'
            ]),
            'ip_address' => '192.168.1.50',
            'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
            'created_at' => date('Y-m-d H:i:s', strtotime('-3 days')),
        ],
        [
            'event_type' => 'database_error',
            'description' => 'Errore database: Duplicate entry in orders table',
            'user_id' => null,
            'metadata' => json_encode([
                'error_code' => '23000',
                'sql_state' => '1062',
                'query' => 'INSERT INTO orders ...',
                'table' => 'orders'
            ]),
            'ip_address' => '127.0.0.1',
            'user_agent' => 'System',
            'created_at' => date('Y-m-d H:i:s', strtotime('-2 days')),
        ],
        [
            'event_type' => 'admin_action',
            'description' => 'Aggiornate impostazioni sito',
            'user_id' => $adminId,
            'metadata' => json_encode([
                'action' => 'settings_updated',
                'changed_fields' => ['site_name', 'contact_email', 'maintenance_mode']
            ]),
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36',
            'created_at' => date('Y-m-d H:i:s', strtotime('-2 days')),
        ],
        [
            'event_type' => 'php_warning',
            'description' => 'Avviso PHP: Deprecated function mysql_connect used',
            'user_id' => null,
            'metadata' => json_encode([
                'error_type' => 'E_DEPRECATED',
                'file' => 'includes/legacy.php',
                'line' => 23,
                'message' => 'mysql_connect(): This function was deprecated in PHP 5.5.0'
            ]),
            'ip_address' => '127.0.0.1',
            'user_agent' => 'System',
            'created_at' => date('Y-m-d H:i:s', strtotime('-1 day')),
        ],
        [
            'event_type' => 'user_action',
            'description' => 'Ordine completato con successo',
            'user_id' => null, // Assuming guest order
            'metadata' => json_encode([
                'action' => 'order_completed',
                'order_id' => 'ORD-001',
                'total_amount' => 15000, // cents
                'products' => ['Visura Camerale']
            ]),
            'ip_address' => '10.0.0.100',
            'user_agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 14_6 like Mac OS X) AppleWebKit/605.1.15',
            'created_at' => date('Y-m-d H:i:s', strtotime('-12 hours')),
        ],
        [
            'event_type' => 'system_info',
            'description' => 'Cache svuotata automaticamente',
            'user_id' => null,
            'metadata' => json_encode([
                'action' => 'cache_cleared',
                'reason' => 'scheduled_cleanup',
                'files_removed' => 15
            ]),
            'ip_address' => '127.0.0.1',
            'user_agent' => 'System Cron',
            'created_at' => date('Y-m-d H:i:s', strtotime('-6 hours')),
        ],
    ];

    $insert = $pdo->prepare('INSERT INTO audit_logs (event_type, description, user_id, metadata, ip_address, user_agent, created_at) VALUES (:event_type, :description, :user_id, :metadata, :ip_address, :user_agent, :created_at)');

    foreach ($logs as $log) {
        $insert->execute($log);
    }
}
