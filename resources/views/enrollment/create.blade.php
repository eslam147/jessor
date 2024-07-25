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
                                    <label>{{ __('price') }}<span class="text-danger">*</span></label>
                                    {!! Form::number('price', null, [
                                        'required',
                                        'min' => 1,
                                        'step' => '0.01',
                                        'placeholder' => __('price'),
                                        'class' => 'form-control',
                                    ]) !!}
                                    @error('price')
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
                                    <label>{{ __('teacher') }}</label>
                                    {!! Form::select('teacher_id', $teachers, old('teacher_id'), [
                                        'required',
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
                                    <select name="lesson_id" id="lesson_id" class="form-control" required></select>
                                    @error('lesson_id')
                                        <p class="text-danger" role="alert">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <input class="btn btn-theme" type="submit" value="{{ __('save') }}">
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
            if(teacherLessons&& teacherLessons.length > 0){
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
