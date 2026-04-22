<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductCatalogController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));
        $categoryId = $request->query('category');
        $sort = $request->query('sort', 'latest'); // latest|price_asc|price_desc

        $query = Product::query()
            ->with(['images', 'category'])
            ->where('is_active', true);

        if ($search !== '') {
            $query->where('name', 'like', "%{$search}%");
        }

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        $query->when($sort === 'price_asc', fn ($q) => $q->orderBy('price'))
            ->when($sort === 'price_desc', fn ($q) => $q->orderByDesc('price'))
            ->when($sort === 'latest', fn ($q) => $q->latest('id'));

        $products = $query->paginate(10)->withQueryString();

        $categories = Category::orderBy('name')->get();

        return view('products.index', compact('products', 'categories', 'search', 'categoryId', 'sort'));
    }

    public function show(Product $product): View
    {
        $product->load(['images', 'store', 'category']);

        abort_if(! $product->is_active, 404);

        return view('products.show', compact('product'));
    }
}

