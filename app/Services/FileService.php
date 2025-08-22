<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class FileService
{
    protected UploadedFile $file;

    /**
     * Store a file locally and return the public URL to be saved in the database.
     *
     * @param Model $model
     * @param string $file_attribute
     * @param UploadedFile $file
     * @param string|null $folder
     * @return string|null  The public URL of the stored file or null on failure.
     */
    public function storeLocal(Model $model, string $file_attribute, UploadedFile $file, string $folder = null)
    {
        $modelName = strtolower(class_basename($model));
        $folderName = $modelName . '_' . $file_attribute;
        return $file->store($folder ?? $folderName, 'public');
    }

    /**
     * Update a file locally and update the model's file attribute using Laravel's store method.
     *
     * @param Model $model
     * @param string $file_attribute
     * @param UploadedFile $file
     * @param string $folder
     * @return bool
     */
    public function updateLocal(Model $model, string $file_attribute, UploadedFile $file, string $folder = null): bool
    {
        if (!empty($model->$file_attribute)) {
            Storage::disk('public')->delete($model->$file_attribute);
        }
        $stored = $this->storeLocal($model, $file_attribute, $file, $folder);
        return is_string($stored) && !empty($stored);
    }

    public function deleteLocal(Model $model, $file_attribute)
    {
        if (!empty($model->$file_attribute)) {
            return Storage::disk('public')->delete($model->$file_attribute);
        }
        return false;
    }
}
