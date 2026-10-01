<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Database\Seeder;

class PopulateProductImagesAndBrands extends Seeder
{
    public function run()
    {
        // 1. Ensure all Brands exist
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
            'SOCIETY' => 'society-tea',
            'MAGGI' => 'maggi',
            'KNORR' => 'knorr',
            'PRIYA' => 'priya',
            'ROOPAK' => 'roopak',
            'BADSHAH' => 'badshah',
            'EVERGREEN' => 'evergreen',
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
        foreach ($brandMap as $name => $slug) {
            $brandModels[$name] = Brand::firstOrCreate(['slug' => $slug], [
                'name' => $name,
                'status' => true,
            ]);
        }

        // 2. Map category slugs to actual database IDs
        $slugToId = Category::pluck('id', 'slug')->toArray();
        $defaultCatId = current($slugToId) ?: 1;

        $categoryImages = [
            'spices-masalas' => [
                'https://images.unsplash.com/photo-1596040033229-a9821ebd058d?w=800',
                'https://images.unsplash.com/photo-1615485290382-441e4d049cb5?w=800',
                'https://images.unsplash.com/photo-1509358271058-acd01cc9386a?w=800',
                'https://images.unsplash.com/photo-1599940824399-b87987ceb72a?w=800',
            ],
            'rice-grains' => [
                'https://images.unsplash.com/photo-1586201375761-83865001e31c?w=800',
                'https://images.unsplash.com/photo-1509440159596-0249088772ff?w=800',
                'https://images.unsplash.com/photo-1536304993881-ff6e9eefa2a6?w=800',
                'https://images.unsplash.com/photo-1541781774459-bb2af2f05b55?w=800',
            ],
            'lentils-pulses' => [
                'https://images.unsplash.com/photo-1515543904379-3d757afe72e3?w=800',
                'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=800',
                'https://images.unsplash.com/photo-1584269600464-37b1b58a9fe7?w=800',
            ],
            'sweets-snacks' => [
                'https://images.unsplash.com/photo-1599488615731-7e5c2823ff28?w=800',
                'https://images.unsplash.com/photo-1565557623262-b51c2513a641?w=800',
                'https://images.unsplash.com/photo-1621996346565-e3d5d6281270?w=800',
            ],
            'ghee-oils' => [
                'https://images.unsplash.com/photo-1474979266404-7eaacbcd87c5?w=800',
                'https://images.unsplash.com/photo-1620706857397-e172df2856c1?w=800',
            ],
            'frozen-foods' => [
                'https://images.unsplash.com/photo-1601050690597-df0568f70950?w=800',
                'https://images.unsplash.com/photo-1567188040759-fb8a883dc6d8?w=800',
            ],
            'flour-atta' => [
                'https://images.unsplash.com/photo-1509440159596-0249088772ff?w=800',
            ],
            'fresh-vegetables' => [
                'https://images.unsplash.com/photo-1540420773420-3366772f4999?w=800',
            ]
        ];

        // 3. Process all products
        $updatedProductsCount = 0;
        $addedImagesCount = 0;

        Product::chunk(200, function ($products) use (&$updatedProductsCount, &$addedImagesCount, $brandModels, $slugToId, $defaultCatId, $categoryImages) {
            foreach ($products as $product) {
                $nameUpper = strtoupper($product->name);

                // Identify Brand
                $matchedBrandId = $product->brand_id;
                foreach ($brandModels as $brandName => $brandModel) {
                    if (str_contains($nameUpper, strtoupper($brandName))) {
                        $matchedBrandId = $brandModel->id;
                        break;
                    }
                }

                // Identify Category Slug
                $targetSlug = 'spices-masalas';
                if (preg_match('/MASALA|POWDER|TURMERIC|CHILLI|CORIANDER|CUMIN|GARAM|SPICE|METHI|HING|AMCHUR|SAFFRON/i', $product->name)) {
                    $targetSlug = 'spices-masalas';
                } elseif (preg_match('/DAL|LENTIL|PULSE|CHANA|RAJMA|MOONG|TOOR|URAD|KABULI|MATAR|PEAS/i', $product->name)) {
                    $targetSlug = 'lentils-pulses';
                } elseif (preg_match('/ATTA|FLOUR|SUJI|MAIDA|BESAN/i', $product->name)) {
                    $targetSlug = 'flour-atta';
                } elseif (preg_match('/RICE|POHA|WHEAT|GRAIN|BASMATI|OATS|CEREAL/i', $product->name)) {
                    $targetSlug = 'rice-grains';
                } elseif (preg_match('/SNACK|NAMKEEN|BHUJIA|BISCUIT|PARLE|CHIPS|SEV|MATHRI|COOKIES|RUSK|PAPAD|GULAB|JAMUN|SWEET|MITHAI|SOAN|RASGULLA|HALWA|LADDU|BARFI|DESSERT/i', $product->name)) {
                    $targetSlug = 'sweets-snacks';
                } elseif (preg_match('/OIL|GHEE|MUSTARD|SUNFLOWER|REFINED|SESAME|CANOLA|COCONUT OIL/i', $product->name)) {
                    $targetSlug = 'ghee-oils';
                } elseif (preg_match('/MILK|BUTTER|PANEER|CHEESE|CREAM|CURD|YOGURT/i', $product->name)) {
                    $targetSlug = 'ghee-oils';
                } elseif (preg_match('/FROZEN|READY|MEAL|PARATHA|NAAN|SAMOSA|PANEER PALAK/i', $product->name)) {
                    $targetSlug = 'frozen-foods';
                } elseif (preg_match('/VEGETABLE|OKRA|KARELA|ONION|POTATO|TOMATO/i', $product->name)) {
                    $targetSlug = 'fresh-vegetables';
                }

                $matchedCategoryId = $slugToId[$targetSlug] ?? $product->category_id ?: $defaultCatId;

                // Update product category & brand if changed
                if ($product->category_id !== $matchedCategoryId || $product->brand_id !== $matchedBrandId) {
                    $product->update([
                        'category_id' => $matchedCategoryId,
                        'brand_id'    => $matchedBrandId,
                    ]);
                    $updatedProductsCount++;
                }

                // Check and Add Images in product_images table
                if ($product->images()->count() === 0) {
                    $imgsPool = $categoryImages[$targetSlug] ?? current($categoryImages);
                    $imgUrl = $imgsPool[$product->id % count($imgsPool)];

                    ProductImage::create([
                        'product_id' => $product->id,
                        'image_path' => $imgUrl,
                        'is_primary' => true,
                    ]);
                    $addedImagesCount++;
                }
            }
        });

        echo "SUCCESS: Updated {$updatedProductsCount} products with brand/category IDs, and added {$addedImagesCount} images to product_images table." . PHP_EOL;
    }
}
