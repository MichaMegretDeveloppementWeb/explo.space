<?php

namespace App\Http\Controllers\Admin\Http\Controllers\Admin;

use App\Helpers\UploadHelper;
use App\Http\Controllers\Controller;

class DiagnosticController extends Controller
{
    public function index()
    {
        // Vérifier les permissions du dossier livewire-tmp
        $livewireTmpPath = storage_path('app/livewire-tmp');
        $livewireTmpExists = is_dir($livewireTmpPath);
        $livewireTmpWritable = is_writable($livewireTmpPath);

        // Vérifier les permissions du dossier storage/app
        $storageAppPath = storage_path('app');
        $storageAppWritable = is_writable($storageAppPath);

        // Configuration PHP
        $phpConfig = [
            'upload_max_filesize' => ini_get('upload_max_filesize').' ('.UploadHelper::getPhpUploadMaxSizeMB().' MB)',
            'post_max_size' => ini_get('post_max_size').' ('.UploadHelper::getPostMaxSizeMB().' MB)',
            'max_execution_time' => ini_get('max_execution_time').' secondes',
            'max_input_time' => ini_get('max_input_time').' secondes',
            'memory_limit' => ini_get('memory_limit'),
            'max_file_uploads' => ini_get('max_file_uploads'),
        ];

        // Configuration Laravel/Livewire
        $laravelConfig = [
            'upload.images.max_size_kb' => config('upload.images.max_size_kb').' KB ('.round(config('upload.images.max_size_kb') / 1024, 1).' MB)',
            'upload.images.max_files' => config('upload.images.max_files'),
            'livewire.temporary_file_upload.max_upload_time' => config('livewire.temporary_file_upload.max_upload_time').' minutes',
            'livewire.temporary_file_upload.disk' => config('livewire.temporary_file_upload.disk') ?? 'default (local)',
            'livewire.temporary_file_upload.directory' => config('livewire.temporary_file_upload.directory') ?? 'livewire-tmp',
            'livewire.temporary_file_upload.middleware' => config('livewire.temporary_file_upload.middleware') ?? 'throttle:60,1',
            'livewire.temporary_file_upload.cleanup' => config('livewire.temporary_file_upload.cleanup') ? 'Activé' : 'Désactivé',
        ];

        // Vérifications critiques
        $checks = [
            [
                'name' => 'Dossier livewire-tmp existe',
                'status' => $livewireTmpExists,
                'message' => $livewireTmpExists ? "Le dossier existe : $livewireTmpPath" : "Le dossier n'existe pas : $livewireTmpPath",
                'fix' => $livewireTmpExists ? null : "Créez le dossier avec : mkdir -p $livewireTmpPath && chmod 775 $livewireTmpPath",
            ],
            [
                'name' => 'Dossier livewire-tmp accessible en écriture',
                'status' => $livewireTmpWritable,
                'message' => $livewireTmpWritable ? 'Permissions correctes' : 'Permissions insuffisantes',
                'fix' => $livewireTmpWritable ? null : "Corrigez les permissions avec : chmod -R 775 $livewireTmpPath && chown -R www-data:www-data $livewireTmpPath",
            ],
            [
                'name' => 'Dossier storage/app accessible en écriture',
                'status' => $storageAppWritable,
                'message' => $storageAppWritable ? 'Permissions correctes' : 'Permissions insuffisantes',
                'fix' => $storageAppWritable ? null : "Corrigez les permissions avec : chmod -R 775 $storageAppPath && chown -R www-data:www-data $storageAppPath",
            ],
            [
                'name' => 'upload_max_filesize suffisant',
                'status' => UploadHelper::getPhpUploadMaxSizeMB() >= 10,
                'message' => 'Actuel : '.UploadHelper::getPhpUploadMaxSizeMB().' MB (recommandé : >= 10 MB)',
                'fix' => UploadHelper::getPhpUploadMaxSizeMB() >= 10 ? null : 'Augmentez upload_max_filesize dans php.ini à 10M minimum',
            ],
            [
                'name' => 'post_max_size suffisant',
                'status' => UploadHelper::getPostMaxSizeMB() >= 100,
                'message' => 'Actuel : '.UploadHelper::getPostMaxSizeMB().' MB (recommandé : >= 100 MB pour uploader plusieurs images)',
                'fix' => UploadHelper::getPostMaxSizeMB() >= 100 ? null : 'Augmentez post_max_size dans php.ini à 100M minimum',
            ],
            [
                'name' => 'max_execution_time suffisant',
                'status' => (int) ini_get('max_execution_time') === 0 || (int) ini_get('max_execution_time') >= 300,
                'message' => 'Actuel : '.ini_get('max_execution_time').' secondes (recommandé : 300 secondes ou 0 pour illimité)',
                'fix' => ((int) ini_get('max_execution_time') === 0 || (int) ini_get('max_execution_time') >= 300) ? null : 'Augmentez max_execution_time dans php.ini à 300 minimum',
            ],
            [
                'name' => 'memory_limit suffisant',
                'status' => $this->parseMemoryLimit(ini_get('memory_limit')) >= 256 * 1024 * 1024,
                'message' => 'Actuel : '.ini_get('memory_limit').' (recommandé : >= 256M)',
                'fix' => $this->parseMemoryLimit(ini_get('memory_limit')) >= 256 * 1024 * 1024 ? null : 'Augmentez memory_limit dans php.ini à 256M minimum',
            ],
        ];

        return view('admin.diagnostic.index', compact('phpConfig', 'laravelConfig', 'checks'));
    }

    private function parseMemoryLimit(string $limit): int
    {
        if ($limit === '-1') {
            return PHP_INT_MAX;
        }

        $unit = strtolower(substr($limit, -1));
        $value = (int) substr($limit, 0, -1);

        return match ($unit) {
            'g' => $value * 1024 * 1024 * 1024,
            'm' => $value * 1024 * 1024,
            'k' => $value * 1024,
            default => (int) $limit,
        };
    }
}
