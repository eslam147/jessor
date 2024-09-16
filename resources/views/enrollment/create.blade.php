@extends('layouts.master')

@section('title', __('assign_coupon'))
@section('css')
    <style>
        label.error {
            color: var(--danger);
            margin: 10px 0;
        }
    </style>
@endsection
@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('create_coupon') }}
            </h3>
        </div>

        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">
                            {{ __('create_coupon') }}
                        </h4>
                        <form class="pt-3 store-enrollment-form" id="store-form" method="POST"
                            action="{{ route('enrollment.store') }}" novalidate="novalidate">
                            <div class="modal-body">
                                @csrf
                                <div class="row justify-content-center">
                                    <div class="col">
                                        <select name="lesson_id" id="lesson_id" required class="form-control">
                                            <option>{{ __('select_lesson') }}</option>
                                            @foreach ($lessons as $lesson)
                                                <option value="{{ $lesson->id }}">{{ $lesson->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col">
                                        <select name="student_id" id="filter_student" required class="form-control select2">
                                            <option>{{ __('select_student') }}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer border-light border-top">
                                <button type="button" class="btn btn-secondary"
                                    data-dismiss="modal">{{ __('close') }}</button>
                                <input class="btn btn-theme" type="submit" value={{ __('create') }} />
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>

@endsection

@section('script')
    <script>
        const lessons = @json($lessons->groupBy('teacher_id')->toArray());

        function setLessons(teacherID) {

            $('#lesson_id').empty();
            const teacherLessons = lessons[Number(teacherID)];
            if (teacherLessons && teacherLessons.length > 0) {
                for (let i = 0; i < teacherLessons.length; i++) {
                    let item = teacherLessons[i];
                    $('#lesson_id').append(`<option value="${item.id}">${item.name}</option>`);
                }
            }

        }
        $('#teacher_id').change(function() {
            setLessons($(this).val());
        });
        if ($('#teacher_id').val()) {

            setLessons($('#teacher_id').val());
        }
    </script>
@endsection
