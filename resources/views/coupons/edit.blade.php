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
                            <input type="hidden" name="edit_id" class="edit_id" data-edit_id="{{ $coupon->id }}" value="{{ $coupon->id }}">
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
                                    <label>{{ __('price') }}<span class="text-danger">*</span></label>
                                    {!! Form::number('price', $coupon->price, [
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
                                    ]) !!}
                                    @error('teacher_id')
                                        <p class="text-danger" role="alert">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="form-group col-sm-12">
                                    <label for="topic_id">{{ __('topic') }}</label>
                                    <div class="form-group">
                                        <select required class="form-control" name="topic_id" id="topic_id">
                                            <option readonly disabled>{{ __('select_topic') }}</option>
                                            @foreach ($topics as $topic)
                                                <option value="{{ $topic->id }}" @selected($coupon->onlyAppliedTo->is($topic))>
                                                    {{ $topic->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @error('topic_id')
                                        <p class="text-danger" role="alert">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            <input type="submit" class="btn btn-theme" value="{{ __('save') }}">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection