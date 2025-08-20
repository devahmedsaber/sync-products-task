<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Jobs\ProcessProductJob;
use App\Services\ProductService;
use Illuminate\Support\Facades\Queue;
use Mockery;

class ProcessProductJobTest extends TestCase
{
    public function test_it_calls_service_process()
    {
        $product = ['id' => 1, 'title' => 'Test', 'price' => 10];
        $logId = 1;

        $service = Mockery::mock(ProductService::class);
        $service->shouldReceive('process')->once()->with($product, $logId);

        $job = new ProcessProductJob($product, $logId);
        $job->handle($service);

        $this->assertTrue(true);
    }

    public function test_job_is_dispatched_to_queue()
    {
        Queue::fake();

        ProcessProductJob::dispatch(['id' => 1, 'title' => 'Test', 'price' => 10], 1);

        Queue::assertPushed(ProcessProductJob::class);
    }
}
