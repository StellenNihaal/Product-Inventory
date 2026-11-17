<?php

namespace App\Http\Controllers\Api\admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    // ✅ List all categories with search
    public function index(Request $request)
    {
        $query = Category::withCount('products');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $categories = $query->latest()->paginate(10);

        return response()->json([
            'success'    => true,
            'categories' => $categories->map(fn($category) => [
                'id'             => $category->id,
                'name'           => $category->name,
                'products_count' => $category->products_count,
            ]),
            'pagination' => [
                'total'        => $categories->total(),
                'per_page'     => $categories->perPage(),
                'current_page' => $categories->currentPage(),
                'last_page'    => $categories->lastPage(),
            ],
        ]);
    }

    // ✅ Get single category
    public function show(Category $category)
    {
        return response()->json([
            'success'  => true,
            'category' => [
                'id'             => $category->id,
                'name'           => $category->name,
                'products_count' => $category->products()->count(),
            ],
        ]);
    }

    // ✅ Create category (admin only)
    public function store(Request $request)
    {
        if (auth()->user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Admin access required.',
            ], 403);
        }

        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
        ], [
            'name.required' => 'Category name is required.',
            'name.unique'   => 'This category already exists.',
            'name.max'      => 'Category name must not exceed 255 characters.',
        ]);

        $category = Category::create(['name' => $request->name]);

        return response()->json([
            'success'  => true,
            'message'  => 'Category created successfully.',
            'category' => [
                'id'   => $category->id,
                'name' => $category->name,
            ],
        ], 201);
    }

    // ✅ Update category (admin only)
    public function update(Request $request, Category $category)
    {
        if (auth()->user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Admin access required.',
            ], 403);
        }

        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
        ], [
            'name.required' => 'Category name is required.',
            'name.unique'   => 'This category name is already taken.',
        ]);

        $category->update(['name' => $request->name]);

        return response()->json([
            'success'  => true,
            'message'  => 'Category updated successfully.',
            'category' => [
                'id'   => $category->id,
                'name' => $category->name,
            ],
        ]);
    }

    // ✅ Delete category (admin only)
    public function destroy(Category $category)
    {
        if (auth()->user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Admin access required.',
            ], 403);
        }

        // ✅ Check if category has products
        if ($category->products()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete category with existing products. Remove products first.',
            ], 422);
        }

        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Category deleted successfully.',
        ]);
    }

    // ✅ Get products by category
    public function products(Category $category)
    {
        $products = $category->products()
            ->with('brand')
            ->latest()
            ->paginate(10);

        return response()->json([
            'success'  => true,
            'category' => [
                'id'   => $category->id,
                'name' => $category->name,
            ],
            'products'   => $products->map(fn($product) => [
                'id'          => $product->id,
                'name'        => $product->name,
                'description' => $product->description,
                'price'       => $product->price,
                'stock'       => $product->stock,
                'image'       => $product->image
                                    ? asset('storage/' . $product->image)
                                    : null,
                'brand'       => $product->brand->name,
            ]),
            'pagination' => [
                'total'        => $products->total(),
                'per_page'     => $products->perPage(),
                'current_page' => $products->currentPage(),
                'last_page'    => $products->lastPage(),
            ],
        ]);
    }
}
