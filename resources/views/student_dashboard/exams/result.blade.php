@extends('student_dashboard.layout.app')
@section('style')
    <style>
        .form-quiz {
            padding: 10px;

        }

        .form-quiz:has(input[type=radio]:checked) {
            background: #0052cc33;
            box-sizing: content-box;
            border: 2px solid #0052cc;
            border-radius: 5px;
        }

        .form-quiz.success:has(input[type=radio]:checked) {
            border: 2px solid #04a08b !important;
            background: #04a08b33 !important;
        }

        .form-quiz.error:has(input[type=radio]:checked) {
            border: 2px solid #ff562f !important;
            background: #ff562f33 !important;
        }
    </style>
@endsection
@section('content')
    <div class="content-wrapper">
        <div class="container-full">
            <!-- Main content -->
            <section class="content">
                <div class="row">

                    <div class="col-12">
                        <div class="col-xl-12 col-lg-12 col-12">
                            <div class="box box-widget widget-user-2">
                                <div class="widget-user-header bg-primary">
                                    <h3 class="widget-user-username">{{ $examResult->exam->title }} </h3>
                                </div>
                                <div class="box-footer no-padding">
                                    <ul class="nav d-block nav-stacked">

                                        <li class="nav-item text-bold"><a href="#" class="nav-link">Total Exam Marks <span
                                                    class="pull-right badge bg-info-light">{{ $examResult->total_marks }}</span></a>
                                        </li>
                                        <li class="nav-item text-bold"><a href="#" class="nav-link">Total Questions <span
                                                    class="pull-right badge bg-info-light">{{ $examResult->total_questions }}</span></a>
                                        </li>
                                        <li class="nav-item text-bold"><a href="#" class="nav-link">Total Correct <span
                                                    class="pull-right badge bg-success-light">{{ $examResult->correct_answers_count }}</span></a>
                                        </li>
                                        <li class="nav-item text-bold"><a href="#" class="nav-link">Obtained <span
                                                    class="pull-right badge bg-success-light">{{ $examResult->total_obtained_marks }}</span></a>
                                        </li>
                                        <li class="nav-item text-bold"><a href="#" class="nav-link">Grade <span
                                                    class="pull-right badge bg-info-light">{{ $examResult->grade }}</span></a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="container mt-5">
                        <div class="card bg-bubbles-white">
                            <div class="card-body">
                                <div class="row justify-content-center">
                                    @foreach ($examResult->examQuestions as $question)
                                        <div class="col-7">
                                            <div class="card">
                                                <div class="box overflow-hidden">
                                                    @isset($question->questions->image_url)
                                                        <figure class="img-hov-zoomin mb-0">
                                                            <img class="ask_img w-p100" draggable="false"
                                                                src="{{ $question->questions->image_url }}" alt="">
                                                        </figure>
                                                    @endisset

                                                    <div class="box-body pt-0 px-0">
                                                        <div
                                                            class="d-block  bg-primary align-items-center p-5 px-25 justify-content-between">
                                                            <h5 class="text-break text-justify text-white mb-0 text-wrap">
                                                                {{ $loop->iteration }}. {!! html_entity_decode($question->questions->question) !!}
                                                            </h5>
                                                        </div>
                                                        <hr class="mt-0">
                                                        <div class="align-items-center mt-3 px-15">
                                                            <input type="hidden"
                                                                name="answers_data[{{ $question['id'] }}][question_id]"
                                                                value="{{ $question['id'] }}">
                                                            @foreach ($question->questions->options as $option)
                                                                <div
                                                                    class="form-check form-quiz {{ in_array($option['id'], $question->correct_answers) ? 'success' : 'error' }}">
                                                                    <input disabled @checked($question->student_answer == $option['id'])
                                                                        class="form-check-input answer_qs text-black"
                                                                        type="radio"
                                                                        name="answers_data[{{ $question['id'] }}][option_id][]"
                                                                        id="question{{ $question['id'] }}_{{ $option['id'] }}"
                                                                        value="{{ $option['id'] }}" required>
                                                                    <label class="form-check-label text-justify text-black"
                                                                        for="question{{ $question['id'] }}_{{ $option['id'] }}">
                                                                        {!! html_entity_decode($option['option']) !!}
                                                                        @if (in_array($option->id, $question->correct_answers))
                                                                            @if ($option->id != $question->student_answer)
                                                                                <span class="badge badge-success">الاجابه
                                                                                    الصحيحه</span>
                                                                            @endif
                                                                            <span
                                                                                class="badge bg-dark mx-2 px-10 text-white">
                                                                                Marks:{{ $question->marks }}
                                                                            </span>
                                                                        @endif
                                                                    </label>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="bg-light card-footer ">
                                                    <b>Explain Answer</b>: <p class="text-justify">
                                                        {{ $question->questions->explain_answer ?? 'No Explanation' }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
@endsection
