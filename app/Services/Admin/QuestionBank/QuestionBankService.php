<?php
namespace App\Services\Admin\QuestionBank;

use Illuminate\Http\UploadedFile;
use App\Models\OnlineExamQuestion;
use App\Models\OnlineExamQuestionAnswer;
use App\Models\OnlineExamQuestionOption;

class QuestionBankService
{
    public function __construct()
    {

    }
    public function storeImageFromRequest($requestImageName)
    {
        if (request()->hasFile($requestImageName)) {
            return $this->storeImage(request()->file($requestImageName));
        }
        return null;
    }
    public function storeImage(UploadedFile $image)
    {
        $fileName = time() . '-' . $image->hashName();

        $file_path = "online-exam-questions/{$fileName}";

        //resized image
        resizeImage($image);

        $destinationPath = storage_path('app/public/online-exam-questions');
        $image->move($destinationPath, $fileName);

        return $file_path;
    }
    public function saveOptions(OnlineExamQuestion $question, $options)
    {
        $optionIds = [];
        foreach ($options as $key => $option) {
            $optionIds[$key] = OnlineExamQuestionOption::create([
                'question_id' => $question->id,
                'option' => htmlspecialchars($option)
            ])->id;
        }
        return $optionIds;
    }
    public function saveAnswers(OnlineExamQuestion $question, $optionIds, $answers)
    {
        foreach ($answers as $answer) {
            foreach ($optionIds as $key => $option) {
                if ($key == $answer) {
                    OnlineExamQuestionAnswer::create([
                        'question_id' => $question->id,
                        'answer' => $option
                    ]);
                }
            }
        }
    }
    public function saveOptionsWithAnswer(OnlineExamQuestion $question, $options, $answers)
    {
        $optionIds = $this->saveOptions($question, $options);
        $this->saveAnswers($question, $optionIds, $answers);
        return true;
    }
}