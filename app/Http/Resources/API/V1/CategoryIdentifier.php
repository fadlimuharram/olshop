<?php

namespace App\Http\Resources\API\V1;

use Illuminate\Http\Resources\Json\Resource;

class CategoryIdentifier extends Resource
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

        return [
            "id" => $this->id,
            "title" => $this->title,
            "parent" => [
                "id" => $this->parent->id,
                "title" => $this->parent->title
            ]
        ];
    }
}
