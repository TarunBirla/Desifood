<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ProductImageGeneratorService
{
    /**
     * Generate a dynamic prompt for the product based on its attributes.
     */
    public function generatePrompt(Product $product): string
    {
        $name = trim($product->name);
        $categoryName = $product->category ? $product->category->name : '';
        $brandName = $product->brand ? $product->brand->name : '';

        // Extract extra specifications if present
        $specText = '';
        if (is_array($product->specifications)) {
            $specs = [];
            foreach ($product->specifications as $key => $val) {
                if (is_string($val) || is_numeric($val)) {
                    $specs[] = "$key: $val";
                }
            }
            if (!empty($specs)) {
                $specText = implode(', ', $specs);
            }
        }

        $promptParts = [];
        $promptParts[] = "Professional commercial e-commerce product photograph of $name";

        if ($categoryName) {
            $promptParts[] = "in $categoryName category";
        }
        if ($brandName && strtolower($brandName) !== 'all brand') {
            $promptParts[] = "by $brandName";
        }
        if ($specText) {
            $promptParts[] = "with features ($specText)";
        }

        $promptParts[] = "centered view, clean neutral studio lighting, high resolution, 8k detail, realistic texture, minimalist backdrop, no people, no watermarks, no extra text, studio quality product photography";

        return implode(', ', $promptParts);
    }

    /**
     * Generate and save product image to the specified destination path.
     *
     * @param Product $product
     * @param string $destinationAbsolutePath
     * @return bool
     */
    public function generateImageForProduct(Product $product, string $destinationAbsolutePath): bool
    {
        $prompt = $this->generatePrompt($product);

        $directory = dirname($destinationAbsolutePath);
        if (!file_exists($directory)) {
            mkdir($directory, 0777, true);
        }

        $apiKey = env('IMAGE_GENERATION_API_KEY', env('OPENAI_API_KEY'));
        $provider = env('IMAGE_GENERATION_PROVIDER', 'auto');

        // 1. Try OpenAI DALL-E if API key is provided
        if (($provider === 'openai' || ($provider === 'auto' && !empty($apiKey))) && !empty($apiKey)) {
            try {
                $response = Http::withToken($apiKey)
                    ->timeout(30)
                    ->post('https://api.openai.com/v1/images/generations', [
                        'model' => 'dall-e-3',
                        'prompt' => $prompt,
                        'n' => 1,
                        'size' => '1024x1024',
                    ]);

                if ($response->successful()) {
                    $imageUrl = $response->json('data.0.url');
                    if ($imageUrl && $this->downloadAndSaveImage($imageUrl, $destinationAbsolutePath)) {
                        return true;
                    }
                } else {
                    $this->logError($product, "OpenAI API returned status " . $response->status() . ": " . $response->body());
                }
            } catch (\Throwable $e) {
                $this->logError($product, "OpenAI API Exception: " . $e->getMessage());
            }
        }

        // 2. Try Pollinations AI (Free, high-quality AI product photo generator)
        try {
            $cleanPrompt = preg_replace('/[^a-zA-Z0-9\s,]/', '', $product->name);
            $pollinationsUrl = "https://image.pollinations.ai/prompt/" . rawurlencode("commercial product photo of $cleanPrompt, studio lighting, clean background, 8k, e-commerce product shot") . "?width=800&height=800&nologo=true&seed=" . ($product->id * 7);

            if ($this->downloadAndSaveImage($pollinationsUrl, $destinationAbsolutePath)) {
                return true;
            }
        } catch (\Throwable $e) {
            $this->logError($product, "Pollinations AI download failed: " . $e->getMessage());
        }

        // 3. Fallback: Unsplash Source Product Search
        try {
            $keyword = Str::slug($product->name);
            $unsplashUrl = "https://source.unsplash.com/800x800/?" . urlencode($keyword . ",product");
            if ($this->downloadAndSaveImage($unsplashUrl, $destinationAbsolutePath)) {
                return true;
            }
        } catch (\Throwable $e) {
            $this->logError($product, "Unsplash fallback failed: " . $e->getMessage());
        }

        // 4. Ultimate Fallback: Generate clean SVG/GD placeholder image
        return $this->generateGDPlaceholder($product, $destinationAbsolutePath);
    }

    /**
     * Download an image URL and save to destination path.
     */
    protected function downloadAndSaveImage(string $url, string $destinationPath): bool
    {
        try {
            $response = Http::timeout(20)->withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36'
            ])->get($url);

            if ($response->successful() && strlen($response->body()) > 1024) {
                file_put_contents($destinationPath, $response->body());
                return $this->validateImageFile($destinationPath);
            }
        } catch (\Throwable $e) {
            // Logged in caller
        }
        return false;
    }

    /**
     * Validate that the generated file exists, is non-empty, and is a valid image.
     */
    public function validateImageFile(string $filePath): bool
    {
        if (!file_exists($filePath) || filesize($filePath) < 512) {
            if (file_exists($filePath)) {
                @unlink($filePath);
            }
            return false;
        }

        $imageInfo = @getimagesize($filePath);
        if ($imageInfo === false || empty($imageInfo[0]) || empty($imageInfo[1])) {
            @unlink($filePath);
            return false;
        }

        return true;
    }

    /**
     * Fallback to create a clean studio product image canvas using GD.
     */
    protected function generateGDPlaceholder(Product $product, string $destinationPath): bool
    {
        if (!function_exists('imagecreatetruecolor')) {
            return false;
        }

        $width = 800;
        $height = 800;
        $image = imagecreatetruecolor($width, $height);

        // Soft studio gradient background
        $bgColor = imagecolorallocate($image, 245, 247, 250);
        $textColor = imagecolorallocate($image, 40, 50, 60);
        $accentColor = imagecolorallocate($image, 230, 81, 0);

        imagefill($image, 0, 0, $bgColor);

        // Draw clean product box outline
        $boxColor = imagecolorallocate($image, 220, 225, 230);
        imagerectangle($image, 40, 40, $width - 40, $height - 40, $boxColor);

        // Render product title text
        $title = $product->name;
        $shortTitle = strlen($title) > 40 ? substr($title, 0, 37) . '...' : $title;

        imagestring($image, 5, 60, $height / 2 - 20, $shortTitle, $textColor);
        imagestring($image, 4, 60, $height / 2 + 10, "SKU: " . $product->sku, $accentColor);

        imagejpeg($image, $destinationPath, 90);
        imagedestroy($image);

        return $this->validateImageFile($destinationPath);
    }

    /**
     * Log error to storage/logs/product-image-generation.log
     */
    public function logError(Product $product, string $errorMessage): void
    {
        $logPath = storage_path('logs/product-image-generation.log');
        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[$timestamp] Product ID: {$product->id} | Name: {$product->name} | Error: {$errorMessage}" . PHP_EOL;

        @file_put_contents($logPath, $logMessage, FILE_APPEND);

        try {
            Log::build([
                'driver' => 'single',
                'path' => $logPath,
            ])->error("Product ID {$product->id} ({$product->name}): {$errorMessage}");
        } catch (\Throwable $e) {}
    }
}
