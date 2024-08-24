@extends('student_dashboard.layout.app')
@section('content')
    <div class="content-wrapper">
        <div class="container-full">
            <!-- Main content -->
            <section class="content">
                <div class="row">

                    <div class="container mt-5">
                        <div class="card">
                            <div class="card-header bg-primary text-white text-center">
                                <h2>Online Exam Results</h2>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <h5>Total Questions:
                                        <span class="badge bg-secondary">
                                            {{ $examResult->total_questions }}
                                        </span>
                                    </h5>
                                </div>

                                <div class="mb-3">
                                    <h5>Total Marks Obtained: <span
                                            class="badge bg-primary">{{ $examResult->total_obtained_marks }}</span></h5>
                                    <h5>Total Marks: <span class="badge bg-secondary">{{ $examResult->total_marks }}</span>
                                    </h5>
                                </div>
                                <hr>
                                <div class="mb-3">
                                    <h5>Correct Answers</h5>
                                    <p>Total Correct:
                                        <span class="badge bg-success">
                                            {{ $examResult->correct_answers['total_questions'] }}
                                        </span>
                                    </p>
                                    <ul class="list-group">
                                        @foreach ($examResult->correct_answers['question_data'] as $question)
                                            <li class="list-group-item row justify-content-between align-items-center">
                                                <div class="col-10">
                                                    <h4>
                                                        {{ $question['question']['question'] }}
                                                    </h4>
                                                </div>
                                                <div class="col-2">
                                                    <span class="badge bg-success">Marks: {{ $question['marks'] }}</span>
                                                </div>
                                                <ul class="col-12 px-4">
                                                    @foreach ($question['question']['options'] as $option)
                                                        <li>{{ $option['option'] }}
                                                            @if (in_array($option['id'], $question['correct_answers']))
                                                                <span class="badge badge-success">
                                                                    الاجابه الصحيحه</span>
                                                            @endif
                                                            @if ($option['id'] == $question['student_answer'])
                                                                <span class="badge badge-success">
                                                                    اختيارك</span>
                                                            @endif
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>

                                <div class="mb-3">
                                    <h5>Incorrect/Unattempted Answers</h5>
                                    <p>Total Incorrect: <span
                                            class="badge bg-danger">{{ $examResult->in_correct_answers['total_questions'] }}</span>
                                    </p>
                                    <ul class="list-group">
                                        @foreach ($examResult->in_correct_answers['question_data'] as $question)
                                            <li class="list-group-item row justify-content-between align-items-center">
                                                <div class="col-10">
                                                    <h4>
                                                        {{ $question['question']['question'] }}
                                                    </h4>
                                                </div>
                                                <div class="col-2">
                                                    <span class="badge bg-success">Marks: {{ $question['marks'] }}</span>
                                                </div>
                                                <ul class="col-12 px-4">
                                                    {{-- @dd(
                                                        $question['correct_answers'],
                                                        $question['student_answer'],
                                                        $question['question']['options']
                                                    ) --}}
                                                    @foreach ($question['question']['options'] as $option)
                                                        <li>{{ $option['option'] }}
                                                            @if (in_array($option['id'], $question['correct_answers']))
                                                                <span class="badge badge-success">
                                                                    الاجابه الصحيحه</span>
                                                            @endif
                                                            {{-- @dd($option , $question['student_answer']) --}}
                                                            @if ($option['id'] == $question['student_answer'])
                                                                <span class="badge badge-danger">
                                                                    اختيارك</span>
                                                            @endif
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </li>
                                        @endforeach
                                        <!-- Add more correct questions as needed -->
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </section>
        </div>
    </div>
@endsection
