<?php

namespace App\Http\Resources\API\V1;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\JsonResponse;

class CategoryOnly extends ResourceCollection
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // return parent::toArray($request);

        return CategoryOnlyIdentifier::collection($this->collection);
    }

    public function with($request)
    {
        return [
            'status' => 'success',
            'link' => [
                'self' => route('categories.index.all')
            ]
        ];
    }

    public function withResponse($request, $response)
    {
        $response->setStatusCode(JsonResponse::HTTP_OK);
    }
}
