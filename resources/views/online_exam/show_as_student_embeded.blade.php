@section('showContentOnly', '')
@include('student_dashboard.exams.show', [
    'isDemo' => true,
    'examEndTime' => now()->addYear(),
    'questions_data' => $questions_data,
]);