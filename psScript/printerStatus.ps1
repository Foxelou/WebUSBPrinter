function Get-PrinterCompleteStatus {
    param($PrinterName)
    
    try {
        $printer = Get-Printer -Name $PrinterName
        $printerWMI = Get-WmiObject -Class Win32_Printer -Filter "Name='$PrinterName'"
        
        # Déterminer le statut réel
        if ($printerWMI.WorkOffline -eq $true) {
            $status = "OFFLINE"
            $message = "Imprimante hors ligne (éteinte)"
        } elseif ($printer.PrinterStatus -eq "PaperOut") {
            $status = "PAPER_OUT"
            $message = "Plus de papier"
        } elseif ($printer.PrinterStatus -eq "Normal") {
            $status = "ONLINE"
            $message = "Prête à imprimer"
        } elseif ($printer.PrinterStatus -eq "Error") {
            $status = "ERROR"
            $message = "Erreur imprimante"
        } else {
            $status = "UNKNOWN"
            $message = "Statut inconnu: $($printer.PrinterStatus)"
        }
        
        return @{
            Name = $PrinterName
            Status = $status
            Message = $message
            PrinterStatus = $printer.PrinterStatus
            PrinterError = $printerWMI.PrinterStatus
            WorkOffline = $printerWMI.WorkOffline
            LastChecked = Get-Date -Format "yyyy-MM-dd HH:mm:ss"
        }
    } catch {
        return @{
            Name = $PrinterName
            Status = "NOT_FOUND"
            Message = "Imprimante non trouvée"
            Error = $_.Exception.Message
            LastChecked = Get-Date -Format "yyyy-MM-dd HH:mm:ss"
        }
    }
}

# Test
Get-PrinterCompleteStatus "Canon MG2500 series Printer" | ConvertTo-Json