<?php

namespace App\Http\Resources;

use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AccountResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $user = Account::where('id', '=', $this->id)->first();
        return [
            'id' => $user->id ?? '',
            'name' => $user->name ?? '',
            'email' => $user->email ?? '',
            'city' => $user->countries->name ?? '',
            'phone' => $user->phone ?? '',
            'image' => url('storage/',  $user->image ?? ''),
            'type' => $user->type ?? '',
            'token' => $this->token,
        ];
    }
}
