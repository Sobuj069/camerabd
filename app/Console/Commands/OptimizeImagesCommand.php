<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class OptimizeImagesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'images:optimize-webp {--force : Re-convert even if webp already exists}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Compress and convert uploaded banners, categories, and products to modern lightweight WebP format';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting image optimization and WebP conversion...');

        $directories = [
            public_path('uploads/banners') => ['maxWidth' => 1200, 'maxHeight' => 675, 'quality' => 82],
            public_path('uploads/categories') => ['maxWidth' => 400, 'maxHeight' => 400, 'quality' => 82],
            public_path('uploads/products') => ['maxWidth' => 800, 'maxHeight' => 800, 'quality' => 82],
            public_path('uploads/logo') => ['maxWidth' => 400, 'maxHeight' => 200, 'quality' => 85],
        ];

        $totalOriginalBytes = 0;
        $totalWebpBytes = 0;
        $convertedCount = 0;
        $force = $this->option('force');

        foreach ($directories as $dir => $config) {
            if (!File::isDirectory($dir)) {
                continue;
            }

            $this->line("<comment>Scanning: {$dir}</comment>");
            $files = File::files($dir);

            foreach ($files as $file) {
                $ext = strtolower($file->getExtension());
                if (!in_array($ext, ['png', 'jpg', 'jpeg'])) {
                    continue;
                }

                $filename = $file->getFilename();
                $baseName = pathinfo($filename, PATHINFO_FILENAME);
                $webpPath = $dir . DIRECTORY_SEPARATOR . $baseName . '.webp';

                $origSize = $file->getSize();
                $totalOriginalBytes += $origSize;

                if (File::exists($webpPath) && !$force) {
                    $totalWebpBytes += File::size($webpPath);
                    continue;
                }

                $success = $this->convertToWebp($file->getRealPath(), $webpPath, $config['maxWidth'], $config['maxHeight'], $config['quality']);
                if ($success && File::exists($webpPath)) {
                    $newSize = File::size($webpPath);
                    $totalWebpBytes += $newSize;
                    $convertedCount++;

                    $savedPercent = $origSize > 0 ? round((($origSize - $newSize) / $origSize) * 100, 1) : 0;
                    $origKb = round($origSize / 1024, 1);
                    $newKb = round($newSize / 1024, 1);

                    $this->info("  ✓ Converted {$filename}: {$origKb}KB -> {$newKb}KB (-{$savedPercent}%)");
                } else {
                    $totalWebpBytes += $origSize;
                }
            }
        }

        $totalOrigMb = round($totalOriginalBytes / (1024 * 1024), 2);
        $totalWebpMb = round($totalWebpBytes / (1024 * 1024), 2);
        $totalSavedMb = round(($totalOriginalBytes - $totalWebpBytes) / (1024 * 1024), 2);
        $totalSavedPercent = $totalOriginalBytes > 0 ? round((($totalOriginalBytes - $totalWebpBytes) / $totalOriginalBytes) * 100, 1) : 0;

        $this->newLine();
        $this->info("==========================================");
        $this->info("Optimization Complete!");
        $this->info("Images Converted: {$convertedCount}");
        $this->info("Original Size:    {$totalOrigMb} MB");
        $this->info("Optimized Size:   {$totalWebpMb} MB");
        $this->info("Total Saved:      {$totalSavedMb} MB (-{$totalSavedPercent}%)");
        $this->info("==========================================");

        return 0;
    }

    /**
     * Convert an image file to WebP with optional resizing
     */
    protected function convertToWebp(string $sourcePath, string $targetPath, int $maxWidth, int $maxHeight, int $quality = 82): bool
    {
        if (!function_exists('imagewebp')) {
            $this->error('PHP GD extension with WebP support is required.');
            return false;
        }

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
                return true;
            default:
                return false;
        }

        if (!$srcImage) {
            return false;
        }

        // Calculate proportional scale
        $ratio = min($maxWidth / max(1, $srcWidth), $maxHeight / max(1, $srcHeight));
        if ($ratio < 1) {
            $dstWidth = (int)round($srcWidth * $ratio);
            $dstHeight = (int)round($srcHeight * $ratio);
        } else {
            $dstWidth = $srcWidth;
            $dstHeight = $srcHeight;
        }

        $dstImage = imagecreatetruecolor($dstWidth, $dstHeight);

        // Preserve alpha channel transparency for PNG/WebP
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
