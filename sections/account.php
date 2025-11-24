<?php
$user = ap_auth_current_user();
$currentUrl = '?page=account';
?>
<section class="account-section section-padding">
    <div class="container-xxl">
        <div class="section-heading text-center mb-5">
            <p class="eyebrow mb-2">Area clienti</p>
            <h2 class="mb-1"><?php echo $user ? 'Ciao ' . htmlspecialchars($user['name'], ENT_QUOTES) : 'Accedi o crea un account'; ?></h2>
            <p class="text-muted">Gestisci ordini, attivazioni e dati personali in modo sicuro.</p>
        </div>
        <?php if (!$user): ?>
            <div class="row g-5">
                <div class="col-md-6">
                    <div class="auth-card">
                        <h4>Login</h4>
                        <form method="post" class="mt-3">
                            <input type="hidden" name="ap_action" value="login">
                            <input type="hidden" name="redirect_to" value="<?php echo $currentUrl; ?>">
                            <div class="mb-3">
                                <label class="form-label" for="login_email">Email</label>
                                <input class="form-control" type="email" id="login_email" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="login_password">Password</label>
                                <input class="form-control" type="password" id="login_password" name="password" required>
                            </div>
                            <button class="ap-btn ap-btn--primary w-100" type="submit">Entra</button>
                        </form>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="auth-card">
                        <h4>Registrazione</h4>
                        <form method="post" class="mt-3">
                            <input type="hidden" name="ap_action" value="register">
                            <input type="hidden" name="redirect_to" value="<?php echo $currentUrl; ?>">
                            <div class="mb-3">
                                <label class="form-label" for="reg_name">Nome e cognome</label>
                                <input class="form-control" type="text" id="reg_name" name="name" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="reg_email">Email</label>
                                <input class="form-control" type="email" id="reg_email" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="reg_password">Password</label>
                                <input class="form-control" type="password" id="reg_password" name="password" minlength="8" required>
                                <small class="text-muted">Minimo 8 caratteri.</small>
                            </div>
                            <button class="ap-btn ap-btn--secondary w-100" type="submit">Crea account</button>
                        </form>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <?php
            $orders = ap_fetch_orders((int) $user['id']);
            $orderStatuses = ['pending' => 'In attesa', 'processing' => 'In lavorazione', 'completed' => 'Completato', 'cancelled' => 'Annullato'];
            $shippingStatuses = ['preparing' => 'In preparazione', 'in_transit' => 'In transito', 'delivered' => 'Consegnato', 'issue' => 'In verifica'];
            $addresses = ap_fetch_addresses((int) $user['id']);
            $defaultAddressId = null;
            foreach ($addresses as $address) {
                if ((int) $address['is_default'] === 1) {
                    $defaultAddressId = (int) $address['id'];
                    break;
                }
            }

            // Check for payment success and pending custom data
            $pendingCustomData = [];
            if (isset($_GET['payment']) && $_GET['payment'] === 'success') {
                // Find recent orders that might need custom data
                $recentOrders = ap_fetch_orders((int) $user['id'], ['limit' => 5, 'status' => ['processing', 'pending']]);
                foreach ($recentOrders as $order) {
                    $orderItems = ap_fetch_order_items((int) $order['id']);
                    foreach ($orderItems as $item) {
                        $product = ap_find_product((int) $item['product_id']);
                        if ($product && $product['fulfillment_type'] === 'digital') {
                            $customFields = ap_fetch_product_custom_fields((int) $product['id']);
                            if (!empty($customFields)) {
                                $existingData = ap_fetch_order_custom_data((int) $order['id'], (int) $product['id']);
                                $hasAllData = true;
                                foreach ($customFields as $field) {
                                    $fieldName = $field['field_name'];
                                    if (!isset($existingData[$fieldName]) || trim($existingData[$fieldName]) === '') {
                                        $hasAllData = false;
                                        break;
                                    }
                                }
                                if (!$hasAllData) {
                                    $pendingCustomData[] = [
                                        'order' => $order,
                                        'product' => $product,
                                        'custom_fields' => $customFields,
                                        'existing_data' => $existingData,
                                    ];
                                }
                            }
                        }
                    }
                }
            }
            
            // Italian provinces and comuni data for custom fields
            $italianProvinces = [
                'Agrigento', 'Alessandria', 'Ancona', 'Aosta', 'Arezzo', 'Ascoli Piceno', 'Asti', 'Avellino', 'Bari', 'Barletta-Andria-Trani', 'Belluno', 'Benevento', 'Bergamo', 'Biella', 'Bologna', 'Bolzano', 'Brescia', 'Brindisi', 'Cagliari', 'Caltanissetta', 'Campobasso', 'Carbonia-Iglesias', 'Caserta', 'Catania', 'Catanzaro', 'Chieti', 'Como', 'Cosenza', 'Cremona', 'Crotone', 'Cuneo', 'Enna', 'Fermo', 'Ferrara', 'Firenze', 'Foggia', 'Forlì-Cesena', 'Frosinone', 'Genova', 'Gorizia', 'Grosseto', 'Imperia', 'Isernia', 'La Spezia', 'L\'Aquila', 'Latina', 'Lecce', 'Lecco', 'Livorno', 'Lodi', 'Lucca', 'Macerata', 'Mantova', 'Massa-Carrara', 'Matera', 'Medio Campidano', 'Messina', 'Milano', 'Modena', 'Monza e della Brianza', 'Napoli', 'Novara', 'Nuoro', 'Ogliastra', 'Olbia-Tempio', 'Oristano', 'Padova', 'Palermo', 'Parma', 'Pavia', 'Perugia', 'Pesaro e Urbino', 'Pescara', 'Piacenza', 'Pisa', 'Pistoia', 'Pordenone', 'Potenza', 'Prato', 'Ragusa', 'Ravenna', 'Reggio Calabria', 'Reggio Emilia', 'Rieti', 'Rimini', 'Roma', 'Rovigo', 'Salerno', 'Sassari', 'Savona', 'Siena', 'Siracusa', 'Sondrio', 'Taranto', 'Teramo', 'Terni', 'Torino', 'Trapani', 'Trento', 'Treviso', 'Trieste', 'Udine', 'Varese', 'Venezia', 'Verbano-Cusio-Ossola', 'Vercelli', 'Verona', 'Vibo Valentia', 'Vicenza', 'Viterbo'
            ];
            $comuniData = json_decode(file_get_contents(__DIR__ . '/../comuni.json'), true);
            $italianComuni = array_column($comuniData, 'nome');
            sort($italianComuni);
            ?>
            <?php if (!empty($pendingCustomData)): ?>
                <div class="alert alert-info">
                    <h5 class="alert-heading">Completa i tuoi ordini digitali</h5>
                    <p class="mb-3">Per procedere con l'elaborazione dei tuoi prodotti digitali, compila i dati richiesti di seguito.</p>
                    <?php foreach ($pendingCustomData as $pending): ?>
                        <div class="border rounded-3 p-4 mb-4 bg-light">
                            <h6>Ordine #<?php echo (int) $pending['order']['id']; ?> - <?php echo htmlspecialchars($pending['product']['name'], ENT_QUOTES); ?></h6>
                            <form method="post" class="mt-3">
                                <input type="hidden" name="ap_action" value="save_order_custom_data">
                                <input type="hidden" name="order_id" value="<?php echo (int) $pending['order']['id']; ?>">
                                <input type="hidden" name="product_id" value="<?php echo (int) $pending['product']['id']; ?>">
                                <input type="hidden" name="redirect_to" value="<?php echo $currentUrl; ?>">
                                <div class="row g-3">
                                    <?php foreach ($pending['custom_fields'] as $field): ?>
                                        <div class="col-md-6">
                                            <label class="form-label" for="field_<?php echo htmlspecialchars($field['field_name'], ENT_QUOTES); ?>">
                                                <?php echo htmlspecialchars($field['field_label'], ENT_QUOTES); ?>
                                                <?php if ((int) $field['is_required'] === 1): ?><span class="text-danger">*</span><?php endif; ?>
                                            </label>
                                            <?php
                                            $fieldName = $field['field_name'];
                                            $existingValue = $pending['existing_data'][$fieldName] ?? '';
                                            $fieldType = $field['field_type'];
                                            $options = trim($field['field_options']);
                                            ?>
                                            <?php if ($fieldType === 'select' && $options !== ''): ?>
                                                <?php
                                                $allOptions = explode("\n", $options);
                                                if ($fieldName === 'province') {
                                                    // Aggiungi città capoluogo per facilitare la ricerca
                                                    $cities = [
                                                        'Agrigento', 'Alessandria', 'Ancona', 'Aosta', 'Arezzo', 'Ascoli Piceno', 'Asti', 'Avellino', 'Bari', 'Barletta', 'Belluno', 'Benevento', 'Bergamo', 'Biella', 'Bologna', 'Bolzano', 'Brescia', 'Brindisi', 'Cagliari', 'Caltanissetta', 'Campobasso', 'Carbonia', 'Caserta', 'Catania', 'Catanzaro', 'Chieti', 'Como', 'Cosenza', 'Cremona', 'Crotone', 'Cuneo', 'Enna', 'Fermo', 'Ferrara', 'Firenze', 'Foggia', 'Forlì', 'Frosinone', 'Genova', 'Gorizia', 'Grosseto', 'Imperia', 'Isernia', 'La Spezia', 'L\'Aquila', 'Latina', 'Lecce', 'Lecco', 'Livorno', 'Lodi', 'Lucca', 'Macerata', 'Mantova', 'Massa', 'Matera', 'Villacidro', 'Messina', 'Milano', 'Modena', 'Monza', 'Napoli', 'Novara', 'Nuoro', 'Lanusei', 'Olbia', 'Oristano', 'Padova', 'Palermo', 'Parma', 'Pavia', 'Perugia', 'Pesaro', 'Pescara', 'Piacenza', 'Pisa', 'Pistoia', 'Pordenone', 'Potenza', 'Prato', 'Ragusa', 'Ravenna', 'Reggio Calabria', 'Reggio Emilia', 'Rieti', 'Rimini', 'Roma', 'Rovigo', 'Salerno', 'Sassari', 'Savona', 'Siena', 'Siracusa', 'Sondrio', 'Taranto', 'Teramo', 'Terni', 'Torino', 'Trapani', 'Trento', 'Treviso', 'Trieste', 'Udine', 'Varese', 'Venezia', 'Verbania', 'Vercelli', 'Verona', 'Vibo Valentia', 'Vicenza', 'Viterbo'
                                                    ];
                                                    $allOptions = array_unique(array_merge($allOptions, $cities));
                                                    sort($allOptions);
                                                }
                                                ?>
                                                <input class="form-control" type="text" list="datalist_<?php echo htmlspecialchars($fieldName, ENT_QUOTES); ?>" id="field_<?php echo htmlspecialchars($fieldName, ENT_QUOTES); ?>" name="<?php echo htmlspecialchars($fieldName, ENT_QUOTES); ?>" value="<?php echo htmlspecialchars($existingValue, ENT_QUOTES); ?>" <?php echo ((int) $field['is_required'] === 1) ? 'required' : ''; ?>>
                                                <datalist id="datalist_<?php echo htmlspecialchars($fieldName, ENT_QUOTES); ?>">
                                                    <?php foreach ($allOptions as $option): ?>
                                                        <option value="<?php echo htmlspecialchars(trim($option), ENT_QUOTES); ?>">
                                                    <?php endforeach; ?>
                                                </datalist>
                                            <?php elseif ($fieldType === 'textarea'): ?>
                                                <textarea class="form-control" id="field_<?php echo htmlspecialchars($fieldName, ENT_QUOTES); ?>" name="<?php echo htmlspecialchars($fieldName, ENT_QUOTES); ?>" rows="3" <?php echo ((int) $field['is_required'] === 1) ? 'required' : ''; ?>><?php echo htmlspecialchars($existingValue, ENT_QUOTES); ?></textarea>
                                            <?php elseif ($fieldType === 'checkbox'): ?>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="field_<?php echo htmlspecialchars($fieldName, ENT_QUOTES); ?>" name="<?php echo htmlspecialchars($fieldName, ENT_QUOTES); ?>" value="1" <?php echo ($existingValue === '1') ? 'checked' : ''; ?>>
                                                    <label class="form-check-label" for="field_<?php echo htmlspecialchars($fieldName, ENT_QUOTES); ?>"><?php echo htmlspecialchars($field['field_label'], ENT_QUOTES); ?></label>
                                                </div>
                                            <?php elseif ($fieldType === 'provincia'): ?>
                                                <select class="form-select" id="field_<?php echo htmlspecialchars($fieldName, ENT_QUOTES); ?>" name="<?php echo htmlspecialchars($fieldName, ENT_QUOTES); ?>" <?php echo ((int) $field['is_required'] === 1) ? 'required' : ''; ?>>
                                                    <option value="">Seleziona provincia</option>
                                                    <?php foreach ($italianProvinces as $prov): ?>
                                                        <option value="<?php echo htmlspecialchars($prov, ENT_QUOTES); ?>" <?php echo ($existingValue === $prov) ? 'selected' : ''; ?>><?php echo htmlspecialchars($prov, ENT_QUOTES); ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            <?php elseif ($fieldType === 'comune'): ?>
                                                <input class="form-control" type="text" list="datalist_comune_<?php echo htmlspecialchars($fieldName, ENT_QUOTES); ?>" id="field_<?php echo htmlspecialchars($fieldName, ENT_QUOTES); ?>" name="<?php echo htmlspecialchars($fieldName, ENT_QUOTES); ?>" value="<?php echo htmlspecialchars($existingValue, ENT_QUOTES); ?>" placeholder="Inserisci comune" <?php echo ((int) $field['is_required'] === 1) ? 'required' : ''; ?>>
                                                <datalist id="datalist_comune_<?php echo htmlspecialchars($fieldName, ENT_QUOTES); ?>">
                                                    <?php foreach ($italianComuni as $comune): ?>
                                                        <option value="<?php echo htmlspecialchars($comune, ENT_QUOTES); ?>">
                                                    <?php endforeach; ?>
                                                </datalist>
                                            <?php elseif ($fieldType === 'cap' || $fieldType === 'citta'): ?>
                                                <input class="form-control" type="text" id="field_<?php echo htmlspecialchars($fieldName, ENT_QUOTES); ?>" name="<?php echo htmlspecialchars($fieldName, ENT_QUOTES); ?>" value="<?php echo htmlspecialchars($existingValue, ENT_QUOTES); ?>" placeholder="Inserisci <?php echo $fieldType; ?>" <?php echo ((int) $field['is_required'] === 1) ? 'required' : ''; ?>>
                                            <?php else: ?>
                                                <input class="form-control" type="<?php echo ($fieldType === 'email') ? 'email' : (($fieldType === 'number') ? 'number' : 'text'); ?>" id="field_<?php echo htmlspecialchars($fieldName, ENT_QUOTES); ?>" name="<?php echo htmlspecialchars($fieldName, ENT_QUOTES); ?>" value="<?php echo htmlspecialchars($existingValue, ENT_QUOTES); ?>" <?php echo ((int) $field['is_required'] === 1) ? 'required' : ''; ?>>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <button class="ap-btn ap-btn--primary mt-3" type="submit">Salva dati</button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            <div class="row g-4 align-items-start">
                <div class="col-lg-4">
                    <div class="profile-card">
                        <h4>Profilo</h4>
                        <form method="post" class="profile-form mt-3">
                            <input type="hidden" name="ap_action" value="update_profile">
                            <input type="hidden" name="redirect_to" value="<?php echo $currentUrl; ?>">
                            <div class="mb-3">
                                <label class="form-label" for="profile_name">Nome completo</label>
                                <input class="form-control" type="text" id="profile_name" name="name" value="<?php echo htmlspecialchars($user['name'], ENT_QUOTES); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="profile_email">Email</label>
                                <input class="form-control" type="email" id="profile_email" name="email" value="<?php echo htmlspecialchars($user['email'], ENT_QUOTES); ?>" required>
                                <small class="text-muted">Usata per accessi e notifiche.</small>
                            </div>
                            <button class="ap-btn ap-btn--secondary w-100" type="submit">Salva modifiche</button>
                        </form>
                        <hr class="my-4">
                        <form method="post">
                            <input type="hidden" name="ap_action" value="logout">
                            <input type="hidden" name="redirect_to" value="?page=home">
                            <button class="ap-btn ap-btn--ghost w-100" type="submit">Esci</button>
                        </form>
                        <?php if (ap_auth_is_admin()): ?>
                            <a class="ap-btn ap-btn--primary w-100 mt-3" href="?page=admin">Apri area admin</a>
                        <?php endif; ?>
                    </div>
                    <div class="profile-card mt-4">
                        <h4 class="mb-3">Aggiorna password</h4>
                        <form method="post" class="password-form">
                            <input type="hidden" name="ap_action" value="change_password">
                            <input type="hidden" name="redirect_to" value="<?php echo $currentUrl; ?>">
                            <div class="mb-3">
                                <label class="form-label" for="current_password">Password attuale</label>
                                <input class="form-control" type="password" id="current_password" name="current_password" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="new_password">Nuova password</label>
                                <input class="form-control" type="password" id="new_password" name="new_password" minlength="8" required>
                                <small class="text-muted">Minimo 8 caratteri, evita password già utilizzate.</small>
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="confirm_password">Conferma nuova password</label>
                                <input class="form-control" type="password" id="confirm_password" name="confirm_password" minlength="8" required>
                            </div>
                            <button class="ap-btn ap-btn--secondary w-100" type="submit">Salva nuova password</button>
                        </form>
                    </div>
                    <div class="profile-card mt-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="mb-0">Rubrica spedizioni</h4>
                            <span class="badge bg-light text-dark"><?php echo count($addresses); ?></span>
                        </div>
                        <?php if (empty($addresses)): ?>
                            <p class="text-muted small mb-3">Salva un indirizzo per compilare il checkout in un click.</p>
                        <?php else: ?>
                            <?php foreach ($addresses as $address): ?>
                                <div class="border rounded-3 p-3 mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <div>
                                            <p class="mb-0 fw-semibold"><?php echo htmlspecialchars($address['label'], ENT_QUOTES); ?></p>
                                            <small class="text-muted"><?php echo htmlspecialchars($address['city'] . ' • ' . ($address['country'] ?? 'Italia'), ENT_QUOTES); ?></small>
                                        </div>
                                        <?php if ((int) $address['is_default'] === 1): ?>
                                            <span class="badge bg-success-subtle text-success">Predefinito</span>
                                        <?php else: ?>
                                            <form method="post" class="d-inline">
                                                <input type="hidden" name="ap_action" value="set_default_address">
                                                <input type="hidden" name="address_id" value="<?php echo (int) $address['id']; ?>">
                                                <input type="hidden" name="redirect_to" value="<?php echo $currentUrl; ?>">
                                                <button class="btn btn-sm btn-outline-primary" type="submit">Imposta</button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                    <p class="small mb-2"><?php echo nl2br(htmlspecialchars(ap_format_address($address), ENT_QUOTES)); ?></p>
                                    <?php if (!empty($address['phone'])): ?>
                                        <p class="small text-muted mb-2">Tel: <?php echo htmlspecialchars($address['phone'], ENT_QUOTES); ?></p>
                                    <?php endif; ?>
                                    <form method="post" onsubmit="return confirm('Rimuovere questo indirizzo?');">
                                        <input type="hidden" name="ap_action" value="delete_address">
                                        <input type="hidden" name="address_id" value="<?php echo (int) $address['id']; ?>">
                                        <input type="hidden" name="redirect_to" value="<?php echo $currentUrl; ?>">
                                        <button class="btn btn-link btn-sm text-danger px-0" type="submit">Elimina</button>
                                    </form>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        <hr>
                        <form method="post" class="address-form">
                            <input type="hidden" name="ap_action" value="save_address">
                            <input type="hidden" name="redirect_to" value="<?php echo $currentUrl; ?>">
                            <div class="mb-2">
                                <label class="form-label" for="addr_label">Etichetta</label>
                                <input class="form-control" type="text" id="addr_label" name="label" placeholder="Es. HQ Roma" required>
                            </div>
                            <div class="mb-2">
                                <label class="form-label" for="addr_recipient">Destinatario</label>
                                <input class="form-control" type="text" id="addr_recipient" name="recipient_name" value="<?php echo htmlspecialchars($user['name'], ENT_QUOTES); ?>" required>
                            </div>
                            <div class="mb-2">
                                <label class="form-label" for="addr_street">Via e civico</label>
                                <textarea class="form-control" id="addr_street" name="street" rows="2" required></textarea>
                            </div>
                            <div class="row g-2">
                                <div class="col-6">
                                    <label class="form-label" for="addr_city">Città</label>
                                    <input class="form-control" type="text" id="addr_city" name="city" required>
                                </div>
                                <div class="col-3">
                                    <label class="form-label" for="addr_postal">CAP</label>
                                    <input class="form-control" type="text" id="addr_postal" name="postal_code" required>
                                </div>
                                <div class="col-3">
                                    <label class="form-label" for="addr_province">Prov.</label>
                                    <input class="form-control" type="text" id="addr_province" name="province">
                                </div>
                            </div>
                            <div class="row g-2 mt-2">
                                <div class="col-6">
                                    <label class="form-label" for="addr_country">Paese</label>
                                    <input class="form-control" type="text" id="addr_country" name="country" value="Italia" required>
                                </div>
                                <div class="col-6">
                                    <label class="form-label" for="addr_phone">Telefono</label>
                                    <input class="form-control" type="tel" id="addr_phone" name="phone" placeholder="+39">
                                </div>
                            </div>
                            <div class="form-check mt-3">
                                <input class="form-check-input" type="checkbox" id="addr_default" name="is_default" <?php echo empty($addresses) ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="addr_default">Imposta come indirizzo predefinito</label>
                            </div>
                            <button class="ap-btn ap-btn--secondary w-100 mt-3" type="submit">Salva indirizzo</button>
                        </form>
                    </div>
                </div>
                <div class="col-lg-8">
                    <div class="orders-card">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="mb-0">I tuoi ordini</h4>
                            <span class="badge bg-light text-dark">Totale <?php echo count($orders); ?></span>
                        </div>
                        <?php if (empty($orders)): ?>
                            <p class="text-muted mb-0">Non hai ancora effettuato ordini.</p>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table align-middle">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Data</th>
                                            <th>Ordine</th>
                                            <th>Pagamento</th>
                                            <th>Spedizione</th>
                                            <th>Totale</th>
                                            <th>Azioni</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($orders as $order): ?>
                                            <?php $orderDate = !empty($order['created_at']) ? date('d/m/Y', strtotime($order['created_at'])) : '-'; ?>
                                            <?php $orderItems = ap_fetch_order_items((int) $order['id']); ?>
                                            <?php
                                                $downloads = [];
                                                foreach ($orderItems as $item) {
                                                    $product = ap_find_product((int) $item['product_id']);
                                                    if ($product && $product['fulfillment_type'] === 'digital' && !empty($item['digital_file_path'])) {
                                                        $downloads[] = [
                                                            'product_name' => $product['name'],
                                                            'download_url' => "?page=download&order_id={$order['id']}&product_id={$item['product_id']}"
                                                        ];
                                                    }
                                                }
                                            ?>
                                            <?php
                                                $addons = ap_order_meta_array($order['addons'] ?? null);
                                                $opsChannels = ap_order_meta_array($order['ops_channels'] ?? null);
                                                $attachments = ap_order_procurement_files($order['procurement_files'] ?? null);
                                                $windowLabels = [
                                                    'standard' => '08:00-18:00',
                                                    'early' => '06:00-09:00',
                                                    'late' => '18:00-22:00',
                                                ];
                                                $slaBadges = [
                                                    'core' => 'Assistenza standard 24h',
                                                    'priority' => 'Gestione prioritaria 12h',
                                                    'mission' => 'Supporto critico 4h',
                                                ];
                                            ?>
                                            <tr>
                                                <td>#<?php echo (int) $order['id']; ?></td>
                                                <td><?php echo $orderDate; ?></td>
                                                <td><span class="status-dot status-<?php echo htmlspecialchars($order['status'], ENT_QUOTES); ?>"><?php echo $orderStatuses[$order['status']] ?? ucfirst($order['status']); ?></span></td>
                                                <td>
                                                    <span class="status-dot status-<?php echo htmlspecialchars($order['payment_status'] ?? 'pending', ENT_QUOTES); ?>"><?php echo ucfirst($order['payment_status'] ?? 'pending'); ?></span>
                                                    <div class="small text-muted"><?php echo strtoupper($order['payment_method'] ?? '-'); ?></div>
                                                </td>
                                                <td>
                                                    <span class="status-dot status-<?php echo htmlspecialchars($order['shipping_status'] ?? 'preparing', ENT_QUOTES); ?>"><?php echo $shippingStatuses[$order['shipping_status'] ?? 'preparing'] ?? 'In preparazione'; ?></span>
                                                    <?php if (!empty($order['tracking_code'])): ?>
                                                        <div class="small text-muted">Tracking: <?php echo htmlspecialchars($order['tracking_code'], ENT_QUOTES); ?></div>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?php echo ap_price_format((int) $order['total_cents']); ?></td>
                                                <td>
                                                    <form method="post" class="d-inline">
                                                        <input type="hidden" name="ap_action" value="repeat_order">
                                                        <input type="hidden" name="order_id" value="<?php echo (int) $order['id']; ?>">
                                                        <input type="hidden" name="redirect_to" value="<?php echo $currentUrl; ?>">
                                                        <button class="btn btn-sm btn-outline-primary" type="submit">Riordina</button>
                                                    </form>
                                                    <details class="mt-2">
                                                        <summary class="small fw-semibold">Dettagli</summary>
                                                        <div class="small text-muted mt-2">
                                                            <?php echo nl2br(htmlspecialchars($order['shipping_address'], ENT_QUOTES)); ?><br>
                                                            <?php if (!empty($order['contact_phone'])): ?>
                                                                Tel: <?php echo htmlspecialchars($order['contact_phone'], ENT_QUOTES); ?><br>
                                                            <?php endif; ?>
                                                            Metodo: <?php echo htmlspecialchars($order['shipping_method'], ENT_QUOTES); ?>
                                                        </div>
                                                        <?php if (!empty($order['po_number']) || !empty($order['cost_center']) || !empty($order['sla_plan']) || !empty($order['delivery_window'])): ?>
                                                            <div class="small mt-2">
                                                                <?php if (!empty($order['po_number'])): ?>PO: <?php echo htmlspecialchars($order['po_number'], ENT_QUOTES); ?><br><?php endif; ?>
                                                                <?php if (!empty($order['cost_center'])): ?>Centro di costo: <?php echo htmlspecialchars($order['cost_center'], ENT_QUOTES); ?><br><?php endif; ?>
                                                                <?php if (!empty($order['sla_plan'])): ?>Livello di servizio: <?php echo htmlspecialchars($slaBadges[$order['sla_plan']] ?? strtoupper($order['sla_plan']), ENT_QUOTES); ?><br><?php endif; ?>
                                                                <?php if (!empty($order['delivery_window'])): ?>Finestra: <?php echo htmlspecialchars($windowLabels[$order['delivery_window']] ?? $order['delivery_window'], ENT_QUOTES); ?><br><?php endif; ?>
                                                            </div>
                                                        <?php endif; ?>
                                                        <?php if (!empty($addons)): ?>
                                                            <div class="small text-muted">Add-on: <?php echo htmlspecialchars(implode(', ', array_map(static function ($value) {
                                                                return ucwords(str_replace('-', ' ', (string) $value));
                                                            }, $addons)), ENT_QUOTES); ?></div>
                                                        <?php endif; ?>
                                                        <?php if (!empty($opsChannels)): ?>
                                                            <div class="small text-muted">Canali operativi: <?php echo htmlspecialchars(implode(', ', array_map(static function ($value) {
                                                                return ucfirst((string) $value);
                                                            }, $opsChannels)), ENT_QUOTES); ?></div>
                                                        <?php endif; ?>
                                                        <?php if (!empty($order['contract_ref'])): ?>
                                                            <div class="small">Rif. contratto: <?php echo htmlspecialchars($order['contract_ref'], ENT_QUOTES); ?></div>
                                                        <?php endif; ?>
                                                        <?php if (!empty($attachments)): ?>
                                                            <div class="small mt-2">Documenti procurement:
                                                                <ul class="ps-3 mb-0">
                                                                    <?php foreach ($attachments as $file): ?>
                                                                        <li><a href="<?php echo htmlspecialchars($file['path'], ENT_QUOTES); ?>" target="_blank" rel="noopener"><?php echo htmlspecialchars($file['name'] ?? basename($file['path']), ENT_QUOTES); ?></a></li>
                                                                    <?php endforeach; ?>
                                                                </ul>
                                                            </div>
                                                        <?php endif; ?>
                                                        <ul class="list-unstyled small mt-2 mb-0">
                                                            <?php foreach ($orderItems as $item): ?>
                                                                <li><?php echo htmlspecialchars($item['name'], ENT_QUOTES); ?> × <?php echo (int) $item['quantity']; ?></li>
                                                            <?php endforeach; ?>
                                                        </ul>
                                                        <?php if (!empty($downloads)): ?>
                                                            <div class="mt-2">
                                                                <strong>Download digitali:</strong>
                                                                <ul class="ps-3 mb-0">
                                                                    <?php foreach ($downloads as $download): ?>
                                                                        <li><a href="<?php echo htmlspecialchars($download['download_url'], ENT_QUOTES); ?>" target="_blank">Scarica <?php echo htmlspecialchars($download['product_name'], ENT_QUOTES); ?></a></li>
                                                                    <?php endforeach; ?>
                                                                </ul>
                                                            </div>
                                                        <?php endif; ?>
                                                    </details>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>
