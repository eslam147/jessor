@extends('student_dashboard.layout.app')
@section('style')
    <link rel="stylesheet"
        href="{{ global_asset('student/assets/vendor_components/horizontal-timeline/css/horizontal-timeline.css') }}">
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
                            <div class="box">
                                <section class="cd-horizontal-timeline">
                                    <div class="timeline">
                                        <div class="events-wrapper">
                                            <div class="events">
                                                <ol>
                                                    @foreach ($questions_data['data'] as $index => $question)
                                                        <li>
                                                            <a href="#0"
                                                                data-date="{{ now()->subDays($index)->format('d/m/Y') }}"
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
                                                    data-date="{{ now()->subDays($index)->format('d/m/Y') }}">
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
                                                                    <h5>{{ $loop->iteration }}. {!! $question['question'] !!}
                                                                    </h5>
                                                                    <div class="align-items-center mt-3">
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
                                                </li>
                                            @endforeach
                                        </ol>
                                    </div>
                                    <!-- .events-content -->
                                </section>
                                <div class="my-5 d-flex justify-content-between w-75 m-auto">
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
    <script>
        $('.exam_submit').click(function(e) {
            e.preventDefault();
            // $(this).addClass('d-none');
            // $('.switch_btn').removeClass('d-none');
            $('#quizForm').submit();
        })
        $('.switch_btn').on('timeline-changed', function() {
            $('.exam_submit,.switch_btn.prev,.switch_btn.next').addClass('d-none');

            if (window.timeline.currentIndex == window.timeline.lastIndex) {
                $('.exam_submit,.switch_btn.prev').removeClass('d-none');
            } else if (window.timeline.currentIndex > 0 && window.timeline.currentIndex < window.timeline
                .lastIndex) {
                $('.switch_btn.next,.switch_btn.prev').removeClass('d-none');
            } else if (window.timeline.currentIndex == 0) {
                $('.switch_btn.next').removeClass('d-none');
            }
        });

        const timerDuration = {{ $duration }};
        const timerElement = $('#timer');
        let timeRemaining = timerDuration * 60;
        $('#quizForm').submit(function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, Submit it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.removeEventListener('beforeunload', examWillLeave);
                    document.getElementById('quizForm').submit();
                }
            })
        });
        let showFiveMinToast = false;
        let showThreeMinToast = false;

        let countdown = setInterval(function() {
            let hours = Math.floor(timeRemaining / 3600);
            let minutes = Math.floor((timeRemaining % 3600) / 60);
            let seconds = timeRemaining % 60;

            timerElement.text(
                `${hours}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`);
            timeRemaining--;

            if (timeRemaining < 0) {
                clearInterval(countdown);
                window.removeEventListener('beforeunload', examWillLeave);
                document.getElementById('quizForm').submit();
            }else{
                if (!showFiveMinToast && timeRemaining === 5 * 60) {
            Swal.fire({
                position: 'top-end',
                icon: 'info',
                title: '5 minutes remaining!',
                showConfirmButton: false,
                timer: 1500,
                toast: true
            });
            showFiveMinToast = true;
        }

        // Check for 3 minutes remaining
        if (!showThreeMinToast && timeRemaining === 3 * 60) {
            Swal.fire({
                position: 'top-end',
                icon: 'warning',
                title: '3 minutes remaining!',
                showConfirmButton: false,
                timer: 1500,
                toast: true
            });
            showThreeMinToast = true;
        }
            }
        }, 1000);
        // let countdown = setInterval(function() {
        //     let minutes = Math.floor(timeRemaining / 60);
        //     let seconds = timeRemaining % 60;
        //     timerElement.text(`${minutes}:${seconds.toString().padStart(2, '0')}`);
        //     timeRemaining--;
        //     if (timeRemaining < 0) {
        //         clearInterval(countdown);
        //         window.removeEventListener('beforeunload', examWillLeave);
        //         document.getElementById('quizForm').submit();
        //     }
        // }, 1000);
    </script>
@endsection
