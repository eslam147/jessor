@extends('student_dashboard.layout.app')
@section('style')
    <link rel="stylesheet"
        href="{{ global_asset('student/assets/vendor_components/horizontal-timeline/css/horizontal-timeline.css') }}">
    <style>
        img.ask_img {
            width: 100% !important;
        }

        .form-quiz {
            padding: 10px;

        }

        .form-quiz:has(input[type=radio]:checked) {
            background: #0052cc33;
            box-sizing: content-box;
            border: 2px solid #0052cc;
            border-radius: 5px;
        }

        .cd-horizontal-timeline .events a::after {
            height: 25px;
            width: 25px;
        }

        .cd-horizontal-timeline .events-wrapper {
            overflow: unset;
        }

        .cd-horizontal-timeline .events a.solved::before {
            content: "";
            width: 8px;
            height: 13px;
            border-top: 2px solid transparent;
            border-left: 2px solid transparent;
            border-right: 2px solid #ffffff !important;
            border-bottom: 2px solid #ffffff !important;
            position: absolute;
            left: 0;
            bottom: 0;
            transform: rotateZ(37deg) translate(100%, 0%);
            z-index: 9999;
        }

        .cd-horizontal-timeline .events a::after {
            right: auto;
            -webkit-transform: translate(-50%, 50%);
            -moz-transform: translate(-50%, 50%);
            -ms-transform: translate(-50%, 50%);
            -o-transform: translate(-50%, 50%);
            transform: translate(-50%, 50%);
            bottom: 0;
        }

        .cd-horizontal-timeline .events a.solved::before {
            border-right-color: #0052cc !important;
            border-bottom-color: #0052cc !important;
        }

        .cd-horizontal-timeline .events a.solved.selected::before {
            border-right-color: #ffffff !important;
            border-bottom-color: #ffffff !important;
        }

        .cd-horizontal-timeline .events a {
            padding-bottom: 25px
        }

        .cd-horizontal-timeline {
            margin-bottom: .5rem;
        }
        .text-justify {
            text-align: justify !important;
        }
        h5 {
        font-size: 1.5rem;
        }
        
    </style>
    <script>
        const examEndTime = @json($examEndTime);
        function examWillLeave() {
            var message = 'Are you sure you want to leave? You might lose your progress.';
            e.preventDefault();
            e.returnValue = message;
            return message;
        }
        window.addEventListener('beforeunload', examWillLeave);
    </script>
@endsection
@section('content')
    <div class="content-wrapper">
        <div class="container-full">
            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center">
                            <h2 class="text-center mb-4">Exam - {{ $questions_data['exam']->title }}</h2>
                            <div class="time">
                                <span>LeftTime: </span>
                                <span id="timer"></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12">
                        <form
                            action="{{ route('student_dashboard.exams.online.submit', ['exam' => $questions_data['exam']->id]) }}"
                            method="POST" id="quizForm">
                            @csrf
                            <div class="box bg-bubbles-white">
                                <section class="cd-horizontal-timeline">
                                    <div class="timeline">
                                        <div class="events-wrapper">
                                            <div class="events">
                                                <ol>
                                                    @foreach ($questions_data['data'] as $index => $question)
                                                        <li>
                                                            <a href="#0"
                                                                data-date="{{ now()->subMonths($index)->format('d/m/Y') }}"
                                                                class="{{ $loop->first ? 'selected' : '' }}">
                                                                {{ $loop->iteration }}. Q
                                                            </a>
                                                        </li>
                                                    @endforeach
                                                </ol> <span class="filling-line" aria-hidden="true"></span>
                                            </div>
                                            <!-- .events -->
                                        </div>
                                        <!-- .events-wrapper -->
                                        <ul class="cd-timeline-navigation">
                                            <li><a href="#0" class="prev inactive">Prev</a></li>
                                            <li><a href="#0" class="next">Next</a></li>
                                        </ul>
                                        <!-- .cd-timeline-navigation -->
                                    </div>
                                    <!-- .timeline -->
                                    <div class="events-content">
                                        <ol>
                                            @foreach ($questions_data['data'] as $index => $question)
                                                <li class="{{ $loop->first ? 'selected' : '' }}"
                                                    data-date="{{ now()->subMonths($index)->format('d/m/Y') }}">
                                                    <div class="col-md-7">
                                                        <div class="card">
                                                            <div class="box overflow-hidden">
                                                                @isset($question['image'])
                                                                    <figure class="img-hov-zoomin mb-0">
                                                                        <img class="ask_img" src="{{ $question['image'] }}"
                                                                            alt="{{ $question['question'] }}">
                                                                    </figure>
                                                                @endisset
                                                                <div class="box-body pt-0 px-0">
                                                                    <h5
                                                                        class="text-justify bg-{{ collect(['primary', 'success', 'danger', 'warning', 'info','bitbucket','dark','body'])->random() }} p-5 px-25 text-break text-white text-wrap">
                                                                        {{ $loop->iteration }}. {!! $question['question'] !!}
                                                                    </h5>
                                                                    <hr>
                                                                    <div class="align-items-center mt-3 px-15">
                                                                        <input type="hidden"
                                                                            name="answers_data[{{ $question['id'] }}][question_id]"
                                                                            value="{{ $question['id'] }}">
                                                                        @foreach ($question['options'] as $option)
                                                                            <div class="form-check form-quiz">
                                                                                <input @checked(old("answers_data.{$question['id']}.option_id") == $option['id'])
                                                                                    class="form-check-input answer_qs"
                                                                                    type="radio"
                                                                                    name="answers_data[{{ $question['id'] }}][option_id][]"
                                                                                    id="question{{ $question['id'] }}_{{ $option['id'] }}"
                                                                                    value="{{ $option['id'] }}" required>
                                                                                <label class="form-check-label text-justify"
                                                                                    for="question{{ $question['id'] }}_{{ $option['id'] }}">
                                                                                    {!! $option['option'] !!}
                                                                                </label>
                                                                            </div>
                                                                        @endforeach
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            @if (!empty($question['note']))
                                                                <div class="bg-light card-footer ">
                                                                    <b>Note</b>: <p class="text-justify">{{ $question['note'] }}</p>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </ol>
                                    </div>
                                    <!-- .events-content -->
                                </section>
                                <div class="bg-secondary-light d-flex justify-content-between m-auto my-5 p-10 rounded-2 w-75">
                                    <button type="button" class="switch_btn prev btn btn-primary d-none">
                                        <i class="fa fa-arrow-left" aria-hidden="true"></i>
                                        Previous
                                    </button>
                                    <button type="button" class="switch_btn next btn btn-primary">
                                        Next
                                        <i class="fa fa-arrow-right" aria-hidden="true"></i>
                                    </button>
                                    <button type="submit" class="btn exam_submit btn-success d-none">
                                        <i class="fa fa-check" aria-hidden="true"></i>
                                        Send Exam
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </section>
        </div>
    </div>
@endsection
@section('script')
    <script src="{{ global_asset('student/assets/vendor_components/horizontal-timeline/js/horizontal-timeline.js') }}">
    </script>
    <script src="{{ global_asset('vendor/sweetalert/sweetalert.all.js') }}"></script>
    <script src="{{ global_asset('assets/js/custom/exam.js') }}"></script>
    <script>

    </script>
@endsection
