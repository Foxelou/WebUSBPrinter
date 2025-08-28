$deviceManager = New-Object -ComObject WIA.DeviceManager
$device = $deviceManager.DeviceInfos | Where-Object { $_.Type -eq 1 } | Select-Object -First 1
if ($device -eq $null) { exit 1 }
$connectedDevice = $device.Connect()
$item = $connectedDevice.Items | Select-Object -First 1
$img = $item.Transfer("{B96B3CAB-0728-11D3-9D7B-0000F81EF32E}")
$img.SaveFile("C:\wamp64\www\printer_project/scans/scan_2025-08-04_13-36-42.jpg")