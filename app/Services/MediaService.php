<?php

namespace App\Services;

use Illuminate\Support\Str;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Illuminate\Contracts\Filesystem\Factory as Filesystem;
use Illuminate\Support\Facades\Log;
use App\MediaLibrary\CustomPathGenerator;


class MediaService
{
    protected $filesystem;
    protected $pathGenerator;

    public function __construct(Filesystem $filesystem, CustomPathGenerator $pathGenerator)
    {
        $this->filesystem = $filesystem;
        $this->pathGenerator = $pathGenerator;
    }

    /**
     * 僅刪除指定媒體的檔案（主檔案、轉換圖、響應式圖），不刪除資料夾
     * 保持共用資料夾結構，精確刪除單一檔案
     */
    public function deleteMediaFileOnly(Media $media): bool
    {
        try {
            Log::info('開始刪除媒體檔案', [
                'media_id' => $media->id,
                'file_name' => $media->file_name,
                'path' => $media->getPath(),
            ]);

            // 1. 刪除主檔案
            $this->deleteMainFile($media);

            // 2. 刪除轉換圖（縮圖等）
            $this->deleteConversions($media);

            // 3. 刪除響應式圖
            $this->deleteResponsiveImages($media);

            // 4. 刪除資料庫記錄
            $deleteSuccess = false;
            Media::withoutEvents(function () use ($media, &$deleteSuccess) {
                $deleteSuccess = $media->forceDelete();
            });

            if (!$deleteSuccess) {
                Log::error('從資料庫刪除媒體記錄失敗', [
                    'media_id' => $media->id,
                    'file_name' => $media->file_name
                ]);
                // 拋出錯誤訊息
                throw new \Exception("Failed to delete media record from database for media ID: {$media->id}.");
            }

            Log::info('媒體檔案刪除成功', [
                'media_id' => $media->id,
                'file_name' => $media->file_name
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('刪除媒體檔案失敗', [
                'media_id' => $media->id,
                'file_name' => $media->file_name,
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }

    /**
     * 批量刪除媒體檔案
     */
    public function deleteMultipleMediaFiles(array $mediaIds): array
    {
        $results = ['success' => [], 'failed' => []];

        foreach ($mediaIds as $mediaId) {
            try {
                $media = Media::findOrFail($mediaId);
                $this->deleteMediaFileOnly($media);
                $results['success'][] = $mediaId;
            } catch (\Exception $e) {
                $results['failed'][] = [
                    'id' => $mediaId,
                    'error' => $e->getMessage()
                ];
            }
        }

        return $results;
    }

    /**
     * 刪除主檔案
     */
    protected function deleteMainFile(Media $media): void
    {
        $disk = $this->filesystem->disk($media->disk);
        $filePath = $media->getPathRelativeToRoot();

        if ($disk->exists($filePath)) {
            if (!$disk->delete($filePath)) {
                throw new \Exception("無法刪除主檔案: {$filePath}");
            }

            Log::debug('主檔案刪除成功', ['path' => $filePath]);
        } else {
            Log::warning('主檔案不存在', ['path' => $filePath]);
        }
    }

    /**
     * 刪除轉換圖（縮圖等）
     */
    protected function deleteConversions(Media $media): void
    {
        $conversionsDirectory = $this->pathGenerator->getPathForConversions($media);
        $this->deleteRelatedFiles($media->conversions_disk, $conversionsDirectory, $media, '轉換圖');
    }

    /**
     * 刪除響應式圖
     */
    protected function deleteResponsiveImages(Media $media): void
    {
        $responsiveDirectory  = $this->pathGenerator->getPathForResponsiveImages($media);
        $this->deleteRelatedFiles($media->disk, $responsiveDirectory, $media, '響應式圖');
    }

    /**
     * 從指定目錄中刪除與 media 相關的檔案
     */
    protected function deleteRelatedFiles(string $diskName, string $directory, Media $media, string $fileType): void
    {
        $disk = $this->filesystem->disk($diskName);

        if (!$disk->exists($directory)) {
            Log::debug("{$fileType}目錄不存在", ['directory' => $directory]);
            return;
        }

        try {
            $matchedFiles = $this->findMatchingFiles($disk, $directory, $media);

            if (!empty($matchedFiles)) {
                foreach ($matchedFiles as $file) {
                    if ($disk->exists($file)) {
                        $disk->delete($file);
                        Log::debug("{$fileType}檔案刪除成功", ['file' => $file]);
                    }
                }
            } else {
                Log::debug("沒有找到相關的{$fileType}檔案", ['directory' => $directory]);
            }
        } catch (\Exception $e) {
            Log::error("刪除{$fileType}時發生錯誤", [
                'media_id' => $media->id,
                'directory' => $directory,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * 找出與媒體檔案相關的所有檔案
     * 改善匹配邏輯，避免誤刪其他檔案
     */
    protected function findMatchingFiles($disk, string $directory, Media $media): array
    {
        $allFiles = $disk->allFiles($directory);
        $mediaBaseName = pathinfo($media->file_name, PATHINFO_FILENAME); // 原始檔案的主檔名 (不含副檔名)

        return array_filter($allFiles, function ($filePathOnDisk) use ($mediaBaseName, $media, $directory) {
            $fileName = basename($filePathOnDisk);

            $isConversionsDirectory = Str::endsWith(rtrim($directory, '/'), '/conversions');
            $isResponsiveImagesDirectory = Str::endsWith(rtrim($directory, '/'), '/responsive-images');

            if ($isConversionsDirectory) {
                // --- 轉換圖 (Conversions) 的匹配邏輯 ---
                $generatedConversions = collect($media->generated_conversions ?? [])->filter()->keys();
                foreach ($generatedConversions as $conversionName) {
                    $absolutePath = $media->getPath($conversionName);
                    // 將絕對路徑轉成相對路徑（相對於 disk root）
                    $relativePath = Str::after($absolutePath, storage_path('app/public/upload') . DIRECTORY_SEPARATOR);
                    if ($filePathOnDisk === $relativePath) {
                        return true;
                    }
                }
            }

            if ($isResponsiveImagesDirectory) {
                // --- 響應式圖片 (Responsive Images) 的匹配邏輯 ---
                // 響應式圖片的檔名模式比較多樣，通常包含原始檔名、尺寸或特定標識。

                // 1. 匹配 Spatie v10+ 基於轉換生成的響應式圖片檔名模式：
                //    例如: {original_base_name}--responsive_images_generated_by_conversion_{conversion_name}_w_{width}_h_{height}...
                if (Str::startsWith($fileName, $mediaBaseName . '--responsive_images_generated_by_conversion_')) {
                    return true;
                }

                // 2. 匹配較舊或常見的響應式圖片檔名模式 (包含原始檔名和可能是尺寸的後綴)：
                //    例如: {originalBaseName}-200x150.jpg, {originalBaseName}-w200-h150.jpg,
                //          {originalBaseName}---media_library_original_200_150.jpg
                if (Str::startsWith($fileName, $mediaBaseName . '-')) {
                    // 使用正則表達式匹配更具體的模式，例如包含數字 (尺寸)
                    // 此正則表達式匹配 "basename-", 後接數字, "x", 數字, 或 "media_library_original_數字_數字"等
                    if (preg_match('/^' . preg_quote($mediaBaseName, '/') . '-(?:(?:[0-9]+(?:x[0-9]+)?)|(?:media_library_original_[0-9]+_[0-9]+))\.[\w]+$/i', $fileName)) {
                        return true;
                    }
                }

                // 3. 匹配響應式圖片的 placeholder (通常是 .svg 檔案)
                //    例如: {originalBaseName}--placeholder.svg (Spatie v10+)
                if ($fileName === $mediaBaseName . '--placeholder.svg') {
                    return true;
                }
                // 較舊的或自訂的 placeholder 可能直接是 {originalBaseName}.svg
                if (pathinfo($fileName, PATHINFO_EXTENSION) === 'svg' && pathinfo($fileName, PATHINFO_FILENAME) === $mediaBaseName) {
                    // 確保它不是原始檔案恰好是SVG且同名（儘管通常響應式 placeholder 會在 responsive-images 目錄）
                    if ($filePathOnDisk !== $media->getPath()) { // 排除原始檔案本身
                        return true;
                    }
                }
            }

            return false;
        });
    }

    /**
     * 檢查媒體檔案是否存在
     */
    public function mediaFileExists(Media $media): bool
    {
        $disk = $this->filesystem->disk($media->disk);
        return $disk->exists($media->getPath());
    }

    /**
     * 取得媒體檔案大小
     */
    public function getMediaFileSize(Media $media): int
    {
        $disk = $this->filesystem->disk($media->disk);

        if (!$disk->exists($media->getPath())) {
            return 0;
        }

        return $disk->size($media->getPath());
    }
}
