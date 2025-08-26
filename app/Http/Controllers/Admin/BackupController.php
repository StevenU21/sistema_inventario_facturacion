<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use File;

class BackupController extends Controller
{
    protected $backupPath = 'C:\\SQLBackups';

    public function index()
    {
        $filter = request('type');
        $backups = File::files($this->backupPath);

        $files = [];
        foreach ($backups as $file) {
            $filename = $file->getFilename();
            $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

            // Filtrar por tipo si se especifica
            if ($filter === 'full' && $extension !== 'bak') {
                continue;
            }
            if ($filter === 'diff' && $extension !== 'diff') {
                continue;
            }
            if ($filter === 'log' && $extension !== 'trn') {
                continue;
            }

            $files[] = [
                'name' => $filename,
                'size' => $file->getSize(),
                'last_modified' => date('Y-m-d H:i:s', $file->getMTime()),
            ];
        }

        usort($files, fn($a, $b) => strtotime($b['last_modified']) - strtotime($a['last_modified']));

        return view('admin.backups.index', compact('files', 'filter'));
    }
}
