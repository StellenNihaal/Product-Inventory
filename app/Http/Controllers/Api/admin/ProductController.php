<?php

namespace App\Http\Controllers\Api\admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    // ✅ List all products with search & filter
    public function index(Request $request)
    {
        $query = Product::with('category', 'brand');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        if ($request->filled('brand_id')) {
            $query->where('brand_id', $request->brand_id);
        }
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        $products = $query->latest()->paginate(10);

        return response()->json([
            'success'  => true,
            'products' => $products->map(fn($product) => [
                'id'          => $product->id,
                'name'        => $product->name,
                'description' => $product->description,
                'price'       => $product->price,
                'stock'       => $product->stock,
                'image'       => $product->image
                    ? asset('storage/' . $product->image)
                    : null,
                'category'    => [
                    'id'   => $product->category->id,
                    'name' => $product->category->name,
                ],
                'brand'       => [
                    'id'   => $product->brand->id,
                    'name' => $product->brand->name,
                ],
            ]),
            'pagination' => [
                'total'        => $products->total(),
                'per_page'     => $products->perPage(),
                'current_page' => $products->currentPage(),
                'last_page'    => $products->lastPage(),
            ],
        ]);
    }

    // ✅ Get single product
    public function show(Product $product)
    {
        $product->load('category', 'brand');

        return response()->json([
            'success' => true,
            'product' => [
                'id'          => $product->id,
                'name'        => $product->name,
                'description' => $product->description,
                'price'       => $product->price,
                'stock'       => $product->stock,
                'image'       => $product->image
                    ? asset('storage/' . $product->image)
                    : null,
                'category'    => [
                    'id'   => $product->category->id,
                    'name' => $product->category->name,
                ],
                'brand'       => [
                    'id'   => $product->brand->id,
                    'name' => $product->brand->name,
                ],
            ],
        ]);
    }

    // ✅ Create product (admin only)
    public function store(Request $request)
    {
        // Admin check
        if (auth()->user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Admin access required.',
            ], 403);
        }

        $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'price'       => 'required|numeric|min:0',
            'stock'       => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'brand_id'    => 'required|exists:brands,id',
            'image'       => 'nullable|image|max:2048',
        ], [
            'name.required'        => 'Product name is required.',
            'price.required'       => 'Price is required.',
            'price.numeric'        => 'Price must be a valid number.',
            'stock.required'       => 'Stock quantity is required.',
            'stock.integer'        => 'Stock must be a whole number.',
            'category_id.required' => 'Please select a category.',
            'category_id.exists'   => 'Selected category does not exist.',
            'brand_id.required'    => 'Please select a brand.',
            'brand_id.exists'      => 'Selected brand does not exist.',
            'image.image'          => 'File must be an image.',
            'image.max'            => 'Image must be under 2MB.',
        ]);

        $data = $request->only([
            'name',
            'description',
            'price',
            'stock',
            'category_id',
            'brand_id'
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product = Product::create($data);
        $product->load('category', 'brand');

        return response()->json([
            'success' => true,
            'message' => 'Product created successfully.',
            'product' => [
                'id'          => $product->id,
                'name'        => $product->name,
                'description' => $product->description,
                'price'       => $product->price,
                'stock'       => $product->stock,
                'image'       => $product->image
                    ? asset('storage/' . $product->image)
                    : null,
                'category'    => $product->category->name,
                'brand'       => $product->brand->name,
            ],
        ], 201);
    }

    // ✅ Update product (admin only)
    public function update(Request $request, Product $product)
    {
        if (auth()->user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Admin access required.',
            ], 403);
        }

        $request->validate([
            'name'        => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'price'       => 'sometimes|required|numeric|min:0',
            'stock'       => 'sometimes|required|integer|min:0',
            'category_id' => 'sometimes|required|exists:categories,id',
            'brand_id'    => 'sometimes|required|exists:brands,id',
            'image'       => 'nullable|image|max:2048',
        ]);

        $data = $request->only([
            'name',
            'description',
            'price',
            'stock',
            'category_id',
            'brand_id'
        ]);

        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($data);
        $product->load('category', 'brand');

        return response()->json([
            'success' => true,
            'message' => 'Product updated successfully.',
            'product' => [
                'id'          => $product->id,
                'name'        => $product->name,
                'description' => $product->description,
                'price'       => $product->price,
                'stock'       => $product->stock,
                'image'       => $product->image
                    ? asset('storage/' . $product->image)
                    : null,
                'category'    => $product->category->name,
                'brand'       => $product->brand->name,
            ],
        ]);
    }

    // ✅ Delete product (admin only)
    public function destroy(Product $product)
    {
        if (auth()->user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Admin access required.',
            ], 403);
        }

        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product deleted successfully.',
        ]);
    }
}
