<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Product::with('category', 'brand')
            ->where('is_validated', true);

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('brand_id')) {
            $query->where('brand_id', $request->brand_id);
        }

        if ($request->filled('q')) {
            $query->whereRaw('LOWER(name) LIKE ?', ['%'.strtolower($request->q).'%']);
        }

        $products = $query->paginate(20);

        return response()->json($products);
    }

    public function show(Product $product): JsonResponse
    {
        $product->load('category', 'brand', 'offers.merchantWebsite', 'offers.discount');

        return response()->json($product);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'unique:products,slug'],
            'description' => ['nullable', 'string'],
            'image_url' => ['nullable', 'url'],
            'category_id' => ['required', 'exists:categories,id'],
            'brand_id' => ['nullable', 'exists:brands,id'],
            'specifications' => ['nullable', 'array'],
        ]);

        $data['is_validated'] = false;

        return response()->json(Product::create($data), 201);
    }

    public function update(Request $request, Product $product): JsonResponse
    {
        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'slug' => ['sometimes', 'string', 'unique:products,slug,'.$product->id],
            'description' => ['nullable', 'string'],
            'image_url' => ['nullable', 'url'],
            'category_id' => ['sometimes', 'exists:categories,id'],
            'brand_id' => ['nullable', 'exists:brands,id'],
            'specifications' => ['nullable', 'array'],
            'is_validated' => ['sometimes', 'boolean'],
        ]);

        $product->update($data);

        return response()->json($product->fresh('category', 'brand'));
    }

    public function destroy(Product $product): JsonResponse
    {
        $product->delete();

        return response()->json(null, 204);
    }

    /** GET /products/{product}/offers — compare all merchant offers for a product */
    public function offers(Product $product): JsonResponse
    {
        $offers = $product->offers()
            ->with('merchantWebsite', 'discount')
            ->where('is_available', true)
            ->orderBy('price')
            ->get();

        return response()->json($offers);
    }
}
