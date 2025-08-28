<?php

function debugLog(string $message): void
{
    $logFile = __DIR__ . '/print_debug.log';
    $date = date('[Y-m-d H:i:s]');
    file_put_contents($logFile, $date . " " . $message . PHP_EOL, FILE_APPEND);
}




/**
 * Trouve l'ID de la dernière tâche d'impression soumise pour une imprimante
 * et un nom de document donnés.
 */
function findLatestJobId(string $printerName, string $documentBaseName): ?int
{
    sleep(2);

    // Récupère toutes les tâches
    $psCommand = "powershell -ExecutionPolicy Bypass -Command \"Get-PrintJob -PrinterName '$printerName' | " .
                 "Select-Object Id,DocumentName,SubmittedTime | ConvertTo-Json\"";

    $output = shell_exec($psCommand);
    if (!$output) {
        debugLog("findLatestJobId: no output from PowerShell");
        return null;
    }

    $jobs = json_decode($output, true);

    if (!$jobs) {
        debugLog("findLatestJobId: invalid JSON: " . var_export($output, true));
        return null;
    }

    // Si un seul job est retourné, $jobs n’est pas un tableau indexé mais un objet
    if (isset($jobs['Id'])) {
        $jobs = [$jobs];
    }

    debugLog("findLatestJobId: jobs list=" . var_export($jobs, true));

    // Cherche le job dont le DocumentName contient le basename
    foreach ($jobs as $job) {
        if (stripos($job['DocumentName'], $documentBaseName) !== false) {
            debugLog("findLatestJobId: matched jobId=" . $job['Id']);
            return (int)$job['Id'];
        }
    }

    debugLog("findLatestJobId: no match found for " . $documentBaseName);
    return null;
}


/**
 * Surveille une tâche d'impression et retourne son statut final.
 *
 * @return array ['status' => 'Completed'|'Error'|'Timeout', 'message' => string]
 */
function monitorPrintJob(string $printerName, int $jobId, int $timeoutSeconds = 600): array
{
    // Augmente la limite de temps d'exécution du script
    set_time_limit($timeoutSeconds + 10);
    
    $startTime = time();

    while (time() - $startTime < $timeoutSeconds) {
        // Commande PowerShell pour obtenir le statut de la tâche en format JSON
        $psCommand = "powershell -ExecutionPolicy Bypass -Command \"Get-PrintJob -PrinterName '$printerName' -Id $jobId | Select-Object JobStatus | ConvertTo-Json\"";
        
        $output = shell_exec($psCommand);
        debugLog("monitorPrintJob: psCommand=$psCommand | output=" . var_export($output, true));
        $trimmedOutput = null;
        if (!empty($output)) {
            $trimmedOutput = trim($output);
        }

        // Si la commande ne retourne rien, la tâche n'existe plus = terminée.
        if (empty($output) || empty($trimmedOutput)) {
            debugLog("monitorPrintJob: JobId $jobId not found in spooler anymore -> Completed");
            return ['status' => 'Completed', 'message' => 'Tâche terminée avec succès.'];
        }

        // Décoder le JSON pour analyser le statut
        $jobInfo = json_decode($trimmedOutput, true);

        if (isset($jobInfo['JobStatus'])) {
            $status = $jobInfo['JobStatus'];

            // Vérifier les statuts d'erreur connus
            $errorStates = ['Error', 'PaperOut', 'Offline', 'Paused', 'UserIntervention'];
            foreach ($errorStates as $errorState) {
                if (stripos($status, $errorState) !== false) {
                    // Erreur détectée, on arrête la surveillance et on renvoie le message
                    return ['status' => 'Error', 'message' => "Erreur d'impression : $status"];
                }
            }
        }

        // Fait une pause de 5 secondes avant la prochaine vérification
        sleep(5);
    }

    // Si la boucle se termine, c'est que le timeout a été atteint
    return ['status' => 'Timeout', 'message' => 'Le suivi de la tâche a expiré (timeout).'];
}