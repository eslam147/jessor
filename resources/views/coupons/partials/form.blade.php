@section('css')
    <link href="https://cdn.jsdelivr.net/npm/@yaireo/tagify/dist/tagify.css" rel="stylesheet" type="text/css" />

    <style>
        label.error {
            color: var(--danger);
            margin: 10px 0;
        }
    </style>
@endsection
<div class="row justify-content-center align-items-center">
    <div class="form-group col-sm-12">
        <label>{{ __('coupon_type') }}<span class="text-danger">*</span></label>
        {!! Form::select(
            'coupon_type',
            [
                'purchase' => __('purchase'),
                'wallet' => __('wallet'),
            ],
            isset($coupon) ? $coupon['coupon_type'] : '',
            ['required', 'placeholder' => __('coupon_type'), 'class' => 'form-control coupon_type'],
        ) !!}
        @error('coupon_type')
            <p class="text-danger" role="alert">{{ $message }}</p>
        @enderror
    </div>

    <div class="details d-none col-12">
        <div class="row">
            @unless (isset($coupon))
                <div class="form-group col-sm-12">
                    <label>{{ __('coupons_count') }}<span class="text-danger">*</span></label>
                    {!! Form::number('coupons_count', null, [
                        'required',
                        'min' => 1,
                        'max' => 1000,
                        'step' => '1',
                        'placeholder' => __('coupons_count'),
                        'class' => 'form-control',
                    ]) !!}
                    @error('coupons_count')
                        <p class="text-danger" role="alert">{{ $message }}</p>
                    @enderror
                </div>
            @endunless

            <div class="form-group col-sm-12 coupon_purchase_type">
                <label>{{ __('usage_limit') }}<span class="text-danger">*</span></label>
                {!! Form::number('usage_limit', isset($coupon) ? $coupon->maximum_usage : '', [
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

            <div class="form-group col-sm-12 coupon_purchase_type with_price">

                <div class="form-check form-check-inline">
                    <label class="form-check-label">
                        <input type="checkbox" value="true" {{ !empty($coupon->price) ? 'checked' : '' }}
                            class="form-check-input coupon_has_price" id="coupon_has_price">With Price
                        <i class="input-helper"></i><i class="input-helper"></i></label>
                </div>
            </div>

            <div
                class="form-group col-sm-12 coupon_wallet_type coupon_purchase_type coupon_price {{ !empty($coupon->price) ? '' : 'd-none' }}">
                <label>{{ __('price') }}<span class="text-danger">*</span></label>

                {!! Form::number('price', isset($coupon) ? $coupon->price : '', [
                    'required',
                    'min' => 0,
                    'step' => '0.01',
                    'placeholder' => __('price'),
                    'disabled',
                    'class' => 'form-control  ',
                ]) !!}
                @error('price')
                    <p class="text-danger" role="alert">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group col-sm-12 ">
                <label>{{ __('expiry_date') }}<span class="text-danger">*</span></label>
                {!! Form::date('expiry_date', isset($coupon) ? $coupon->expiry_date : '', [
                    'required',
                    'placeholder' => __('expiry_date'),
                    'class' => 'form-control',
                ]) !!}
                @error('expiry_date')
                    <p class="text-danger" role="alert">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group col-sm-12">
                <label>{{ __('tags') }} <small class="text-info"> (Maximum : 5)</small></label>
                {!! Form::text('tags', old('tags', isset($coupon) ? $coupon->tags->pluck('name')->implode(',') : ''), [
                    'placeholder' => __('tags'),
                    'class' => 'form-control',
                    'id' => 'coupon_tags',
                ]) !!}
                @error('tags')
                    <p class="text-danger" role="alert">{{ $message }}</p>
                @enderror
            </div>


            <div class="row coupon_purchase_type  d-none">
                <div class="form-group col-sm-12 ">
                    <label>{{ __('medium') }}</label>

                    <select name="medium_id" required id="medium_id" class="form-control">
                        <option selected disabled readonly>{{ __('select_medium') }}</option>
                        @foreach ($mediums as $medium)
                            <option value="{{ $medium->id }}" @selected(old('medium_id', isset($coupon) ? $coupon->classModel->medium_id : '') == $medium->id)>
                                {{ $medium->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('medium_id')
                        <p class="text-danger" role="alert">{{ $message }}</p>
                    @enderror
                </div>
                <div class="row col-12 mb-2  d-none">
                    <div class="col-sm-6">
                        <label>{{ __('class') }}</label>
                        <select name="class_id" required id="class_m_id" readonly class="form-control"></select>
                        @error('class_id')
                            <p class="text-danger" role="alert">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="col-sm-6">
                        <label>{{ __('subject') }}</label>
                        {!! Form::select('subject_id', $subjects->pluck('subject.name', 'id'), old('subject_id'), [
                            'placeholder' => __('all_subjects'),
                            'class' => 'form-control',
                            'id' => 'subject_id',
                        ]) !!}
                        @error('subject_id')
                            <p class="text-danger" role="alert">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="row col-12 mb-2 coupon_purchase_type d-none">
                <div class="col-sm-6">
                    <label>{{ __('teacher') }}</label>
                    {!! Form::select('teacher_id', $teachers->pluck('user.full_name', 'id'), old('teacher_id'), [
                        'placeholder' => __('all_teachers'),
                        'class' => 'form-control',
                        'id' => 'teacher_id',
                    ]) !!}
                    @error('teacher_id')
                        <p class="text-danger" role="alert">{{ $message }}</p>
                    @enderror
                </div>

                <div class="col-sm-6">
                    <label>{{ __('lesson') }}</label>
                    <select name="lesson_id" readonly id="lesson_id" class="form-control">
                        <option selected disabled readonly>{{ __('all_lessons') }}</option>
                        @foreach ($lessons as $lesson)
                            <option value="{{ $lesson->id }}">{{ $lesson->name }}</option>
                        @endforeach
                    </select>
                    @error('lesson_id')
                        <p class="text-danger" role="alert">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>
    </div>
</div>
@section('script')
    <script src="https://cdn.jsdelivr.net/npm/@yaireo/tagify"></script>
    <script>
        $('.coupon_type').change(function() {
            $('.details').removeClass('d-none');
            if ($(this).val().length > 0) {
                $('.action_btn').removeAttr('disabled');
                if ($(this).val() == 'wallet') {

                    $('.coupon_purchase_type').addClass('d-none');
                    $('.coupon_wallet_type').removeClass('d-none');

                    $('.coupon_price').removeClass('d-none');
                    $('.coupon_price input').removeAttr('disabled');

                } else if ($(this).val() == 'purchase') {
                    $('.coupon_price,.coupon_wallet_type').addClass('d-none');
                    $('.coupon_purchase_type').removeClass('d-none');
                    // $('.coupon_price').removeClass('d-none');
                    $('.coupon_price input').attr('disabled');

                } else {
                    $('.coupon_purchase_type').addClass('d-none');
                    $('.coupon_wallet_type').addClass('d-none');
                    $('.coupon_price input').attr('disabled');

                }
            }
        })
        const couponTagsInput = document.querySelector('#coupon_tags');
        var tagify = new Tagify(couponTagsInput, {
            originalInputValueFormat: valuesArr => valuesArr.map(item => item.value).join(','),
            maxTags: 5,
            duplicates: false,
            validate: tagValue => {
                const length = tagValue.value.length;
                return length >= 3 && length <= 255 ? true : 'Tag length must be between 3 and 255 characters';
            },
        });

        $('.action_btn').click(function(e) {
            let type = $(this).data('value');
            if (type != 'save') {
                $('input[name="action"]').val(type);
            }
        });
        $('.coupon_has_price').change(function(e) {
            if ($(this).is(':checked')) {
                $('.coupon_price').removeClass('d-none');
                $('.coupon_price input').removeAttr('disabled');
            } else {
                $('.coupon_price input').attr('disabled', true);
                $('.coupon_price').addClass('d-none');
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
                let type = $(`input[name="action"]`).val();

                var a = document.createElement('a');
                if (type == 'save_and_print') {
                    a.href = response.data.file_url;
                    a.download = response.data.file_name;
                    document.body.appendChild(a);
                    a.click();
                    a.remove();
                }
                formElement[0].reset();
                $('.coupon_purchase_type').addClass('d-none');
                $('.coupon_wallet_type').addClass('d-none');
                $('.coupon_price input').attr('disabled');

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

        const classes = @json($mediums->pluck('classes', 'id')->toArray());
        const classSubjects = @json($subjects);
        const teachers = @json($teachers);
        const lessons = @json($lessons->groupBy('teacher_id')->toArray());

        function setTeachers(classSubjectId) {
            $('#teacher_id').empty();
            $('#teacher_id').removeAttr('readonly');

            const teachersBySubject = teachers.filter(teacher => teacher.subjects.filter(subject => subject.id ==
                classSubjectId));
            $('#teacher_id').append(`<option value>{{ __('all_teachers') }}</option>`);

            if (teachersBySubject && teachersBySubject.length > 0) {
                for (let i = 0; i < teachersBySubject.length; i++) {
                    let item = teachersBySubject[i];

                    $('#teacher_id').append(
                        `<option value="${item.id}">${item.user.first_name + ' ' + item.user.last_name}</option>`
                    );
                }
            }
            $('#teacher_id').trigger('change');
            // setLessons(teacherID, classSectionId)
        }

        function setSubjects(classId) {
            const $this = $('#subject_id');
            $this.empty();
            $this.removeAttr('readonly');

            const subjects = classSubjects.filter(classSubject => classSubject.class_id == Number(classId));

            if (subjects && subjects.length > 0) {
                $('#subject_id').append(`<option value>{{ __('all_subjects') }}</option>`);

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
            $this.trigger('change');

        }

        function setLessons(teacherID, classId, subjectId) {
            const $this = $('#lesson_id');
            $this.removeAttr('readonly');
            $this.empty();
            const teacherLessons = lessons[Number(teacherID)];
            $('#lesson_id').append(`<option value>{{ __('all_lessons') }}</option>`);

            if (teacherLessons && teacherLessons.length > 0) {
                for (let i = 0; i < teacherLessons.length; i++) {
                    let item = teacherLessons[i];
                    if (item.class_id == Number(classId) && item.subject_id == Number(subjectId)) {
                        $('#lesson_id').append(`<option value="${item.id}">${item.name}</option>`);
                    }
                }
            }
        }

        function setClasses(mediumId) {
            const $this = $('#class_m_id');
            $this.removeAttr('readonly');
            $this.empty();
            const classSections = classes[Number(mediumId)];
            // $('#class_m_id').append(`<option>All</option>`);
            if (classSections && classSections.length > 0) {
                for (let i = 0; i < classSections.length; i++) {
                    let item = classSections[i];
                    $('#class_m_id').append(`<option value="${item.id}">${item.name}</option>`);
                }
            }
            $this.trigger('change');
        }
        $('#medium_id').change(function() {
            setClasses($(this).val());
        });
        setClasses($('#medium_id').val())
        $('#teacher_id').change(function() {
            setLessons($(this).val(), $('#class_m_id').val(), $('#subject_id').val());
        });

        $('#class_m_id').change(function() {
            setSubjects($(this).val());
        });

        $('#subject_id').change(function() {
            setTeachers($(this).data('class_subject-id'));
        });
    </script>
    @isset($coupon)
        <script>
            let coupon = @json($coupon);

            function setCouponData() {
                if (medium = $('#medium_id').val()) {
                    setClasses(medium);
                }
                if (coupon.class_id) {
                    setSubjects(coupon.class_id);
                    $('#subject_id').val(coupon.subject_id);
                }
                if (coupon.subject_id) {
                    setTeachers(coupon.subject_id);
                    $('#teacher_id').val(coupon.teacher_id);

                }
                if (coupon.teacher_id) {
                    setLessons(coupon.teacher_id, coupon.class_id, coupon.subject_id);
                    $('#lesson_id').val(coupon.lesson_id);
                }
            }
            setCouponData()
        </script>
    @endisset
@endsection
