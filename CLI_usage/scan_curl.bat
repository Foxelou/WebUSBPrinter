@echo off
echo Lancement d'une numérisation via curl...

REM Paramètres par défaut
set RESOLUTION=300
set COLOR=color
set FORMAT=jpeg
set PAPER=A4

REM Traitement des arguments
if not "%1"=="" set RESOLUTION=%1
if not "%2"=="" set COLOR=%2
if not "%3"=="" set FORMAT=%3
if not "%4"=="" set PAPER=%4

REM Affichage des paramètres
echo Résolution: %RESOLUTION%
echo Mode couleur: %COLOR%
echo Format: %FORMAT%
echo Taille papier: %PAPER%

REM Exécution de la commande curl
curl -X POST http://localhost/WebUSBPrinter/scan_action.php ^
  -d "resolution=%RESOLUTION%" ^
  -d "color=%COLOR%" ^
  -d "format=%FORMAT%" ^
  -d "paper=%PAPER%"

echo.
echo Numérisation terminée. Vérifiez le dossier des scans.
pause