<?php
declare(strict_types=1);

// Production cleanup script for Apotek Baraya
// - Move backup/test files to storage/temp
// - Remove junk files (.DS_Store, Thumbs.db)
// - Ensure storage/framework/testing is empty except .gitignore

$projectRoot = realpath(__DIR__ . '/..') ?: getcwd();
chdir($projectRoot);

$stamp = date('Ymd_His');
$archiveRoot = $projectRoot . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'temp';
if (!is_dir($archiveRoot)) {
    mkdir($archiveRoot, 0777, true);
}
$backupDir = $archiveRoot . DIRECTORY_SEPARATOR . 'archived_backups_' . $stamp;
if (!is_dir($backupDir)) {
    mkdir($backupDir, 0777, true);
}

function moveIfExists(string $from, string $toDir): void
{
    if (file_exists($from)) {
        if (!is_dir($toDir)) {
            mkdir($toDir, 0777, true);
        }
        $to = rtrim($toDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . basename($from);
        if (@rename($from, $to)) {
            echo "Moved: $from => $to\n";
        } else {
            echo "Failed move: $from\n";
        }
    }
}

// Move known backup files
moveIfExists('app/Livewire/PrescriptionUpload.backup.php', $backupDir);
moveIfExists('resources/views/livewire/prescription-upload.blade.php.backup', $backupDir);
moveIfExists('resources/views/livewire/prescription-upload.backup.blade.php', $backupDir);

// Move root test-image.svg if exists
$srcTestImg = $projectRoot . DIRECTORY_SEPARATOR . 'test-image.svg';
if (file_exists($srcTestImg)) {
    $to = $archiveRoot . DIRECTORY_SEPARATOR . 'archived_test-image_' . $stamp . '.svg';
    if (@rename($srcTestImg, $to)) {
        echo "Moved: test-image.svg => $to\n";
    } else {
        echo "Failed move: test-image.svg\n";
    }
}

// Delete junk files
$junkNames = ['.DS_Store', 'Thumbs.db'];
$deletedJunk = 0;
$deletedBakTmp = 0;
$it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($projectRoot, FilesystemIterator::SKIP_DOTS));
foreach ($it as $f) {
    $path = (string) $f;
    $bn = basename($path);
    // Name-based junk
    if (in_array($bn, $junkNames, true)) {
        if (@unlink($path)) {
            $deletedJunk++;
            echo "Deleted junk: $path\n";
        }
        continue;
    }
    // Extension-based junk (*.bak, *.tmp)
    $lower = strtolower($bn);
    if (str_ends_with($lower, '.bak') || str_ends_with($lower, '.tmp')) {
        if (@unlink($path)) {
            $deletedBakTmp++;
            echo "Deleted bak/tmp: $path\n";
        }
    }
}
echo "Junk deleted count: $deletedJunk; bak/tmp deleted count: $deletedBakTmp\n";

// Ensure storage/framework/testing only contains .gitignore
$testingDir = $projectRoot . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'framework' . DIRECTORY_SEPARATOR . 'testing';
if (is_dir($testingDir)) {
    foreach (glob($testingDir . DIRECTORY_SEPARATOR . '*') as $fp) {
        if (basename($fp) !== '.gitignore') {
            @unlink($fp);
            echo "Deleted testing file: $fp\n";
        }
    }
}

echo "Cleanup complete.\n";