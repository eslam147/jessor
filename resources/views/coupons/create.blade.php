@extends('layouts.master')

@section('title', __('create_coupon'))

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
                                    value="{{ __('save_and_export') }}">

                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection

