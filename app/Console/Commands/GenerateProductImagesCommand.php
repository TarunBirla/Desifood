<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\ProductImage;
use App\Services\ProductImageGeneratorService;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class GenerateProductImagesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:generate-images
                            {--limit= : Limit the number of products to process}
                            {--product= : Process a specific product by ID}
                            {--force : Force regenerate images even if valid images already exist}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate automated product images and database records for products needing images.';

    /**
     * Execute the console command.
     */
    public function handle(ProductImageGeneratorService $imageGeneratorService): int
    {
        $limit = $this->option('limit') ? (int) $this->option('limit') : null;
        $productId = $this->option('product') ? (int) $this->option('product') : null;
        $force = (bool) $this->option('force');

        $this->info('Starting automated product image generation process...');
        $this->line('');

        $query = Product::with(['category', 'brand', 'images', 'primaryImage']);

        if ($productId) {
            $query->where('id', $productId);
        }

        $totalQueryCount = $query->count();
        if ($totalQueryCount === 0) {
            $this->warn('No products found matching criteria.');
            return Command::SUCCESS;
        }

        $targetCount = ($limit && $limit < $totalQueryCount) ? $limit : $totalQueryCount;

        $this->info("Found {$totalQueryCount} product(s). Processing target: {$targetCount} product(s).");
        $this->line('--------------------------------------------------');

        $totalChecked = 0;
        $alreadyHadImages = 0;
        $imagesGenerated = 0;
        $failedCount = 0;

        $uploadDirectory = public_path('uploads/products');
        if (!file_exists($uploadDirectory)) {
            mkdir($uploadDirectory, 0777, true);
        }

        // Process products in chunks
        $query->chunkById(50, function ($products) use (
            $limit, $force, $imageGeneratorService,
            &$totalChecked, &$alreadyHadImages, &$imagesGenerated, &$failedCount
        ) {
            foreach ($products as $product) {
                if ($limit && $totalChecked >= $limit) {
                    return false; // Break chunk loop
                }

                $totalChecked++;
                $currentIndex = $totalChecked;

                $this->line("[{$currentIndex}] Product ID: {$product->id} - {$product->name}");

                // Check for valid existing images unless --force is set
                if (!$force && $this->hasValidImage($product)) {
                    $alreadyHadImages++;
                    $this->line("  ✓ Already has valid product image. Skipped.");
                    $this->line('');
                    continue;
                }

                // Determine file name & paths
                $slug = Str::slug($product->name);
                if (!$slug) {
                    $slug = 'product-' . $product->id;
                }
                $filename = "product-{$product->id}-{$slug}.jpg";
                $relativePath = "/uploads/products/{$filename}";
                $absolutePath = public_path("uploads/products/{$filename}");

                $this->output->write("  → Generating product image... ");

                $success = $imageGeneratorService->generateImageForProduct($product, $absolutePath);

                if ($success && file_exists($absolutePath)) {
                    // Check primary image status
                    $hasPrimary = $product->images->contains('is_primary', true);

                    if ($force) {
                        // In force mode, delete previous invalid or duplicate records pointing to non-existent files
                        ProductImage::where('product_id', $product->id)->where('image_path', $relativePath)->delete();
                    }

                    // Create database record
                    $productImage = ProductImage::create([
                        'product_id' => $product->id,
                        'image_path' => $relativePath,
                        'is_primary' => !$hasPrimary,
                        'sort_order' => $product->images->count(),
                    ]);

                    $primaryLabel = $productImage->is_primary ? ' (Primary: Yes)' : ' (Primary: No)';
                    $this->line("\n  ✓ Image generated: {$relativePath}");
                    $this->line("  ✓ Database record created{$primaryLabel}");
                    $imagesGenerated++;
                } else {
                    $failedCount++;
                    $this->line("\n  ✗ Image generation failed. Logged to storage/logs/product-image-generation.log");
                }

                $this->line('');
            }
        });

        $this->line('==================================================');
        $this->info('Product Image Generation Summary:');
        $this->line("Total products checked  : {$totalChecked}");
        $this->line("Already had valid images : {$alreadyHadImages}");
        $this->line("Images generated        : {$imagesGenerated}");
        $this->line("Failed                  : {$failedCount}");
        $this->line('==================================================');

        return Command::SUCCESS;
    }

    /**
     * Check if product already has valid image records and files.
     */
    protected function hasValidImage(Product $product): bool
    {
        if ($product->images->isEmpty()) {
            return false;
        }

        foreach ($product->images as $img) {
            $path = $img->image_path;

            // If HTTP/HTTPS URL
            if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
                return true;
            }

            // Local relative path
            $relativePath = ltrim($path, '/');
            $fullPath = public_path($relativePath);

            if (file_exists($fullPath) && filesize($fullPath) > 512) {
                return true;
            }
        }

        return false;
    }
}
