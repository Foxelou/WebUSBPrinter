<?php
return [
    // Imprimante favorite (nom exact ou ID système)
    'printer_favorite' => 'MyPrinter',

    // Répertoires importants
    'paths' => [
        'upload_dir' => __DIR__ . '/data/uploads/',
        'scan_dir' => __DIR__ . '/data/scans/',
        'log_dir' => __DIR__ . '/data/logs/',
        'history_dir' => __DIR__ . '/data/history/'
    ],

    // Options par défaut pour l’impression
    'print_options' => [
        'color' => 'color',         // 'color' or 'monochrome'
        'duplex' => 'simplex',      // 'simplex', 'duplex', 'duplexshort' and 'duplexlong'
        'orientation' => 'portrait' // 'portrait' or 'landscape'
    ],

    'security' => [
        // Enable or disable access to the main history page
        // true: accessible - false: returns 403 code (forbidden)
        'allow_history' => true,
        // Activer ou désactiver l'accès à la page d'historique des scans ou des impressions
        // true : accessible - false : renvoie un code 403 (interdit)
        'allow_scan_history' => true,
        'allow_print_history' => true,

        // true = fichiers récents cliquables sur le dashboard | false = noms affichés sans lien
        'show_recent_links_dashboard' => true,

        // Nombre de fichiers récents affichés sur dashboard
        'recent_files_display_limit' => 3,
    ],

    // Chemin vers SumatraPDF (Windows, pour impression silencieuse)
    'sumatra_path' =>  __DIR__ . '/tools/SumatraPDF-x.x.x-64/SumatraPDF-x.x.x-64.exe'
];
