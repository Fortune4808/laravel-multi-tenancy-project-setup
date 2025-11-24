<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StaffResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return parent::toArray($request);
        return [
            'staffId' => $this->staff_id,
            'firstName' => $this->first_name,
            'middleName' => $this->middle_name,
            'lastName' => $this->last_name,
            'emailAddress' => $this->email,
            'phoneNumber' => $this->phone_number,
            'lastLoginAt' => Carbon::parse($this->last_login_at)->toDayDateTimeString(),
            'createdAt' => Carbon::parse($this->created_at)->toDayDateTimeString(),
            'updatedAt' => Carbon::parse($this->updated_at)->toDayDateTimeString(),
            'createdBy' => $this->created_by,
            'status' => [
                'statusId' => $this->status_id ?? null,
                'statusName' => $this->status->status_name ?? null,
            ],
            'role' => [
                'roleName' => $this->roles->pluck('name') ?? null,
                'roleId' => $this->roles->pluck('id') ?? null,
                'permissions' => $this->getAllPermissions()->pluck('name') ?? null,
            ],
        ];
    }
}
