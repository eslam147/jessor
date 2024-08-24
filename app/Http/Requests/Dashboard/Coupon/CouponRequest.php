<?php

namespace App\Http\Requests\Dashboard\Coupon;

use App\Rules\TagCommaSperated;
use Illuminate\Foundation\Http\FormRequest;

class CouponRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    private function onCreate(): array
    {
        return [
            // ------------------------------------------------ \\
            'coupons_count' => 'required|integer|min:1|max:1000',
            'coupon_type' => 'required||in:purchase,wallet',
            // ------------------------------------------------ \\
            'usage_limit' => 'required_if:coupon_type,purchase|nullable|integer|min:1|max:100',
            'expiry_date' => 'nullable|date|after:today',
            // ------------------------------------------------ \\
            'price' => 'nullable|min:0.01|numeric',
            // ------------------------------------------------ \\
            'class_id' => 'nullable|required_if:coupon_type,purchase|exists:classes,id',
            'subject_id' => 'nullable|exists:subjects,id',
            // ------------------------------------------------ \\
            'teacher_id' => 'nullable|exists:teachers,id',
            'lesson_id' => 'nullable|exists:lessons,id',
            // ------------------------------------------------ \\
            'tags' => ['string', 'nullable', new TagCommaSperated],
            // ------------------------------------------------ \\
        ];
    }
    private function onUpdate(): array
    {
        return [
            // ------------------------------------------------ \\
            'teacher_id' => 'nullable|exists:teachers,id',
            'lesson_id' => 'nullable|exists:lessons,id',
            // ------------------------------------------------ \\
            'subject_id' => 'nullable|exists:subjects,id',
            'tags' => ['string', 'nullable', new TagCommaSperated],
            // ------------------------------------------------ \\
            'usage_limit' => 'required|integer|min:1|max:100',
            'expiry_date' => 'nullable|date|after:today',
            // ------------------------------------------------ \\
            'price' => 'nullable|min:0.01|numeric',
            // ------------------------------------------------ \\
            'class_id' => 'required|exists:classes,id',
            // ------------------------------------------------ \\
        ];
    }
    private function onDelete(): array
    {
        return [
            // 'coupon_id' => 'required|exists:coupons,id',
        ];
    }
    public function rules(): ?array
    {
        return match ($this->method()) {
            'POST' => $this->onCreate(),
            'PUT' => $this->onUpdate(),
            'DELETE' => $this->onDelete(),
            default => abort(404),
        };
    }
}
