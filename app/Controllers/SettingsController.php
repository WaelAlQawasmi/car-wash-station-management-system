<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Services\LanguageService;
use App\Repositories\AuditRepository;
use App\Core\Database;
use App\Core\Csrf;
use PDO;

class SettingsController extends Controller
{
    public function __construct(private readonly AuditRepository $auditRepository)
    {
    }

    public function index(): void
    {
        $auditLogs = $this->auditRepository->getAll(50);
        $backups = glob(__DIR__ . '/../../database/backups/*.sql') ?: [];
        $backupList = [];
        foreach ($backups as $file) {
            $backupList[] = [
                'filename' => basename($file),
                'size' => round(filesize($file) / 1024, 2) . ' KB',
                'created_at' => date('Y-m-d H:i:s', filemtime($file))
            ];
        }

        $this->view('settings/index', [
            'auditLogs' => $auditLogs,
            'backups' => $backupList
        ]);
    }

    public function setLanguage(): void
    {
        $lang = $_GET['lang'] ?? 'en';
        LanguageService::setLocale($lang);
        
        $referer = $_SERVER['HTTP_REFERER'] ?? '/dashboard';
        header("Location: $referer");
        exit;
    }

    public function backup(): void
    {
        if (!Csrf::validate($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Security validation failed.';
            header('Location: /settings');
            exit;
        }

        $backupDir = __DIR__ . '/../../database/backups';
        if (!is_dir($backupDir)) {
            mkdir($backupDir, 0755, true);
        }

        try {
            $pdo = Database::getConnection();
            $tables = ['users', 'branches', 'customers', 'vehicles', 'services', 'work_orders', 'inventory', 'stock_movements', 'audit_logs'];
            $sqlContent = "-- Car Stashen Backup\n-- Date: " . date('Y-m-d H:i:s') . "\n\n";

            foreach ($tables as $table) {
                // Get create table query
                $stmt = $pdo->query("SHOW CREATE TABLE `$table`");
                $row = $stmt->fetch(PDO::FETCH_NUM);
                $sqlContent .= "DROP TABLE IF EXISTS `$table`;\n" . $row[1] . ";\n\n";

                // Get insert data
                $stmtData = $pdo->query("SELECT * FROM `$table`");
                $rows = $stmtData->fetchAll(PDO::FETCH_ASSOC);

                if (!empty($rows)) {
                    $sqlContent .= "INSERT INTO `$table` VALUES \n";
                    $insertRows = [];
                    foreach ($rows as $dataRow) {
                        $values = array_map(function($val) use ($pdo) {
                            if ($val === null) return 'NULL';
                            return $pdo->quote($val);
                        }, $dataRow);
                        $insertRows[] = "(" . implode(", ", $values) . ")";
                    }
                    $sqlContent .= implode(",\n", $insertRows) . ";\n\n";
                }
            }

            $filename = 'backup_' . date('Ymd_His') . '_' . rand(1000,9999) . '.sql';
            $filePath = $backupDir . '/' . $filename;
            file_put_contents($filePath, $sqlContent);

            $userId = $_SESSION['user']['id'] ?? null;
            $this->auditRepository->log($userId, 'backup_database', 'database', null, ['filename' => $filename]);

            $_SESSION['success'] = "Database backup created successfully: $filename";
        } catch (\Exception $e) {
            $_SESSION['error'] = "Backup failed: " . $e->getMessage();
        }

        header('Location: /settings');
        exit;
    }

    public function restore(): void
    {
        if (!Csrf::validate($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Security validation failed.';
            header('Location: /settings');
            exit;
        }

        $filename = trim($_POST['filename'] ?? '');
        $filePath = __DIR__ . '/../../database/backups/' . $filename;

        if (empty($filename) || !is_file($filePath)) {
            $_SESSION['error'] = 'Invalid backup file selected.';
            header('Location: /settings');
            exit;
        }

        try {
            $pdo = Database::getConnection();
            $sql = file_get_contents($filePath);

            // Execute restore
            $pdo->exec($sql);

            $userId = $_SESSION['user']['id'] ?? null;
            $this->auditRepository->log($userId, 'restore_database', 'database', null, ['filename' => $filename]);

            $_SESSION['success'] = "Database restored successfully from: $filename";
        } catch (\Exception $e) {
            $_SESSION['error'] = "Restore failed: " . $e->getMessage();
        }

        header('Location: /settings');
        exit;
    }
}
