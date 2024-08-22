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
        // made file name with combination of current time
        $file_name = time() . '-' . $image->hashName();

        //made file path to store in database
        $file_path = 'online-exam-questions/' . $file_name;

        //resized image
        resizeImage($image);

        $destinationPath = storage_path('app/public/online-exam-questions');
        $image->move($destinationPath, $file_name);

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
                        'answer' => $key
                    ]);
                }
            }
        }
    }
    public function saveOptionsWithAnswer(OnlineExamQuestion $question, $request)
    {
        $optionIds = $this->saveOptions($question, $request->eoption);
        $this->saveAnswers($question, $optionIds,$request->answer);
        return true;
    }
}