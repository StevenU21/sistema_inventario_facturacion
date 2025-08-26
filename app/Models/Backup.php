<?php

namespace App\Models;

use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use File;

class Backup
{
    public $name;
    public $size;
    public $last_modified;
    public $extension;

    public function __construct($name, $size, $last_modified, $extension)
    {
        $this->name = $name;
        $this->size = $size;
        $this->last_modified = $last_modified;
        $this->extension = $extension;
    }

    public static function all($path)
    {
        $files = collect(File::files($path))->map(function ($file) {
            $name = $file->getFilename();
            $size = $file->getSize();
            $last_modified = date('Y-m-d H:i:s', $file->getMTime());
            $extension = strtolower(pathinfo($name, PATHINFO_EXTENSION));
            return new self($name, $size, $last_modified, $extension);
        });
        return $files->sortByDesc('last_modified')->values();
    }

    public static function filterByType(Collection $backups, $type)
    {
        if ($type === 'full') {
            return $backups->where('extension', 'bak');
        }
        if ($type === 'diff') {
            return $backups->where('extension', 'diff');
        }
        if ($type === 'log') {
            return $backups->where('extension', 'trn');
        }
        return $backups;
    }

    public static function paginate(Collection $backups, $perPage = 10)
    {
        $page = request()->input('page', 1);
        $total = $backups->count();
        $results = $backups->slice(($page - 1) * $perPage, $perPage)->values();
        return new LengthAwarePaginator(
            $results,
            $total,
            $perPage,
            $page,
            [
                'path' => request()->url(),
                'query' => request()->query(),
            ]
        );
    }
}
