<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'type' => 'bs',
            'attribute' => [
                'id' => $this['id'],
                'title' => $this['title'],
                'high' => $this['high'],
                'status' => $this['status'],
                'responsible_person' => $this['responsible_person'],
                'created_at' => $this['created_at'],
                'manegement_person' => $this['manegement_person']
            ]
        ];
    }
}
