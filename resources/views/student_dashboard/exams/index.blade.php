@extends('student_dashboard.layout.app')
@section('content')
    <div class="content-wrapper">
        <div class="container-full">
            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12 col-lg-4">
                        @foreach ($exams['data'] as $exam)
                            <div class="box-body bg-primary-light mb-30 px-xl-5 px-xxl-20 pull-up">
                                <div class="d-flex align-items-center ps-xl-20">
                                    <div class="me-20">
                                        <svg class="circle-chart" viewBox="0 0 33.83098862 33.83098862" width="80"
                                            height="80">
                                            <defs>
                                                <pattern id="img" patternUnits="userSpaceOnUse" height="80"
                                                    width="80">
                                                    <image x="5" y="5" height="70%" width="70%"
                                                        xlink:href="{{ tenant_asset(settingByType('logo1')) }}">
                                                    </image>
                                                </pattern>
                                            </defs>
                                            <circle class="circle-chart__background" stroke="#efefef" stroke-width="2"
                                                cx="16.91549431" cy="16.91549431" r="15.91549431" fill="url(#img)"></circle>
                                            <circle class="circle-chart__circle" stroke="#68CA77" stroke-width="2"
                                                stroke-dasharray="79" stroke-linecap="round" fill="none" cx="16.91549431"
                                                cy="16.91549431" r="15.91549431"></circle>
                                        </svg>
                                    </div>
                                    <div class="d-flex flex-column w-75">
                                        <a href="#"
                                            class="text-dark hover-primary mb-5 fw-600 fs-18">{{ $exam->title }}</a>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between  mx-lg-10 mt-10">
                                    {{-- <h2 class="my-5 c-green">79%</h2> --}}
                                    <div class="text-center">
                                        {{-- @dd(
                                            // $exam->student_attempt
                                        ) --}}
                                        @if (!$exam->student_attempt)
                                            <a data-exam-id="{{ $exam->exam_id }}"
                                                class="start_exam waves-effect waves-light btn bg-green mb-5 px-25 py-8">
                                                Start Exam
                                            </a>
                                        @else
                                            <a href="{{ route('student_dashboard.exams.online.result', $exam->exam_id) }}"
                                                class="waves-effect waves-light btn bg-green mb-5 px-25 py-8">Show Marks</a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            {{-- <tr>
                            <td>
                                {{ $exam->id }}
                            </td>
                            <td class="fw-600">{{ $exam->name }}</td>
                            <td class="text-fade">{{ $exam->description }}</td>
                            <td class="text-fade">{{ $exam->starting_date }}</td>
                            <td class="text-fade">{{ $exam->ending_date }}</td>
                            <td class="text-fade">{{ $exam->session_year->name }}</td>
                            <td class="text-fade">
                                @if (now()->parse($exam->starting_date)->isPast() &&
    now()->parse($exam->ending_date)->isPast())
                                    OnGoinng
                                @elseif (now()->parse($exam->starting_date)->isFuture())
                                    UpComing
                                @else
                                    Completed
                                @endif
                            </td>
                        </tr> --}}
                        @endforeach

                    </div>

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
