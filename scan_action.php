<?php
$config = require __DIR__ . '/config.php';
header('Content-Type: application/json');

// Récupération des paramètres du formulaire
$resolution = isset($_POST['resolution']) ? intval($_POST['resolution']) : 300;
$colorMode = isset($_POST['color']) ? $_POST['color'] : 'color';
$format = isset($_POST['format']) ? $_POST['format'] : 'jpeg';
$paperSize = isset($_POST['paper']) ? $_POST['paper'] : 'A4';

// Validation des paramètres
if (!$resolution || $resolution < 100 || $resolution > 600) {
    $resolution = 300; // Valeur par défaut
}

$validColorModes = ['color', 'grayscale', 'blackwhite'];
if (!in_array($colorMode, $validColorModes)) {
    $colorMode = 'color'; // Valeur par défaut
}

$validFormats = ['jpeg', 'png', 'pdf'];
if (!in_array($format, $validFormats)) {
    $format = 'jpeg'; // Valeur par défaut
}

$validPaperSizes = ['A4', 'A3', 'Letter'];
if (!in_array($paperSize, $validPaperSizes)) {
    $paperSize = 'A4'; // Valeur par défaut
}

// Déterminer l'extension du fichier en fonction du format
$fileExtension = ($format === 'png') ? 'png' : (($format === 'bmp') ? 'bmp' : 'jpg');

$uploadDir = $config['paths']['scan_dir'];
$psScriptDir = __DIR__ . "\\psScript\\";
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}
if (!is_dir($psScriptDir)) {
    mkdir($psScriptDir, 0777, true);
}

$scanPath = $uploadDir . "scan_" . date("Y-m-d_H-i-s") . "." . $fileExtension;

// Définir les paramètres WIA en fonction des options sélectionnées
$wiaFormatGuid = "";
switch ($format) {
    case 'bmp':
        $wiaFormatGuid = "{B96B3CAB-0728-11D3-9D7B-0000F81EF32E}"; // BMP (très lourd)
        break;
    case 'jpeg':
        $wiaFormatGuid = "{B96B3CAE-0728-11D3-9D7B-0000F81EF32E}"; // JPEG (par défaut, compressé)
        break;
    case 'png':
        $wiaFormatGuid = "{B96B3CAF-0728-11D3-9D7B-0000F81EF32E}"; // PNG (compressé, sans perte)
        break;
    default:
        $wiaFormatGuid = "{B96B3CAE-0728-11D3-9D7B-0000F81EF32E}"; // JPEG (par défaut, compressé)
        break;
}

// Construire le script PowerShell avec les options
$psScript = <<<PS
\$deviceManager = New-Object -ComObject WIA.DeviceManager
\$device = \$deviceManager.DeviceInfos | Where-Object { \$_.Type -eq 1 } | Select-Object -First 1

if (\$device -eq \$null) { 
    Write-Error "Aucun scanner n'a été trouvé."
    exit 1 
}

\$connectedDevice = \$device.Connect()

# Configurer les propriétés du scanner
\$item = \$connectedDevice.Items | Select-Object -First 1

# Définir la résolution
try {
    \$item.Properties | Where-Object { \$_.Name -eq 'Horizontal Resolution' } | ForEach-Object { \$_.Value = $resolution }
    \$item.Properties | Where-Object { \$_.Name -eq 'Vertical Resolution' } | ForEach-Object { \$_.Value = $resolution }
} catch {
    Write-Warning "Impossible de définir la résolution: \$_"
}

# Définir le mode couleur
try {
    switch ("$colorMode") {
        "color" {
            \$item.Properties | Where-Object { \$_.Name -eq 'Current Intent' } | ForEach-Object { \$_.Value = 1 } # WIA_INTENT_IMAGE_TYPE_COLOR
        }
        "grayscale" {
            \$item.Properties | Where-Object { \$_.Name -eq 'Current Intent' } | ForEach-Object { \$_.Value = 2 } # WIA_INTENT_IMAGE_TYPE_GRAYSCALE
        }
        "blackwhite" {
            \$item.Properties | Where-Object { \$_.Name -eq 'Current Intent' } | ForEach-Object { \$_.Value = 4 } # WIA_INTENT_IMAGE_TYPE_TEXT
        }
    }
} catch {
    Write-Warning "Impossible de définir le mode couleur: \$_"
}

# Définir la taille du papier si possible
try {
    switch ("$paperSize") {
        "A4" {
            # A4: 210 x 297 mm
            \$item.Properties | Where-Object { \$_.Name -eq 'Page Size' } | ForEach-Object { \$_.Value = 0 } # WIA_PAGE_A4
        }
        "A3" {
            # A3: 297 x 420 mm
            \$item.Properties | Where-Object { \$_.Name -eq 'Page Size' } | ForEach-Object { \$_.Value = 1 } # WIA_PAGE_A3
        }
        "Letter" {
            # Letter: 8.5 x 11 inches
            \$item.Properties | Where-Object { \$_.Name -eq 'Page Size' } | ForEach-Object { \$_.Value = 2 } # WIA_PAGE_LETTER
        }
    }
} catch {
    Write-Warning "Impossible de définir la taille du papier: \$_"
}

# Effectuer la numérisation
\$img = \$item.Transfer("$wiaFormatGuid")

# Sauvegarder directement dans le format demandé
\$img.SaveFile("$scanPath")
PS;


$psFile = $psScriptDir . "scan.ps1";
file_put_contents($psFile, $psScript);

exec("powershell -ExecutionPolicy Bypass -File " . escapeshellarg($psFile) . " 2>&1", $output, $ret);

// Chemin relatif pour les scans
$publicPath = trim(str_replace(__DIR__, '', realpath($config['paths']['scan_dir'])), '/\\');
$publicPath .= '/';

$status = 'unknown';
if ($ret === 0 && file_exists($scanPath)) {
    echo json_encode([
        "success" => true,
        "file" => $publicPath . basename($scanPath),
        "settings" => [
            "resolution" => $resolution,
            "colorMode" => $colorMode,
            "format" => $format,
            "paperSize" => $paperSize
        ]
    ]);
    $status = 'success';
} else {
    echo json_encode([
        "success" => false,
        "error" => implode("\n", $output),
        "settings" => [
            "resolution" => $resolution,
            "colorMode" => $colorMode,
            "format" => $format,
            "paperSize" => $paperSize
        ]
    ]);
    $status = 'error';
}


// Journaliser la numérisation

require_once 'history.php';

$history = new History(
    file: $publicPath . basename($scanPath),
    status: $status,
    statusMessage: implode("\n", $output),
    settings: [
            "resolution" => $resolution,
            "colorMode" => $colorMode,
            "format" => $format,
            "paperSize" => $paperSize
        ],
);
$history->logScanHistory();
