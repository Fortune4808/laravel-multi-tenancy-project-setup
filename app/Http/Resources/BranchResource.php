<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BranchResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'branchId' => $this->branch_id,
            'branchName' => $this->branch_name,
            'createdBy' => $this->created_by,
            'updatedBy' => $this->updated_by,
            'status' => [
                'statusId' => $this->status_id,
                'statusName' => $this->status->status_name
            ]
        ];
    }
}
