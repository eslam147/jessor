<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class FilterWordsRule implements Rule
{
    protected $bannedPhrases = ['مرحبا', 'اهلا بكم في', 'كيف حالك'];

    public function passes($attribute, $value)
    {
        $filteredValue = $this->cleanText($value);
        return !$this->containsBannedPhrases($filteredValue);
    }

    /**
     * دالة لتنظيف النص
     */
    protected function cleanText($value)
    {
        // إزالة الإيموجي
        $value = $this->removeEmojis($value);
        // إزالة علامات <img>
        $value = $this->removeImgTags($value);
        $value = strip_tags($value);
        $value = $this->removeNbsp($value);
        $value = $this->removeDiacritics($value);
        $value = $this->removePunctuation($value);
        $value = $this->normalizeAlef($value);
        // إزالة المسافات بين الأحرف
        $value = $this->removeSpacesBetweenLetters($value);
        // إزالة التكرار المتتالي للأحرف
        $value = $this->normalizeRepeatedCharacters($value);
        return $value;
    }

    function normalizeAlef($text) {
        // استبدال الحروف "أ"، "إ"، و"آ" بحرف "ا"
        return str_replace(['أ', 'إ', 'آ'], 'ا', $text);
    }
    
    function removeDiacritics($text) {
        $diacriticsPattern = '/[\x{0610}-\x{061A}\x{064B}-\x{065F}]+/u'; // نطاق علامات التشكيل في العربية
        return preg_replace($diacriticsPattern, '', $text);
    }

    
    function removePunctuation($text): array|string|null {
        $punctuationPattern = '/[.,!؟?;:ـ()\[\]{}_"\'،؛*\\\\\/]/u'; // التعبير النمطي لعلامات الترقيم
        return preg_replace($punctuationPattern, '', $text);
    }
    
    /**
     * دالة لإزالة الإيموجي من النص
     */
    protected function removeEmojis($value)
    {
        return preg_replace('/[\x{1F600}-\x{1F64F}\x{1F300}-\x{1F5FF}\x{1F680}-\x{1F6FF}\x{2600}-\x{26FF}\x{2700}-\x{27BF}]/u', '', $value);
    }
        /**
     * دالة لإزالة &nbsp;
     */

    protected function removeNbsp($value)
    {
        // استبدال &nbsp; بفراغ عادي أو إزالته
        return str_replace('&nbsp;', ' ', $value);
    }

    /**
     * دالة لإزالة علامات <img> من النص
     */
    protected function removeImgTags($value)
    {
        return preg_replace('/<img\s+[^>]*>/i', '', $value);
    }

    /**
     * دالة لإزالة تكرار الأحرف المتتالي
     */
    protected function normalizeRepeatedCharacters($value)
    {
        return preg_replace('/(.)\1{1,}/u', '$1', $value);
    }

    /**
     * دالة لإزالة المسافات بين الحروف
     */
    protected function removeSpacesBetweenLetters($value)
    {
        return preg_replace('/\s+/', '', $value);
    }

    /**
     * دالة للتحقق من وجود العبارات المحظورة في النص
     */
    protected function containsBannedPhrases($value)
    {
        foreach ($this->bannedPhrases as $phrase) {
            $cleanedPhrase = $this->cleanText($phrase);
            if (stripos($value, $cleanedPhrase) !== false) {
                return true;
            }
        }
        return false;
    }
    public function message()
    {
        return __('your_message_contains_inappropriate_words');
    }
}