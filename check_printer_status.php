<?php
header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
$printerName = $input['printer'] ?? '';

if (empty($printerName)) {
    echo json_encode(['error' => 'Nom d\'imprimante manquant']);
    exit;
}

// Créer un fichier PowerShell temporaire pour éviter les problèmes d'échappement
$tempScript = tempnam(sys_get_temp_dir(), 'printer_check_') . '.ps1';

$psScript = "
try {
    \$printer = Get-Printer -Name '$printerName' -ErrorAction Stop
    \$printerWMI = Get-WmiObject -Class Win32_Printer -Filter \"Name='$printerName'\" -ErrorAction Stop
    
    \$online = -not \$printerWMI.WorkOffline
    \$printerStatus = \$printer.PrinterStatus
    \$printerState = \$printerWMI.PrinterState
    \$errorCode = \$printerWMI.PrinterStatus
    
    # Déterminer le statut détaillé
    if (\$printerWMI.WorkOffline) {
        \$status = 'offline'
    } elseif (\$printerStatus -eq 'PaperOut') {
        \$status = 'paper_out'
    } elseif (\$printerStatus -eq 'TonerLow') {
        \$status = 'toner_low'
    } elseif (\$printerStatus -eq 'DoorOpen') {
        \$status = 'door_open'
    } elseif (\$printerStatus -eq 'PaperJam') {
        \$status = 'paper_jam'
    } elseif (\$printerStatus -eq 'PaperProblem') {
        \$status = 'paper_problem'
    } elseif (\$printerStatus -eq 'OutputBinFull') {
        \$status = 'output_full'
    } elseif (\$printerStatus -eq 'Paused') {
        \$status = 'paused'
    } elseif (\$printerStatus -eq 'Error') {
        \$status = 'error'
    } elseif (\$printerStatus -eq 'Busy') {
        \$status = 'busy'
    } elseif (\$printerStatus -eq 'NotAvailable') {
        \$status = 'not_available'
    } elseif (\$printerStatus -eq 'Waiting') {
        \$status = 'waiting'
    } elseif (\$printerStatus -eq 'Processing') {
        \$status = 'processing'
    } elseif (\$printerStatus -eq 'Initializing') {
        \$status = 'initializing'
    } elseif (\$printerStatus -eq 'WarmingUp') {
        \$status = 'warming_up'
    } elseif (\$printerStatus -eq 'Normal' -or \$printerStatus -eq 'Idle') {
        \$status = 'ready'
    } else {
        # Vérifier les codes d'erreur WMI pour plus de détails
        switch (\$errorCode) {
            1 { \$status = 'error' }
            2 { \$status = 'unknown' }
            3 { \$status = 'ready' }
            4 { \$status = 'not_available' }
            5 { \$status = 'busy' }
            default { \$status = 'error' }
        }
    }
    
    # Informations supplémentaires
    \$extraInfo = @{
        printerStatus = \$printerStatus
        printerState = \$printerState
        errorCode = \$errorCode
    }
    
    @{
        online = \$online
        status = \$status
        details = \$extraInfo
    } | ConvertTo-Json -Compress
} catch {
    @{
        online = \$false
        status = 'not_found'
        error = \$_.Exception.Message
    } | ConvertTo-Json -Compress
}
";

file_put_contents($tempScript, $psScript);

// Exécuter le script
$output = shell_exec("powershell -ExecutionPolicy Bypass -File \"$tempScript\" 2>&1");

// Supprimer le fichier temporaire
unlink($tempScript);

if ($output === null) {
    echo json_encode(['error' => 'Impossible d\'exécuter PowerShell']);
    exit;
}

$output = trim($output);
$result = json_decode($output, true);

if ($result === null) {
    echo json_encode(['error' => 'Réponse invalide: ' . $output]);
    exit;
}

echo json_encode($result);
?>