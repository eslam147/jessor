@extends('layouts.master')

@section('title')
    {{ __('section') }}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title mb-0">
                {{ __('meeting.title') }}
            </h3>
        </div>
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card search-container">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <h3>
                                {{ trans('meeting.services.' . $service['name']) }}
                            </h3>
                            <div class="actions">
                                <div class="alert alert-info" role="alert">
                                    <i class="fa fa-info-circle"></i>
                                    How To Get Credentials
                                    <!-- Button trigger modal -->
                                    <button type="button" class="btn btn-sm " data-toggle="modal" data-target="#tutoModel">
                                        Show
                                    </button>

                                </div>
                            </div>
                        </div>
                        <hr>
                        <form class="pt-3 edit-content" method="POST" id="edit-content-about" autocomplete="off"
                            action="{{ route('meeting.provider.settings.update', $service['name']) }}"
                            novalidate="novalidate">
                            @csrf
                            <div class="row">
                                <div class="form-group col-sm-4">
                                    <label>{{ trans('meeting.is_enabled') }} </label>
                                    <div class="form-group">
                                        <div class="input-group">
                                            <input type="checkbox" name="is_enabled" @checked($serviceSettings['is_enabled'] ?? false)>
                                        </div>
                                    </div>
                                </div>
                                @foreach ($service['fields'] as $field => $values)
                                    <div class="form-group col-sm-6 col-md-6">
                                        <label>{{ trans("meeting.fields.{$field}") }}
                                            @if ($values['required'])
                                                <span class="text-danger">*</span>
                                            @endif
                                        </label>
                                        @switch($values['type'])
                                            @case('text')
                                                <div class="form-group {{ $values['secret'] ? 'secret' : '' }}">
                                                    <div class="input-group">
                                                        <input autocomplete="off" autocorrect="off" autofocus="" role="combobox"
                                                            spellcheck="false" type="{{ $values['secret'] ? 'password' : 'text' }}"
                                                            value="{{ $serviceSettings[$field] }}" autocomplete="false"
                                                            class="form-control " name="{{ $field }}"
                                                            id="{{ $field }}" @required($values['required'])
                                                            placeholder="{{ trans("meeting.fields.{$field}") }}">
                                                        @if ($values['secret'])
                                                            <div class="input-group-append">
                                                                <span class="input-group-text">
                                                                    <i class="fa fa-eye-slash secret_icon"></i>
                                                                </span>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            @break

                                            @case('textarea')
                                                {!! Form::textarea($field, $serviceSettings[$field], [
                                                    'required',
                                                    'placeholder' => trans("meeting.fields.{$field}"),
                                                    'class' => 'form-control',
                                                    'id' => $field,
                                                    'autocomplete' => 'off',
                                                ]) !!}
                                            @break

                                            @default
                                        @endswitch

                                    </div>
                                @endforeach
                            </div>
                            <hr>
                            <div class="text-center">
                                <button class="btn btn-theme" type="submit">{{ __('save') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="tutoModel" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Show Tutorial</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    {!! $tutorial !!}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        $(".secret").each(function() {
            let secret = $(this);
            let input = secret.find("input");
            let secretBtn = secret.find(".secret_icon");
            secretBtn.click(function() {
                const type = input.attr("type") === "password" ? "text" : "password";
                input.attr("type", type);
                if (input.attr("type") === 'password') {
                    secret.find('.secret_icon').toggleClass('fa-eye', 'fa-eye-slash');
                }
            })
        });
    </script>
@endsection
