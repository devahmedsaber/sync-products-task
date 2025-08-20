<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\Product;
use App\Repositories\ProductRepository;
use App\Repositories\CategoryRepository;
use App\Repositories\SyncLogRepository;
use App\Services\ProductService;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ProductServiceTest extends TestCase
{
    protected $productRepo;
    protected $categoryRepo;
    protected $syncLogRepo;
    protected $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->productRepo = Mockery::mock(ProductRepository::class);
        $this->categoryRepo = Mockery::mock(CategoryRepository::class);
        $this->syncLogRepo = Mockery::mock(SyncLogRepository::class);

        $this->service = new ProductService(
            $this->productRepo,
            $this->categoryRepo,
            $this->syncLogRepo
        );
    }

    #[Test]
    public function it_creates_new_product()
    {
        $logId = 1;
        $category = new Category(['id' => 5, 'name' => 'Electronics']);

        $this->categoryRepo
            ->shouldReceive('findOrCreate')
            ->once()
            ->with('Electronics')
            ->andReturn($category);

        $this->productRepo
            ->shouldReceive('findByExternalId')
            ->once()
            ->with(100)
            ->andReturn(null);

        $this->productRepo
            ->shouldReceive('create')
            ->once()
            ->andReturn(new Product([
                'id' => 1,
                'external_id' => 100,
                'title' => 'Test Product'
            ]));

        $this->syncLogRepo
            ->shouldReceive('increment')
            ->once()
            ->with($logId, 'created');

        $data = [
            'id' => 100,
            'title' => 'Test Product',
            'price' => 200,
            'category' => 'Electronics',
        ];

        $this->service->process($data, $logId);

        $this->assertTrue(true);
    }

    #[Test]
    public function it_updates_existing_product()
    {
        $logId = 2;
        $category = new Category(['id' => 6, 'name' => 'Books']);

        $this->categoryRepo
            ->shouldReceive('findOrCreate')
            ->once()
            ->with('Books')
            ->andReturn($category);

        $existingProduct = new Product([
            'id' => 2,
            'external_id' => 200,
            'title' => 'Old Title',
            'price' => 100,
            'category_id' => 6,
        ]);

        $this->productRepo
            ->shouldReceive('findByExternalId')
            ->once()
            ->with(200)
            ->andReturn($existingProduct);

        $this->productRepo
            ->shouldReceive('update')
            ->once()
            ->with($existingProduct, Mockery::on(function ($payload) {
                return $payload['title'] === 'Updated Product';
            }));

        $this->syncLogRepo
            ->shouldReceive('increment')
            ->once()
            ->with($logId, 'updated');

        $data = [
            'id' => 200,
            'title' => 'Updated Product',
            'price' => 150,
            'category' => 'Books',
        ];

        $this->service->process($data, $logId);

        $this->assertTrue(true);
    }

    #[Test]
    public function it_skips_when_missing_required_fields()
    {
        $logId = 3;

        $this->syncLogRepo
            ->shouldReceive('increment')
            ->once()
            ->with($logId, 'skipped');

        $data = [
            'id' => 300,
            'price' => 100,
        ];

        $this->service->process($data, $logId);

        $this->assertTrue(true);
    }
}
