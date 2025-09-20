<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class FileNativeService
{
    public function storeLocal(Model $model, UploadedFile $uploadedFile): ?string
    {
        $folder = strtolower(class_basename($model));
        // usar disco local en vez de public, para que se guarde en appData/storage/app
        $storedPath = $uploadedFile->store($folder, 'local');
        return $storedPath ?: null;
    }

    public function updateLocal(Model $model, string $attribute, $request): ?string
    {
        if ($request->hasFile($attribute)) {
            if (!empty($model->$attribute) && Storage::disk('local')->exists($model->$attribute)) {
                Storage::disk('local')->delete($model->$attribute);
            }
            $uploadedFile = $request->file($attribute);
            $storedPath = $this->storeLocal($model, $uploadedFile);
            return $storedPath ?: null;
        }
        return null;
    }

    public function deleteLocal(Model $model, $file_attribute): bool
    {
        if (!empty($model->$file_attribute) && Storage::disk('local')->exists($model->$file_attribute)) {
            return Storage::disk('local')->delete($model->$file_attribute);
        }
        return false;
    }
}
