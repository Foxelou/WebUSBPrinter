const statusMessages = {
    offline: {
        text: "Imprimante hors ligne (éteinte)",
        class: "status-offline",
        icon: `<path d="M12 20h.01"></path>
                <path d="M8.5 16.429a5 5 0 0 1 7 0"></path>
                <path d="M5 12.859a10 10 0 0 1 5.17-2.69"></path>
                <path d="M19 12.859a10 10 0 0 0-2.007-1.523"></path>
                <path d="M2 8.82a15 15 0 0 1 4.177-2.643"></path>
                <path d="M22 8.82a15 15 0 0 0-11.288-3.764"></path>
                <path d="m2 2 20 20"></path>`
    },
    ready: {
        text: "Imprimante prête à imprimer",
        class: "status-online",
        icon: `<path d="M12 20h.01"></path>
                <path d="M2 8.82a15 15 0 0 1 20 0"></path>
                <path d="M5 12.859a10 10 0 0 1 14 0"></path>
                <path d="M8.5 16.429a5 5 0 0 1 7 0"></path>`
    },
    paper_out: {
        text: "Plus de papier dans l'imprimante",
        class: "status-warning",
        icon: `<path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"></path>
                <path d="M14 2v4a2 2 0 0 0 2 2h4"></path>
                <path d="m14.5 12.5-5 5"></path>
                <path d="m9.5 12.5 5 5"></path>`
    },
    error: {
        text: "Erreur imprimante",
        class: "status-offline",
        icon: `<circle cx="12" cy="12" r="10"></circle>
                <line x1="12" x2="12" y1="8" y2="12"></line>
                <line x1="12" x2="12.01" y1="16" y2="16"></line>`
    },
    not_found: {
        text: "Imprimante non trouvée",
        class: "status-offline",
        icon: `<path d="M12 20h.01"></path>
                <path d="M8.5 16.429a5 5 0 0 1 7 0"></path>
                <path d="M5 12.859a10 10 0 0 1 5.17-2.69"></path>
                <path d="M19 12.859a10 10 0 0 0-2.007-1.523"></path>
                <path d="M2 8.82a15 15 0 0 1 4.177-2.643"></path>
                <path d="M22 8.82a15 15 0 0 0-11.288-3.764"></path>
                <path d="m2 2 20 20"></path>`
    },
    busy: {
        text: "Imprimante occupée",
        class: "status-busy",
        icon: `<circle cx="12" cy="12" r="10"></circle>
                <polyline points="12 6 12 12 16 14"></polyline>`
    },
    toner_low: {
        text: "Toner faible",
        class: "status-warning",
        icon: `<path d="M7 16.3c2.2 0 4-1.83 4-4.05 0-1.16-.57-2.26-1.71-3.19S7.29 6.75 7 5.3c-.29 1.45-1.14 2.84-2.29 3.76S3 11.1 3 12.25c0 2.22 1.8 4.05 4 4.05z"></path>
                <path d="M12.56 6.6A10.97 10.97 0 0 0 14 3.02c.5 2.5 2 4.9 4 6.5s3 3.5 3 5.5a6.98 6.98 0 0 1-11.91 4.97"></path>`
    },
    door_open: {
        text: "Porte ouverte",
        class: "status-error",
        icon: `<path d="M13 4h3a2 2 0 0 1 2 2v14"></path>
                <path d="M2 20h3"></path>
                <path d="M13 20h9"></path>
                <path d="M10 12v.01"></path>
                <path d="M13 4.562v16.157a1 1 0 0 1-1.242.97L5 20V5.562a2 2 0 0 1 1.515-1.94l4-1A2 2 0 0 1 13 4.561Z"></path>`
    },
    paper_jam: {
        text: "Bourrage papier",
        class: "status-error",
        icon: `<path d="M21 10V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l2-1.14"></path>
                <path d="m7.5 4.27 9 5.15"></path>
                <polyline points="3.29 7 12 12 20.71 7"></polyline>
                <line x1="12" x2="12" y1="22" y2="12"></line>
                <path d="m17 13 5 5m-5 0 5-5"></path>`
    },
    paper_problem: {
        text: "Problème de papier",
        class: "status-warning",
        icon: `<path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"></path>
                <path d="M14 2v4a2 2 0 0 0 2 2h4"></path>
                <path d="M10 9H8"></path>
                <path d="M16 13H8"></path>
                <path d="M16 17H8"></path>`
    },
    output_full: {
        text: "Bac de sortie plein",
        class: "status-warning",
        icon: `<path d="M21 10V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l2-1.14"></path>
                <path d="m7.5 4.27 9 5.15"></path>
                <polyline points="3.29 7 12 12 20.71 7"></polyline>
                <line x1="12" x2="12" y1="22" y2="12"></line>
                <path d="m17 13 5 5m-5 0 5-5"></path>`
    },
    paused: {
        text: "Impression en pause",
        class: "status-warning",
        icon: `<rect x="14" y="4" width="4" height="16" rx="1"></rect>
                <rect x="6" y="4" width="4" height="16" rx="1"></rect>`
    },
    waiting: {
        text: "En attente",
        class: "status-info",
        icon: `<circle cx="12" cy="12" r="10"></circle>
                <polyline points="12 6 12 12 16 14"></polyline>`
    },
    processing: {
        text: "En cours de traitement",
        class: "status-info",
        icon: `<path d="M21 12a9 9 0 1 1-9-9c2.52 0 4.93 1 6.74 2.74L21 8"></path>
                <path d="M21 3v5h-5"></path>`
    },
    initializing: {
        text: "Initialisation",
        class: "status-info",
        icon: `<path d="M21 12a9 9 0 1 1-9-9c2.52 0 4.93 1 6.74 2.74L21 8"></path>
                <path d="M21 3v5h-5"></path>`
    },
    warming_up: {
        text: "Préparation",
        class: "status-info",
        icon: `<path d="M4 14a1 1 0 0 1-.78-1.63l9.9-10.2a.5.5 0 0 1 .86.46l-1.92 6.02A1 1 0 0 0 13 10h7a1 1 0 0 1 .78 1.63l-9.9 10.2a.5.5 0 0 1-.86-.46l1.92-6.02A1 1 0 0 0 11 14z"></path>`
    },
    maintenance: {
        text: "Maintenance",
        class: "status-maintenance",
        icon: `<path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"></path>`
    }
};