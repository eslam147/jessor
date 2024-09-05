@extends('layouts.master')

@section('title')
    {{ __('show') . ' ' . __('as') . ' ' . __('student') }}
@endsection
@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="w-100" style="height:90vh;">
                            <iframe class="w-100 h-100" src="{{ route('online-exam.show-as-student.embeded', $id) }}"
                                frameborder="0"></iframe>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
