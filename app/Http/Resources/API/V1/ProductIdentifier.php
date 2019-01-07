<?php

namespace App\Http\Resources\API\V1;

use Illuminate\Http\Resources\Json\Resource;

class ProductIdentifier extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'photo' => $this->photo,
            'model' => $this->model,
            'price' => $this->price,
            'categories' => CategoryParentIdentifier::collection(
                $this->categories
            )
        ];
    }
}
