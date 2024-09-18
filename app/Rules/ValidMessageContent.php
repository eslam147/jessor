<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ValidMessageContent implements Rule
{
    /**
     * تحديد ما إذا كانت القيمة صحيحة.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {

        // التعبير النمطي للتحقق من النصوص والعلامات <img>
        $pattern = '/^([a-zA-Z0-9\s.,!?;:\'"()أ-ي٠-٩&\s*\\\\\/\-ـ\[\]{}_"\'،؛؟\x{1F600}-\x{1F64F}\x{1F300}-\x{1F5FF}\x{1F680}-\x{1F6FF}\x{2600}-\x{26FF}\x{2700}-\x{27BF}]*|<img\s+[^>]*>|&nbsp;)*$/u';
        return preg_match($pattern, $value);
    }
    /**
     * الحصول على رسالة التحقق عند الفشل.
     *
     * @return string
     */
    public function message()
    {
        return __('your_message_can_only_contain_text_or_image_tags');
    }
}
