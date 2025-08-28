# Scan avec compression JPEG
$deviceManager = New-Object -ComObject WIA.DeviceManager
$device = $deviceManager.DeviceInfos | Where-Object { $_.Type -eq 1 } | Select-Object -First 1
if ($device -eq $null) { exit 1 }
$connectedDevice = $device.Connect()
$item = $connectedDevice.Items | Select-Object -First 1

# Réglages
$item.Properties["6147"].Value = 150  # DPI horizontal
$item.Properties["6148"].Value = 150  # DPI vertical
$item.Properties["6146"].Value = 2    # Grayscale Format de couleur : 1 = Noir & Blanc, 2 = niveaux de gris, 3 = couleur
$item.Properties["6151"].Value = 1240 # Largeur en pixels
$item.Properties["6152"].Value = 1754 # Hauteur en pixels

# Scan en JPEG
$jpegPath = "C:\wamp64\www\printer_project\scans\scan_temp.jpg"
# BMP : {B96B3CAB-0728-11D3-9D7B-0000F81EF32E} (par défaut, très lourd)
# JPEG : {B96B3CAE-0728-11D3-9D7B-0000F81EF32E} (compressé)
# PNG : {B96B3CAF-0728-11D3-9D7B-0000F81EF32E} (compressé, sans perte)
$img = $item.Transfer("{B96B3CAE-0728-11D3-9D7B-0000F81EF32E}")
$img.SaveFile($jpegPath)

# Convertir en PDF
#Add-Type -AssemblyName System.Drawing
#Add-Type -AssemblyName System.Windows.Forms
#Add-Type -AssemblyName System.IO.Compression.FileSystem
#Add-Type -AssemblyName System.Drawing.Common

#Add-Type -TypeDefinition @"
#using System.Drawing;
#using System.Drawing.Imaging;
#using System.IO;
#using System;
#using System.Drawing.Printing;
#using System.Runtime.InteropServices;
#using System.Windows.Forms;
#public class PDFExporter {
#    public static void ExportToPdf(string imagePath, string outputPdfPath) {
#        using (var doc = new System.Drawing.Printing.PrintDocument()) {
#            doc.PrintPage += (sender, e) => {
#                using (Image img = Image.FromFile(imagePath)) {
#                    e.Graphics.DrawImage(img, e.MarginBounds);
#                }
#            };
#            doc.PrinterSettings.PrintToFile = true;
#            doc.PrinterSettings.PrintFileName = outputPdfPath;
#            doc.Print();
#        }
#    }
#}
#"@

## Génère un PDF
#$outputPdf = "C:\wamp64\www\printer_project\scans\scan_2025-08-04_13-36-42.pdf"
#[PDFExporter]::ExportToPdf($jpegPath, $outputPdf)

# Supprimer le JPEG temporaire (optionnel)
#Remove-Item $jpegPath
