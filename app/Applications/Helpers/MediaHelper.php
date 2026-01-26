<?php

namespace App\Applications\Helpers;

use App\Repositories\MediaHelperRepositoryInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class MediaHelper implements MediaHelperRepositoryInterface
{
    protected ImageManager $manager;

    public function __construct()
    {
        // GD driver (safe for almost all servers)
        $this->manager = new ImageManager(new Driver());
    }

    /* ================= UPLOAD ================= */

    /**
     * Upload single image
     * Returns ONLY relative path (no domain)
     */
    public function upload(
        UploadedFile $file,
        string $path,
        string $disk = 'public',
        bool $watermark = false
    ): string {
        $filename = $this->uniqueName($file);

        // Read image
        $image = $this->manager->read($file->getPathname());

        // Smart resize (max 2000px, aspect ratio safe)
        $image->scaleDown(2000);

        // Optional watermark
        if ($watermark) {
            $this->applyWatermark($image);
        }

        // Save to disk (local / s3 / others)
        Storage::disk($disk)->put(
            "{$path}/{$filename}",
            $image->encode()->toString(),
            'public'
        );

        // ✅ Return RELATIVE PATH only
        return "{$path}/{$filename}";
    }

    /**
     * Upload multiple images
     */
    public function uploadMultiple(
        array $files,
        string $path,
        string $disk = 'public',
        bool $watermark = false
    ): array {
        $paths = [];

        foreach ($files as $file) {
            if ($file instanceof UploadedFile) {
                $paths[] = $this->upload($file, $path, $disk, $watermark);
            }
        }

        return $paths;
    }

    /* ================= GET ================= */

    /**
     * Get first image URL from a path
     */
    public function get(string $path, string $disk = 'public'): ?string
    {
        $all = $this->all($path, $disk);
        return $all[0] ?? null;
    }

    /**
     * Get all image URLs from a path
     */
    public function all(string $path, string $disk = 'public'): array
    {
        if (!Storage::disk($disk)->exists($path)) {
            return [];
        }

        return collect(Storage::disk($disk)->files($path))
            ->map(fn ($file) => Storage::disk($disk)->url($file))
            ->toArray();
    }

    /* ================= DELETE ================= */

    /**
     * Delete full directory
     */
    public function delete(string $path, string $disk = 'public'): bool
    {
        if (!Storage::disk($disk)->exists($path)) {
            return false;
        }

        Storage::disk($disk)->deleteDirectory($path);
        return true;
    }

    /**
     * Delete single file
     */
    public function deleteFile(string $filePath, string $disk = 'public'): bool
    {
        if (!Storage::disk($disk)->exists($filePath)) {
            return false;
        }

        Storage::disk($disk)->delete($filePath);
        return true;
    }

    /* ================= UTILITY ================= */

    public function exists(string $path, string $disk = 'public'): bool
    {
        return Storage::disk($disk)->exists($path);
    }

    public function count(string $path, string $disk = 'public'): int
    {
        if (!Storage::disk($disk)->exists($path)) {
            return 0;
        }

        return count(Storage::disk($disk)->files($path));
    }

    /**
     * Convert relative path to full URL (when needed)
     */
    public function url(string $path, string $disk = 'public'): string
    {
        return Storage::disk($disk)->url($path);
    }

    /* ================= INTERNAL ================= */

    protected function uniqueName(UploadedFile $file): string
    {
        return Str::uuid()->toString() . '.' . $file->getClientOriginalExtension();
    }

    protected function applyWatermark($image): void
    {
        $watermarkPath = public_path('watermark.png');

        if (!file_exists($watermarkPath)) {
            return;
        }

        $watermark = $this->manager->read($watermarkPath);

        $image->place(
            $watermark,
            'bottom-right',
            10,
            10
        );
    }
}
