<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Bus;
use App\Jobs\ProcessProductJob;
use App\Models\SyncLog;

class SyncProductsCommand extends Command
{
    protected $signature = 'sync:products';

    protected $description = 'Sync products from Fake Store API into local database';

    public function handle()
    {
        $this->info("🚀 Starting product sync...");

        $response = Http::get(config('services.fakestore.url', 'https://fakestoreapi.com/products'));

        if ($response->failed()) {
            $this->error("Failed to fetch products from API");
            return Command::FAILURE;
        }

        $products = $response->json();
        $total = count($products);

        if ($total === 0) {
            $this->warn("No products found in API");
            return Command::SUCCESS;
        }

        $syncLog = SyncLog::create([
            'fetched' => $total,
            'status'  => 'pending',
        ]);

        $this->info("Fetched {$total} products from API");

        $this->output->progressStart($total);

        $chunks = array_chunk($products, 10);
        $batchJobs = [];

        foreach ($chunks as $chunk) {
            foreach ($chunk as $product) {
                $batchJobs[] = new ProcessProductJob($product, $syncLog->id);
            }
        }

        Bus::batch($batchJobs)
            ->name('Sync Products Batch')
            ->onQueue('products')
            ->then(function () use ($syncLog) {
                $syncLog->update(['status' => 'success']);
            })
            ->catch(function () use ($syncLog) {
                $syncLog->update(['status' => 'failed']);
            })
            ->finally(function () {
                // CleanUp
            })
            ->dispatch();

        $this->output->progressFinish();
        $this->info("Sync process dispatched to queue.");

        return Command::SUCCESS;
    }
}
