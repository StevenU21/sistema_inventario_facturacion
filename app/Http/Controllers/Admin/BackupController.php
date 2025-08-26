<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Backup;

class BackupController extends Controller
{
    protected $backupPath = 'C:\\SQLBackups';

    public function index()
    {
        $filter = request('type');
        $backups = Backup::all($this->backupPath);
        $filtered = Backup::filterByType($backups, $filter);
        $files = Backup::paginate($filtered, 10);
        return view('admin.backups.index', [
            'files' => $files,
            'filter' => $filter,
        ]);
    }
}
