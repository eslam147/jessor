<?php

namespace App\Http\Resources\Dashboard;

use App\Models\Lesson;
use Illuminate\Http\Resources\Json\JsonResource;

class CouponResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'code' => $this->code,
            'price' => $this->price,
            'maximum_usage' => $this->maximum_usage,
            // ---------------------------------------------- \\
            'expiry_date' => $this->expiry_date->toDateString(),
            'only_applied_to' => $this->appliedToFormat($this->onlyAppliedTo),
            // ---------------------------------------------- \\
            'is_disabled' => $this->is_disabled,
            'used_count' => $this->usages->count(),
            // ---------------------------------------------- \\
            'teacher' => optional($this->teacher)->user->full_name ?? 'N/A',
            // ---------------------------------------------- \\
            'type' => $this->type->translatedName(),
            'usages' => $this->usages->map(function ($usage) {
                $user = optional($usage->usedByUser);
                $usedIn = $this->appliedToFormat($usage->appliedTo);
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
            }),
            'created_at' => convertDateFormat($this->created_at, 'd-m-Y H:i:s'),
        ];
    }
    private function appliedToFormat($appliedTo): string
    {
        if (empty($appliedTo)) {
            return 'N/A';
        }
        return match (get_class($appliedTo)) {
            Lesson::class => "Lesson: {$appliedTo->name}",
            default => "N/A"
        };
    }
}
