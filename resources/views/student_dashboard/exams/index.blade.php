@extends('student_dashboard.layout.app')
@section('content')
    <div class="content-wrapper">
        <div class="container-full">
            <!-- Main content -->
            <section class="bg-temple-white content">
                <div class="row">
                    @foreach ($exams['by_subject'] as $subject)
                        <div class="col-xs-12 col-lg-4">
                            <div
                                class="bg-{{ collect(['primary', 'info', 'warning', 'danger', 'success', 'bitbucket', 'dark', 'body'])->random() }}-light box-body">
                                <div class="accordion" id="accordionPanelsStayOpenExample">
                                    <div class="accordion-item p-20 border-0">
                                        <h2 class="accordion-header mt-0 pb-20" id="panelsStayOpen-headingOne">
                                            <button class="accordion-button p-0 bg-white border-0 no-shadow" type="button"
                                                data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseOne"
                                                aria-expanded="true" aria-controls="panelsStayOpen-collapseOne">
                                                <div class="d-flex align-items-center">
                                                    <div class="me-15 h-50 w-50 l-h-60 rounded text-center">
                                                        <img src="{{ $subject['image'] }}" width="50" alt="">
                                                    </div>
                                                    <div class="d-flex flex-column fw-500">
                                                        <a href="#"
                                                            class="text-dark hover-primary mb-1 fs-16">{{ $subject['name'] }}</a>
                                                    </div>
                                                </div>
                                            </button>
                                        </h2>
                                        <div id="panelsStayOpen-collapseOne" class="accordion-collapse collapse show"
                                            aria-labelledby="panelsStayOpen-headingOne">
                                            <div class="accordion-body p-0">
                                                <div class="media-list media-list-hover">
                                                    @foreach ($subject['exams'] as $exam)
                                                        <div class="card mb-3">
                                                            <div class="card-header">
                                                                <h4 class="card-title mb-0">{{ $exam->title }}</h4>
                                                            </div>

                                                            <div class="card-body">
                                                                @if (empty(optional($exam->student_attempt)->status))
                                                                    @if(!empty($exam->exam_key))
                                                                    <a data-exam-id="{{ $exam->id }}"
                                                                        class="start_exam btn btn-primary-light mb-5 px-25 py-8">
                                                                        Start Exam
                                                                    </a>
                                                                    @else
                                                                    <a href="{{ route('student_dashboard.exams.online.show', $exam->id) }}"
                                                                        class="btn btn-primary-light mb-5 px-25 py-8">
                                                                        Start Exam
                                                                    </a>
                                                                    @endif
                                                                @else
                                                                    <a href="{{ route('student_dashboard.exams.online.result', $exam->id) }}"
                                                                        class="btn btn-success-light mb-5 px-25 py-8">
                                                                        Show Marks
                                                                    </a>
                                                                @endif
                                                            </div>
                                                            <div class="card-footer d-flex justify-content-between">
                                                                @if (empty($exam->student_attempt))
                                                                    <span class="fs-14 text-muted">{{ $exam->duration }}
                                                                        {{ __('minutes') }}</span>
                                                                    <span class="fs-14 text-muted">Due in
                                                                        {{ now()->parse($exam->end_date)->diffInDays() }}
                                                                        days</span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="start_exam_modal" tabindex="-1" aria-labelledby="start_exam_modalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="examTermsModalLabel">Exam Terms</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="terms">
                        <p>{!! html_entity_decode(settingByType('online_exam_terms_condition')) !!}</p>
                    </div>
                    <form id="examForm" method="POST" autocomplete="off"
                        action="{{ route('student_dashboard.exams.online.start') }}">
                        @csrf
                        <input type="hidden" name="exam_id" id="exam_id">
                        <div class="mb-3">
                            <label for="examKey" class="form-label">Exam Key</label>
                            <input type="text" class="form-control" aut id="examKey" name="exam_key"
                                placeholder="Enter Exam Key" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer d-flex justify-content-center">
                    <button type="submit" form="examForm" class="btn btn-primary">Start</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        $('.start_exam').click(function() {
            let exam = $(this);
            $('#exam_id').val(exam.data('exam-id'));
            $('#start_exam_modal').modal('show');
        });
    </script>
@endsection
