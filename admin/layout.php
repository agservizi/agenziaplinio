<?php
$siteName = $site['name'] ?? 'Agenzia Plinio';
$userName = $currentUser['name'] ?? ($currentUser['email'] ?? 'Admin');
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($meta['title'] ?? ($siteName . ' | Admin'), ENT_QUOTES); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($meta['description'] ?? '', ENT_QUOTES); ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="../assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/admin.css" rel="stylesheet">
</head>
<body class="admin-shell">
    <header class="admin-header shadow-sm">
        <div class="admin-header__brand">
            <span class="admin-logo">AP</span>
            <div>
                <strong><?php echo htmlspecialchars($siteName, ENT_QUOTES); ?></strong>
                <small>Control Room</small>
            </div>
        </div>
        <div class="admin-header__actions">
            <a class="btn btn-outline-light btn-sm" href="/" target="_blank" rel="noopener">Apri sito</a>
            <div class="admin-user-chip">
                <span class="admin-user-chip__avatar"><?php echo strtoupper(substr($userName, 0, 1)); ?></span>
                <div>
                    <strong><?php echo htmlspecialchars($userName, ENT_QUOTES); ?></strong>
                    <small>Administrator</small>
                </div>
            </div>
        </div>
    </header>
    <div class="admin-layout">
        <aside class="admin-sidebar">
            <nav>
                <ul class="admin-sidebar__menu">
                    <li><a href="?page=admin">Dashboard</a></li>
                    <li><a href="?page=admin&section=annunci">News topbar</a></li>
                    <li><a href="?page=admin&section=faq-bot">FAQ Bot</a></li>
                    <li><a href="?page=admin&section=catalogo">Catalogo</a></li>
                    <li><a href="?page=admin&section=ordini">Ordini</a></li>
                    <li><a href="?page=admin&section=utenti">Utenti</a></li>
                    <li><a href="?page=admin&section=coupons">Coupons</a></li>
                    <li><a href="?page=admin&section=impostazioni">Impostazioni</a></li>
                    <li><a href="?page=admin&section=statistiche">Statistiche</a></li>
                    <li><a href="?page=admin&section=sicurezza">Sicurezza</a></li>
                    <li><a href="?page=admin&section=audit">Audit Log</a></li>
                </ul>
            </nav>
        </aside>
        <main class="admin-main" id="top">
            <?php echo $pageContent; ?>
        </main>
    </div>
    <?php if (!empty($flashMessages)): ?>
        <div class="admin-toast-stack">
            <?php foreach ($flashMessages as $toast): ?>
                <div class="admin-toast">
                    <strong><?php echo htmlspecialchars($toast['title'] ?? 'Completato', ENT_QUOTES); ?></strong>
                    <p><?php echo htmlspecialchars($toast['message'] ?? '', ENT_QUOTES); ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    <script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script type="module" src="../assets/js/admin.js"></script>
</body>
</html>
