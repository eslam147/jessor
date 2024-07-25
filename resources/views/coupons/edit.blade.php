@extends('layouts.master')

@section('title', __('edit_coupon'))

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('edit_coupon') }}
            </h3>
        </div>

        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">
                            {{ __('edit_coupon') }}
                        </h4>

                        <form id="edit-form" class="pt-3 coupon-edit-form" action="{{ route('coupons.update', '') }}"
                            method="POST" novalidate="novalidate" data-edit_id="{{ $coupon->id }}">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="edit_id" class="edit_id" data-edit_id="{{ $coupon->id }}"
                                value="{{ $coupon->id }}">
                            <div class="row">

                                <div class="form-group col-sm-12">
                                    <label>{{ __('usage_limit') }}<span class="text-danger">*</span></label>
                                    {!! Form::number('usage_limit', $coupon->maximum_usage, [
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
                                    <label>{{ __('code') }}<span class="text-danger">*</span></label>
                                    {!! Form::number('code', $coupon->code, [
                                        'required',
                                        'min' => 1,
                                        'step' => '1',
                                        'placeholder' => __('code'),
                                        'class' => 'form-control',
                                    ]) !!}
                                    @error('code')
                                        <p class="text-danger" role="alert">{{ $message }}</p>
                                    @enderror
                                </div>


                                <div class="form-group col-sm-12">
                                    <label>{{ __('expiry_date') }}<span class="text-danger">*</span></label>
                                    {!! Form::date('expiry_date', $coupon->expiry_date->toDateString(), [
                                        'required',
                                        'placeholder' => __('expiry_date'),
                                        'class' => 'form-control',
                                    ]) !!}
                                    @error('expiry_date')
                                        <p class="text-danger" role="alert">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="form-group col-sm-12">
                                    <label>{{ __('expiry_time') }}<span class="text-danger">*</span></label>
                                    {!! Form::time('expiry_time', $coupon->expiry_date->format('H:i'), [
                                        'required',
                                        'placeholder' => __('expiry_time'),
                                        'class' => 'form-control',
                                    ]) !!}
                                    @error('expiry_time')
                                        <p class="text-danger" role="alert">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="form-group col-sm-12">
                                    <label>{{ __('teacher') }}</label>
                                    {!! Form::select('teacher_id', $teachers, $coupon->teacher_id, [
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
                                    <label for="lesson_id">{{ __('lesson') }}</label>

                                    <select name="lesson_id" id="lesson_id" class="form-control" required></select>
                                    @error('lesson_id')
                                        <p class="text-danger" role="alert">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            <hr>
                            <div class="text-center m-auto">
                                <input class="btn btn-theme" type="submit" value="{{ __('save') }}">
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
        console.log(lessons);

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
