<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductCategory\StoreProductCategoryRequest;
use App\Http\Requests\ProductCategory\UpdateProductCategoryRequest;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProductCategoryController extends Controller
{
    public function index(Request $request): Response
    {
        $search = $request->input('search');

        $productCategories = ProductCategory::query()
            ->when($search, function ($query, string $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like' , "%{$search}%");
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();
        
        return Inertia::render('ProductCategories/Index', [
            'productCategories' => $productCategories,
            'filters' => [
                'search' => $search,
            ],
        ]);
    }
    
    public function create(): Response
    {
        return Inertia::render('ProductCategories/Create');
    }
    
    public function store(StoreProductCategoryRequest $request)
    {
        ProductCategory::create($request->validated());

        return redirect()
            ->route('product-categories.index')
            ->with('success', 'Product category created successfully.');
    }
    
    public function show(ProductCategory $productCategory)
    {
        return redirect()->route('product-categories.edit', $productCategory);
    }
    
    public function edit(ProductCategory $productCategory): Response
    {
        return Inertia::render('ProductCategories/Edit', [
            'productCategory' => $productCategory,
        ]);
    }
    
    public function update(UpdateProductCategoryRequest $request, ProductCategory $productCategory)
    {
        $productCategory->update($request->validated());

        return redirect()
            ->route('product-categories.index')
            ->with('success', 'Product category updated successfully.');
    }
    
    public function destroy(ProductCategory $productCategory)
    {
        $productCategory->delete();

        return redirect()
            ->route('product-categories.index')
            ->with('success', 'Product category deleted successfully.');
    }
}