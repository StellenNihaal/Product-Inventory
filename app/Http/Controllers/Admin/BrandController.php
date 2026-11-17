<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    public function index(Request $request)
    {
        $query = Brand::withCount('products');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }


        $brands = $query->latest()->paginate(10)->withQueryString();
        return view('admin.brands.index', compact('brands'));
    }

    public function create()
    {
        return view('admin.brands.create');
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255|unique:brands,name']);
        Brand::create(['name' => $request->name]);
        return redirect()->route('admin.brands.index')->with('success', 'Brand added!');
    }

    public function edit(Brand $brand)
    {
        return view('admin.brands.edit', compact('brand'));
    }

    public function update(Request $request, Brand $brand)
    {
        $request->validate(['name' => 'required|string|max:255|unique:brands,name,' . $brand->id]);
        $brand->update(['name' => $request->name]);
        return redirect()->route('admin.brands.index')->with('success', 'Brand updated!');
    }

    public function destroy(Brand $brand)
    {
        $brand->delete();

        if (request()->ajax()) {
            return response()->json(['message' => 'Brand deleted successfully.']);
        }

        return redirect()->route('admin.brands.index')
            ->with('success', 'Brand deleted!');
    }

    public function products(Brand $brand)
    {
        $products = $brand->products()->with('category')->latest()->paginate(10);
        return view('admin.brands.products', compact('brand', 'products'));
    }
}
