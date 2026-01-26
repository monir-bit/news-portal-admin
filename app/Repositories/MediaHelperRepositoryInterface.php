<?php

namespace App\Repositories;

use Illuminate\Http\UploadedFile;

interface MediaHelperRepositoryInterface
{
    /* ================= UPLOAD ================= */

    public function upload(
        UploadedFile $file,
        string $path,
        string $disk = 'public',
        bool $watermark = false
    ): string;

    public function uploadMultiple(
        array $files,
        string $path,
        string $disk = 'public',
        bool $watermark = false
    ): array;

    public function get(string $path, string $disk = 'public'): ?string;

    public function all(string $path, string $disk = 'public'): array;

    public function delete(string $path, string $disk = 'public'): bool;

    public function deleteFile(string $filePath, string $disk = 'public'): bool;

    public function exists(string $path, string $disk = 'public'): bool;

    public function count(string $path, string $disk = 'public'): int;
}
