<?php

namespace Database\Seeders;

use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use Illuminate\Database\Seeder;

class CatalogSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Food Categories
        $catSpices = Category::firstOrCreate(['slug' => 'spices-masalas'], [
            'name' => 'Spices & Masalas',
            'image' => 'https://images.unsplash.com/photo-1596040033229-a9821ebd058d?w=800',
            'banner' => 'https://images.unsplash.com/photo-1596040033229-a9821ebd058d?w=1200',
            'description' => 'Aromatic ground spices, whole spices, and authentic Indian masala blends.',
            'sort_order' => 1,
            'status' => true,
        ]);

        $catRice = Category::firstOrCreate(['slug' => 'rice-grains'], [
            'name' => 'Rice & Basmati',
            'image' => 'https://images.unsplash.com/photo-1586201375761-83865001e31c?w=800',
            'banner' => 'https://images.unsplash.com/photo-1586201375761-83865001e31c?w=1200',
            'description' => 'Aged Extra Long Basmati Rice, Sona Masoori, Poha, and whole grains.',
            'sort_order' => 2,
            'status' => true,
        ]);

        $catLentils = Category::firstOrCreate(['slug' => 'lentils-pulses'], [
            'name' => 'Lentils & Pulses',
            'image' => 'https://images.unsplash.com/photo-1515543904379-3d757afe72e3?w=800',
            'description' => 'Toor Dal, Moong Dal, Chana Dal, Rajma, Kala Chana, and chickpeas.',
            'sort_order' => 3,
            'status' => true,
        ]);

        $catVegetables = Category::firstOrCreate(['slug' => 'fresh-vegetables'], [
            'name' => 'Fresh Vegetables',
            'image' => 'https://images.unsplash.com/photo-1540420773420-3366772f4999?w=800',
            'description' => 'Fresh Okra (Bhindi), Karela, Curry Leaves, Green Chillies, Ginger, Garlic.',
            'sort_order' => 4,
            'status' => true,
        ]);

        $catGhee = Category::firstOrCreate(['slug' => 'ghee-oils'], [
            'name' => 'Ghee & Oils',
            'image' => 'https://images.unsplash.com/photo-1589927986089-35812388d1f4?w=800',
            'description' => 'Pure Desi Cow Ghee, Mustard Oil, Sesame Oil, Coconut Oil, Sunflower Oil.',
            'sort_order' => 5,
            'status' => true,
        ]);

        $catFrozen = Category::firstOrCreate(['slug' => 'frozen-foods'], [
            'name' => 'Frozen Foods',
            'image' => 'https://images.unsplash.com/photo-1601050690597-df0568f70950?w=800',
            'description' => 'Frozen Parathas, Samosas, Naan, Paneer, Frozen Vegetables, Spring Rolls.',
            'sort_order' => 6,
            'status' => true,
        ]);

        $catSnacks = Category::firstOrCreate(['slug' => 'sweets-snacks'], [
            'name' => 'Sweets & Snacks',
            'image' => 'https://images.unsplash.com/photo-1599488615731-7e5c2823ff28?w=800',
            'description' => 'Haldiram Namkeen, Gulab Jamun, Soan Papdi, Bikaji Bhujia, Parle-G.',
            'sort_order' => 7,
            'status' => true,
        ]);

        $catAtta = Category::firstOrCreate(['slug' => 'flour-atta'], [
            'name' => 'Flour & Atta',
            'image' => 'https://images.unsplash.com/photo-1509440159596-0249088772ff?w=800',
            'description' => 'Chakki Whole Wheat Atta, Besan (Gram Flour), Maida, Suji (Semolina).',
            'sort_order' => 8,
            'status' => true,
        ]);

        // 2. Brands
        $bMDH = Brand::firstOrCreate(['slug' => 'mdh'], ['name' => 'MDH', 'description' => 'Asli Masale Sach Sach MDH!']);
        $bEverest = Brand::firstOrCreate(['slug' => 'everest'], ['name' => 'EVEREST', 'description' => 'Taste with Taste Maker!']);
        $bTRS = Brand::firstOrCreate(['slug' => 'trs'], ['name' => 'TRS', 'description' => 'Asia\'s Finest Indian Grocery Brand.']);
        $bNatco = Brand::firstOrCreate(['slug' => 'natco'], ['name' => 'NATCO', 'description' => 'Quality spices and lentils.']);
        $bTilda = Brand::firstOrCreate(['slug' => 'tilda'], ['name' => 'TILDA', 'description' => 'Pure Basmati Rice.']);
        $bDaawat = Brand::firstOrCreate(['slug' => 'daawat'], ['name' => 'DAAWAT', 'description' => 'The Fine Art of Basmati.']);
        $bAmul = Brand::firstOrCreate(['slug' => 'amul'], ['name' => 'AMUL', 'description' => 'The Taste of India.']);
        $bHaldirams = Brand::firstOrCreate(['slug' => 'haldirams'], ['name' => 'HALDIRAM\'S', 'description' => 'Authentic Indian Sweets & Snacks.']);
        $bShana = Brand::firstOrCreate(['slug' => 'shana'], ['name' => 'SHANA', 'description' => 'Authentic Frozen Vegetables & Breads.']);
        $bAshoka = Brand::firstOrCreate(['slug' => 'ashoka'], ['name' => 'ASHOKA', 'description' => 'Authentic Indian Foods.']);
        $bParle = Brand::firstOrCreate(['slug' => 'parle'], ['name' => 'PARLE', 'description' => 'India\'s Favourite Biscuits.']);
        $bAashirvaad = Brand::firstOrCreate(['slug' => 'aashirvaad'], ['name' => 'AASHIRVAAD', 'description' => 'Pure Chakki Atta.']);
        $bBikaji = Brand::firstOrCreate(['slug' => 'bikaji'], ['name' => 'BIKAJI', 'description' => 'Amitji Ki Choice Bikaji Bhujia.']);

        // 3. Products

        // P1: MDH Garam Masala
        $p1 = Product::updateOrCreate(['sku' => 'DESI-SP-001'], [
            'name' => 'MDH Garam Masala Powder 100g',
            'slug' => 'mdh-garam-masala-powder-100g',
            'category_id' => $catSpices->id,
            'brand_id' => $bMDH->id,
            'price' => 2.49,
            'sale_price' => 1.99,
            'cost_price' => 1.20,
            'stock' => 150,
            'min_stock_warning' => 10,
            'description' => 'MDH Garam Masala is a traditional spicy blend of aromatic herbs and whole ground spices. Perfect for curry, biryani, and vegetable subzi.',
            'specifications' => [
                'Dietary Type' => '100% Vegetarian',
                'Net Weight' => '100g',
                'Ingredients' => 'Coriander, Cumin, Black Pepper, Cinnamon, Cardamom, Cloves, Nutmeg',
                'Storage' => 'Store in a cool dry place away from sunlight',
                'Country of Origin' => 'India'
            ],
            'faqs' => [
                ['q' => 'Is this product 100% vegetarian?', 'a' => 'Yes, all MDH spice powders are 100% vegetarian.'],
                ['q' => 'What is the shelf life?', 'a' => 'Best before 12 months from manufacture date.']
            ],
            'is_active' => true,
            'is_featured' => true,
            'is_trending' => true,
            'is_new_arrival' => false,
            'has_variants' => false,
            'rating_avg' => 4.90,
            'reviews_count' => 128,
            'warranty_info' => 'Quality Sealed Guarantee',
            'return_policy_info' => '7 days store return available',
            'seo_title' => 'MDH Garam Masala 100g | Desi Foods Hounslow',
            'seo_description' => 'Buy MDH Garam Masala 100g at best prices in Hounslow. Genuine Indian spice blend.'
        ]);
        ProductImage::firstOrCreate(['product_id' => $p1->id, 'image_path' => 'https://images.unsplash.com/photo-1596040033229-a9821ebd058d?w=800'], ['is_primary' => true, 'sort_order' => 1]);

        // P2: Daawat Extra Long Basmati Rice 5kg
        $p2 = Product::updateOrCreate(['sku' => 'DESI-RC-002'], [
            'name' => 'Daawat Extra Long Premium Basmati Rice 5kg',
            'slug' => 'daawat-extra-long-premium-basmati-rice-5kg',
            'category_id' => $catRice->id,
            'brand_id' => $bDaawat->id,
            'price' => 12.99,
            'sale_price' => 9.99,
            'cost_price' => 6.50,
            'stock' => 200,
            'min_stock_warning' => 15,
            'description' => 'Aged Extra Long Grain Basmati Rice. Fluffy, non-sticky grains with captivating natural aroma. Ideal for Royal Biryani, Pulao, and everyday meals.',
            'specifications' => [
                'Grain Length' => 'Extra Long Grain (Aged 2 Years)',
                'Net Weight' => '5 kg Bag',
                'Dietary Type' => '100% Gluten-Free Vegetarian',
                'Cooking Time' => '12-15 Minutes',
                'Origin' => 'Himalayan Foothills, India'
            ],
            'is_active' => true,
            'is_featured' => true,
            'is_trending' => true,
            'is_new_arrival' => true,
            'has_variants' => false,
            'rating_avg' => 4.95,
            'reviews_count' => 245,
            'warranty_info' => 'Aged Basmati Quality Seal'
        ]);
        ProductImage::firstOrCreate(['product_id' => $p2->id, 'image_path' => 'https://images.unsplash.com/photo-1586201375761-83865001e31c?w=800'], ['is_primary' => true, 'sort_order' => 1]);

        // P3: Shana Frozen Plain Paratha Pack 5pc
        $p3 = Product::updateOrCreate(['sku' => 'DESI-FZ-003'], [
            'name' => 'Shana Frozen Plain Paratha Pack (5pc)',
            'slug' => 'shana-frozen-plain-paratha-pack-5pc',
            'category_id' => $catFrozen->id,
            'brand_id' => $bShana->id,
            'price' => 2.99,
            'cost_price' => 1.50,
            'stock' => 90,
            'min_stock_warning' => 10,
            'description' => 'Crispy, flaky multi-layered Indian parathas ready in 3 minutes on a hot tava. Made with unbleached wheat flour and no artificial preservatives.',
            'specifications' => [
                'Pack Quantity' => '5 Parathas (400g)',
                'Dietary Info' => '100% Veg, No Trans Fat',
                'Preparation' => 'Cook from frozen on skillet for 3 mins'
            ],
            'is_active' => true,
            'is_featured' => true,
            'is_new_arrival' => true,
            'has_variants' => false,
            'rating_avg' => 4.80,
            'reviews_count' => 89,
        ]);
        ProductImage::firstOrCreate(['product_id' => $p3->id, 'image_path' => 'https://images.unsplash.com/photo-1601050690597-df0568f70950?w=800'], ['is_primary' => true, 'sort_order' => 1]);

        // P4: Haldiram's All-in-One Mixture 400g
        $p4 = Product::updateOrCreate(['sku' => 'DESI-SN-004'], [
            'name' => 'Haldiram\'s Nagpur All-in-One Mixture 400g',
            'slug' => 'haldirams-nagpur-all-in-one-mixture-400g',
            'category_id' => $catSnacks->id,
            'brand_id' => $bHaldirams->id,
            'price' => 3.49,
            'cost_price' => 1.80,
            'stock' => 180,
            'min_stock_warning' => 20,
            'description' => 'India\'s favorite sweet & spicy crunchy mixture containing cornflakes, nuts, lentils, and crispy sev. Perfect with evening Masala Chai!',
            'specifications' => [
                'Net Weight' => '400g',
                'Flavor Profile' => 'Sweet, Spicy & Tangy',
                'Packaging' => 'Foil Sealed Freshness Pack'
            ],
            'is_active' => true,
            'is_featured' => true,
            'rating_avg' => 4.92,
            'reviews_count' => 312,
        ]);
        ProductImage::firstOrCreate(['product_id' => $p4->id, 'image_path' => 'https://images.unsplash.com/photo-1599488615731-7e5c2823ff28?w=800'], ['is_primary' => true, 'sort_order' => 1]);

        // P5: TRS Turmeric Powder Haldi 400g
        $p5 = Product::updateOrCreate(['sku' => 'DESI-SP-005'], [
            'name' => 'TRS Turmeric Powder (Haldi) 400g',
            'slug' => 'trs-turmeric-powder-haldi-400g',
            'category_id' => $catSpices->id,
            'brand_id' => $bTRS->id,
            'price' => 1.99,
            'cost_price' => 0.90,
            'stock' => 220,
            'description' => 'Pure golden turmeric ground from premium Alleppey roots. Known for vibrant color, rich aroma, and natural antioxidant benefits.',
            'specifications' => [
                'Net Weight' => '400g Bag',
                'Curcumin Content' => 'High Grade 3.5%',
                'Origin' => 'India'
            ],
            'is_active' => true,
            'is_featured' => false,
            'rating_avg' => 4.88,
            'reviews_count' => 156,
        ]);
        ProductImage::firstOrCreate(['product_id' => $p5->id, 'image_path' => 'https://images.unsplash.com/photo-1615485290382-441e4d049cb5?w=800'], ['is_primary' => true, 'sort_order' => 1]);

        // P6: TRS Toor Dal Arhar 2kg
        $p6 = Product::updateOrCreate(['sku' => 'DESI-LN-006'], [
            'name' => 'TRS Toor Dal (Yellow Split Pigeon Peas) 2kg',
            'slug' => 'trs-toor-dal-yellow-split-pigeon-peas-2kg',
            'category_id' => $catLentils->id,
            'brand_id' => $bTRS->id,
            'price' => 5.50,
            'sale_price' => 4.49,
            'cost_price' => 2.50,
            'stock' => 130,
            'description' => 'High protein polished yellow split pigeon peas. Essential staple for Gujarati Dal, Sambar, and everyday Dal Tadka.',
            'specifications' => [
                'Net Weight' => '2 kg',
                'Protein' => '22g per 100g',
                'Dietary Type' => '100% Vegan & Gluten Free'
            ],
            'is_active' => true,
            'is_featured' => true,
            'rating_avg' => 4.85,
            'reviews_count' => 98,
        ]);
        ProductImage::firstOrCreate(['product_id' => $p6->id, 'image_path' => 'https://images.unsplash.com/photo-1515543904379-3d757afe72e3?w=800'], ['is_primary' => true, 'sort_order' => 1]);

        // P7: Ashoka Frozen Vegetable Samosas 12pc
        $p7 = Product::updateOrCreate(['sku' => 'DESI-FZ-007'], [
            'name' => 'Ashoka Vegetable Samosas (12pc)',
            'slug' => 'ashoka-vegetable-samosas-12pc',
            'category_id' => $catFrozen->id,
            'brand_id' => $bAshoka->id,
            'price' => 3.99,
            'cost_price' => 2.00,
            'stock' => 85,
            'description' => 'Authentic hand-folded crispy samosas stuffed with spiced potatoes, green peas, and traditional Indian spices.',
            'specifications' => [
                'Quantity' => '12 Pieces (480g)',
                'Cooking Method' => 'Deep fry, air fry or oven bake'
            ],
            'is_active' => true,
            'is_featured' => true,
            'rating_avg' => 4.75,
            'reviews_count' => 67,
        ]);
        ProductImage::firstOrCreate(['product_id' => $p7->id, 'image_path' => 'https://images.unsplash.com/photo-1601050690597-df0568f70950?w=800'], ['is_primary' => true, 'sort_order' => 1]);

        // P8: Parle-G Family Pack
        $p8 = Product::updateOrCreate(['sku' => 'DESI-SN-008'], [
            'name' => 'Parle-G Biscuits Original Family Pack 800g',
            'slug' => 'parle-g-biscuits-original-family-pack-800g',
            'category_id' => $catSnacks->id,
            'brand_id' => $bParle->id,
            'price' => 2.30,
            'sale_price' => 1.79,
            'cost_price' => 0.90,
            'stock' => 300,
            'description' => 'The World\'s largest selling biscuit brand! Crisp glucose biscuits that go perfectly with hot morning tea.',
            'specifications' => [
                'Net Weight' => '800g Value Pack',
                'Ingredients' => 'Wheat Flour, Sugar, Vegetable Oil, Invert Sugar Syrup'
            ],
            'is_active' => true,
            'is_featured' => true,
            'rating_avg' => 4.98,
            'reviews_count' => 421,
        ]);
        ProductImage::firstOrCreate(['product_id' => $p8->id, 'image_path' => 'https://images.unsplash.com/photo-1558961363-fa8fdf82db35?w=800'], ['is_primary' => true, 'sort_order' => 1]);

        // P9: Amul Pure Desi Ghee 500ml
        $p9 = Product::updateOrCreate(['sku' => 'DESI-GH-009'], [
            'name' => 'Amul Pure Desi Ghee 500ml Pack',
            'slug' => 'amul-pure-desi-ghee-500ml-pack',
            'category_id' => $catGhee->id,
            'brand_id' => $bAmul->id,
            'price' => 6.99,
            'cost_price' => 4.50,
            'stock' => 110,
            'description' => 'Made from fresh milk cream with granular texture and rich nutty aroma. Essential for rotis, sweets, halwa, and tempering dals.',
            'specifications' => [
                'Volume' => '500ml Carton',
                'Fat Content' => '99.7% Milk Fat',
                'Origin' => 'Gujarat, India'
            ],
            'is_active' => true,
            'is_featured' => true,
            'rating_avg' => 4.96,
            'reviews_count' => 189,
        ]);
        ProductImage::firstOrCreate(['product_id' => $p9->id, 'image_path' => 'https://images.unsplash.com/photo-1589927986089-35812388d1f4?w=800'], ['is_primary' => true, 'sort_order' => 1]);

        // P10: Aashirvaad Whole Wheat Chakki Atta 10kg
        $p10 = Product::updateOrCreate(['sku' => 'DESI-AT-010'], [
            'name' => 'Aashirvaad Whole Wheat Chakki Atta 10kg',
            'slug' => 'aashirvaad-whole-wheat-chakki-atta-10kg',
            'category_id' => $catAtta->id,
            'brand_id' => $bAashirvaad->id,
            'price' => 10.00,
            'sale_price' => 8.49,
            'cost_price' => 5.20,
            'stock' => 160,
            'description' => 'Made from 100% heavy grains sourced from Indian farms. Traditional stone-ground chakki process ensures soft, fluffy rotis for hours.',
            'specifications' => [
                'Net Weight' => '10 kg Bag',
                'Grain Type' => '100% Whole Wheat Sharbati blend',
                'Dietary Fiber' => 'High Fiber'
            ],
            'is_active' => true,
            'is_featured' => true,
            'rating_avg' => 4.94,
            'reviews_count' => 267,
        ]);
        ProductImage::firstOrCreate(['product_id' => $p10->id, 'image_path' => 'https://images.unsplash.com/photo-1509440159596-0249088772ff?w=800'], ['is_primary' => true, 'sort_order' => 1]);

        // P11: Alphonso Mango Pulp 850g
        $p11 = Product::updateOrCreate(['sku' => 'DESI-SN-011'], [
            'name' => 'Ratnagiri Alphonso Mango Pulp (Sweetened) 850g',
            'slug' => 'ratnagiri-alphonso-mango-pulp-sweetened-850g',
            'category_id' => $catSnacks->id,
            'price' => 3.99,
            'cost_price' => 2.00,
            'stock' => 95,
            'description' => 'Pure pulp from handpicked Ratnagiri Alphonso mangoes. Perfect for Aamras, Mango Lassi, Kulfi, Milkshakes, and desserts.',
            'specifications' => [
                'Net Weight' => '850g Can',
                'Ingredients' => 'Alphonso Mango Pulp (95%), Sugar Syrup',
                'Origin' => 'Maharashtra, India'
            ],
            'is_active' => true,
            'is_featured' => true,
            'is_new_arrival' => true,
            'rating_avg' => 4.90,
            'reviews_count' => 54,
        ]);
        ProductImage::firstOrCreate(['product_id' => $p11->id, 'image_path' => 'https://images.unsplash.com/photo-1553279768-865429fa0078?w=800'], ['is_primary' => true, 'sort_order' => 1]);
    }
}
