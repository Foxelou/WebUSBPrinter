<?php

class History {
    private string $file;
    private string $status;
    private string $statusMessage;
    private array $settings;

    private array $config;
    
    public function __construct(
        string $file,
        string $status,
        string $statusMessage,
        array $settings,
    ) {
        $this->config = require __DIR__ . '/config.php';
        $this->file = $file;
        $this->status = $status;
        $this->statusMessage = $statusMessage;
        $this->settings = $settings;
    }

    public function logScanHistory(): void {
        $historyDir = $this->config['paths']['history_dir'];

        if (!is_dir($historyDir)) {
            mkdir($historyDir, 0777, true);
        }

        $logEntry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'file' => basename($this->file),
            'status' => $this->status,
            'statusMessage' => $this->statusMessage,
            'settings' => $this->settings
        ];

        $historyFile = $historyDir . 'scan_history.json';

        // Read existing logs or create empty array
        $existingLogs = [];
        if (file_exists($historyFile)) {
            $content = file_get_contents($historyFile);
            if (!empty($content)) {
                $existingLogs = json_decode($content, true) ?: [];
            }
        }

        // Add new log entry
        $existingLogs[] = $logEntry;

        // Write back all logs as JSON array
        file_put_contents($historyFile, json_encode($existingLogs, JSON_PRETTY_PRINT));
    }


    public function logPrintHistory(): void {
        $historyDir = $this->config['paths']['history_dir'];

        if (!is_dir($historyDir)) {
            mkdir($historyDir, 0777, true);
        }

        $logEntry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'file' => basename($this->file),
            'status' => $this->status,
            'settings' => $this->settings
        ];

        $historyFile = $historyDir . 'print_history.json';

        // Read existing logs or create empty array
        $existingLogs = [];
        if (file_exists($historyFile)) {
            $content = file_get_contents($historyFile);
            if (!empty($content)) {
                $existingLogs = json_decode($content, true) ?: [];
            }
        }

        // Add new log entry
        $existingLogs[] = $logEntry;

        // Write back all logs as JSON array
        file_put_contents($historyFile, json_encode($existingLogs, JSON_PRETTY_PRINT));
    }

}