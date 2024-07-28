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
                        <form id="coupon-create-form" class="pt-3 coupon-create-form" action="{{ route('coupons.store') }}"
                            method="POST" novalidate="novalidate">
                            @csrf
                            @include('coupons.partials.form')
                            <hr>
                            <input type="hidden" name="action" value="save">
                            <div class="d-flex justify-content-between">
                                <input class="btn btn-theme action_btn" data-value="save" type="submit"
                                    value="{{ __('save') }}">
                                <input class="btn btn-success action_btn" data-value="save_and_print" type="submit"
                                    value="{{ __('save_and_print') }}">

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
        $('.action_btn').click(function(e) {
            let type = $(this).data('value');
            if (type != 'save') {
                $('input[name="action"]').val(type);
            }
        });
        $('.coupon-create-form').on('submit', function(e) {
            e.preventDefault();
            e.stopPropagation();
            let formElement = $(this);

            let submitButtonElement = $(this).find(':submit');
            let url = $(this).attr('action');
            let data = new FormData(this);

            function successCallback(response) {
                var a = document.createElement('a');

                a.href = response.data.file_url;
                a.download = response.data.file_name;
                document.body.appendChild(a);
                a.click();
                a.remove();

                formElement[0].reset();
            }
            // To Remove Red Border from the Validation tag.
            formElement.find('.has-danger').removeClass("has-danger");
            formElement.validate();
            if (formElement.valid()) {
                let submitButtonText = submitButtonElement.val();

                function beforeSendCallback() {
                    submitButtonElement.attr('disabled', true);
                }

                function mainSuccessCallback(response) {
                    showSuccessToast(response.message);
                    if (successCallback != null) {
                        successCallback(response);
                    }
                }

                function mainErrorCallback(response) {
                    showErrorToast(response.message);
                    if (errorCallback != null) {
                        errorCallback(response);
                    }
                }

                function finalCallback(response) {
                    submitButtonElement.attr('disabled', false);
                }

                ajaxRequest("POST", url, data, beforeSendCallback, mainSuccessCallback, mainErrorCallback,
                    finalCallback, false)
            }
        })

        const classes = @json($mediums->pluck('classes','id')->toArray());
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

            const subjects = classSubjects.filter(classSubject => classSubject.class_id == Number(classId));

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
        function setClasses(mediumId) {
            $('#class_m_id').removeAttr('readonly');
            $('#class_m_id').empty();
            const classSections = classes[Number(mediumId)];
            console.log(classSections);
            if (classSections && classSections.length > 0) {
                for (let i = 0; i < classSections.length; i++) {
                    let item = classSections[i];
                    $('#class_m_id').append(`<option value="${item.id}">${item.name}</option>`);
                }
            }

        }
        $('#medium_id').change(function() {
            setClasses($(this).val());
        });
        if (medium = $('#medium_id').val()) {
            setClasses(medium);
        }
        $('#teacher_id').change(function() {
            setLessons($(this).val());
        });
        if ($('#teacher_id').val()) {
            setLessons($('#teacher_id').val());
        }
        $('#class_m_id').change(function() {
            setSubjects($(this).val());
        });
        if ($('#class_m_id').val()) {
            setSubjects($('#class_m_id').val());
        }
        $('#subject_id').change(function() {
            setTeachers($(this).data('class_subject-id'));
        });
        if ($('#subject_id').val()) {
            setTeachers($('#subject_id option:selected').val());
        }
    </script>
@endsection
