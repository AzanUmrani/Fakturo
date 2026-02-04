<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'user_id',

        'type',
        'name',
        'description',
        'price',
        'taxRate',
        'discount',

        'unit',
        'sku',
        'weight',

        'has_image',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the URL of the product image.
     *
     * @return string|null
     */
    public function getImageUrl(): ?string
    {
        if (!$this->has_image) {
            return null;
        }

        // Check for common image extensions
        $extensions = ['jpg', 'jpeg', 'png', 'gif'];

        foreach ($extensions as $extension) {
            $imagePath = 'products/' . $this->uuid . '.' . $extension;

            if (Storage::disk('public')->exists($imagePath)) {
                return Storage::url($imagePath);
            }
        }

        return null;
    }
}
