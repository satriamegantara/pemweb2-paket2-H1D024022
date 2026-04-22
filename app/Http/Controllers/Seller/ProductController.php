<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductStoreRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProductController extends Controller
{
    private function sellerStoreId(Request $request): ?int
    {
        return $request->user()->store?->id;
    }

    public function index(Request $request): View
    {
        $storeId = $this->sellerStoreId($request);
        if (!$storeId) {
            return redirect()->route('seller.store.create')->with('error', 'Silakan buat toko dulu.');
        }

        $search = trim((string) $request->query('q', ''));
        $active = $request->query('active', 'all'); // all|1|0

        $products = Product::query()
            ->where('store_id', $storeId)
            ->when($search !== '', fn($q) => $q->where('name', 'like', "%{$search}%"))
            ->when($active === '1', fn($q) => $q->where('is_active', true))
            ->when($active === '0', fn($q) => $q->where('is_active', false))
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('seller.products.index', compact('products', 'search', 'active'));
    }

    public function create(Request $request): View
    {
        if (!$this->sellerStoreId($request)) {
            return redirect()->route('seller.store.create')->with('error', 'Silakan buat toko dulu.');
        }

        $categories = Category::orderBy('name')->get();

        return view('seller.products.create', compact('categories'));
    }

    public function store(ProductStoreRequest $request): RedirectResponse
    {
        $storeId = $this->sellerStoreId($request);
        if (!$storeId) {
            return redirect()->route('seller.store.create')->with('error', 'Silakan buat toko dulu.');
        }

        $data = $request->validated();

        DB::transaction(function () use ($data, $request, $storeId) {
            $slugBase = Str::slug($data['name']);
            $slug = $slugBase !== '' ? $slugBase : Str::random(8);
            $i = 2;
            while (Product::withTrashed()->where('slug', $slug)->exists()) {
                $slug = "{$slugBase}-{$i}";
                $i++;
            }

            $product = Product::create([
                'store_id' => $storeId,
                'category_id' => $data['category_id'],
                'name' => $data['name'],
                'slug' => $slug,
                'description' => $data['description'] ?? null,
                'price' => $data['price'],
                'stock' => $data['stock'],
                'weight' => $data['weight'] ?? null,
                'is_active' => (bool) ($data['is_active'] ?? true),
            ]);

            $paths = [];
            foreach ($request->file('images', []) as $file) {
                $paths[] = $file->store("products/{$product->id}", 'public');
            }

            foreach ($paths as $idx => $path) {
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $path,
                    'is_primary' => $idx === 0,
                ]);
            }
        });

        return redirect()->route('seller.products.index')->with('success', 'Produk berhasil dibuat.');
    }

    public function edit(Request $request, Product $product): View
    {
        $storeId = $this->sellerStoreId($request);
        if (!$storeId) {
            return redirect()->route('seller.store.create')->with('error', 'Silakan buat toko dulu.');
        }

        Gate::authorize('update', $product);

        $product->load('images');
        $categories = Category::orderBy('name')->get();

        return view('seller.products.edit', compact('product', 'categories'));
    }

    public function update(ProductUpdateRequest $request, Product $product): RedirectResponse
    {
        $storeId = $this->sellerStoreId($request);
        if (!$storeId) {
            return redirect()->route('seller.store.create')->with('error', 'Silakan buat toko dulu.');
        }

        Gate::authorize('update', $product);

        $data = $request->validated();

        DB::transaction(function () use ($data, $request, $product) {
            if ($product->name !== $data['name']) {
                $slugBase = Str::slug($data['name']);
                $slug = $slugBase !== '' ? $slugBase : Str::random(8);
                $i = 2;
                while (Product::withTrashed()->where('slug', $slug)->whereKeyNot($product->id)->exists()) {
                    $slug = "{$slugBase}-{$i}";
                    $i++;
                }
                $product->slug = $slug;
            }

            $product->fill([
                'category_id' => $data['category_id'],
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'price' => $data['price'],
                'stock' => $data['stock'],
                'weight' => $data['weight'] ?? null,
                'is_active' => (bool) ($data['is_active'] ?? false),
            ])->save();

            if ($request->hasFile('images')) {
                $oldImages = $product->images()->get();
                foreach ($oldImages as $img) {
                    Storage::disk('public')->delete($img->image_path);
                }
                $product->images()->delete();

                $paths = [];
                foreach ($request->file('images', []) as $file) {
                    $paths[] = $file->store("products/{$product->id}", 'public');
                }

                foreach ($paths as $idx => $path) {
                    ProductImage::create([
                        'product_id' => $product->id,
                        'image_path' => $path,
                        'is_primary' => $idx === 0,
                    ]);
                }
            }
        });

        return redirect()->route('seller.products.index')->with('success', 'Produk berhasil diupdate.');
    }

    public function destroy(Request $request, Product $product): RedirectResponse
    {
        $storeId = $this->sellerStoreId($request);
        if (!$storeId) {
            return redirect()->route('seller.store.create')->with('error', 'Silakan buat toko dulu.');
        }

        Gate::authorize('delete', $product);

        DB::transaction(function () use ($product) {
            $product->load('images');
            foreach ($product->images as $img) {
                Storage::disk('public')->delete($img->image_path);
            }
            $product->delete();
        });

        return redirect()->route('seller.products.index')->with('success', 'Produk berhasil dihapus.');
    }

    public function setPrimaryImage(Request $request, Product $product, ProductImage $image): RedirectResponse
    {
        $storeId = $this->sellerStoreId($request);
        if (!$storeId) {
            abort(403);
        }

        Gate::authorize('setPrimaryImage', $product);

        $image->setAsPrimary();

        return back()->with('success', 'Gambar utama berhasil diubah.');
    }
}

