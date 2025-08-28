<!-- Printer Status -->
<div id="printerStatus" class="form-group printer-status">
    <div class="status-header">
        <h3>Statut de l'imprimante</h3>
        <button type="button" id="refreshButton" onclick="refreshPrinterStatus()" class="refresh-button">
            Actualiser
        </button>
    </div>

    <div id="statusLoading" class="status-loading" style="display: none;">
        <div class="loading-spinner"></div>
        <span style="margin-left: 0.5rem; font-size: 0.875rem; color: var(--muted-foreground);">Vérification du statut...</span>
    </div>

    <div id="statusContent" class="status-content">
        <!-- Status Info -->
        <div class="status-info">
            <div class="status-indicator">
                <svg id="statusIcon" class="icon status-online" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0" />
                </svg>
                <span id="statusText" class="status-online">En ligne</span>
            </div>
            <span id="statusTime" class="status-time">Mis à jour: 14:32:15</span>
        </div>

        <!-- Error Message -->
        <div id="errorMessage" class="error-message" style="display: none;">
            <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
            </svg>
            <span id="errorText" class="error-text">Message d'erreur</span>
        </div>
    </div>
</div>