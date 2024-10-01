<?php

namespace App\Http\Resources\Dashboard;

use App\Models\Lesson;
use Bavix\Wallet\Models\Wallet;
use Illuminate\Http\Resources\Json\JsonResource;

class CouponResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        // $appliedTo =
        return [
            'code' => $this->code,
            'price' => $this->price,
            'maximum_usage' => $this->maximum_usage,
            'expiry_date' => $this->expiry_date->toDateString(),
            'only_applied_to' => '$appliedTo',
            'is_disabled' => $this->is_disabled,
            'used_count' => $this->usages->count(),
            'teacher' => optional($this->teacher)->user->full_name ?? 'N/A',
            'created_at' => convertDateFormat($this->created_at, 'd-m-Y H:i:s'),
            'type' => $this->type->translatedName(),
            'usages' => $this->usages->map(function ($usage) {
                $user = optional($usage->usedByUser);
                $usedIn = match (get_class($usage->appliedTo)) {
                    Lesson::class => "Lesson:" . $usage->appliedTo->name,
                    Wallet::class => "Wallet",
                    default => "N/A"
                };
                return [
                    'id' => $usage->id,
                    'price' => $usage->amount,
                    'applied' => $usedIn,
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->full_name ?? 'N/A',
                        'email' => $user->email ?? 'N/A',
                        'phone' => $user->mobile ?? 'N/A',
                    ],
                    'created_at' => convertDateFormat($usage->created_at, "Y-m-d h:i A"),
                ];
            })
        ];
    }
}
