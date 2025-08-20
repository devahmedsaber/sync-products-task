<?php

namespace App\Services;

use App\Repositories\ProductRepository;
use App\Repositories\CategoryRepository;
use App\Repositories\SyncLogRepository;
use App\Models\Product;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProductService
{
    protected ProductRepository $productRepo;
    protected CategoryRepository $categoryRepo;
    protected SyncLogRepository $syncLogRepo;

    public function __construct(
        ProductRepository $productRepo,
        CategoryRepository $categoryRepo,
        SyncLogRepository $syncLogRepo
    ) {
        $this->productRepo = $productRepo;
        $this->categoryRepo = $categoryRepo;
        $this->syncLogRepo = $syncLogRepo;
    }

    public function process(array $data, int $logId): void
    {
        try {
            if (empty($data['id']) || empty($data['title']) || empty($data['price'])) {
                $this->syncLogRepo->increment($logId, 'skipped');
                return;
            }

            $category = $this->categoryRepo->findOrCreate($data['category'] ?? 'Uncategorized');

            $existing = $this->productRepo->findByExternalId($data['id']);

            $payload = [
                'external_id' => $data['id'],
                'title'       => $data['title'],
                'description' => $data['description'] ?? null,
                'price'       => $data['price'],
                'image_url'   => $data['image'] ?? null,
                'category_id' => $category->id,
            ];

            if ($existing) {
                $this->productRepo->update($existing, $payload);
                $this->syncLogRepo->increment($logId, 'updated');
            } else {
                $new = $this->productRepo->create($payload);
                $this->downloadImage($new, $data['image'] ?? null);
                $this->syncLogRepo->increment($logId, 'created');
            }
        } catch (Exception $e) {
            $this->syncLogRepo->increment($logId, 'failed');
        }
    }

    private function downloadImage(Product $product, ?string $url): void
    {
        if (!$url) return;

        try {
            $contents = file_get_contents($url);
            $filename = 'products/' . $product->external_id . '.jpg';
            Storage::disk('public')->put($filename, $contents);

            $this->productRepo->update($product, [
                'image_path' => $filename,
            ]);
        } catch (Exception $e) {
            Log::info($e->getMessage());
        }
    }
}
