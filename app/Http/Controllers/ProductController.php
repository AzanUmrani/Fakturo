<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Http\Utils\FileHelper;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $products = Product::where('user_id', Auth::id())
            ->when($request->search, function ($query, $search) {
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('sku', 'like', "%{$search}%")
                      ->orWhere('type', 'like', "%{$search}%");
                });
            })
            ->orderBy($request->sort_field ?? 'name', $request->sort_direction ?? 'asc')
            ->paginate(10)
            ->withQueryString();

        // Add image URL to each product
        $products->getCollection()->transform(function ($product) {
            $product->image_url = $product->getImageUrl();
            return $product;
        });

        syncLangFiles(['products']);

        return Inertia::render('products/products', [
            'products' => $products,
            'filters' => $request->only(['search', 'sort_field', 'sort_direction']),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $product = new Product($validated);
        $product->user_id = Auth::id();
        $product->uuid = (string) Str::uuid();

        // Handle image upload
        if ($request->hasFile('image')) {
            $savedImage = Storage::disk('local')->put(
                FileHelper::getProductImageFilePath($product->user_id, $product->uuid),
                $request->file('image')->get()
            );

            if ($savedImage) {
                $product->has_image = true;
            }
        }

        $product->save();

        return to_route('products.index')->with('success', 'Product created successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductRequest $request, Product $product): RedirectResponse
    {
        // Check if the product belongs to the authenticated user
        if ($product->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validated();

        // Handle image upload
        if ($request->hasFile('image')) {
            $savedImage = Storage::disk('local')->put(
                FileHelper::getProductImageFilePath($product->user_id, $product->uuid),
                $request->file('image')->get()
            );
            if ($savedImage) {
                $validated['has_image'] = true;
            }
        }

        $product->update($validated);

        return to_route('products.index')->with('success', 'Product updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product): RedirectResponse
    {
        // Check if the product belongs to the authenticated user
        if ($product->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $product->delete();

        return to_route('products.index')->with('success', 'Product deleted successfully.');
    }

    /**
     * Display the product image.
     */
    public function showImage(string $productUuid)
    {
        // Find the product by UUID
        $product = Product::where('uuid', $productUuid)
            ->where('user_id', Auth::id())
            ->first();

        // Check if the product exists and has an image
        if (!$product || !$product->has_image) {
            abort(404, 'Image not found');
        }

        // Get the image path
        $imagePath = FileHelper::getProductImageFilePath($product->user_id, $product->uuid);

        // Check if the image exists in storage
        if (!Storage::disk('local')->exists($imagePath)) {
            abort(404, 'Image not found');
        }

        // Return the image as a response
        return Storage::disk('local')->response($imagePath);
    }
}
