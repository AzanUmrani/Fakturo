<?php

namespace App\Http\Services;

use App\Exceptions\Api\ApiProductException;
use App\Http\Requests\Product\CreateProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Http\Resources\Api\ProductCollectionResource;
use App\Http\Resources\Api\ProductResource;
use App\Http\Utils\FileHelper;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use mysql_xdevapi\Exception;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProductService
{

    public function getLoggedUserProducts(): \Illuminate\Database\Eloquent\Collection
    {
        return Auth::user()->products()->orderBy('name')->get();
    }

    public function listProducts(): ProductCollectionResource
    {
        return ProductCollectionResource::make(
            $this->getLoggedUserProducts()
        );
    }

    public function createProduct(CreateProductRequest $request): ProductResource
    {
        $user = Auth::user();

        $newProduct = Product::make([
            'uuid' => Uuid::uuid4()->toString(),
            'user_id' => $user->id,

            'type' => $request->input('type'),
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'price' => $request->input('price'),
            'taxRate' => $request->input('taxRate'),
            'discount' => $request->input('discount'),

            'unit' => $request->input('unit'),
            'sku' => $request->input('sku'),
            'weight' => $request->input('weight'),
            'has_image' => false,
        ]);
        $newProduct->save();

        if ($request->has('imageBase64') && !empty($request->input('imageBase64'))) {
            $savedImage = Storage::disk('local')->put(
                FileHelper::getProductImageFilePath($user->id, $newProduct->uuid),
                base64_decode(
                    $request->input('imageBase64')
                )
            );

            if ($savedImage) {
                $newProduct->has_image = true;
                $newProduct->save();
            }
        }

        return ProductResource::make($newProduct);
    }

    /**
     * @throws ApiProductException
     */
    public function getProduct(string $productUuid): ProductResource
    {
        $product = $this->getLoggedUserProducts()->firstWhere('uuid', $productUuid);
        if (!$product) {
            throw ApiProductException::notFound();
        }

        return ProductResource::make(
            $product
        );
    }

    /**
     * @throws ApiProductException
     */
    public function updateProduct(string $productUuid, UpdateProductRequest $request): ProductResource
    {
        $product = $this->getLoggedUserProducts()->firstWhere('uuid', $productUuid);

        if (!$product) {
            throw ApiProductException::notFound();
        }

        $product->type = $request->input('type');
        $product->name = $request->input('name');
        $product->description = $request->input('description');
        $product->price = $request->input('price');
        $product->taxRate = $request->input('taxRate');
        $product->discount = $request->input('discount');

        $product->unit = $request->input('unit');
        $product->sku = $request->input('sku');
        $product->weight = $request->input('weight');

        $product->save();

        $imageTask = $request->input('imageTask');
        if ($imageTask === 'upload') {
            if ($request->has('imageBase64') && !empty($request->input('imageBase64'))) {
                $savedImage = Storage::disk('local')->put(
                    FileHelper::getProductImageFilePath($product->user_id, $product->uuid),
                    base64_decode(
                        $request->input('imageBase64')
                    )
                );

                if ($savedImage) {
                    $product->has_image = true;
                    $product->save();
                }
            }
        } elseif ($imageTask === 'delete') {
            if ($product->has_image) {
                Storage::disk('local')->delete(
                    FileHelper::getProductImageFilePath($product->user_id, $product->uuid)
                );
                $product->has_image = false;
                $product->save();
            }
        }

        return ProductResource::make($product);
    }

    /**
     * @throws ApiProductException
     */
    public function deleteProduct(string $productUuid): JsonResponse
    {
        $product = $this->getLoggedUserProducts()->firstWhere('uuid', $productUuid);

        if (!$product) {
            throw ApiProductException::notFound();
        }

        if ($product->has_image) {
            Storage::disk('local')->delete(
                FileHelper::getProductImageFilePath($product->user_id, $product->uuid)
            );
        }

        $product->delete();

        return response()->json([
            'message' => 'USER_PRODUCT_DELETED',
        ]);
    }

    /**
     * @throws ApiProductException
     */
    public function getProductImage(string $productUuid): StreamedResponse
    {
        $userProduct = $this->getLoggedUserProducts()->firstWhere('uuid', $productUuid);

        if (!$userProduct || !$userProduct->has_image) {
            throw ApiProductException::notFound();
        }

        return Storage::disk('local')->response(
            FileHelper::getProductImageFilePath(
                auth()->id(),
                $productUuid
            )
        );
    }
}
