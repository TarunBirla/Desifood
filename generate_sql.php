<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

config(['database.default' => 'mysql']);
config(['database.connections.mysql.host' => '127.0.0.1']);
config(['database.connections.mysql.username' => 'root']);
config(['database.connections.mysql.password' => '']);
\Illuminate\Support\Facades\DB::purge('mysql');

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;

echo "Generating SQL file...\n";

$sql = "-- Desi Foods Categories, Brands & Product Images Import Script\n";
$sql .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

// Categories
$sql .= "-- Categories\n";
$categories = Category::all();
foreach ($categories as $c) {
    $name = addslashes($c->name);
    $slug = addslashes($c->slug);
    $img = addslashes($c->image ?? '');
    $sql .= "INSERT INTO `categories` (`id`, `name`, `slug`, `sort_order`, `status`, `image`, `created_at`, `updated_at`) VALUES ({$c->id}, '{$name}', '{$slug}', {$c->sort_order}, 1, '{$img}', NOW(), NOW()) ON DUPLICATE KEY UPDATE `name`='{$name}', `slug`='{$slug}';\n";
}

// Brands
$sql .= "\n-- Brands\n";
$brands = Brand::all();
foreach ($brands as $b) {
    $name = addslashes($b->name);
    $slug = addslashes($b->slug);
    $sql .= "INSERT INTO `brands` (`id`, `name`, `slug`, `status`, `created_at`, `updated_at`) VALUES ({$b->id}, '{$name}', '{$slug}', 1, NOW(), NOW()) ON DUPLICATE KEY UPDATE `name`='{$name}', `slug`='{$slug}';\n";
}

// Product Updates (Category ID & Brand ID)
$sql .= "\n-- Product Category & Brand Assignments\n";
Product::chunk(500, function ($products) use (&$sql) {
    foreach ($products as $p) {
        $catId = $p->category_id ? (int)$p->category_id : 'NULL';
        $brandId = $p->brand_id ? (int)$p->brand_id : 'NULL';
        $sql .= "UPDATE `products` SET `category_id`={$catId}, `brand_id`={$brandId} WHERE `id`={$p->id};\n";
    }
});

// Product Images
$sql .= "\n-- Product Images\n";
ProductImage::chunk(500, function ($images) use (&$sql) {
    foreach ($images as $img) {
        $imgPath = addslashes($img->image_path);
        $isPrimary = $img->is_primary ? 1 : 0;
        $sql .= "INSERT INTO `product_images` (`id`, `product_id`, `image_path`, `is_primary`, `sort_order`, `created_at`, `updated_at`) VALUES ({$img->id}, {$img->product_id}, '{$imgPath}', {$isPrimary}, 0, NOW(), NOW()) ON DUPLICATE KEY UPDATE `image_path`='{$imgPath}';\n";
    }
});

$sql .= "\nSET FOREIGN_KEY_CHECKS=1;\n";

file_put_contents(__DIR__ . '/update_products_categories_brands_images.sql', $sql);

echo "DONE! SQL file saved to: update_products_categories_brands_images.sql\n";
