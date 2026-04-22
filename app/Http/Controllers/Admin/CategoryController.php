<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryStoreRequest;
use App\Http\Requests\CategoryUpdateRequest;
use App\Models\Category;
use App\Traits\GeneratesSlug;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    use GeneratesSlug;
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
        $slug = self::generateUniqueSlug(new Category(), $request->name);

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
            $category->slug = self::generateUniqueSlug($category, $request->name, $category->id);
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

