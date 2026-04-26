<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\CategoryResource;

class BookResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'isbn'           => $this->isbn,
            'title'          => $this->title,
            'author'         => $this->author,
            'publisher'      => $this->publisher,
            'price'          => (float) $this->price,
            'stock_quantity' => $this->stock_quantity,
            'published_at'   => $this->published_at?->toIso8601String(),
            'format'         => $this->format,
            'category'       => new CategoryResource($this->whenLoaded('category')),
            // Only load description on detail routes
            'description'    => $this->when(
                $request->routeIs('books.show'),
                $this->description
            ),
        ];
    }
}