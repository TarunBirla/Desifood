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

        // 2. High Quality Category Image Mapping
        $categoryImages = [
            1 => [ // Rice, Atta & Grains
                'https://images.unsplash.com/photo-1586201375761-83865001e31c?w=800',
                'https://images.unsplash.com/photo-1509440159596-0249088772ff?w=800',
                'https://images.unsplash.com/photo-1536304993881-ff6e9eefa2a6?w=800',
                'https://images.unsplash.com/photo-1541781774459-bb2af2f05b55?w=800',
            ],
            2 => [ // Spices & Masalas
                'https://images.unsplash.com/photo-1596040033229-a9821ebd058d?w=800',
                'https://images.unsplash.com/photo-1615485290382-441e4d049cb5?w=800',
                'https://images.unsplash.com/photo-1509358271058-acd01cc9386a?w=800',
                'https://images.unsplash.com/photo-1599940824399-b87987ceb72a?w=800',
            ],
            3 => [ // Dal, Pulses & Lentils
                'https://images.unsplash.com/photo-1515543904379-3d757afe72e3?w=800',
                'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=800',
                'https://images.unsplash.com/photo-1584269600464-37b1b58a9fe7?w=800',
            ],
            4 => [ // Snacks & Namkeen
                'https://images.unsplash.com/photo-1599488615731-7e5c2823ff28?w=800',
                'https://images.unsplash.com/photo-1565557623262-b51c2513a641?w=800',
                'https://images.unsplash.com/photo-1621996346565-e3d5d6281270?w=800',
            ],
            5 => [ // Oils & Cooking Mediums
                'https://images.unsplash.com/photo-1474979266404-7eaacbcd87c5?w=800',
                'https://images.unsplash.com/photo-1620706857397-e172df2856c1?w=800',
            ],
            6 => [ // Dairy & Ghee
                'https://images.unsplash.com/photo-1589927986089-35812388d1f4?w=800',
                'https://images.unsplash.com/photo-1628088062854-d1870b4553da?w=800',
            ],
            7 => [ // Pickles, Chutneys & Sauces
                'https://images.unsplash.com/photo-1541832676-9b763b0239ab?w=800',
                'https://images.unsplash.com/photo-1589301760014-d929f3979dbc?w=800',
            ],
            8 => [ // Tea, Coffee & Beverages
                'https://images.unsplash.com/photo-1576092768241-dec231879fc3?w=800',
                'https://images.unsplash.com/photo-1544787219-7f47ccb76574?w=800',
                'https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?w=800',
            ],
            9 => [ // Ready Meals & Frozen Foods
                'https://images.unsplash.com/photo-1601050690597-df0568f70950?w=800',
                'https://images.unsplash.com/photo-1567188040759-fb8a883dc6d8?w=800',
            ],
            10 => [ // Sweets, Mithai & Desserts
                'https://images.unsplash.com/photo-1599488615731-7e5c2823ff28?w=800',
                'https://images.unsplash.com/photo-1587314168485-3236d6710814?w=800',
            ],
        ];

        // 3. Process all 5,741 products
        $updatedProductsCount = 0;
        $addedImagesCount = 0;

        Product::chunk(200, function ($products) use (&$updatedProductsCount, &$addedImagesCount, $brandModels, $categoryImages) {
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

                // Identify Category
                $matchedCategoryId = $product->category_id ?: 1;
                if (preg_match('/MASALA|POWDER|TURMERIC|CHILLI|CORIANDER|CUMIN|GARAM|SPICE|METHI|HING|AMCHUR|SAFFRON/i', $product->name)) {
                    $matchedCategoryId = 2;
                } elseif (preg_match('/DAL|LENTIL|PULSE|CHANA|RAJMA|MOONG|TOOR|URAD|KABULI|MATAR|PEAS/i', $product->name)) {
                    $matchedCategoryId = 3;
                } elseif (preg_match('/RICE|ATTA|FLOUR|POHA|WHEAT|GRAIN|SUJI|MAIDA|BESAN|BASMATI|OATS|CEREAL/i', $product->name)) {
                    $matchedCategoryId = 1;
                } elseif (preg_match('/SNACK|NAMKEEN|BHUJIA|BISCUIT|PARLE|CHIPS|SEV|MATHRI|COOKIES|RUSK|PAPAD/i', $product->name)) {
                    $matchedCategoryId = 4;
                } elseif (preg_match('/OIL|GHEE|MUSTARD|SUNFLOWER|REFINED|SESAME|CANOLA|COCONUT OIL/i', $product->name)) {
                    if (preg_match('/GHEE|BUTTER/i', $product->name)) {
                        $matchedCategoryId = 6;
                    } else {
                        $matchedCategoryId = 5;
                    }
                } elseif (preg_match('/MILK|BUTTER|PANEER|CHEESE|CREAM|CURD|YOGURT/i', $product->name)) {
                    $matchedCategoryId = 6;
                } elseif (preg_match('/PICKLE|CHUTNEY|SAUCE|PASTE|KETCHUP|ACCHAR|VINEGAR/i', $product->name)) {
                    $matchedCategoryId = 7;
                } elseif (preg_match('/TEA|COFFEE|BEVERAGE|JUICE|DRINK|ROOH|SODA|SHARBATH/i', $product->name)) {
                    $matchedCategoryId = 8;
                } elseif (preg_match('/FROZEN|READY|MEAL|PARATHA|NAAN|SAMOSA|PANEER PALAK/i', $product->name)) {
                    $matchedCategoryId = 9;
                } elseif (preg_match('/GULAB|JAMUN|SWEET|MITHAI|SOAN|RASGULLA|HALWA|LADDU|BARFI|DESSERT/i', $product->name)) {
                    $matchedCategoryId = 10;
                }

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
                    $imgsPool = $categoryImages[$matchedCategoryId] ?? $categoryImages[1];
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
