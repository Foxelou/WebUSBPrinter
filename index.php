<?php
$config = require __DIR__ . '/config.php';

// Configuration de la sécurité pour l'affichage des fichiers récents
// Si false, désactive les liens cliquables pour éviter l'accès direct aux fichiers sensibles (sur le dashboard)
$showRecentFileLinks = $config['security']['show_recent_links_dashboard'];
$recentFilesDisplay = $config['security']['recent_files_display_limit'];

$uploadDir = $config['paths']['upload_dir'];
$scanDir = $config['paths']['scan_dir'];
$logDir = $config['paths']['log_dir'];
$psScriptDir = __DIR__ . "\\psScript\\";


if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}
if (!is_dir($scanDir)) {
    mkdir($scanDir, 0777, true);
}
if (!is_dir($logDir)) {
    mkdir($logDir, 0777, true);
}
if (!is_dir($psScriptDir)) {
    mkdir($psScriptDir, 0777, true);
}

?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Centre d'Impression & Scan</title>
    <link rel="stylesheet" href="assets/styles/style.css">
    <link rel="stylesheet" href="assets/styles/dashboard.css">
    <!--<script src="scripts/app.js" defer></script>-->
</head>

<body>
    <div class="dashboard-container">
        <div class="dashboard-content">
            <!-- Header -->
            <header class="dashboard-header">
                <h1 class="dashboard-title">Centre d'Impression & Scan</h1>
                <p class="dashboard-subtitle">Gestion centralisée pour vos documents</p>
            </header>

            <!-- Action Cards Grid -->
            <div class="action-cards-grid">
                <!-- Print Card -->
                <div class="action-card">
                    <a href="print" class="action-card-button">
                        <div class="action-card-content">
                            <div class="action-icon action-icon-primary">
                                <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                                    <path d="M6 9V3a1 1 0 0 1 1-1h10a1 1 0 0 1 1 1v6"></path>
                                    <rect x="6" y="14" width="12" height="8" rx="1"></rect>
                                </svg>
                            </div>
                            <div class="action-text">
                                <h2 class="action-title">Imprimer</h2>
                                <p class="action-description">Lancer une nouvelle impression</p>
                            </div>
                        </div>
                    </a>

                    <div class="recent-section">
                        <div class="recent-header">
                            <svg class="icon-small" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"></path>
                                <path d="M3 3v5h5"></path>
                                <path d="M12 7v5l4 2"></path>
                            </svg>
                            <span class="recent-label">Récentes</span>
                        </div>
                        <div class="recent-items">
                            <?php
                            $dir = $config['paths']['upload_dir'];
                            $files = array_diff(scandir($dir), ['.', '..']);

                            $publicPath = trim(str_replace(__DIR__, '', realpath($dir)), '/\\') . '/';

                            // Trier par date (récents en premier)
                            usort($files, fn($a, $b) => filemtime($dir . $b) - filemtime($dir . $a));

                            $recentFiles = array_slice($files, 0, $recentFilesDisplay);
                            ?>

                            <?php if (empty($recentFiles)): ?>
                                <div class="recent-item">Aucun document disponible.</div>
                            <?php else: ?>
                                <?php foreach ($recentFiles as $file):
                                    $filePath = $dir . $file;
                                    $fileTime = date("D d M H:i", filemtime($filePath));
                                    $fileLink = $publicPath . urlencode($file);
                                ?>
                                    <div class="recent-item">
                                        <?php if ($showRecentFileLinks): ?>
                                            <!-- Si active dans la config, on autorise le lien vers le fichier -->
                                            <a href="<?= htmlspecialchars($fileLink) ?>" target="_blank" class="recent-filename">
                                                <?= htmlspecialchars($file) ?>
                                            </a>
                                        <?php else: ?>
                                            <!-- Sinon on affiche juste le nom du fichier, sans lien cliquable -->
                                            <?= htmlspecialchars($file) ?>
                                        <?php endif; ?>
                                        <div class="recent-meta">
                                            <span class="status-success">✓</span>
                                            <svg class="icon-tiny" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <circle cx="12" cy="12" r="10"></circle>
                                                <polyline points="12,6 12,12 16,14"></polyline>
                                            </svg>
                                            <span class="recent-time"><?= $fileTime ?></span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        <a href="print-history" class="view-all-link">Voir tout l'historique →</a>
                    </div>
                </div>

                <!-- Scan Card -->
                <div class="action-card">
                    <a href="scan" class="action-card-button">
                        <div class="action-card-content">
                            <div class="action-icon action-icon-accent">
                                <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M3 7V5a2 2 0 0 1 2-2h2"></path>
                                    <path d="M17 3h2a2 2 0 0 1 2 2v2"></path>
                                    <path d="M21 17v2a2 2 0 0 1-2 2h-2"></path>
                                    <path d="M7 21H5a2 2 0 0 1-2-2v-2"></path>
                                    <!--<path d="M7 8h10"></path>-->
                                    <path d="M7 12h10"></path>
                                    <!--<path d="M7 16h10"></path>-->
                                </svg>
                            </div>
                            <div class="action-text">
                                <h2 class="action-title">Scanner</h2>
                                <p class="action-description">Numériser un nouveau document</p>
                            </div>
                        </div>
                    </a>

                    <div class="recent-section">
                        <div class="recent-header">
                            <svg class="icon-small" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"></path>
                                <path d="M3 3v5h5"></path>
                                <path d="M12 7v5l4 2"></path>
                            </svg>
                            <span class="recent-label">Récents</span>
                        </div>
                        <div class="recent-items">
                            <?php
                            $dir = $config['paths']['scan_dir'];
                            $files = array_diff(scandir($dir), ['.', '..']);

                            $publicPath = trim(str_replace(__DIR__, '', realpath($dir)), '/\\') . '/';

                            // Trier par date (récents en premier)
                            usort($files, fn($a, $b) => filemtime($dir . $b) - filemtime($dir . $a));

                            $recentFiles = array_slice($files, 0, $recentFilesDisplay);
                            ?>

                            <?php if (empty($recentFiles)): ?>
                                <div class="recent-item">Aucun document disponible.</div>
                            <?php else: ?>
                                <?php foreach ($recentFiles as $file):
                                    $filePath = $dir . $file;
                                    $fileTime = date("D d M H:i", filemtime($filePath));
                                    $fileLink = $publicPath . urlencode($file);
                                ?>
                                    <div class="recent-item">
                                        <?php if ($showRecentFileLinks): ?>
                                            <!-- Si active dans la config, on autorise le lien vers le fichier -->
                                            <a href="<?= htmlspecialchars($fileLink) ?>" target="_blank" class="recent-filename">
                                                <?= htmlspecialchars($file) ?>
                                            </a>
                                        <?php else: ?>
                                            <!-- Sinon on affiche juste le nom du fichier, sans lien cliquable -->
                                            <?= htmlspecialchars($file) ?>
                                        <?php endif; ?>
                                        <div class="recent-meta">
                                            <span class="status-success">✓</span>
                                            <svg class="icon-tiny" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <circle cx="12" cy="12" r="10"></circle>
                                                <polyline points="12,6 12,12 16,14"></polyline>
                                            </svg>
                                            <span class="recent-time"><?= $fileTime ?></span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            <!--<div class="recent-item">
                                <span class="recent-filename">carte.jpg (exemple)</span>
                                <div class="recent-meta">
                                    <span class="status-error">✗</span>
                                    <svg class="icon-tiny" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <polyline points="12,6 12,12 16,14"></polyline>
                                    </svg>
                                    <span class="recent-time">10:00</span>
                                </div>
                            </div>-->
                        </div>
                        <a href="scan-history" class="view-all-link view-all-accent">Voir tout l'historique →</a>
                    </div>
                </div>
            </div>

            <!-- Quick Stats 
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-number stat-primary">12</div>
                    <div class="stat-label">Impressions aujourd'hui</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number stat-accent">8</div>
                    <div class="stat-label">Scans aujourd'hui</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number stat-warning">3</div>
                    <div class="stat-label">En attente</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number stat-muted">95%</div>
                    <div class="stat-label">Taux de réussite</div>
                </div>
            </div> -->

            <!-- Quick Access Buttons -->
            <div class="quick-actions">
                <a href="print-history" class="quick-action-btn">
                    <svg class="icon-small" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14,2 14,8 20,8"></polyline>
                        <line x1="16" y1="13" x2="8" y2="13"></line>
                        <line x1="16" y1="17" x2="8" y2="17"></line>
                        <polyline points="10,9 9,9 8,9"></polyline>
                    </svg>
                    Historique Impressions
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
</body>

</html>