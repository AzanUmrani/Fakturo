<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\Api\ApiProductException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Product\CreateProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Http\Resources\Api\ProductCollectionResource;
use App\Http\Resources\Api\ProductResource;
use App\Http\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ApiProductController extends Controller
{
    public function listProducts(ProductService $productService): ProductCollectionResource
    {
        return $productService->listProducts();
    }

    public function createProduct(CreateProductRequest $request, ProductService $productService): ProductResource
    {
        return $productService->createProduct($request);
    }

    /**
     * @throws ApiProductException
     */
    public function getProduct(string $productUuid, ProductService $productService): ProductResource
    {
        return $productService->getProduct($productUuid);
    }

    /**
     * @throws ApiProductException
     */
    public function updateProduct(string $productUuid, UpdateProductRequest $request, ProductService $productService): ProductResource
    {
        return $productService->updateProduct($productUuid, $request);
    }

    /**
     * @throws ApiProductException
     */
    public function deleteProduct(string $productUuid, ProductService $productService): JsonResponse
    {
        return $productService->deleteProduct($productUuid);
    }

    public function getProductImage(string $productUuid, ProductService $productService): StreamedResponse
    {
        return $productService->getProductImage($productUuid);
    }
}
