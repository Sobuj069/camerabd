<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;

class ImageOptimizer
{
    /**
     * Optimize and convert an uploaded image to WebP format.
     *
     * @param UploadedFile $file
     * @param string $destinationDir
     * @param string $prefix
     * @param int $maxWidth
     * @param int $maxHeight
     * @param int $quality
     * @return string Generated filename (e.g., 'banner_20261102120000_abc123.webp')
     */
    public static function convertUploadedFileToWebp(
        UploadedFile $file,
        string $destinationDir,
        string $prefix = '',
        int $maxWidth = 1200,
        int $maxHeight = 1200,
        int $quality = 82
    ): string {
        if (!File::isDirectory($destinationDir)) {
            File::makeDirectory($destinationDir, 0755, true, true);
        }

        $uniqueName = ($prefix ? $prefix . '_' : '') . date('YmdHis') . '_' . uniqid();
        $webpFilename = $uniqueName . '.webp';
        $targetPath = rtrim($destinationDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $webpFilename;

        $sourcePath = $file->getRealPath();

        if (function_exists('imagewebp') && self::processWebp($sourcePath, $targetPath, $maxWidth, $maxHeight, $quality)) {
            return $webpFilename;
        }

        // Fallback: if GD WebP fails, save with original extension
        $origName = $uniqueName . '.' . $file->getClientOriginalExtension();
        $file->move($destinationDir, $origName);
        return $origName;
    }

    /**
     * Convert and resize image via GD library
     */
    public static function processWebp(string $sourcePath, string $targetPath, int $maxWidth, int $maxHeight, int $quality = 82): bool
    {
        $info = @getimagesize($sourcePath);
        if (!$info) {
            return false;
        }

        $mime = $info['mime'];
        $srcWidth = $info[0];
        $srcHeight = $info[1];

        switch ($mime) {
            case 'image/jpeg':
            case 'image/jpg':
                $srcImage = @imagecreatefromjpeg($sourcePath);
                break;
            case 'image/png':
                $srcImage = @imagecreatefrompng($sourcePath);
                break;
            case 'image/webp':
                $srcImage = @imagecreatefromwebp($sourcePath);
                break;
            default:
                return false;
        }

        if (!$srcImage) {
            return false;
        }

        $ratio = min($maxWidth / max(1, $srcWidth), $maxHeight / max(1, $srcHeight));
        if ($ratio < 1) {
            $dstWidth = (int)round($srcWidth * $ratio);
            $dstHeight = (int)round($srcHeight * $ratio);
        } else {
            $dstWidth = $srcWidth;
            $dstHeight = $srcHeight;
        }

        $dstImage = imagecreatetruecolor($dstWidth, $dstHeight);

        imagealphablending($dstImage, false);
        imagesavealpha($dstImage, true);
        $transparent = imagecolorallocatealpha($dstImage, 255, 255, 255, 127);
        imagefilledrectangle($dstImage, 0, 0, $dstWidth, $dstHeight, $transparent);

        imagecopyresampled($dstImage, $srcImage, 0, 0, 0, 0, $dstWidth, $dstHeight, $srcWidth, $srcHeight);

        $saved = imagewebp($dstImage, $targetPath, $quality);

        imagedestroy($srcImage);
        imagedestroy($dstImage);

        return $saved;
    }
}
