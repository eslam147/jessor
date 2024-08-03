<?php

namespace App\Http\Requests\Dashboard\Coupon;

use Illuminate\Foundation\Http\FormRequest;

class CouponRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    private function onCreate(): array
    {
        // ------------------------------------------------ \\
        return [
            'coupons_count' => 'required|integer|min:1|max:1000',
            // ------------------------------------------------ \\
            'usage_limit' => 'required|integer|min:1|max:100',
            'expiry_date' => 'nullable|date|after:today',
            // ------------------------------------------------ \\
            'teacher_id' => 'nullable|exists:teachers,id',
            'lesson_id' => 'nullable|exists:lessons,id',
            // ------------------------------------------------ \\
            'subject_id' => 'nullable|exists:subjects,id',
            'class_id' => 'nullable|exists:classes,id',
        ];
        // ------------------------------------------------ \\
    }
    private function onUpdate(): array
    {
        // ------------------------------------------------ \\
        return [
            'code' => 'required|string|unique:coupons,code,' . $this->route('id'),
            // ------------------------------------------------ \\
            'usage_limit' => 'required|integer|min:1',
            'expiry_date' => 'nullable|date|after:today',
            'expiry_time' => 'nullable|date_format:H:i',
            // ------------------------------------------------ \\
            'teacher_id' => 'nullable|exists:teachers,id',
            'lesson_id' => 'nullable|exists:lessons,id',
            // ------------------------------------------------ \\
            'subject_id' => 'nullable|exists:subjects,id',
            'class_id' => 'nullable|exists:classes,id',
            // ------------------------------------------------ \\
        ];
        // ------------------------------------------------ \\
    }
    private function onDelete(): array
    {
        return [
            // 'coupon_id' => 'required|exists:coupons,id',
            // 'course_id' => 'required|exists:courses,id',
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
