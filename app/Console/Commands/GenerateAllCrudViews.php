<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class GenerateAllCrudViews extends Command
{
    protected $signature = 'make:all-crud-views';
    protected $description = 'Generate CRUD views for all predefined models';

    protected $models = [
        'users',
        'supplier',
        'barang',
        'transaksi_pembelian',
        'transaksi_penjualan',
        'pajak_transaksi',
    ];

    protected $views = ['index', 'create', 'edit', 'show'];

    public function handle()
    {
        foreach ($this->models as $model) {
            $path = resource_path("views/{$model}");

            if (!File::exists($path)) {
                File::makeDirectory($path, 0755, true);
            }

            foreach ($this->views as $view) {
                $filePath = "{$path}/{$view}.blade.php";
                if (!File::exists($filePath)) {
                    File::put($filePath, "<h1>Halaman " . ucfirst($view) . " {$model}</h1>");
                    $this->info("Created: {$filePath}");
                } else {
                    $this->warn("Skipped (already exists): {$filePath}");
                }
            }
        }

        $this->info('✅ Semua CRUD views berhasil dibuat!');
    }
}
