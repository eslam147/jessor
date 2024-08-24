@extends('student_dashboard.layout.app')
@section('style')
    <style>
        img.ask_img {
            width: 100% !important;
        }
    </style>
    <script>
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
                    <div class="col-xs-12">

                        <div class=" my-5">
                            <div class="d-flex justify-content-between align-items-center">
                                <h2 class="text-center mb-4">Exam - {{ $questions_data['exam']->title }}</h2>
                                <div class="time">
                                    <span>LeftTime : </span>
                                    <span id="timer"></span>
                                </div>
                            </div>
                            <form
                                action="{{ route('student_dashboard.exams.online.submit', ['exam' => $questions_data['exam']->id]) }}"
                                method="POST" id="quizForm">
                                @csrf
                                <div class="row align-items-center justify-content-center flex-column">

                                    @foreach ($questions_data['data'] as $index => $question)
                                        <div class="col-md-5">
                                            <div class="card">
                                                <div class="box overflow-hidden">
                                                    @isset($question['image'])
                                                        <figure class="img-hov-zoomin mb-0">
                                                            <img class="ask_img" src="{{ $question['image'] }}"
                                                                alt="{{ $question['question'] }}">
                                                        </figure>
                                                    @endisset
                                                    <div class="box-body">
                                                        <h5>{{ $loop->iteration }}. {!! $question['question'] !!}</h5>
                                                        {{-- 'answers_data' => 'required|array',
                                                    'answers_data.*.question_id' => 'required|numeric',
                                                    'answers_data.*.option_id' => 'required|array', --}}

                                                        {{-- <h4><a href="#">Ut ac purus ultricies, convallis dui auctor, dignissim
                                                            turpis.</a></h4> --}}
                                                        {{-- <p>October 19, 2018</p>
                                                    <p>{!! $question['image'] !!}</p> --}}
                                                        <div class=" align-items-center mt-3">
                                                            <input type="hidden"
                                                                name="answers_data[{{ $question['id'] }}][question_id]"
                                                                value="{{ $question['id'] }}">
                                                            @foreach ($question['options'] as $option)
                                                                <div class="form-check">
                                                                    <input @checked(old("answers_data.{$question['id']}.option_id") == $option['id'])
                                                                        class="form-check-input" type="radio"
                                                                        name="answers_data[{{ $question['id'] }}][option_id][]"
                                                                        id="question{{ $question['id'] }}_{{ $option['id'] }}"
                                                                        value="{{ $option['id'] }}" required>
                                                                    <label class="form-check-label"
                                                                        for="question{{ $question['id'] }}_{{ $option['id'] }}">
                                                                        {!! $option['option'] !!}
                                                                    </label>
                                                                </div>
                                                            @endforeach
                                                        </div>

                                                    </div>
                                                </div>
                                                @if (!empty($question['note']))
                                                    <div class="bg-light card-footer">
                                                        <b>Note</b>: <p>{{ $question['note'] }}</p>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <hr>
                                <div class="text-center mt-4">
                                    <button type="submit" class="btn btn-primary">Submit Quiz</button>
                                </div>
                            </form>
                        </div>

                    </div>

                </div>
            </section>
        </div>
    </div>
@endsection
@section('script')
    <script>
        let timerDuration = {{ $duration }};
        let timerElement = $('#timer');
        let timeRemaining = timerDuration * 60;

        let countdown = setInterval(function() {
            let minutes = Math.floor(timeRemaining / 60);
            let seconds = timeRemaining % 60;
            timerElement.text(`${minutes}:${seconds.toString().padStart(2, '0')}`);
            timeRemaining--;
            if (timeRemaining < 0) {
                clearInterval(countdown);
                window.removeEventListener('beforeunload', examWillLeave);
                document.getElementById('quizForm').submit();
            }
        }, 1000);
    </script>
@endsection
