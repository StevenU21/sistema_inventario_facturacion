<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Backup;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class BackupController extends Controller
{
    use AuthorizesRequests;
    protected $backupPath = 'C:\\SQLBackups';

    public function index()
    {
        $this->authorize('viewAny', Backup::class);
        $filter = request('type');
        $backups = Backup::all($this->backupPath);
        $filtered = Backup::filterByType($backups, $filter);
        $files = collect($filtered)->sortByDesc('created_at')->paginate(10);
        return view('admin.backups.index', [
            'files' => $files,
            'filter' => $filter,
        ]);
    }
}