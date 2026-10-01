<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

/**
 * Browser-based bulk product image fetcher (for shared hosting).
 * Open:  /tools/product-images?key=YOUR_IMAGE_TOOL_KEY
 * .env:  SERPER_API_KEY=...   IMAGE_TOOL_KEY=some-long-random-string
 * Remove the routes when the job is finished.
 */
class ProductImageToolController extends Controller
{
    private const PLACEHOLDER = 'images.unsplash.com';
    private const DIR = 'uploads/products';
    private const BLOCKED = [
        'shutterstock', 'alamy', 'gettyimages', 'istockphoto', 'dreamstime',
        'depositphotos', '123rf', 'adobestock', 'pinterest', 'facebook', 'instagram',
    ];

    private function guard(Request $r): void
    {
        $k = env('IMAGE_TOOL_KEY');
        abort_unless($k && hash_equals($k, (string) $r->query('key', $r->header('X-Tool-Key', ''))), 404);
    }

    private function pending()
    {
        return DB::table('products as p')
            ->leftJoin('product_images as i', function ($j) {
                $j->on('i.product_id', '=', 'p.id')->where('i.is_primary', 1);
            })
            ->whereNull('p.deleted_at')
            ->where(function ($q) {
                $q->whereNull('i.id')->orWhere('i.image_path', 'like', '%' . self::PLACEHOLDER . '%');
            });
    }

    /** POST /tools/product-images/run  {after, size, dry, ids[], offset} */
    public function run(Request $r)
    {
        $this->guard($r);
        @set_time_limit(120);

        $apiKey = env('SERPER_API_KEY');
        if (!$apiKey) {
            return response()->json(['error' => 'SERPER_API_KEY missing in .env'], 500);
        }

        $size = min(10, max(1, (int) $r->input('size', 5)));
        $dry = (bool) $r->input('dry', false);
        $offset = max(0, (int) $r->input('offset', 0));

        $q = DB::table('products as p')
            ->leftJoin('product_images as i', function ($j) {
                $j->on('i.product_id', '=', 'p.id')->where('i.is_primary', 1);
            })
            ->select('p.id', 'p.name', 'i.id as image_id')
            ->orderBy('p.id');

        if ($ids = $r->input('ids')) {
            $q->whereIn('p.id', array_map('intval', (array) $ids));      // redo specific products
        } else {
            $q = $this->pending()->select('p.id', 'p.name', 'i.id as image_id')->orderBy('p.id')
                ->where('p.id', '>', (int) $r->input('after', 0))->limit($size);
        }

        $products = $q->get()->values();
        if ($products->isEmpty()) {
            return response()->json(['done' => true, 'items' => [], 'remaining' => 0]);
        }

        $queries = $products->map(fn ($p) => $this->cleanName($p->name));
        $responses = Http::pool(fn ($pool) => $products->map(
            fn ($p, $n) => $pool->as((string) $n)->timeout(20)->withOptions(['force_ip_resolve' => 'v4', 'connect_timeout' => 10])
                ->withHeaders(['X-API-KEY' => $apiKey])
                ->post('https://google.serper.dev/images', ['q' => $queries[$n], 'gl' => 'gb', 'num' => 10])
        )->all());

        $dest = public_path(self::DIR);
        if (!is_dir($dest)) {
            mkdir($dest, 0775, true);
        }

        $items = [];
        foreach ($products as $n => $p) {
            $res = $responses[(string) $n] ?? null;
            $cands = [];
            $err = null;
            $raw = 0;
            if ($res instanceof \Throwable) {
                $err = 'Search request failed: ' . $res->getMessage();
            } elseif (!$res) {
                $err = 'No response from search API';
            } elseif (!$res->ok()) {
                $err = 'Search API HTTP ' . $res->status() . ': ' . substr($res->body(), 0, 150);
            } else {
                $list = $res->json('images') ?? [];
                $raw = count($list);
                foreach ($list as $img) {
                    if (!empty($img['imageUrl']) && $this->allowed($img['imageUrl'])) {
                        $cands[] = $img['imageUrl'];
                    }
                }
                if (!$raw) {
                    $err = 'Search returned 0 results';
                } elseif (!$cands) {
                    $err = "Search returned {$raw} results, all filtered out";
                }
            }
            $cands = array_slice($cands, $offset, 5);

            $path = null;
            if ($dry) {
                $path = $cands[0] ?? null;
            } else {
                $dlErr = [];
                foreach ($cands as $url) {
                    $file = $this->download($url, $p->id, $dest, $why);
                    if (!$file) {
                        $dlErr[] = $why;
                        continue;
                    }
                    if ($file) {
                        $path = '/' . self::DIR . '/' . $file;
                        $now = now();
                        if ($p->image_id) {
                            DB::table('product_images')->where('id', $p->image_id)
                                ->update(['image_path' => $path, 'updated_at' => $now]);
                        } else {
                            DB::table('product_images')->insert([
                                'product_id' => $p->id, 'image_path' => $path, 'is_primary' => 1,
                                'sort_order' => 0, 'created_at' => $now, 'updated_at' => $now,
                            ]);
                        }
                        break;
                    }
                }
            }
            if (!$path && !$err && !empty($dlErr)) {
                $err = 'Downloads failed: ' . implode(' | ', array_slice($dlErr, 0, 3));
            }
            $items[] = ['id' => $p->id, 'name' => $p->name, 'query' => $queries[$n], 'path' => $path, 'error' => $path ? null : $err];
        }

        return response()->json([
            'done' => false,
            'items' => $items,
            'last_id' => $products->last()->id,
            'remaining' => $this->pending()->count(),
        ]);
    }

    /** GET /tools/product-images/ping?key=...  - checks what this server can reach */
    public function ping(Request $r)
    {
        $this->guard($r);
        $out = [];
        $tests = [
            'serper (default)' => ['https://google.serper.dev', []],
            'serper (IPv4 forced)' => ['https://google.serper.dev', ['force_ip_resolve' => 'v4']],
            'example.com (IPv4 forced)' => ['https://example.com', ['force_ip_resolve' => 'v4']],
            'google.com (IPv4 forced)' => ['https://www.google.com', ['force_ip_resolve' => 'v4']],
        ];
        foreach ($tests as $label => [$url, $opt]) {
            $t = microtime(true);
            try {
                $res = Http::timeout(12)->withOptions($opt + ['connect_timeout' => 8])->get($url);
                $out[] = "OK   {$label}: HTTP " . $res->status() . ' in ' . round(microtime(true) - $t, 1) . 's';
            } catch (\Throwable $e) {
                $out[] = "FAIL {$label}: " . substr($e->getMessage(), 0, 120);
            }
        }
        return response(implode("\n", $out), 200, ['Content-Type' => 'text/plain']);
    }

    public function index(Request $r)
    {
        $this->guard($r);
        $key = e($r->query('key'));
        $total = $this->pending()->count();
        $csrf = csrf_token();
        $run = url('/tools/product-images/run');

        return response(<<<HTML
<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Product Image Tool</title>
<style>
body{font-family:system-ui,sans-serif;margin:0;background:#f4f6fb;color:#1c2333}
.wrap{max-width:980px;margin:24px auto;padding:0 16px}
.card{background:#fff;border-radius:12px;padding:16px 20px;box-shadow:0 1px 4px #0001;margin-bottom:16px}
button{padding:9px 16px;border:0;border-radius:8px;background:#2f5bea;color:#fff;font-weight:600;cursor:pointer;margin-right:6px}
button.alt{background:#e5e9f5;color:#1c2333}button.red{background:#d64545}button:disabled{opacity:.5}
.bar{height:10px;background:#e5e9f5;border-radius:6px;overflow:hidden;margin:12px 0}.bar div{height:100%;background:#2f5bea;width:0}
.row{display:flex;gap:12px;align-items:center;padding:8px 0;border-top:1px solid #eef0f6}
.row img{width:56px;height:56px;object-fit:contain;background:#f4f6fb;border-radius:6px}
.row .t{flex:1;min-width:0}.row small{color:#6b7488;display:block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.fail{color:#d64545;font-weight:600}
</style></head><body><div class="wrap">
<div class="card"><h2 style="margin-top:0">Product Image Tool</h2>
<p>Products still needing an image: <b id="rem">{$total}</b> &nbsp; Processed this session: <b id="done">0</b> &nbsp; Not found: <b id="nf">0</b></p>
<div class="bar"><div id="pb"></div></div>
<button id="test" class="alt">Test 5 (no save)</button>
<button id="start">Start</button>
<button id="stop" class="red" disabled>Stop</button>
<span id="msg" style="margin-left:8px"></span></div>
<div class="card" id="log"></div></div>
<script>
const KEY="{$key}",CSRF="{$csrf}",URL_RUN="{$run}";
let running=false,after=0,done=0,nf=0,start=null,streak=0;
const \$=id=>document.getElementById(id);
async function call(body){
  const r=await fetch(URL_RUN+"?key="+encodeURIComponent(KEY),{method:"POST",headers:{"Content-Type":"application/json","X-CSRF-TOKEN":CSRF,"Accept":"application/json"},body:JSON.stringify(body)});
  if(!r.ok)throw new Error("HTTP "+r.status+" "+(await r.text()).slice(0,200));
  return r.json();
}
function add(it,dry){
  const row=document.createElement("div");row.className="row";row.dataset.id=it.id;
  const img=it.path?'<img src="'+it.path+'">':'<div style="width:56px;height:56px"></div>';
  row.innerHTML=img+'<div class="t"><b>'+it.name+'</b><small>'+it.query+'</small>'+(it.path?'<small>'+it.path+'</small>':'<span class="fail">not found</span><small style="white-space:normal;color:#d64545">'+(it.error||'')+'</small>')+'</div>'
    +(dry?'':'<button class="alt redo">Redo (next image)</button>');
  const b=row.querySelector(".redo");
  if(b)b.onclick=async()=>{b.disabled=true;b.textContent="...";const n=(+row.dataset.off||0)+1;row.dataset.off=n;
    try{const d=await call({ids:[it.id],offset:n});const x=d.items[0];if(x&&x.path){row.querySelector("img")&&(row.querySelector("img").src=x.path+"?"+Date.now());b.textContent="Redo (next image)";}else{b.textContent="no more";}}catch(e){b.textContent="error"}b.disabled=false;};
  \$("log").prepend(row);
}
async function loop(dry,max){
  running=true;\$("start").disabled=\$("test").disabled=true;\$("stop").disabled=false;
  start=start||+("{$total}");let batches=0;streak=0;
  while(running){
    try{
      const d=await call({after:after,size:5,dry:dry});
      if(d.done){\$("msg").textContent="Finished.";break;}
      d.items.forEach(i=>{add(i,dry);done++;if(!i.path){nf++;streak++;}else{streak=0;}});
      if(streak>=5){\$("msg").textContent="Stopped: 5 failures in a row. Read the red error under the products.";break;}
      after=d.last_id;\$("rem").textContent=d.remaining;\$("done").textContent=done;\$("nf").textContent=nf;
      \$("pb").style.width=Math.min(100,done/start*100)+"%";
      if(dry||(max&&++batches>=max))break;
    }catch(e){\$("msg").textContent="Error: "+e.message+" - retrying in 10s";await new Promise(r=>setTimeout(r,10000));}
  }
  if(dry)after=0;
  running=false;\$("start").disabled=\$("test").disabled=false;\$("stop").disabled=true;
}
\$("start").onclick=()=>{\$("msg").textContent="";loop(false)};
\$("test").onclick=()=>{\$("msg").textContent="";loop(true)};
\$("stop").onclick=()=>{running=false;\$("msg").textContent="Stopping after current batch..."};
</script></body></html>
HTML);
    }

    public function cleanName(string $name): string
    {
        $n = preg_replace('/\b(SW)?PM\s?\d+\b/i', '', $name);
        return preg_replace('/\s+/', ' ', trim($n)) . ' uk grocery product';
    }

    private function allowed(string $url): bool
    {
        $host = strtolower(parse_url($url, PHP_URL_HOST) ?? '');
        if (!$host || !preg_match('#^https?://#i', $url)) {
            return false;
        }
        foreach (self::BLOCKED as $b) {
            if (str_contains($host, $b)) {
                return false;
            }
        }
        return true;
    }

    private function download(string $url, int $productId, string $dest, &$why = null): ?string
    {
        $host = parse_url($url, PHP_URL_HOST);
        try {
            $r = Http::timeout(10)->withOptions(['force_ip_resolve' => 'v4', 'connect_timeout' => 6])->withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124 Safari/537.36',
                'Accept' => 'image/avif,image/webp,image/*,*/*;q=0.8',
            ])->get($url);
            if (!$r->ok()) {
                $why = "{$host} HTTP " . $r->status();
                return null;
            }
            $body = $r->body();
            $info = @getimagesizefromstring($body);
            if (!$info) {
                $why = "{$host} not an image";
                return null;
            }
            if ($info[0] < 200 || $info[1] < 200 || strlen($body) < 5000 || strlen($body) > 4 * 1024 * 1024) {
                $why = "{$host} bad size {$info[0]}x{$info[1]}, " . strlen($body) . ' bytes';
                return null;
            }
            $ext = [IMAGETYPE_JPEG => 'jpg', IMAGETYPE_PNG => 'png', IMAGETYPE_WEBP => 'webp'][$info[2]] ?? null;
            if (!$ext) {
                $why = "{$host} unsupported type";
                return null;
            }
            $file = time() . "_{$productId}_" . substr(md5($url), 0, 6) . ".{$ext}";
            if (@file_put_contents($dest . '/' . $file, $body) === false) {
                $why = 'cannot write to public/uploads/products (permissions?)';
                return null;
            }
            return $file;
        } catch (\Throwable $e) {
            $why = "{$host} " . substr($e->getMessage(), 0, 80);
            return null;
        }
    }
}