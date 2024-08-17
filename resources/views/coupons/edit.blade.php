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
                            @include('coupons.partials.form', ['coupon' => $coupon])
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
