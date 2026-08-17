<?php

namespace Database\Seeders;

use App\Models\Blog;
use App\Models\BlogCategory;
use App\Models\Page;
use App\Models\Testimonial;
use Illuminate\Database\Seeder;

class PageBlogSeeder extends Seeder
{
    public function run(): void
    {
        // CMS Static Pages
        Page::updateOrCreate(['slug' => 'about-us'], [
            'title' => 'Our Story — Desi Foods Hounslow',
            'content' => '<h1>A Home Away From Home for Every Desi Family</h1><p>Nestled in the heart of Hounslow at 3-4 Green Parade on Whitton Road, Desi Foods has been the go-to destination for authentic Indian groceries since 2010. We understand the nostalgia of home — the aroma of masala chai, the comfort of basmati rice, and the ritual of festive cooking.</p><p>Our shelves are carefully curated with over 4,000 products spanning fresh produce, premium spices, frozen delicacies, and everyday pantry essentials.</p>',
            'seo_title' => 'Our Story | Desi Foods Hounslow',
            'seo_description' => 'Hounslow\'s premier destination for authentic Indian groceries since 2010.'
        ]);

        Page::updateOrCreate(['slug' => 'privacy-policy'], [
            'title' => 'Privacy Policy',
            'content' => '<h1>Privacy Policy</h1><p>Your privacy is important to us. Desi Foods Hounslow ensures all your personal details, order records, and contact information are protected and never shared with third parties.</p>',
            'seo_title' => 'Privacy Policy | Desi Foods Hounslow'
        ]);

        Page::updateOrCreate(['slug' => 'terms-conditions'], [
            'title' => 'Terms & Conditions',
            'content' => '<h1>Terms & Conditions</h1><p>Welcome to Desi Foods Hounslow online store. By placing an order, you agree to our standard store policies, doorstep delivery terms, and customer satisfaction guarantee.</p>',
            'seo_title' => 'Terms & Conditions | Desi Foods Hounslow'
        ]);

        // Blog Categories & Articles
        $bCatCooking = BlogCategory::firstOrCreate(['slug' => 'desi-recipes-cooking'], ['name' => 'Desi Recipes & Cooking Guides']);
        $bCatFestivals = BlogCategory::firstOrCreate(['slug' => 'festivals-sweets'], ['name' => 'Festivals & Indian Sweets']);

        Blog::updateOrCreate(['slug' => 'essential-spices-every-indian-kitchen-must-have'], [
            'title' => 'Essential Spices Every Indian Kitchen Must Have',
            'category_id' => $bCatCooking->id,
            'featured_image' => 'https://images.unsplash.com/photo-1596040033229-a9821ebd058d?w=800',
            'excerpt' => 'A guide to the core spices behind authentic Indian cooking — Garam Masala, Turmeric, Cumin, Coriander & Hing.',
            'content' => '<p>Indian cuisine is world-renowned for its complex flavors and health-giving aromatic spices. To cook authentic desi curries, every home needs five essential spices: Garam Masala, Haldi (Turmeric), Jeera (Cumin seeds), Dhania (Coriander powder), and Hing (Asafoetida)...</p>',
            'is_published' => true,
            'published_at' => now(),
        ]);

        Blog::updateOrCreate(['slug' => 'secret-to-cooking-perfect-fluffy-basmati-rice'], [
            'title' => 'The Secret to Cooking Perfect Fluffy Basmati Rice Every Time',
            'category_id' => $bCatCooking->id,
            'featured_image' => 'https://images.unsplash.com/photo-1586201375761-83865001e31c?w=800',
            'excerpt' => 'Learn the simple soaking and absorption technique to get separate, fragrant Basmati grains.',
            'content' => '<p>Cooking restaurant-quality Basmati rice at home is easy when you follow three rules: soak aged grains for 30 minutes, use a 1:2 rice-to-water ratio, and let it rest off the heat covered for 10 minutes before fluffing with a fork...</p>',
            'is_published' => true,
            'published_at' => now(),
        ]);

        Blog::updateOrCreate(['slug' => 'top-5-popular-indian-sweets-for-festive-celebrations'], [
            'title' => 'Top 5 Popular Indian Sweets for Festive Celebrations',
            'category_id' => $bCatFestivals->id,
            'featured_image' => 'https://images.unsplash.com/photo-1599488615731-7e5c2823ff28?w=800',
            'excerpt' => 'From warm Gulab Jamun to flaky Soan Papdi, discover the favorite sweets for Diwali, Eid, and Rakhi.',
            'content' => '<p>No Indian celebration is complete without Mithai! Whether gifting family or serving guests, Gulab Jamun, Rasgulla, Soan Papdi, Kaju Katli, and Motichoor Ladoo top the list of delicious treats available at Desi Foods Hounslow...</p>',
            'is_published' => true,
            'published_at' => now(),
        ]);

        // Client Testimonials
        Testimonial::updateOrCreate(['client_name' => 'Jyoshna'], [
            'client_title' => 'Hounslow Resident',
            'company_name' => 'Verified Shopper',
            'client_avatar' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=200',
            'content' => 'This store gives "Home away from Home" feeling. They always have what I need from lentils to frozen parathas to snacks!! The staff are extremely friendly and helpful.',
            'rating' => 5,
            'is_featured' => true,
            'status' => true,
        ]);

        Testimonial::updateOrCreate(['client_name' => 'Divya Goswami'], [
            'client_title' => 'Regular Customer',
            'company_name' => 'Verified Shopper',
            'client_avatar' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?w=200',
            'content' => 'Lovely little shop with all the Indian groceries I need. The staff are so kind and welcoming — always a pleasure to visit! Neatly arranged with great variety.',
            'rating' => 5,
            'is_featured' => true,
            'status' => true,
        ]);

        Testimonial::updateOrCreate(['client_name' => 'Movies Weekly'], [
            'client_title' => 'Local Foodie',
            'company_name' => 'Verified Shopper',
            'client_avatar' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=200',
            'content' => 'Nice store with a wide variety of Indian groceries. Well organized and the staff is really helpful. You can get all the things you need — veggies, masala items, sweets & crisps.',
            'rating' => 5,
            'is_featured' => true,
            'status' => true,
        ]);
    }
}
