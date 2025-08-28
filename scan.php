<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scanner</title>
    <link rel="stylesheet" href="assets/styles/style.css">
    <link rel="stylesheet" href="assets/styles/forms.css">
    <link rel="stylesheet" href="assets/styles/notifications.css">
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
                    <div class="header-icon scan">
                        <svg class="icon-large icon-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path d="M3 7V5a2 2 0 0 1 2-2h2"></path>
                            <path d="M17 3h2a2 2 0 0 1 2 2v2"></path>
                            <path d="M21 17v2a2 2 0 0 1-2 2h-2"></path>
                            <path d="M7 21H5a2 2 0 0 1-2-2v-2"></path>
                            <!--<path d="M7 8h10"></path>-->
                            <path d="M7 12h10"></path>
                            <!--<path d="M7 16h10"></path>-->
                        </svg>
                    </div>
                    <h1 class="page-title">Nouveau Scan</h1>
                    <p class="page-subtitle">Configurez vos paramètres de numérisation</p>
                </div>
            </header>

            <!-- Form Card -->
            <div class="form-card">
                <form method="post" enctype="multipart/form-data" class="scan-form">
                    <!-- Scan Settings Grid -->
                    <div class="form-grid">
                        <!-- Resolution -->
                        <div class="form-group">
                            <label class="form-label">Résolution</label>
                            <select name="resolution" class="form-select">
                                <option value="100">100 DPI</option>
                                <option value="200">200 DPI</option>
                                <option value="300" selected>300 DPI</option>
                                <option value="600">600 DPI</option>
                            </select>
                        </div>

                        <!-- Color Mode -->
                        <div class="form-group">
                            <label class="form-label">Mode de couleur</label>
                            <select name="color" class="form-select">
                                <option value="color">Couleur</option>
                                <option value="grayscale">Niveaux de gris</option>
                                <option value="blackwhite">Noir et blanc</option>
                            </select>
                        </div>

                        <!-- Format -->
                        <div class="form-group">
                            <label class="form-label">Format</label>
                            <select name="format" class="form-select">
                                <option value="jpeg">JPEG</option>
                                <option value="png">PNG</option>
                                <option value="pdf">PDF</option>
                            </select>
                        </div>

                        <!-- Paper Size -->
                        <div class="form-group">
                            <label class="form-label">Taille du papier</label>
                            <select name="paper" class="form-select">
                                <option value="A4">A4</option>
                                <option value="A3">A3</option>
                                <option value="Letter">Letter</option>
                            </select>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="form-submit">
                        <p id="scan-file" style="text-align: center; padding-bottom: 1rem;"></p>
                        <button type="submit" class="submit-button">
                            Lancer la numérisation
                        </button>
                    </div>
                </form>
            </div>

            <!-- Quick Actions -->
            <div class="quick-actions">
                <a href="index" class="quick-action-link quick-action-secondary">
                    <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Dashboard
                </a>
                <a href="print" class="quick-action-link quick-action-accent">
                    Imprimer
                </a>
            </div>
        </div>
    </div>
    <div id="notification-container"></div>

    <script src="assets/scripts/notifications.js"></script>
    <!--<script src="scripts/scan.js"></script>-->

    <script>
    document.querySelector('.scan-form').addEventListener('submit', function(e) {
        e.preventDefault();
        document.getElementById('scan-file').textContent = "Scan en cours...";
        
        // Get form data
        const formData = new FormData(this);
        
        fetch('scan_action.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            console.log(data);
            if (data.success) {
                document.getElementById('scan-file').innerHTML = 
                    "Scan terminé : <a href='" + data.file + "' target='_blank'>Voir le fichier</a>";
                // Show success notification
                showNotification('Scan terminé avec succès', 'success');
            } else {
                document.getElementById('scan-file').textContent = "Erreur : " + data.error;
                // Show error notification
                showNotification('Erreur lors du scan: ' + data.error, 'error');
            }
        })
        .catch(err => {
            document.getElementById('scan-file').textContent = "Erreur de connexion : " + err;
            // Show error notification
            showNotification('Erreur de connexion', 'error');
        });
    });
    </script>
</body>
</html>