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
        return [
            
            'coupons_count' => 'required|integer|min:1',
            'usage_limit' => 'required|integer|min:1',
            
            'price' => 'required|numeric|min:0',
            
            'expiry_date' => 'nullable|date|after:today',
            
            'teacher_id' => 'nullable|exists:teachers,id',

            'topic_id' => 'nullable|exists:lesson_topics,id',
        ];
    }
    private function onUpdate(): array
    {
        return [
            // 'coupon_id' => 'required|exists:coupons,id',
            // 'course_id' => 'required|exists:courses,id',
        ];
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
            'POST'          => $this->onCreate(),
            'PUT'           => $this->onUpdate(),
            'DELETE'        => $this->onDelete(),
            default         => abort(404),
        };
    }
}
