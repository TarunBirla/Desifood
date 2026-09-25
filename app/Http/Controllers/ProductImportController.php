<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ProductImportController extends Controller
{
    /**
     * Bulk import products from storage/app/imports/products.xlsx
     *
     * URL: https://desifoods.thenexteck.com/run-product-import-x9k2p7?key=DesiFoodImport2026SecretKey
     */
    public function import(Request $request)
    {
        // 1. Security Check via Secret Key
        $secretKey = env('PRODUCT_IMPORT_KEY', 'DesiFoodImport2026SecretKey');
        if ($request->query('key') !== $secretKey) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized access. Valid secret key required (?key=SECRET_KEY).'
            ], 403);
        }

        // 2. Increase execution time and memory limit for bulk processing
        set_time_limit(0);
        ini_set('memory_limit', '512M');

        $candidatePaths = [
            storage_path('app/imports/products.xlsx'),
            storage_path('app/imports/Products.xlsx'),
            storage_path('app/imports/products.XLSX'),
            storage_path('app/products.xlsx'),
            storage_path('imports/products.xlsx'),
            base_path('storage/app/imports/products.xlsx'),
            base_path('products.xlsx'),
        ];

        $filePath = null;
        foreach ($candidatePaths as $candidate) {
            if (file_exists($candidate)) {
                $filePath = $candidate;
                break;
            }
        }

        // Case-insensitive search in storage/app/imports directory
        if (!$filePath) {
            $importsDir = storage_path('app/imports');
            if (is_dir($importsDir)) {
                $files = scandir($importsDir);
                foreach ($files as $file) {
                    if (strtolower($file) === 'products.xlsx') {
                        $filePath = $importsDir . '/' . $file;
                        break;
                    }
                }
            }
        }

        if (!$filePath) {
            $appFiles = is_dir(storage_path('app')) ? array_values(array_diff(scandir(storage_path('app')), ['.', '..'])) : [];
            $importsFiles = is_dir(storage_path('app/imports')) ? array_values(array_diff(scandir(storage_path('app/imports')), ['.', '..'])) : [];

            return response()->json([
                'status' => 'error',
                'message' => "Excel file missing on server at: /home/nextecki/desifoods/storage/app/imports/products.xlsx",
                'instructions' => "Aapko cPanel File Manager me '/home/nextecki/desifoods/storage/app/' ke andar 'imports' folder bana kar usme 'products.xlsx' upload karna hai.",
                'server_storage_app_contents' => $appFiles,
                'server_storage_app_imports_contents' => $importsFiles,
            ], 404, [], JSON_PRETTY_PRINT);
        }

        $startTime = microtime(true);

        // 3. Reset Brands table and create "All Brand"
        try {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        } catch (\Throwable $e) {}

        Brand::query()->delete();

        try {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        } catch (\Throwable $e) {}

        $brand = Brand::create([
            'name' => 'All Brand',
            'slug' => 'all-brand',
            'status' => true,
        ]);

        // 4. Create or fetch default "All Products" category
        $category = Category::firstOrCreate(
            ['slug' => 'all-products'],
            [
                'name' => 'All Products',
                'status' => true,
                'sort_order' => 0,
            ]
        );

        // 5. Pre-fetch existing SKUs and Slugs to prevent duplicate key errors
        $existingSkus = Product::pluck('sku')->mapWithKeys(fn($sku) => [$sku => true])->toArray();
        $existingSlugs = Product::pluck('slug')->mapWithKeys(fn($slug) => [$slug => true])->toArray();

        // 6. Read Excel spreadsheet in memory-efficient read-only mode
        $reader = IOFactory::createReaderForFile($filePath);
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($filePath);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        $totalRows = count($rows);
        $insertedCount = 0;
        $skippedCount = 0;
        $batch = [];
        $now = now();

        foreach ($rows as $index => $row) {
            // Skip header row if present
            if ($index === 0 && isset($row[0]) && strtolower(trim((string)$row[0])) === 'productcode') {
                continue;
            }

            $sku = trim((string)($row[0] ?? ''));
            $name = trim((string)($row[1] ?? ''));
            $itemSize = trim((string)($row[2] ?? ''));
            $priceRaw = $row[4] ?? 0;
            $price = is_numeric($priceRaw) ? (float)$priceRaw : 0;

            // Skip invalid rows without name or SKU
            if ($sku === '' || $name === '') {
                $skippedCount++;
                continue;
            }

            // Skip duplicate SKUs
            if (isset($existingSkus[$sku])) {
                $skippedCount++;
                continue;
            }

            $existingSkus[$sku] = true;

            // Generate unique slug
            $baseSlug = Str::slug($name);
            if (!$baseSlug) {
                $baseSlug = 'product-' . $sku;
            }

            $slug = $baseSlug;
            $suffix = 1;
            while (isset($existingSlugs[$slug])) {
                $slug = $baseSlug . '-' . $suffix;
                $suffix++;
            }
            $existingSlugs[$slug] = true;

            // Format specifications JSON
            $specifications = !empty($itemSize) ? json_encode(['ItemSize' => $itemSize]) : null;

            $batch[] = [
                'name' => $name,
                'slug' => $slug,
                'sku' => $sku,
                'category_id' => $category->id,
                'brand_id' => $brand->id,
                'price' => $price,
                'sale_price' => null,
                'cost_price' => null,
                'stock' => 0,
                'min_stock_warning' => 5,
                'description' => null,
                'specifications' => $specifications,
                'faqs' => null,
                'is_active' => true,
                'is_featured' => false,
                'is_trending' => false,
                'is_new_arrival' => false,
                'has_variants' => false,
                'rating_avg' => 0.00,
                'reviews_count' => 0,
                'warranty_info' => null,
                'return_policy_info' => null,
                'seo_title' => null,
                'seo_description' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ];

            // Batch insert every 500 records
            if (count($batch) >= 500) {
                DB::table('products')->insert($batch);
                $insertedCount += count($batch);
                $batch = [];
            }
        }

        // Insert remaining batch
        if (count($batch) > 0) {
            DB::table('products')->insert($batch);
            $insertedCount += count($batch);
            $batch = [];
        }

        $executionTime = round(microtime(true) - $startTime, 2);

        return response()->json([
            'status' => 'success',
            'message' => 'Product import completed successfully!',
            'summary' => [
                'total_excel_rows' => $totalRows,
                'inserted_products' => $insertedCount,
                'skipped_products' => $skippedCount,
                'brand' => $brand->name,
                'category' => $category->name,
                'execution_time_seconds' => $executionTime,
            ],
            'REMINDER' => 'SECURITY NOTICE: Import full complete ho chuka hai. Security purpose ke liye routes/web.php me se is route ko delete ya comment out kar dein.'
        ], 200, [], JSON_PRETTY_PRINT);
    }
}
