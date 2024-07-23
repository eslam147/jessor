@extends('student_dashboard.layout.app')
@section('style')

<style>
    .bg-green{
        background-color: #6AC977;
        color: #ffffff;
        border-radius: 15px;
    }

    .bg-green-br{
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
                <div class="col-4" >
                    <div class="box">
                        <div class="box-body text-center">
                            <div class="mb-20 mt-20">
                                <img src="{{ asset('student/images/avatar/avatar-12.png') }}" width="150" class="rounded-circle bg-info-light" alt="user" />
                                <h4 class="mt-20 mb-0"> {{ $row->first_name.' '.$row->last_name }} </h4>

                            </div>
                            <div class="badge badge-pill badge-info-light fs-16">{{ $subject->name }}</div>

                        </div>
                        <div class="p-25 mt-15 bt-1">
                            <div class="row text-center">
                                <div class="col-12  ">
                                    <div class="bg-primary mt-5 rounded">
                                      
                                        <a href="{{ route('teacher.lessons',['teacher_id' => $row->id,'subject_id' => $subject->id]) }}" >
                                            <h5 class="text-white text-center p-10"> show lessons </h5>
                                        </a>
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
@endsection
