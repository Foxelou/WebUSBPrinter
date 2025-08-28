
# Scan command
```
curl -X POST http://localhost/WebUSBPrinter/scan_action.php
```

## Return

* {"success":true,"file":"scans\/scan_2025-08-22_12-40-01.jpg","settings":{"resolution":300,"colorMode":"color","format":"jpeg","paperSize":"A4"}}

<br>
<br>

# Print command
```
curl -X POST http://localhost/WebUSBPrinter/scan_action.php ^
    -F "pdf=@c:\Users\...\blank.pdf ^
    -F "printer=MyPrinterName"
```

## Return

* --- HTML --- (TODO: separate the action from printing in a new print-action.php file.)
* result (success/error)