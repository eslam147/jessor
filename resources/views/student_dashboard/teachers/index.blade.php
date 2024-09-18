@extends('student_dashboard.layout.app')
@section('style')
    <style>
        .bg-green {
            background-color: #6AC977;
            color: #ffffff;
            border-radius: 15px;
        }

        .bg-green-br {
            border-color: #6ac977;
            color: #7E8FC1;
            background-color: transparent;
            border-radius: 15px;
        }
    </style>
@endsection
@section('content')
    <div class="content-wrapper">
        <div class="container-full">
            <!-- Main content -->
            <section class="content">
                <div class="row">
                    @foreach ($subjectTeachers as $row)
                        <div class="col-lg-4 col-12">
                            <div class="box bg-body-secondary">
                                <div class="box-body">
                                    <div class="d-flex flex-row">
                                        <div>
                                            <img src="{{ !empty($row->user->getRawOriginal('image')) ? $row->user->image : global_asset('student/images/avatar/avatar-12.png') }}"
                                                alt="user"
                                                class="bg-success-light h-150 object-fit-cover rounded-circle w-150"
                                                width="100">
                                        </div>
                                        <div class="ps-20">
                                            <h3>{{ $row->user->full_name }}</h3>
                                            <h6>{{ $subject->name }}</h6>
                                            <a class="btn btn-primary-light text-center p-10"
                                                href="{{ route('teacher.lessons', ['teacher_id' => $row->id, 'subject_id' => $subject->id]) }}">
                                                Show Lessons
                                            </a>
                                        </div>
                                    </div>
                                    {{-- @dd(
                                        $row
                                    ) --}}
                                    <div class="row mt-40">
                                        <div class="col b-r text-center">
                                            <h2 class="font-light">{{ $row->lessons_count }}</h2>
                                            <h6>Lessons Count</h6>
                                        </div>
                                        <div class="col b-r text-center">
                                            <h2 class="font-light">{{ $row->questions_count }}</h2>
                                            <h6>Questions Count</h6>
                                        </div>
                                        <div class="col text-center">
                                            <h2 class="font-light">{{ $row->students_count ?? 0 }}</h2>
                                            <h6>Students Count</h6>
                                        </div>
                                    </div>
                                </div>
                                <div class="box-body">
                                    <p dir="auto" class="aboutscroll">
                                        {{ str($row->qualification)->limit(50) }}
                                    </p>
                                    <ul class="list-inline text-center">
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        </div>
    </div>
@endsection
