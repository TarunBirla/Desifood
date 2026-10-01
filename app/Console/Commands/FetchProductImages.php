<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;


class FetchProductImages extends Command
{
    protected $signature = 'products:fetch-images
        {--limit=0 : Max products to process (0 = all)}
        {--batch=10 : Parallel requests per batch}
        {--dry : Search only, do not download or update DB}
        {--redo-ids= : Comma separated product IDs to force re-fetch}';

    protected $description = 'Auto-fetch product images via Google Images search for products with placeholder/no image';

    private const PLACEHOLDER = 'images.unsplash.com';
    private const DIR = 'uploads/products';
    private const BLOCKED_HOSTS = [
        'shutterstock', 'alamy', 'gettyimages', 'istockphoto', 'dreamstime',
        'depositphotos', '123rf', 'adobestock', 'pinterest', 'facebook', 'instagram',
    ];

    public function handle(): int
    {
        $key = env('SERPER_API_KEY');
        if (!$key) {
            $this->error('SERPER_API_KEY missing in .env');
            return self::FAILURE;
        }

        $dest = public_path(self::DIR);
        if (!is_dir($dest)) {
            mkdir($dest, 0775, true);
        }

        $query = DB::table('products as p')
            ->leftJoin('product_images as i', function ($j) {
                $j->on('i.product_id', '=', 'p.id')->where('i.is_primary', 1);
            })
            ->whereNull('p.deleted_at')
            ->select('p.id', 'p.name', 'i.id as image_id', 'i.image_path')
            ->orderBy('p.id');

        if ($ids = $this->option('redo-ids')) {
            $query->whereIn('p.id', array_map('intval', explode(',', $ids)));
        } else {
            $query->where(function ($q) {
                $q->whereNull('i.id')->orWhere('i.image_path', 'like', '%' . self::PLACEHOLDER . '%');
            });
        }

        if ($limit = (int) $this->option('limit')) {
            $query->limit($limit);
        }

        $products = $query->get();
        $this->info("Products to process: {$products->count()}");

        $logPath = storage_path('app/product_image_log.csv');
        $log = fopen($logPath, 'a');
        $ok = $fail = 0;
        $bar = $this->output->createProgressBar($products->count());

        foreach ($products->chunk(max(1, (int) $this->option('batch'))) as $chunk) {
            $chunk = $chunk->values();
            $queries = $chunk->map(fn ($p) => $this->cleanName($p->name));

            // 1) parallel searches
            $responses = Http::pool(fn ($pool) => $chunk->map(
                fn ($p, $n) => $pool->as((string) $n)->timeout(20)
                    ->withHeaders(['X-API-KEY' => $key])
                    ->post('https://google.serper.dev/images', ['q' => $queries[$n], 'gl' => 'gb', 'num' => 8])
            )->all());

            // 2) per product: try candidates until one downloads and validates
            foreach ($chunk as $n => $p) {
                $bar->advance();
                $res = $responses[(string) $n] ?? null;
                $candidates = [];
                if ($res && !($res instanceof \Throwable) && $res->ok()) {
                    foreach ($res->json('images') ?? [] as $img) {
                        $u = $img['imageUrl'] ?? null;
                        if ($u && $this->allowed($u)) {
                            $candidates[] = $u;
                        }
                    }
                }

                if ($this->option('dry')) {
                    fputcsv($log, [now(), $p->id, $p->name, $queries[$n], $candidates[0] ?? 'NONE', 'dry']);
                    $candidates ? $ok++ : $fail++;
                    continue;
                }

                $saved = null;
                foreach (array_slice($candidates, 0, 5) as $url) {
                    if ($saved = $this->download($url, $p->id, $dest)) {
                        break;
                    }
                }

                if ($saved) {
                    $path = '/' . self::DIR . '/' . $saved;
                    $now = now();
                    if ($p->image_id) {
                        DB::table('product_images')->where('id', $p->image_id)
                            ->update(['image_path' => $path, 'updated_at' => $now]);
                    } else {
                        DB::table('product_images')->insert([
                            'product_id' => $p->id, 'image_path' => $path,
                            'is_primary' => 1, 'sort_order' => 0,
                            'created_at' => $now, 'updated_at' => $now,
                        ]);
                    }
                    $ok++;
                } else {
                    $fail++;
                }
                fputcsv($log, [now(), $p->id, $p->name, $queries[$n], $saved ? '/' . self::DIR . '/' . $saved : 'FAILED', $saved ? 'ok' : 'fail']);
            }
        }

        fclose($log);
        $bar->finish();
        $this->newLine(2);
        $this->info("Done. Found/saved: {$ok}  Failed: {$fail}");
        $this->line("Log: {$logPath}  (re-run failures later; already-fixed products are skipped automatically)");
        return self::SUCCESS;
    }

    /** "CHETOS TWSTD SWT&SPICY PM135" => "CHETOS TWSTD SWT&SPICY" */
    public function cleanName(string $name): string
    {
        $n = preg_replace('/\b(SW)?PM\s?\d+\b/i', '', $name);   // price-mark codes
        $n = preg_replace('/\s+/', ' ', trim($n));
        return $n . ' uk grocery product';
    }

    private function allowed(string $url): bool
    {
        $host = strtolower(parse_url($url, PHP_URL_HOST) ?? '');
        if (!$host || !preg_match('/\.(jpe?g|png|webp)(\?|$)/i', $url)) {
            return false;
        }
        foreach (self::BLOCKED_HOSTS as $b) {
            if (str_contains($host, $b)) {
                return false;
            }
        }
        return true;
    }

    private function download(string $url, int $productId, string $dest): ?string
    {
        try {
            $r = Http::timeout(20)
                ->withHeaders(['User-Agent' => 'Mozilla/5.0 (compatible; ProductImageBot/1.0)'])
                ->get($url);
            if (!$r->ok()) {
                return null;
            }
            $body = $r->body();
            $info = @getimagesizefromstring($body);
            if (!$info || $info[0] < 200 || $info[1] < 200 || strlen($body) < 5000 || strlen($body) > 4 * 1024 * 1024) {
                return null;
            }
            $ext = [IMAGETYPE_JPEG => 'jpg', IMAGETYPE_PNG => 'png', IMAGETYPE_WEBP => 'webp'][$info[2]] ?? null;
            if (!$ext) {
                return null;
            }
            $file = time() . "_{$productId}.{$ext}";
            file_put_contents($dest . '/' . $file, $body);
            return $file;
        } catch (\Throwable $e) {
            return null;
        }
    }
}
