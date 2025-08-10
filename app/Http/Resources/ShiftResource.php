<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShiftResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'max_employees' => $this->max_employees,
            'max_resources' => $this->max_resources,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,

            'relations' => [
                'department' => DepartmentResource::make($this->whenLoaded('department')),
            ]
        ];
    }
}
