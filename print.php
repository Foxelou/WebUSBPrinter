<?php
session_start();
$config = require __DIR__ . '/config.php';
require_once 'print_job_monitor.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['pdf'])) {
    $uploadDir = $config['paths']['upload_dir'];
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

    $safeName = preg_replace('/[^a-zA-Z0-9_\-\.]/', '_', basename($_FILES['pdf']['name']));
    $timestamp = date("Y-m-d_H-i-s");
    $_SESSION['last_print_file'] = $timestamp . '_' . $safeName;
    $targetFile = $uploadDir . $_SESSION['last_print_file'];

    if (move_uploaded_file($_FILES['pdf']['tmp_name'], $targetFile)) {
        $printer = $_POST['printer'] ?? 'MyPrinter';
        $color = $_POST['color'] ?? 'color';
        $duplex = $_POST['duplex'] ?? 'simplex';
        $orient = ($_POST['orientation'] === 'Landscape') ? 'landscape' : 'portrait';
        $paper = $_POST['paper'] ?? 'A4';
        $customPages = trim($_POST['customPages'] ?? '');
        $pagePreset = $_POST['pagePreset'] ?? 'all';
        $copies = max(1, intval($_POST['copies'] ?? 1));
        //$marge = intval($_POST['marge'] ?? 0);

        $pages = '';

        switch ($pagePreset) {
            case 'odd':
                $pages = 'odd';
                break;
            case 'even':
                $pages = 'even';
                break;
            case 'custom':
                $pages = $customPages;
                break;
            default:
                $pages = ''; // Toutes les pages
                break;
        }

        // Configuration
        $sumatraPath = $config['sumatra_path']; // Chemin vers SumatraPDF
        if (!is_dir($config['paths']['log_dir'])) mkdir($config['paths']['log_dir'], 0777, true);

        // Vérification de l'exécutable
        if (!file_exists($sumatraPath)) {
            $_SESSION['notification'] = [
                'message' => "❌ Erreur SumatraPDF est introuvable.",
                'type' => 'error' // success, error, info, warn
            ];
            exit;
        }

        // Construction des options
        $settings = "$copies" . "x";
        if ($pages) {
            $settings .= ",$pages";
        }
        $settings .= ",$orient,$color,$duplex,paper=$paper";

        // Construction de la commande
        $cmd = escapeshellarg($sumatraPath)
            . " -print-to " . escapeshellarg($printer)
            . " -print-settings " . escapeshellarg($settings)
            . " -silent "
            . escapeshellarg($targetFile);

        // Exécution
        exec($cmd . " 2>&1", $output, $code);

        // Résultat utilisateur
        $status = 'unknown';
        if ($code === 0) {
            $jobId = findLatestJobId($printer, $_SESSION['last_print_file']);
            debugLog("print.php: jobId=" . var_export($jobId, true));
            if ($jobId) {
                $monitoringResult = monitorPrintJob($printer, $jobId);
                debugLog("print.php: monitoringResult=" . var_export($monitoringResult, true));
                
                switch ($monitoringResult['status']) {
                    case 'Completed':
                        $_SESSION['notification'] = [
                            'message' => "✅ " . htmlspecialchars($monitoringResult['message']),
                            'type' => 'success'
                        ];
                        $status = 'success';
                        break;

                    case 'Error':
                        $_SESSION['notification'] = [
                            'message' => "❌ " . htmlspecialchars($monitoringResult['message']),
                            'type' => 'error'
                        ];
                        $status = $monitoringResult['message'];
                        break;

                    case 'Timeout':
                    default:
                        $_SESSION['notification'] = [
                            'message' => "⚠️ " . htmlspecialchars($monitoringResult['message']),
                            'type' => 'warn'
                        ];
                        $status = 'warn';
                        break;
                }
            } else {
                $_SESSION['notification'] = [
                    'message' => "⚠️ L'impression a été envoyée, mais n'a pas pu être suivie dans la file d'attente.",
                    'type' => 'warn'
                ];
            }

            //$_SESSION['notification'] = [
            //    'message' => '✅ Impression lancée.',
            //    'type' => 'success' // success, error, info, warn
            //];
            
        } else {
            $_SESSION['notification'] = [
                'message' => "❌ Erreur lors de l'impression.",
                'type' => 'error' // success, error, info, warn
            ];
            $status = 'error';
        }

        // Journaliser l'impression

        // Chemin relatif pour les impressions
        $publicPath = trim(str_replace(__DIR__, '', realpath($config['paths']['upload_dir'])), '/\\');
        $publicPath .= '/';

        require_once 'history.php';

        $history = new History(
            file: $publicPath . basename($targetFile),
            status: $status,
            statusMessage: '',
            settings: [
                'printer' => $printer,
                'color' => $color,
                'duplex' => $duplex,
                'orient' => $orient,
                'paper' => $paper,
                'customPages' => $customPages,
                'pagePreset' => $pagePreset,
                'copies' => $copies,
            ],
        );
        $history->logPrintHistory();
    } else {
        $_SESSION['notification'] = [
            'message' => "❌ Erreur lors de l'upload.",
            'type' => 'error' // success, error, info, warn
        ];
    }
}


function getAvailablePrinters(): array {
    $printers = [];
    exec('wmic printer get name', $output);
    foreach ($output as $line) {
        $line = trim($line);
        if ($line !== '' && stripos($line, 'name') === false) {
            $printers[] = $line;
        }
    }
    return $printers;
}

$favoritePrinter = $config['printer_favorite'];
$availablePrinters = getAvailablePrinters();

// Met l'imprimante favorite en premier si elle existe
usort($availablePrinters, function ($a, $b) use ($favoritePrinter) {
    return ($a === $favoritePrinter) ? -1 : (($b === $favoritePrinter) ? 1 : 0);
});

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouvelle Impression</title>
    <link rel="stylesheet" href="assets/styles/style.css">
    <link rel="stylesheet" href="assets/styles/forms.css">
    <link rel="stylesheet" href="assets/styles/notifications.css">
    <link rel="stylesheet" href="assets/styles/printer_status.css">
</head>

<body>
    <div class="page-container">
        <div class="page-content">
            <!-- Header -->
            <header class="page-header">
                <a href="index" class="back-link">
                    <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Retour au dashboard
                </a>
                <div class="header-content">
                    <div class="header-icon">
                        <svg class="icon-large icon-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                    </div>
                    <h1 class="page-title">Nouvelle Impression</h1>
                    <p class="page-subtitle">Configurez vos paramètres d'impression</p>
                </div>
            </header>

            <!-- Form Card -->
            <div class="form-card">
                <form method="post" enctype="multipart/form-data" class="print-form">

                    <!-- File Upload -->
                    <div class="form-group">
                        <label class="form-label">
                            <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Fichier à imprimer
                        </label>
                        <div class="file-upload-container">
                            <input type="file" name="pdf" accept="application/pdf" required
                                onchange="updateFileName(this)" class="file-input" />
                            <div class="file-upload-area">
                                <svg class="file-upload-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                </svg>
                                <div class="file-upload-text">
                                    <span id="fileDisplay">Cliquez pour sélectionner un fichier PDF</span>
                                    <span id="filePlaceholder" class="file-placeholder">ou glissez-déposez votre fichier
                                        ici</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Printer Selection -->
                    <div class="form-group">
                        <label for="printer" class="form-label">
                            <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                            </svg>
                            Imprimante
                        </label>
                        <select name="printer" id="printer" required class="form-select">
                            <option value="">Sélectionnez une imprimante</option>
                            <?php foreach ($availablePrinters as $printer): ?>
                                <option value="<?= htmlspecialchars($printer) ?>"
                                    <?= $printer === $favoritePrinter ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($printer) ?>
                                    <?= $printer === $favoritePrinter ? '⭐' : '' ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <?php require_once "printer_status.php";?>

                    <!-- Print Settings Grid -->
                    <div class="form-grid">
                        <!-- Color Mode -->
                        <div class="form-group">
                            <label class="form-label">
                                <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                Mode d'impression
                            </label>
                            <select name="color" class="form-select">
                                <option value="color">Couleur</option>
                                <option value="monochrome">Noir et Blanc</option>
                            </select>
                        </div>

                        <!-- Duplex -->
                        <div class="form-group">
                            <label class="form-label">Recto/Verso</label>
                            <select name="duplex" class="form-select">
                                <option value="simplex">Recto</option>
                                <option value="duplex">Recto-Verso</option>
                                <option value="duplexshort">Recto-verso, reliure petit côté</option>
                                <option value="duplexlong">Recto-verso, reliure grand côté</option>
                            </select>
                        </div>

                        <!-- Orientation -->
                        <div class="form-group">
                            <label class="form-label">Orientation</label>
                            <select name="orientation" class="form-select">
                                <option value="portrait">Portrait</option>
                                <option value="landscape">Paysage</option>
                            </select>
                        </div>

                        <!-- Paper Format -->
                        <div class="form-group">
                            <label class="form-label">Format</label>
                            <select name="paper" class="form-select">
                                <option value="A4">A4</option>
                                <option value="A3">A3</option>
                                <option value="Letter">Letter</option>
                            </select>
                        </div>
                    </div>

                    <!-- Page Selection -->
                    <div class="form-group">
                        <label class="form-label">Pages à imprimer</label>
                        <select name="pagePreset" onchange="toggleCustomPageInput(this.value)" class="form-select">
                            <option value="all">Toutes les pages</option>
                            <option value="odd">Pages impaires</option>
                            <option value="even">Pages paires</option>
                            <option value="custom">Personnalisé…</option>
                        </select>

                        <input id="customPagesInput" type="text" name="customPages" placeholder="Ex : 1-3,5,10-8"
                            class="form-input custom-pages-input" />
                    </div>

                    <!-- Number of Copies -->
                    <div class="form-group">
                        <label for="copies" class="form-label">Nombre de copies</label>
                        <input type="number" id="copies" name="copies" min="1" value="1"
                            class="form-input number-input" />
                    </div>

                    <!-- Submit Button -->
                    <div class="form-submit">
                        <button type="submit" class="submit-button">
                            Lancer l'impression
                        </button>
                    </div>
                </form>
            </div>

            <!-- Quick Actions -->
            <div class="quick-actions">
                <a href="index" class="quick-action-link quick-action-secondary">
                    <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Dashboard
                </a>
                <a href="scan" class="quick-action-link quick-action-accent">
                    Scanner
                </a>
            </div>
        </div>
    </div>
    <div id="notification-container"></div>
    <?php if (isset($_SESSION['notification'])): ?>
        <script>
            window.addEventListener('DOMContentLoaded', () => {
                showNotification(<?= json_encode($_SESSION['notification']['message']) ?>, '<?= $_SESSION['notification']['type'] ?>');
            });
        </script>
    <?php unset($_SESSION['notification']);
    endif; ?>

    <script src="assets/scripts/notifications.js"></script>
    <script src="assets/scripts/print.js"></script>

    <script src="assets/scripts/statusMessages.js"></script>
    <script>
        let statusCheckInterval = null;
        let currentPrinter = null;


        // Fonction pour arrêter la vérification régulière
        function stopStatusChecking() {
            if (statusCheckInterval) {
                clearInterval(statusCheckInterval);
                statusCheckInterval = null;
                console.log('Vérification du statut arrêtée');
            }
            currentPrinter = null;
        }

        // Fonction pour démarrer la vérification régulière
        function startStatusChecking(printerName, intervalSeconds = 60) {
            stopStatusChecking(); // Arrêter toute vérification en cours
            
            currentPrinter = printerName;
            
            // Vérification immédiate
            fetchPrinterStatus(currentPrinter);
            
            // Programmer les vérifications régulières
            statusCheckInterval = setInterval(() => {
                if (currentPrinter === printerName) {
                    fetchPrinterStatus(printerName);
                }
            }, intervalSeconds * 1000);
            
            console.log(`Vérification du statut démarrée pour "${printerName}" toutes les ${intervalSeconds} secondes`);
        }

        // Arrêter la vérification quand la page est fermée
        window.addEventListener('beforeunload', function() {
            stopStatusChecking();
        });

        document.getElementById('printer').addEventListener('change', function() {
            const printerId = this.value;
            const statusSection = document.getElementById('printerStatus');
            
            if (printerId) {
                statusSection.classList.add('show');
                startStatusChecking(printerId, 60)
            } else {
                statusSection.classList.remove('show');
            }
        });

        async function fetchPrinterStatus(printerId) {
            const loadingElement = document.getElementById('statusLoading');
            const contentElement = document.getElementById('statusContent');
            const refreshButton = document.getElementById('refreshButton');
            
            // Show loading state
            loadingElement.style.display = 'flex';
            contentElement.style.display = 'none';
            refreshButton.disabled = true;
            refreshButton.textContent = 'Actualisation...';

            try {
                const response = await fetch('check_printer_status.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ printer: printerId })
                });

                if (!response.ok) {
                    throw new Error('Erreur réseau');
                }

                const result = await response.json();
                console.log(result);
                
                loadingElement.style.display = 'none';
                contentElement.style.display = 'block';
                refreshButton.disabled = false;
                refreshButton.textContent = 'Actualiser';

                if (result) {
                    updatePrinterStatus(result);
                } else {
                    showStatusError();
                }
            } catch (error) {
                console.error('Erreur lors de la vérification:', error);
            } finally {
                isChecking = false;
            }
        }

        function updatePrinterStatus(data) {
            // Update status indicator
            updateStatusIndicator(data.status);
            
            // Update timestamp
            const statusTime = document.getElementById('statusTime');
            statusTime.textContent = `Mis à jour: ${new Date().toLocaleTimeString('fr-FR')}`;
        }

        function updateStatusIndicator(status) {
            const statusIcon = document.getElementById('statusIcon');
            const statusText = document.getElementById('statusText');
            
            // Reset classes
            statusIcon.classList.remove('status-online');
            statusIcon.classList.remove('status-offline');
            statusIcon.classList.remove('status-busy');
            statusIcon.classList.remove('status-error');
            statusIcon.classList.remove('status-maintenance');
            statusText.className = '';

            const statusInfo = statusMessages[status] || statusMessages.error;

            console.log(statusInfo.text);
    
            // Afficher l'icône et le texte
            const statusElement = document.getElementById('status-info');
            if (statusInfo) {
                console.log(statusInfo.class);
                statusIcon.classList.add(`${statusInfo.class}`);
                statusText.classList.add(`${statusInfo.class}`);
                statusText.textContent = statusInfo.text;
                statusIcon.innerHTML = statusInfo.icon;
            }
        }

        function showStatusError() {
            const contentElement = document.getElementById('statusContent');
            contentElement.innerHTML = '<div style="text-align: center; padding: 1rem; font-size: 0.875rem; color: var(--muted-foreground);">Impossible de récupérer le statut de l\'imprimante</div>';
        }

        function refreshPrinterStatus() {
            const selectedPrinter = document.getElementById('printer').value;
            if (selectedPrinter) {
                fetchPrinterStatus(selectedPrinter);
            }
        }


        // Gérer la visibilité de la page (pause/reprise)
        document.addEventListener('visibilitychange', function() {
            if (currentPrinter) {
                if (document.hidden) {
                    // Page cachée, ralentir les vérifications
                    printerName = document.getElementById('printer').value;
                    startStatusChecking(printerName, 120);
                } else {
                    // Page visible, reprendre les vérifications normales
                    startStatusChecking(currentPrinter, 60);
                }
            }
        });

    </script>
</body>

</html>