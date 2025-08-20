<?php

namespace App\Jobs;

use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Services\ProductService;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessProductJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Batchable;

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
