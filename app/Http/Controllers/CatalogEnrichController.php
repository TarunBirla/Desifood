<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CatalogEnrichController extends Controller
{
    /**
     * One-time web endpoint to populate Categories, Brands, and Product Images for all products in DB.
     * URL: https://desifoods.thenexteck.com/run-catalog-enrich-k9x2m4
     */
    public function enrich(Request $request)
    {
        set_time_limit(0);
        ini_set('memory_limit', '512M');

        $startTime = microtime(true);

        // 1. Ensure all 10 standard Categories exist
        $categoriesData = [
            ['name' => 'Rice, Atta & Grains', 'slug' => 'rice-atta-grains', 'sort_order' => 1, 'image' => 'https://images.unsplash.com/photo-1586201375761-83865001e31c?w=800'],
            ['name' => 'Spices & Masalas', 'slug' => 'spices-masalas', 'sort_order' => 2, 'image' => 'https://images.unsplash.com/photo-1596040033229-a9821ebd058d?w=800'],
            ['name' => 'Dal, Pulses & Lentils', 'slug' => 'dal-pulses-lentils', 'sort_order' => 3, 'image' => 'https://images.unsplash.com/photo-1515543904379-3d757afe72e3?w=800'],
            ['name' => 'Snacks & Namkeen', 'slug' => 'snacks-namkeen', 'sort_order' => 4, 'image' => 'https://images.unsplash.com/photo-1599488615731-7e5c2823ff28?w=800'],
            ['name' => 'Oils & Cooking Mediums', 'slug' => 'oils-cooking-mediums', 'sort_order' => 5, 'image' => 'https://images.unsplash.com/photo-1474979266404-7eaacbcd87c5?w=800'],
            ['name' => 'Dairy & Ghee', 'slug' => 'dairy-ghee', 'sort_order' => 6, 'image' => 'https://images.unsplash.com/photo-1589927986089-35812388d1f4?w=800'],
            ['name' => 'Pickles, Chutneys & Sauces', 'slug' => 'pickles-chutneys-sauces', 'sort_order' => 7, 'image' => 'https://images.unsplash.com/photo-1541832676-9b763b0239ab?w=800'],
            ['name' => 'Tea, Coffee & Beverages', 'slug' => 'tea-coffee-beverages', 'sort_order' => 8, 'image' => 'https://images.unsplash.com/photo-1576092768241-dec231879fc3?w=800'],
            ['name' => 'Ready Meals & Frozen Foods', 'slug' => 'ready-meals-frozen-foods', 'sort_order' => 9, 'image' => 'https://images.unsplash.com/photo-1601050690597-df0568f70950?w=800'],
            ['name' => 'Sweets, Mithai & Desserts', 'slug' => 'sweets-mithai-desserts', 'sort_order' => 10, 'image' => 'https://images.unsplash.com/photo-1587314168485-3236d6710814?w=800'],
        ];

        $categoryModels = [];
        foreach ($categoriesData as $cData) {
            $cat = Category::firstOrCreate(['slug' => $cData['slug']], [
                'name' => $cData['name'],
                'sort_order' => $cData['sort_order'],
                'image' => $cData['image'],
                'status' => true,
            ]);
            $categoryModels[$cData['slug']] = $cat->id;
        }

        // Default category fallback
        $defaultCatId = reset($categoryModels);

        // 2. Ensure Brands exist
        $brandMap = [
            'MDH' => 'mdh',
            'EVEREST' => 'everest',
            'CATCH' => 'catch',
            'TATA SAMPANN' => 'tata-sampann',
            'TATA' => 'tata',
            'ROYAL' => 'royal',
            'INDIA GATE' => 'india-gate',
            'DAAWAT' => 'daawat',
            'FORTUNE' => 'fortune',
            '24 MANTRA' => '24-mantra',
            'AASHIRVAAD' => 'aashirvaad',
            'PILLSBURY' => 'pillsbury',
            'ORGANIC TATTVA' => 'organic-tattva',
            'AMUL' => 'amul',
            'MOTHER\'S RECIPE' => 'mothers-recipe',
            'MOTHERS RECIPE' => 'mothers-recipe',
            'DABUR' => 'dabur',
            'PATANJALI' => 'patanjali',
            'RED LABEL' => 'red-label',
            'TAJ MAHAL' => 'taj-mahal',
            'HALDIRAM\'S' => 'haldirams',
            'HALDIRAM' => 'haldirams',
            'BIKAJI' => 'bikaji',
            'PARLE' => 'parle',
            'PARLE-G' => 'parle',
            'SAFFOLA' => 'saffola',
            'REAL' => 'real',
            'MTR' => 'mtr',
            'TRS' => 'trs',
            'NATCO' => 'natco',
            'SHANA' => 'shana',
            'ASHOKA' => 'ashoka',
            'SOCIETY TEA' => 'society-tea',
            'MAGGI' => 'maggi',
            'KNORR' => 'knorr',
            'PRIYA' => 'priya',
            'ROOPAK' => 'roopak',
            'BADSHAH' => 'badshah',
            'BHALO' => 'bhalo',
            'HEERA' => 'heera',
            'EASTERN' => 'eastern',
            'NIRAPARA' => 'nirapara',
            'DOUBLE HORSE' => 'double-horse',
            'BAMBINO' => 'bambino',
            'SUNPURE' => 'sunpure',
            'SUNDROP' => 'sundrop',
            'BRITANNIA' => 'britannia',
            'SUNFEAST' => 'sunfeast',
            'UNIBIC' => 'unibic',
            'LAYS' => 'lays',
            'KURKURE' => 'kurkure',
            'LIPTON' => 'lipton',
            'BROOKE BOND' => 'brooke-bond',
            'NESCAFE' => 'nescafe',
            'BRU' => 'bru',
            'PAPER BOAT' => 'paper-boat',
            'ROOH AFZA' => 'rooh-afza',
            'HAMDARD' => 'hamdard',
        ];

        $brandModels = [];
        foreach ($brandMap as $bName => $bSlug) {
            $brandModels[$bName] = Brand::firstOrCreate(['slug' => $bSlug], [
                'name' => $bName,
                'status' => true,
            ]);
        }

        // 3. Category image pool
        $categoryImagesPool = [
            $categoryModels['rice-atta-grains'] ?? 1 => [
                'https://images.unsplash.com/photo-1586201375761-83865001e31c?w=800',
                'https://images.unsplash.com/photo-1509440159596-0249088772ff?w=800',
                'https://images.unsplash.com/photo-1536304993881-ff6e9eefa2a6?w=800',
                'https://images.unsplash.com/photo-1541781774459-bb2af2f05b55?w=800',
            ],
            $categoryModels['spices-masalas'] ?? 2 => [
                'https://images.unsplash.com/photo-1596040033229-a9821ebd058d?w=800',
                'https://images.unsplash.com/photo-1615485290382-441e4d049cb5?w=800',
                'https://images.unsplash.com/photo-1509358271058-acd01cc9386a?w=800',
                'https://images.unsplash.com/photo-1599940824399-b87987ceb72a?w=800',
            ],
            $categoryModels['dal-pulses-lentils'] ?? 3 => [
                'https://images.unsplash.com/photo-1515543904379-3d757afe72e3?w=800',
                'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=800',
                'https://images.unsplash.com/photo-1584269600464-37b1b58a9fe7?w=800',
            ],
            $categoryModels['snacks-namkeen'] ?? 4 => [
                'https://images.unsplash.com/photo-1599488615731-7e5c2823ff28?w=800',
                'https://images.unsplash.com/photo-1565557623262-b51c2513a641?w=800',
                'https://images.unsplash.com/photo-1621996346565-e3d5d6281270?w=800',
            ],
            $categoryModels['oils-cooking-mediums'] ?? 5 => [
                'https://images.unsplash.com/photo-1474979266404-7eaacbcd87c5?w=800',
                'https://images.unsplash.com/photo-1620706857397-e172df2856c1?w=800',
            ],
            $categoryModels['dairy-ghee'] ?? 6 => [
                'https://images.unsplash.com/photo-1589927986089-35812388d1f4?w=800',
                'https://images.unsplash.com/photo-1628088062854-d1870b4553da?w=800',
            ],
            $categoryModels['pickles-chutneys-sauces'] ?? 7 => [
                'https://images.unsplash.com/photo-1541832676-9b763b0239ab?w=800',
                'https://images.unsplash.com/photo-1589301760014-d929f3979dbc?w=800',
            ],
            $categoryModels['tea-coffee-beverages'] ?? 8 => [
                'https://images.unsplash.com/photo-1576092768241-dec231879fc3?w=800',
                'https://images.unsplash.com/photo-1544787219-7f47ccb76574?w=800',
                'https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?w=800',
            ],
            $categoryModels['ready-meals-frozen-foods'] ?? 9 => [
                'https://images.unsplash.com/photo-1601050690597-df0568f70950?w=800',
                'https://images.unsplash.com/photo-1567188040759-fb8a883dc6d8?w=800',
            ],
            $categoryModels['sweets-mithai-desserts'] ?? 10 => [
                'https://images.unsplash.com/photo-1599488615731-7e5c2823ff28?w=800',
                'https://images.unsplash.com/photo-1587314168485-3236d6710814?w=800',
            ],
        ];

        // Existing product image map to skip duplicates
        $existingProductImageIds = ProductImage::pluck('product_id')->mapWithKeys(fn($id) => [$id => true])->toArray();

        $updatedProducts = 0;
        $imagesInserted = 0;

        Product::chunk(300, function ($products) use (
            &$updatedProducts, &$imagesInserted, $brandModels,
            $categoryModels, $defaultCatId, $categoryImagesPool, $existingProductImageIds
        ) {
            $imageBatch = [];
            $now = now();

            foreach ($products as $product) {
                $nameUpper = strtoupper($product->name);

                // Determine Brand ID
                $matchedBrandId = $product->brand_id;
                foreach ($brandModels as $bName => $bModel) {
                    if (str_contains($nameUpper, strtoupper($bName))) {
                        $matchedBrandId = $bModel->id;
                        break;
                    }
                }

                // Determine Category ID
                $matchedCategoryId = $product->category_id ?: $defaultCatId;
                if (preg_match('/MASALA|POWDER|TURMERIC|CHILLI|CORIANDER|CUMIN|GARAM|SPICE|METHI|HING|AMCHUR|SAFFRON/i', $product->name)) {
                    $matchedCategoryId = $categoryModels['spices-masalas'] ?? $matchedCategoryId;
                } elseif (preg_match('/DAL|LENTIL|PULSE|CHANA|RAJMA|MOONG|TOOR|URAD|KABULI|MATAR|PEAS/i', $product->name)) {
                    $matchedCategoryId = $categoryModels['dal-pulses-lentils'] ?? $matchedCategoryId;
                } elseif (preg_match('/RICE|ATTA|FLOUR|POHA|WHEAT|GRAIN|SUJI|MAIDA|BESAN|BASMATI|OATS|CEREAL/i', $product->name)) {
                    $matchedCategoryId = $categoryModels['rice-atta-grains'] ?? $matchedCategoryId;
                } elseif (preg_match('/SNACK|NAMKEEN|BHUJIA|BISCUIT|PARLE|CHIPS|SEV|MATHRI|COOKIES|RUSK|PAPAD/i', $product->name)) {
                    $matchedCategoryId = $categoryModels['snacks-namkeen'] ?? $matchedCategoryId;
                } elseif (preg_match('/OIL|GHEE|MUSTARD|SUNFLOWER|REFINED|SESAME|CANOLA|COCONUT OIL/i', $product->name)) {
                    if (preg_match('/GHEE|BUTTER/i', $product->name)) {
                        $matchedCategoryId = $categoryModels['dairy-ghee'] ?? $matchedCategoryId;
                    } else {
                        $matchedCategoryId = $categoryModels['oils-cooking-mediums'] ?? $matchedCategoryId;
                    }
                } elseif (preg_match('/MILK|BUTTER|PANEER|CHEESE|CREAM|CURD|YOGURT/i', $product->name)) {
                    $matchedCategoryId = $categoryModels['dairy-ghee'] ?? $matchedCategoryId;
                } elseif (preg_match('/PICKLE|CHUTNEY|SAUCE|PASTE|KETCHUP|ACCHAR|VINEGAR/i', $product->name)) {
                    $matchedCategoryId = $categoryModels['pickles-chutneys-sauces'] ?? $matchedCategoryId;
                } elseif (preg_match('/TEA|COFFEE|BEVERAGE|JUICE|DRINK|ROOH|SODA|SHARBATH/i', $product->name)) {
                    $matchedCategoryId = $categoryModels['tea-coffee-beverages'] ?? $matchedCategoryId;
                } elseif (preg_match('/FROZEN|READY|MEAL|PARATHA|NAAN|SAMOSA|PANEER PALAK/i', $product->name)) {
                    $matchedCategoryId = $categoryModels['ready-meals-frozen-foods'] ?? $matchedCategoryId;
                } elseif (preg_match('/GULAB|JAMUN|SWEET|MITHAI|SOAN|RASGULLA|HALWA|LADDU|BARFI|DESSERT/i', $product->name)) {
                    $matchedCategoryId = $categoryModels['sweets-mithai-desserts'] ?? $matchedCategoryId;
                }

                // Update category and brand if different
                if ($product->category_id !== $matchedCategoryId || $product->brand_id !== $matchedBrandId) {
                    DB::table('products')->where('id', $product->id)->update([
                        'category_id' => $matchedCategoryId,
                        'brand_id'    => $matchedBrandId,
                    ]);
                    $updatedProducts++;
                }

                // Insert missing Product Image
                if (!isset($existingProductImageIds[$product->id])) {
                    $pool = $categoryImagesPool[$matchedCategoryId] ?? reset($categoryImagesPool);
                    $imgUrl = $pool[$product->id % count($pool)];

                    $imageBatch[] = [
                        'product_id' => $product->id,
                        'image_path' => $imgUrl,
                        'is_primary' => true,
                        'sort_order' => 0,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];

                    $existingProductImageIds[$product->id] = true;
                }
            }

            if (!empty($imageBatch)) {
                DB::table('product_images')->insert($imageBatch);
                $imagesInserted += count($imageBatch);
            }
        });

        $executionTime = round(microtime(true) - $startTime, 2);

        return response()->json([
            'status' => 'success',
            'message' => 'Database successfully populated with Categories, Brands, and Product Images!',
            'summary' => [
                'total_products_in_db' => Product::count(),
                'products_updated_with_brand_and_category' => $updatedProducts,
                'new_product_images_inserted' => $imagesInserted,
                'total_product_images_now' => ProductImage::count(),
                'total_categories_now' => Category::count(),
                'total_brands_now' => Brand::count(),
                'execution_time_seconds' => $executionTime,
            ]
        ], 200, [], JSON_PRETTY_PRINT);
    }
}
