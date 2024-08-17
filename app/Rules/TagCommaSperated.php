<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class TagCommaSperated implements Rule
{
    public function passes($attribute, $value)
    {
        $items = explode(',', $value);

        if (count($items) > 5) {
            return false;
        }

        $trimmedTags = array_map('trim', $items);
        $uniqueItems = array_unique($trimmedTags);

        foreach ($trimmedTags as $item) {
            $length = strlen($item);
            if ($length < 3 || $length > 255) {
                return false;
            }
        }

        if (count($trimmedTags) !== count($uniqueItems)) {
            return false;
        }

        return true;
    }

    public function message()
    {
        return 'The :attribute must be with up to 5 unique items, each between 3 and 255 characters long.';
    }
}
