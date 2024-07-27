@extends('layouts.master')

@section('title', __('create_coupon'))
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
                        <form id="create-form" class="pt-3 coupon-create-form" action="{{ route('coupons.store') }}"
                            method="POST" novalidate="novalidate">
                            @csrf
                            <div class="row">
                                <div class="form-group col-sm-12">
                                    <label>{{ __('coupons_count') }}<span class="text-danger">*</span></label>
                                    {!! Form::number('coupons_count', null, [
                                        'required',
                                        'min' => 1,
                                        'max' => 50,
                                        'step' => '1',
                                        'placeholder' => __('coupons_count'),
                                        'class' => 'form-control',
                                    ]) !!}
                                    @error('coupons_count')
                                        <p class="text-danger" role="alert">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="form-group col-sm-12">
                                    <label>{{ __('usage_limit') }}<span class="text-danger">*</span></label>
                                    {!! Form::number('usage_limit', null, [
                                        'required',
                                        'min' => 1,
                                        'step' => '1',
                                        'placeholder' => __('usage_limit'),
                                        'class' => 'form-control',
                                    ]) !!}
                                    @error('usage_limit')
                                        <p class="text-danger" role="alert">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="form-group col-sm-12">
                                    <label>{{ __('expiry_date') }}<span class="text-danger">*</span></label>
                                    {!! Form::date('expiry_date', null, ['required', 'placeholder' => __('expiry_date'), 'class' => 'form-control']) !!}
                                    @error('expiry_date')
                                        <p class="text-danger" role="alert">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="form-group col-sm-12">
                                    <label>{{ __('class') }}</label>

                                    <select name="class_id" id="class_id" class="form-control">
                                        @foreach ($classes as $section)
                                            <option value="{{ $section->id }}" @selected(old('class_id'))
                                                data-class="{{ $section->class->id }}">
                                                {{ $section->class->name }} - {{ $section->section->name }}
                                                {{ optional($section->class->streams)->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('class_id')
                                        <p class="text-danger" role="alert">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="form-group col-sm-12">
                                    <label>{{ __('subject') }}</label>
                                    {!! Form::select('subject_id', [], old('subject_id'), [
                                        'required',
                                        'readonly',
                                        'placeholder' => __('select_subject'),
                                        'class' => 'form-control',
                                        'id' => 'subject_id',
                                    ]) !!}
                                    @error('subject_id')
                                        <p class="text-danger" role="alert">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="form-group col-sm-12">
                                    <label>{{ __('teacher') }}</label>
                                    {!! Form::select('teacher_id', [], old('teacher_id'), [
                                        'readonly',
                                    
                                        'placeholder' => __('select_teacher'),
                                        'class' => 'form-control',
                                        'id' => 'teacher_id',
                                    ]) !!}
                                    @error('teacher_id')
                                        <p class="text-danger" role="alert">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="form-group col-sm-12">
                                    <label>{{ __('lesson') }}</label>
                                    <select name="lesson_id" id="lesson_id" class="form-control" readonly></select>
                                    @error('lesson_id')
                                        <p class="text-danger" role="alert">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            <hr>
                            <input type="hidden" name="action" value="save">
                            <div class="d-flex justify-content-between">
                                <input class="btn btn-theme action_btn" data-value="save" type="submit"
                                    value="{{ __('save') }}">

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
        $('.action_btn').click(function() {
            let type = $(this).data('value');
            if (type != 'save') {
                $('input[name="action"]').val(type);
            }
        });
    

        const classSubjects = @json($subjects);
        const teachers = @json($teachers);
        const lessons = @json($lessons->groupBy('teacher_id')->toArray());

        function setTeachers(classSubjectId) {
            $('#teacher_id').empty();
            $('#teacher_id').removeAttr('readonly');

            const teachersBySubject = teachers.filter(teacher => teacher.subjects.filter(subject => subject.id ==
                classSubjectId));
            if (teachersBySubject && teachersBySubject.length > 0) {
                for (let i = 0; i < teachersBySubject.length; i++) {
                    let item = teachersBySubject[i];

                    $('#teacher_id').append(
                        `<option value="${item.id}">${item.user.first_name + ' ' + item.user.last_name}</option>`
                    );
                }
            }
            setLessons()
        }

        function setSubjects(classId) {
            $('#subject_id').empty();
            $('#subject_id').removeAttr('readonly');

            const subjects = classSubjects.filter(classSubject => classSubject.subject.id == Number(classId));

            if (subjects && subjects.length > 0) {
                for (let i = 0; i < subjects.length; i++) {
                    let item = subjects[i];
                    $('#subject_id').append(
                        `<option data-class_subject-id="${item.id}" value="${item.subject_id}">${item.subject.name}</option>`
                    );
                }
            }
            if ($('#subject_id').val()) {
                setTeachers($('#subject_id option:selected').data('class_subject-id'));
            }
        }

        function setLessons(teacherID, classSectionId) {
            $('#lesson_id').removeAttr('readonly');
            $('#lesson_id').empty();
            const teacherLessons = lessons[Number(teacherID)];
            if (teacherLessons && teacherLessons.length > 0) {
                for (let i = 0; i < teacherLessons.length; i++) {
                    let item = teacherLessons[i];
                    if (itemsclass_section_id == Number(classSectionId)) {
                        $('#lesson_id').append(`<option value="${item.id}">${item.user.name}</option>`);
                    }
                }
            }

        }
        $('#teacher_id').change(function() {
            setLessons($(this).val());
        });
        if ($('#teacher_id').val()) {
            setLessons($('#teacher_id').val());
        }
        $('#class_id').change(function() {
            setSubjects($(this).val());
        });
        if ($('#class_id').val()) {
            setSubjects($('#class_id').val());
        }
        $('#subject_id').change(function() {
            setTeachers($(this).data('class_subject-id'));
        });
        if ($('#subject_id').val()) {
            setTeachers($('#subject_id option:selected').val());
        }
    </script>
@endsection
