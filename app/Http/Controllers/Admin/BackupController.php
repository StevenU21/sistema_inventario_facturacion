<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Backup;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

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

    public function restore(Request $request)
    {
        $filename = $request->input('file');
        if (!$filename) {
            return back()->with('error', 'Archivo de backup no especificado.');
        }

        $backupPath = $this->backupPath . DIRECTORY_SEPARATOR . $filename;
        if (!file_exists($backupPath)) {
            return back()->with('error', 'El archivo de backup no existe.');
        }

        $database = config('database.connections.sqlsrv.database');
        $setSingleUser = "ALTER DATABASE [$database] SET SINGLE_USER WITH ROLLBACK IMMEDIATE;";
        $restore = "RESTORE DATABASE [$database] FROM DISK = N'$backupPath' WITH REPLACE;";
        $setMultiUser = "ALTER DATABASE [$database] SET MULTI_USER;";

        try {
            DB::connection('sqlsrv')->statement($setSingleUser);
            DB::connection('sqlsrv')->statement($restore);
            DB::connection('sqlsrv')->statement($setMultiUser);
        } catch (\Exception $e) {
            return back()->with('error', 'Error al restaurar: ' . $e->getMessage());
        }

        return back()->with('success', 'La base de datos fue restaurada correctamente desde el backup seleccionado.');
    }
}
