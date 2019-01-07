<?php

namespace App\Http\Resources\API\V1;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\JsonResponse;

class JSend_Fail extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return parent::toArray($request);
    }

    public function with($request)
    {
        return [
            'status' => 'fail'
        ];
    }

    public function withResponse($request, $response)
    {
        $response->setStatusCode(JsonResponse::HTTP_BAD_REQUEST);
    }
}
