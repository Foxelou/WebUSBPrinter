@echo off
echo Lancement d'une impression via curl...

REM Vérification du fichier PDF
if "%~1"=="" (
  echo Erreur: Vous devez indiquer le chemin du fichier PDF en premier argument.
  echo Exemple: print_curl.bat "C:\docs\monfichier.pdf" MyPrinter color duplex portrait A4 all 2
  pause
  exit /b
)

set PDF=%~1
shift

REM Paramètres par défaut
set PRINTER=MyPrinter
set COLOR=color
set DUPLEX=simplex
set ORIENT=portrait
set PAPER=A4
set PAGES=all
set COPIES=1

REM Traitement des arguments (décalés après le PDF)
if not "%~1"=="" set PRINTER=%~1
if not "%~2"=="" set COLOR=%~2
if not "%~3"=="" set DUPLEX=%~3
if not "%~4"=="" set ORIENT=%~4
if not "%~5"=="" set PAPER=%~5
if not "%~6"=="" set PAGES=%~6
if not "%~7"=="" set COPIES=%~7

REM Affichage des paramètres
echo Fichier PDF: %PDF%
echo Imprimante: %PRINTER%
echo Mode couleur: %COLOR%
echo Recto/Verso: %DUPLEX%
echo Orientation: %ORIENT%
echo Format: %PAPER%
echo Pages: %PAGES%
echo Copies: %COPIES%

REM Exécution de la commande curl
curl -X POST http://localhost/WebUSBPrinter/print.php ^
  -F "pdf=@%PDF%" ^
  -F "printer=%PRINTER%" ^
  -F "color=%COLOR%" ^
  -F "duplex=%DUPLEX%" ^
  -F "orientation=%ORIENT%" ^
  -F "paper=%PAPER%" ^
  -F "pagePreset=%PAGES%" ^
  -F "copies=%COPIES%"

echo.
echo Impression lancée. Vérifiez l'historique des impressions.
pause
