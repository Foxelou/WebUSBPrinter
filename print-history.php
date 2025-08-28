<?php
$config = require __DIR__ . '/config.php';

if (!$config['security']['allow_print_history']) {
    http_response_code(403);
    header('Location: /printer_project/history-disabled.html');
    exit();
}

$historyFilePath = __DIR__ . '/data/history/print_history.json';
$printHistory = [];

if (file_exists($historyFilePath)) {
    $jsonData = file_get_contents($historyFilePath);
    $printHistory = json_decode($jsonData, true);
    if ($printHistory === null && json_last_error() !== JSON_ERROR_NONE) {
        // Handle JSON decoding error, e.g., log it or set $printHistory to an empty array
        $printHistory = [];
    }
} else {
    // Handle file not found, e.g., log it or set $printHistory to an empty array
    $printHistory = [];
}

// Calculate summary statistics
$totalPrints = count($printHistory);
$successfulPrints = 0;
$pendingPrints = 0;
$errorPrints = 0;

foreach ($printHistory as $job) {
    switch ($job['status']) {
        case 'success':
            $successfulPrints++;
            break;
        case 'pending':
            $pendingPrints++;
            break;
        case 'error':
            $errorPrints++;
            break;
        case 'warn':
            $errorPrints++;
            break;
    }
}

// Sort history by timestamp in descending order
usort($printHistory, function($a, $b) {
    return strtotime($b['timestamp']) - strtotime($a['timestamp']);
});

$publicPath = trim(str_replace(__DIR__, '', realpath($config['paths']['upload_dir'])), '/\\');
$publicPath .= '/'; // forcer slash final
$fileLink = $publicPath . urlencode($job['file']);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historique des Impressions - Centre d'Impression & Scan</title>
    <link rel="stylesheet" href="assets/styles/dashboard.css">
    <link rel="stylesheet" href="assets/styles/style.css">
    <link rel="stylesheet" href="assets/styles/print-history.css">
    <!--<link rel="stylesheet" href="assets/styles/forms.css">-->
</head>
<body>
    <div class="dashboard-container">
        <div class="dashboard-content">
            <!-- Header -->
            <header class="page-header">
                <div class="header-navigation">
                    <a href="index" class="back-link">
                        <svg class="icon-small" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M19 12H6m6-7-7 7 7 7"/>
                        </svg>
                        Retour au dashboard
                    </a>
                </div>
                <div class="header-content">
                    <div class="header-icon">
                        <svg class="icon-large icon-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                    </div>
                    <h1 class="page-title">Historique des Impressions</h1>
                    <p class="page-subtitle">Suivi de toutes vos impressions récentes</p>
                </div>
            </header>

            <!-- Stats Summary -->
                        <div class="summary-stats">
                <div class="summary-item">
                    <span class="summary-number"><?= $totalPrints ?></span>
                    <span class="summary-label">Total</span>
                </div>
                <div class="summary-item">
                    <span class="summary-number success"><?= $successfulPrints ?></span>
                    <span class="summary-label">Réussies</span>
                </div>
                <div class="summary-item">
                    <span class="summary-number pending"><?= $pendingPrints ?></span>
                    <span class="summary-label">En cours</span>
                </div>
                <div class="summary-item">
                    <span class="summary-number error"><?= $errorPrints ?></span>
                    <span class="summary-label">Échecs</span>
                </div>
            </div>

            <!-- Filters 
            <div class="filters-section">
                <div class="filters-header">
                    <h2 class="filters-title">Filtrer les résultats</h2>
                    <button class="filters-toggle" onclick="toggleFilters()">
                        <svg class="icon-small" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polygon points="22,3 2,3 10,12.46 10,19 14,21 14,12.46"></polygon>
                        </svg>
                        Filtres
                    </button>
                </div>
                <div class="filters-content" id="filtersContent">
                    <div class="filter-group">
                        <label class="filter-label">Période</label>
                        <select class="filter-select">
                            <option value="today">Aujourd'hui</option>
                            <option value="week" selected>Cette semaine</option>
                            <option value="month">Ce mois</option>
                            <option value="all">Tout</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label class="filter-label">Statut</label>
                        <select class="filter-select">
                            <option value="all">Tous les statuts</option>
                            <option value="success">Réussies</option>
                            <option value="pending">En cours</option>
                            <option value="error">Échecs</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label class="filter-label">Imprimante</label>
                        <select class="filter-select">
                            <option value="all">Toutes les imprimantes</option>
                            <option value="hp-office">HP Office Pro</option>
                            <option value="canon-color">Canon Color</option>
                            <option value="xerox-a3">Xerox A3</option>
                        </select>
                    </div>
                </div>
            </div> -->

            <!-- Print History List -->
            <div class="history-section">
                <div class="history-header">
                    <h2 class="history-title">Impressions récentes</h2>
                    <div class="history-actions">
                        <button class="refresh-btn" onclick="refreshHistory()">
                            <svg class="icon-small" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M3 12a9 9 0 0 1 9-9 9.75 9.75 0 0 1 6.74 2.74L21 8"/>
                                <path d="M21 3v5h-5"/>
                                <path d="M21 12a9 9 0 0 1-9 9 9.75 9.75 0 0 1-6.74-2.74L3 16"/>
                                <path d="M3 21v-5h5"/>
                            </svg>
                            Actualiser
                        </button>
                    </div>
                </div>

                <div class="history-list">
                    <!-- Print Job Items -->
                    <?php if (empty($printHistory)): ?>
                        <p>Aucun historique d'impression disponible.</p>
                    <?php else: ?>
                        <?php foreach ($printHistory as $job): ?>
                            <div class="print-item">
                                <div class="print-item-header">
                                    <div class="print-status <?= htmlspecialchars($job['status']) ?>">
                                        <?php if ($job['status'] === 'success'): ?>
                                            <svg class="status-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M20 6L9 17l-5-5"/>
                                            </svg>
                                        <?php elseif ($job['status'] === 'pending'): ?>
                                            <svg class="status-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <circle cx="12" cy="12" r="10"/>
                                                <polyline points="12,6 12,12 16,14"/>
                                            </svg>
                                        <?php elseif ($job['status'] === 'error' || $job['status'] === 'warn'): ?>
                                            <svg class="status-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M18 6L6 18"/>
                                                <path d="M6 6l12 12"/>
                                            </svg>
                                        <?php endif; ?>
                                    </div>
                                    <div class="print-info">
                                        <h3 class="print-filename"><a href="<?php echo $fileLink; ?>" target="_blank"><?= htmlspecialchars($job['file']) ?></a></h3>
                                        <div class="print-meta">
                                            <span class="print-time"><?= htmlspecialchars($job['timestamp']) ?></span>
                                            <span class="print-separator">•</span>
                                            <span class="print-printer"><?= htmlspecialchars($job['settings']['printer']) ?></span>
                                        </div>
                                    </div>
                                    <!--<div class="print-actions">
                                        <?php if ($job['status'] === 'error'): ?>
                                            <button class="action-btn retry-btn" title="Réessayer">
                                                <svg class="icon-tiny" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M3 12a9 9 0 0 1 9-9 9.75 9.75 0 0 1 6.74 2.74L21 8"/>
                                                    <path d="M21 3v5h-5"/>
                                                </svg>
                                            </button>
                                        <?php else: ?>
                                            <button class="action-btn" title="Détails">
                                                <svg class="icon-tiny" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <circle cx="12" cy="12" r="1"/>
                                                    <circle cx="19" cy="12" r="1"/>
                                                    <circle cx="5" cy="12" r="1"/>
                                                </svg>
                                            </button>
                                        <?php endif; ?>
                                    </div>-->
                                </div>
                                <div class="print-details">
                                    <?php if (($job['status'] === 'error' || $job['status'] === 'warn')): ?>
                                        <div class="detail-row">
                                            <span class="detail-label">Erreur:</span>
                                            <span class="detail-value error-message"><?= isset($job['errorMessage']) ? htmlspecialchars($job['errorMessage']) : 'N/A' ?></span>
                                        </div>
                                    <?php endif; ?>
                                    <div class="detail-row">
                                        <span class="detail-label">Pages:</span>
                                        <span class="detail-value">
                                            <?php
                                                if (isset($job['settings']['customPages']) && !empty($job['settings']['customPages'])) {
                                                    echo htmlspecialchars($job['settings']['customPages']) . ' pages';
                                                } elseif (isset($job['settings']['pagePreset'])) {
                                                    echo htmlspecialchars($job['settings']['pagePreset']) . ' pages';
                                                } else {
                                                    echo 'N/A';
                                                }
                                            ?>
                                        </span>
                                    </div>
                                    <div class="detail-row">
                                        <span class="detail-label">Copies:</span>
                                        <span class="detail-value"><?= htmlspecialchars($job['settings']['copies']) ?> exemplaire(s)</span>
                                    </div>
                                    <div class="detail-row">
                                        <span class="detail-label">Format:</span>
                                        <span class="detail-value">
                                            <?= htmlspecialchars($job['settings']['paper']) ?>, 
                                            <?= htmlspecialchars($job['settings']['color']) ? 'Couleur' : 'Noir et blanc' ?>, 
                                            <?= htmlspecialchars($job['settings']['duplex']) ? 'Recto-verso' : 'Recto' ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- Load More 
                <div class="load-more">
                    <button class="load-more-btn" onclick="loadMoreHistory()">
                        Charger plus d'historique
                        <svg class="icon-small" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M7 13l3 3 7-7"/>
                            <path d="M12 19V5"/>
                        </svg>
                    </button>
                </div>-->
            </div>

            <!-- Quick Actions -->
            <div class="quick-actions">
                <a href="index" class="quick-action-btn">
                    <svg class="icon-small" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                        <polyline points="9,22 9,12 15,12 15,22"/>
                    </svg>
                    Dashboard
                </a>
                <a href="scan-history" class="quick-action-btn">
                    <svg class="icon-small" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14,2 14,8 20,8"></polyline>
                        <line x1="16" y1="13" x2="8" y2="13"></line>
                        <line x1="16" y1="17" x2="8" y2="17"></line>
                        <polyline points="10,9 9,9 8,9"></polyline>
                    </svg>
                    Historique Scans
                </a>
            </div>
        </div>
    </div>

    <script>
        function toggleFilters() {
            const filtersContent = document.getElementById('filtersContent');
            filtersContent.classList.toggle('expanded');
        }

        function refreshHistory() {
            // Simulation du rechargement
            window.location.reload();
        }

        function loadMoreHistory() {
            // Simulation du chargement de plus d'éléments
            alert('Fonctionnalité à implémenter avec votre backend PHP');
        }
    </script>
</body>
</html>
