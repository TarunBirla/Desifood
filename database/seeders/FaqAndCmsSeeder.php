<?php

namespace Database\Seeders;

use App\Models\CmsPage;
use App\Models\Faq;
use Illuminate\Database\Seeder;

class FaqAndCmsSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Initial FAQs
        $faqs = [
            [
                'question' => 'How does In-Store Takeaway Pickup work at Desi Foods Hounslow?',
                'answer' => 'You can browse over 4,000+ authentic Indian groceries online, select your preferred takeaway date and time slot at checkout, and pick up your order directly from our store at 3-4 Green Parade, Whitton Road, Hounslow TW3 2EN.',
                'order' => 1,
            ],
            [
                'question' => 'What payment methods are accepted for grocery pickup?',
                'answer' => 'We accept Pay at Store upon takeaway collection. You can pay conveniently using Cash or Credit/Debit Card when you arrive to collect your groceries.',
                'order' => 2,
            ],
            [
                'question' => 'What are your store pickup operating hours?',
                'answer' => 'Our store is open 7 days a week, Monday through Sunday, from 09:00 AM to 09:00 PM for takeaway order collection.',
                'order' => 3,
            ],
            [
                'question' => 'How does the "Order Again Next Month" recurring feature work?',
                'answer' => 'When viewing your cart, check the "Order Again Next Month" box for any essential grocery item. That product will remain saved in your Next-Month Recurring Orders dashboard. Every month, you can review your saved list, update quantities, and place your recurring monthly order with a single click.',
                'order' => 4,
            ],
            [
                'question' => 'Do you guarantee fresh produce and daily vegetable arrivals?',
                'answer' => 'Yes! We receive daily shipments of fresh Indian vegetables, fruits, and dairy products directly at our Hounslow store. You can inspect all fresh items when picking up your order.',
                'order' => 5,
            ],
            [
                'question' => 'Can I modify my takeaway order after placing it online?',
                'answer' => 'If you need to add or remove items from your order, please call our store staff directly at +44 20 8570 1234 or speak with us upon arrival.',
                'order' => 6,
            ],
        ];

        foreach ($faqs as $faqData) {
            Faq::updateOrCreate(
                ['question' => $faqData['question']],
                $faqData
            );
        }

        // 2. Terms & Conditions Content
        $termsContent = <<<HTML
<h3>1. Introduction & Store Overview</h3>
<p>Welcome to <strong>Desi Foods Hounslow</strong>. By accessing our website, placing takeaway grocery orders, or using our services, you agree to be bound by these Terms and Conditions. Please read them carefully before completing any transaction.</p>

<h3>2. In-Store Takeaway & Order Collections</h3>
<p>Desi Foods operates as an <strong>In-Store Takeaway Pickup</strong> grocery store located at <em>3-4 Green Parade, Whitton Road, Hounslow TW3 2EN</em>. When placing an order online, you select a pickup date and time slot. Please arrive at the store within your chosen time window to ensure your items are ready for collection.</p>

<h3>3. Pricing & Payments</h3>
<p>All prices displayed on our website are in British Pounds (£ GBP) and include applicable VAT. Payments are collected in-store upon takeaway pickup via Cash, Credit Card, or Debit Card. No online card details are charged at checkout.</p>

<h3>4. Next-Month Recurring Orders</h3>
<p>Selecting the "Order Again Next Month" option saves your selected grocery items to your personal recurring dashboard for convenient monthly re-ordering. Selecting this checkbox does not automatically charge your account or dispatch orders; you retain full control to edit quantities and confirm re-orders each month.</p>

<h3>5. Returns, Exchanges & Refunds</h3>
<p>We take pride in providing top-quality groceries and fresh produce. If you discover any defective or damaged product, please present your order receipt at our Hounslow store within 48 hours for an exchange or full refund.</p>

<h3>6. Contact Information</h3>
<p>For questions or store support, please visit us at <strong>3-4 Green Parade, Whitton Road, Hounslow TW3 2EN</strong> or call us at <strong>+44 20 8570 1234</strong>.</p>
HTML;

        CmsPage::updateOrCreate(
            ['slug' => 'terms-and-conditions'],
            [
                'title' => 'Terms & Conditions',
                'content' => $termsContent,
            ]
        );

        // 3. Privacy Policy Content
        $privacyContent = <<<HTML
<h3>1. Data Protection & Privacy Commitment</h3>
<p>At <strong>Desi Foods Hounslow</strong>, we respect your privacy and are committed to protecting the personal data of our customers. This Privacy Policy explains how we collect, use, and safeguard your information.</p>

<h3>2. Information We Collect</h3>
<p>When you register an account or place an order with Desi Foods, we collect basic customer information including your name, email address, contact phone number, and order preferences.</p>

<h3>3. How We Use Your Information</h3>
<p>Your information is used strictly to process your takeaway grocery orders, manage your recurring monthly preferences, provide customer service notifications regarding order status, and send optional promotional offers.</p>

<h3>4. Email Notifications</h3>
<p>We may send direct email notifications regarding order updates, recurring order reminders, or exclusive store specials. You can opt out of marketing emails at any time.</p>

<h3>5. Security & Third-Party Sharing</h3>
<p>We maintain strict security measures to protect your account. Desi Foods does not sell, rent, or trade customer personal data to third parties.</p>

<h3>6. Your Rights</h3>
<p>You have the right to access, update, or request the deletion of your personal account information at any time by logging into your account dashboard or contacting store management.</p>
HTML;

        CmsPage::updateOrCreate(
            ['slug' => 'privacy-policy'],
            [
                'title' => 'Privacy Policy',
                'content' => $privacyContent,
            ]
        );
    }
}
