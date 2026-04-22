<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryStoreRequest;
use App\Http\Requests\CategoryUpdateRequest;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));
        $iconFilter = $request->query('icon', 'all'); // all|with|without

        $categories = Category::query()
            ->when($search !== '', fn ($q) => $q->where('name', 'like', "%{$search}%"))
            ->when($iconFilter === 'with', fn ($q) => $q->whereNotNull('icon')->where('icon', '!=', ''))
            ->when($iconFilter === 'without', fn ($q) => $q->where(function ($q) {
                $q->whereNull('icon')->orWhere('icon', '');
            }))
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('admin.categories.index', compact('categories', 'search', 'iconFilter'));
    }

    public function create(): View
    {
        return view('admin.categories.create');
    }

    public function store(CategoryStoreRequest $request): RedirectResponse
    {
        $slugBase = Str::slug($request->name);
        $slug = $slugBase !== '' ? $slugBase : Str::random(8);

        $i = 2;
        while (Category::where('slug', $slug)->exists()) {
            $slug = "{$slugBase}-{$i}";
            $i++;
        }

        Category::create([
            'name' => $request->name,
            'slug' => $slug,
            'icon' => $request->icon,
        ]);

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Kategori berhasil dibuat.');
    }

    public function edit(Category $category): View
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(CategoryUpdateRequest $request, Category $category): RedirectResponse
    {
        if ($category->name !== $request->name) {
            $slugBase = Str::slug($request->name);
            $slug = $slugBase !== '' ? $slugBase : Str::random(8);

            $i = 2;
            while (Category::where('slug', $slug)->whereKeyNot($category->id)->exists()) {
                $slug = "{$slugBase}-{$i}";
                $i++;
            }

            $category->slug = $slug;
        }

        $category->name = $request->name;
        $category->icon = $request->icon;
        $category->save();

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Kategori berhasil diupdate.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        $category->delete();

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Kategori berhasil dihapus.');
    }
}

