<?php

namespace App\Http\Resources\API\V1;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\JsonResponse;
class Product extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return ProductIdentifier::collection($this->collection);
    }

    public function with($request)
    {
        return [
            'status' => 'success',
            'link' => [
                'self' => route('products.index')
            ]
        ];
    }

    public function withResponse($request, $response)
    {
        $response->setStatusCode(JsonResponse::HTTP_OK);
    }
}
