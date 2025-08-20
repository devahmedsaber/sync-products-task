<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Services\ProductService;

class ProcessProductJob implements ShouldQueue
{
    use Queueable;

    public $queue = 'products';
    protected array $product;
    protected int $logId;

    public function __construct(array $product, int $logId)
    {
        $this->product = $product;
        $this->logId = $logId;
    }

    public function handle(ProductService $service)
    {
        $service->process($this->product, $this->logId);
    }
}
