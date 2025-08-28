$deviceManager = New-Object -ComObject WIA.DeviceManager
$device = $deviceManager.DeviceInfos | Where-Object { $_.Type -eq 1 } | Select-Object -First 1

if ($device -eq $null) { 
    Write-Error "Aucun scanner n'a été trouvé."
    exit 1 
}

$connectedDevice = $device.Connect()

# Configurer les propriétés du scanner
$item = $connectedDevice.Items | Select-Object -First 1

# Définir la résolution
try {
    $item.Properties | Where-Object { $_.Name -eq 'Horizontal Resolution' } | ForEach-Object { $_.Value = 300 }
    $item.Properties | Where-Object { $_.Name -eq 'Vertical Resolution' } | ForEach-Object { $_.Value = 300 }
} catch {
    Write-Warning "Impossible de définir la résolution: $_"
}

# Définir le mode couleur
try {
    switch ("color") {
        "color" {
            $item.Properties | Where-Object { $_.Name -eq 'Current Intent' } | ForEach-Object { $_.Value = 1 } # WIA_INTENT_IMAGE_TYPE_COLOR
        }
        "grayscale" {
            $item.Properties | Where-Object { $_.Name -eq 'Current Intent' } | ForEach-Object { $_.Value = 2 } # WIA_INTENT_IMAGE_TYPE_GRAYSCALE
        }
        "blackwhite" {
            $item.Properties | Where-Object { $_.Name -eq 'Current Intent' } | ForEach-Object { $_.Value = 4 } # WIA_INTENT_IMAGE_TYPE_TEXT
        }
    }
} catch {
    Write-Warning "Impossible de définir le mode couleur: $_"
}

# Définir la taille du papier si possible
try {
    switch ("A4") {
        "A4" {
            # A4: 210 x 297 mm
            $item.Properties | Where-Object { $_.Name -eq 'Page Size' } | ForEach-Object { $_.Value = 0 } # WIA_PAGE_A4
        }
        "A3" {
            # A3: 297 x 420 mm
            $item.Properties | Where-Object { $_.Name -eq 'Page Size' } | ForEach-Object { $_.Value = 1 } # WIA_PAGE_A3
        }
        "Letter" {
            # Letter: 8.5 x 11 inches
            $item.Properties | Where-Object { $_.Name -eq 'Page Size' } | ForEach-Object { $_.Value = 2 } # WIA_PAGE_LETTER
        }
    }
} catch {
    Write-Warning "Impossible de définir la taille du papier: $_"
}

# Effectuer la numérisation
$img = $item.Transfer("{B96B3CAE-0728-11D3-9D7B-0000F81EF32E}")
# Sauvegarder directement dans le format demandé
$img.SaveFile("C:\wamp64\www\printer_project/scans/scan_2025-08-20_12-36-37.jpg")