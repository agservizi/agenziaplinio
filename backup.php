<?php
/**
 * Script di backup automatico database e files
 * Eseguire con cron: 0 2 * * * php /path/to/backup.php
 */

require __DIR__ . '/includes/env.php';
require __DIR__ . '/includes/database.php';

$backupDir = __DIR__ . '/../backups/';
if (!is_dir($backupDir)) {
    mkdir($backupDir, 0755, true);
}

$date = date('Y-m-d_H-i-s');
$dbFile = $backupDir . 'db_' . $date . '.sql';
$filesZip = $backupDir . 'files_' . $date . '.zip';

// Backup database
$pdo = ap_db();
$tables = $pdo->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);

$backup = '';
foreach ($tables as $table) {
    $backup .= "DROP TABLE IF EXISTS `$table`;\n";
    $create = $pdo->query("SHOW CREATE TABLE `$table`")->fetch();
    $backup .= $create['Create Table'] . ";\n\n";

    $rows = $pdo->query("SELECT * FROM `$table`");
    while ($row = $rows->fetch(PDO::FETCH_ASSOC)) {
        $backup .= "INSERT INTO `$table` VALUES (";
        $values = array_map(function($value) use ($pdo) {
            return $value === null ? 'NULL' : $pdo->quote($value);
        }, $row);
        $backup .= implode(',', $values) . ");\n";
    }
    $backup .= "\n";
}

file_put_contents($dbFile, $backup);

// Backup files (uploads)
$zip = new ZipArchive();
if ($zip->open($filesZip, ZipArchive::CREATE) === TRUE) {
    $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(__DIR__ . '/../uploads/'));
    foreach ($files as $file) {
        if (!$file->isDir()) {
            $zip->addFile($file->getRealPath(), str_replace(__DIR__ . '/../uploads/', '', $file->getRealPath()));
        }
    }
    $zip->close();
}

// Pulizia vecchi backup (mantieni ultimi 30)
$files = glob($backupDir . '*');
usort($files, function($a, $b) {
    return filemtime($b) - filemtime($a);
});
$toDelete = array_slice($files, 30);
foreach ($toDelete as $file) {
    unlink($file);
}

echo "Backup completato: $dbFile, $filesZip\n";