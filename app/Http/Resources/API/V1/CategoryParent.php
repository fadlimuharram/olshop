<?php

namespace App\Http\Resources\API\V1;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\JsonResponse;
class CategoryParent extends ResourceCollection
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return CategoryParentIdentifier::collection($this->collection);
    }

    public function with($request)
    {
        return [
            'status' => 'success',
            'link' => [
                'self' => route('categories.parent')
            ]
        ];
    }

    public function withResponse($request, $response)
    {
        $response->setStatusCode(JsonResponse::HTTP_OK);
    }
}
