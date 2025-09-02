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
    public function storeLocal(Model $model, UploadedFile $uploadedFile)
    {
        $folder = strtolower(class_basename($model));
        $storedPath = $uploadedFile->store($folder, 'public');
        return $storedPath ?: null;
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
    /**
     * Update a file locally if present in the request, and return the new path or null.
     *
     * @param Model $model
     * @param string $attribute
     * @param \Illuminate\Http\Request $request
     * @return string|null
     */
    public function updateLocal(Model $model, string $attribute, $request): string|null
    {
        if ($request->hasFile($attribute)) {
            if (!empty($model->$attribute)) {
                Storage::disk('public')->delete($model->$attribute);
            }
            $uploadedFile = $request->file($attribute);
            $storedPath = $this->storeLocal($model, $uploadedFile);
            return $storedPath ?: null;
        }
        return null;
    }

    public function deleteLocal(Model $model, $file_attribute)
    {
        if (!empty($model->$file_attribute)) {
            return Storage::disk('public')->delete($model->$file_attribute);
        }
        return false;
    }
}
