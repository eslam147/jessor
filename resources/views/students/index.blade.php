@extends('layouts.master')

@section('title')
    {{ __('students') }}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('manage') . ' ' . __('students') }}
            </h3>
        </div>

        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">
                            {{ __('create') . ' ' . __('students') }}
                        </h4>
                        <form class="pt-3 student-registration-form" id="student-registration-form" enctype="multipart/form-data" action="{{ route('students.store') }}" method="POST" novalidate="novalidate">
                            @csrf
                            <div class="row">
                                <div class="form-group col-sm-12 col-md-4">
                                    <label>{{ __('first_name') }} <span class="text-danger">*</span></label>
                                    {!! Form::text('first_name', null, ['placeholder' => __('first_name'), 'class' => 'form-control']) !!}

                                </div>
                                <div class="form-group col-sm-12 col-md-4">
                                    <label>{{ __('last_name') }} <span class="text-danger">*</span></label>
                                    {!! Form::text('last_name', null, ['placeholder' => __('last_name'), 'class' => 'form-control']) !!}

                                </div>

                                <div class="form-group col-sm-12 col-md-4">
                                    <label>{{ __('gender') }} <span class="text-danger">*</span></label><br>
                                    <div class="d-flex">
                                        <div class="form-check form-check-inline">
                                            <label class="form-check-label">
                                                {!! Form::radio('gender', 'male') !!}
                                                {{ __('male') }}
                                            </label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <label class="form-check-label">
                                                {!! Form::radio('gender', 'female') !!}
                                                {{ __('female') }}
                                            </label>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="row">
                                <div class="form-group col-sm-12 col-md-4">
                                    <label>{{ __('class') . ' ' . __('section') }} <span class="text-danger">*</span></label>
                                    <select name="class_section_id" id="class_section" class="form-control select2">
                                        <option value="">{{ __('select') . ' ' . __('class') . ' ' . __('section') }}</option>
                                        @foreach ($class_section as $section)
                                            <option value="{{ $section->id }}">{{ $section->class->name }} - {{ $section->section->name }} {{ $section->class->medium->name }} {{$section->class->streams->name ?? ' '}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-sm-12 col-md-4">
                                    <label> {{ __('category') }} <span class="text-danger">*</span></label>
                                    <select name="category_id" class="form-control">
                                        <option value="">{{ __('select') . ' ' . __('category') }}</option>
                                        @foreach ($category as $cat)
                                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-sm-12 col-md-4">
                                    <label> student email <span class="text-danger">*</span></label>
                                    <input type="email" name="student_email" class="form-control" required>
                                </div>
                                <div class="form-group col-sm-12 col-md-4">
                                    <label> student password <span class="text-danger">*</span></label>
                                    <input type="password" name="student_password" class="form-control" required>
                                </div>
                            </div>

                            <div class="row">
                                @foreach ($studentFields as $row)
                                    @if($row->type==="text" || $row->type==="number")
                                        <div class="form-group col-sm-12 col-md-4">
                                            <label>{{ ucwords(str_replace('_', ' ', $row->name)) }} {!! ($row->is_required) ? ' <span class="text-danger">*</span></label>': '' !!}</label></label>
                                            <input type="{{$row->type}}" name="{{$row->name}}" placeholder="{{ ucwords(str_replace('_', ' ', $row->name)) }}" class="form-control" {{($row->is_required===1)?"required":''}}>
                                        </div>
                                    @endif
                                    @if($row->type==="dropdown")
                                        <div class="form-group col-sm-12 col-md-4">
                                            <label>{{ ucwords(str_replace('_', ' ', $row->name)) }} {!! ($row->is_required) ? ' <span class="text-danger">*</span></label>': '' !!}</label></label>
                                            <select name="{{ $row->name }}" class="form-control" {{($row->is_required===1)?"required":''}}>
                                                <option value="">Please Select</option>
                                                @foreach(json_decode($row->default_values) as $options)
                                                    @if($options != null)
                                                        <option value="{{$options}}">{{ucfirst(str_replace('_', ' ', $options))}}</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </div>
                                    @endif
                                    @if($row->type==="radio")
                                        <div class="form-group col-sm-12 col-md-4">
                                            <label>{{ ucwords(str_replace('_', ' ', $row->name)) }} {!! ($row->is_required) ? ' <span class="text-danger">*</span></label>': '' !!}</label></label>
                                            <br>
                                            <div class="d-flex">
                                                @foreach(json_decode($row->default_values) as $options)
                                                    @if($options != null)
                                                        <div class="form-check form-check-inline">
                                                            <label class="form-check-label">
                                                                <input type="radio" name="{{$row->name}}" value="{{$options}}" {{($row->is_required===1)?"required":''}}>
                                                                {{ ucfirst($options) }}
                                                            </label>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                    @if($row->type==="checkbox")
                                        <div class="form-group col-sm-12 col-md-4">
                                            <label>{{ ucwords(str_replace('_', ' ', $row->name)) }} {!! ($row->is_required) ? ' <span class="text-danger">*</span></label>': '' !!}</label>
                                            <br>
                                            <div class="col-md-10" id="{{$row->name}}">
                                                @foreach(json_decode($row->default_values) as $options)
                                                    @if($options != null)
                                                        <div class="checkbox form-check form-check-inline"  {{($row->is_required===1)?"required":''}}>
                                                            <label class="form-check-label">
                                                                <input type="checkbox" name="{{ 'checkbox[' . $row->name . '][' . $options . ']' }}" value="{{$options}}"> {{ ucfirst(str_replace('_', ' ', $options)) }}
                                                            </label>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>

                                        </div>
                                    @endif
                                    @if($row->type==="textarea")
                                        <div class="form-group col-sm-12 col-md-4">
                                            <label>{{ ucwords(str_replace('_', ' ', $row->name)) }} {!! ($row->is_required) ? ' <span class="text-danger">*</span></label>': '' !!}</label></label>
                                            <textarea name="{{$row->name}}" cols="10" rows="3" placeholder="{{ ucwords(str_replace('_', ' ', $row->name)) }}" class="form-control" {{($row->is_required===1)?"required":''}}></textarea>
                                        </div>
                                    @endif
                                    @if($row->type==="file")
                                        <div class="form-group col-sm-12 col-md-4">
                                            <label>{{ ucwords(str_replace('_', ' ', $row->name)) }} {!! ($row->is_required) ? ' <span class="text-danger">*</span></label>': '' !!}</label>
                                                <input type="file" name="{{$row->name}}" class="file-upload-default" {{($row->is_required===1)?"required":''}}/>
                                                <div class="input-group col-xs-12">
                                                    <input type="text" class="form-control file-upload-info" disabled="" placeholder="{{ ucwords(str_replace('_', ' ', $row->name)) }}" required />
                                                    <span class="input-group-append">
                                                        <button class="file-upload-browse btn btn-theme" type="button">{{ __('upload') }}</button>
                                                    </span>
                                                </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                            <div class="form-group col-sm-12 col-md-12">
                                <div class="d-flex">
                                    <div class="form-check form-check-inline">
                                        <label class="form-check-label">
                                            <input type="checkbox" name="parent" value="Parent" class="form-check-input parent-check" id="show-parents-details">{{ __('parents_details') }}
                                        </label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <label class="form-check-label">
                                            <input type="checkbox" name="guardian" value="Guardian" class="form-check-input parent-check" id="show-guardian-details">{{ __('guardian_details') }}
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="row" id="parents_div" style="display:none;">
                                <div class="form-group col-sm-12 col-md-12">
                                    <label>{{ __('father_email') }} <span class="text-danger">*</span></label>
                                    <select class="father-search w-100" id="father_email" name="father_email"></select>
                                </div>

                                <div class="form-group col-sm-12 col-md-4">
                                    <label>{{ __('father') . ' ' . __('first_name') }} <span class="text-danger">*</span></label>
                                    {!! Form::text('father_first_name', null, ['placeholder' => __('father') . ' ' . __('first_name'), 'class' => 'form-control', 'id' => 'father_first_name']) !!}
                                </div>
                                <div class="form-group col-sm-12 col-md-4">
                                    <label>{{ __('father') . ' ' . __('last_name') }} <span class="text-danger">*</span></label>
                                    {!! Form::text('father_last_name', null, ['placeholder' => __('father') . ' ' . __('last_name'), 'class' => 'form-control', 'id' => 'father_last_name']) !!}
                                </div>
                                <div class="form-group col-sm-12 col-md-4">
                                    <label>{{ __('father') . ' ' . __('mobile') }} <span class="text-danger">*</span></label>
                                    {!! Form::number('father_mobile', null, ['placeholder' => __('father') . ' ' . __('mobile'), 'class' => 'form-control', 'id' => 'father_mobile', 'min' => 0]) !!}
                                </div>
                                <div class="form-group col-sm-12 col-md-4">
                                    <label> father password <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control" name="father_password" >
                                </div>

                                <div class="col-12">
                                    <div class="row father-extra-div">
                                    @foreach ($parentFields as $row)
                                        @if($row->type==="text" || $row->type==="number")
                                            <div class="form-group col-sm-12 col-md-4">
                                                <label>{{ ucwords(str_replace('_', ' ', $row->name)) }} {!! ($row->is_required) ? ' <span class="text-danger">*</span></label>': '' !!}</label></label>
                                                <input type="{{$row->type}}" name="father_{{$row->name}}" placeholder="{{ ucwords(str_replace('_', ' ', $row->name)) }}" class="form-control" {{($row->is_required===1)?"required":''}}>
                                            </div>
                                        @endif
                                        @if($row->type==="dropdown")
                                            <div class="form-group col-sm-12 col-md-4">
                                                <label>{{ ucwords(str_replace('_', ' ', $row->name)) }} {!! ($row->is_required) ? ' <span class="text-danger">*</span></label>': '' !!}</label></label>
                                                <select name="father_{{ $row->name }}" class="form-control" {{($row->is_required===1)?"required":''}}>
                                                    <option value="">Please Select</option>
                                                    @foreach(json_decode($row->default_values) as $options)
                                                        @if($options != null)
                                                            <option value="{{$options}}">{{ucfirst(str_replace('_', ' ', $options))}}</option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                            </div>
                                        @endif
                                        @if($row->type==="radio")
                                            <div class="form-group col-sm-12 col-md-4">
                                                <label>{{ ucwords(str_replace('_', ' ', $row->name)) }} {!! ($row->is_required) ? ' <span class="text-danger">*</span></label>': '' !!}</label></label>
                                                <br>
                                                <div class="d-flex">
                                                    @foreach(json_decode($row->default_values) as $options)
                                                        @if($options != null)
                                                            <div class="form-check form-check-inline">
                                                                <label class="form-check-label">
                                                                    <input type="radio" name="father_{{$row->name}}" value="{{$options}}" {{($row->is_required===1)?"required":''}}>
                                                                    {{ ucfirst($options) }}
                                                                </label>
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                        @if($row->type==="checkbox")
                                            <div class="form-group col-sm-12 col-md-4">
                                                <label>{{ ucwords(str_replace('_', ' ', $row->name)) }} {!! ($row->is_required) ? ' <span class="text-danger">*</span></label>': '' !!}</label>
                                                <br>
                                                <div class="col-md-10" id="{{$row->name}}">
                                                    @foreach(json_decode($row->default_values) as $options)
                                                        @if($options != null)
                                                            <div class="checkbox form-check form-check-inline"  {{($row->is_required===1)?"required":''}}>
                                                                <label class="form-check-label">
                                                                    <input type="checkbox" name="father_{{ 'checkbox[' . $row->name . '][' . $options . ']' }}" value="{{$options}}"> {{ ucfirst(str_replace('_', ' ', $options)) }}
                                                                </label>
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                </div>

                                            </div>
                                        @endif
                                        @if($row->type==="textarea")
                                            <div class="form-group col-sm-12 col-md-4">
                                                <label>{{ ucwords(str_replace('_', ' ', $row->name)) }} {!! ($row->is_required) ? ' <span class="text-danger">*</span></label>': '' !!}</label></label>
                                                <textarea name="father_{{$row->name}}" cols="10" rows="3" placeholder="{{ ucwords(str_replace('_', ' ', $row->name)) }}" class="form-control" {{($row->is_required===1)?"required":''}}></textarea>
                                            </div>
                                        @endif
                                        @if($row->type==="file")
                                            <div class="form-group col-sm-12 col-md-4">
                                                <label>{{ ucwords(str_replace('_', ' ', $row->name)) }} {!! ($row->is_required) ? ' <span class="text-danger">*</span></label>': '' !!}</label>
                                                    <input type="file" name="father_{{$row->name}}" class="file-upload-default" {{($row->is_required===1)?"required":''}}/>
                                                    <div class="input-group col-xs-12">
                                                        <input type="text" class="form-control file-upload-info" disabled="" placeholder="{{ ucwords(str_replace('_', ' ', $row->name)) }}" required />
                                                        <span class="input-group-append">
                                                            <button class="file-upload-browse btn btn-theme" type="button">{{ __('upload') }}</button>
                                                        </span>
                                                    </div>
                                            </div>
                                        @endif
                                    @endforeach
                                    </div>
                                </div>

                                <div class="form-group col-sm-12 col-md-12">
                                    <label>{{ __('mother_email') }} <span class="text-danger">*</span></label>
                                    <select class="mother-search w-100" id="mother_email" name="mother_email"></select>
                                </div>

                                <div class="form-group col-sm-12 col-md-4">
                                    <label>{{ __('mother') . ' ' . __('first_name') }} <span class="text-danger">*</span></label>
                                    {!! Form::text('mother_first_name', null, ['placeholder' => __('mother') . ' ' . __('first_name'), 'class' => 'form-control', 'id' => 'mother_first_name']) !!}
                                </div>

                                <div class="form-group col-sm-12 col-md-4">
                                    <label>{{ __('mother') . ' ' . __('last_name') }} <span class="text-danger">*</span></label>
                                    {!! Form::text('mother_last_name', null, ['placeholder' => __('mother') . ' ' . __('last_name'), 'class' => 'form-control', 'id' => 'mother_last_name']) !!}
                                </div>
                                <div class="form-group col-sm-12 col-md-4">
                                    <label>{{ __('mother') . ' ' . __('mobile') }} <span class="text-danger">*</span></label>
                                    {!! Form::number('mother_mobile', null, ['placeholder' => __('mother') . ' ' . __('mobile'), 'class' => 'form-control', 'id' => 'mother_mobile', 'min' => 0]) !!}
                                </div>
                                <div class="form-group col-sm-12 col-md-4">
                                    <label> Mother Password <span class="text-danger">*</span></label>
                                    <input type="password" name="mother_password" class="form-control">
                                </div>


                                <div class="col-12">
                                    <div class="row mother-extra-div">
                                        @foreach ($parentFields as $row)
                                            @if($row->type==="text" || $row->type==="number")
                                                <div class="form-group col-sm-12 col-md-4">
                                                    <label>{{ ucwords(str_replace('_', ' ', $row->name)) }} {!! ($row->is_required) ? ' <span class="text-danger">*</span></label>': '' !!}</label></label>
                                                    <input type="{{$row->type}}" name="mother_{{$row->name}}" placeholder="{{ ucwords(str_replace('_', ' ', $row->name)) }}" class="form-control" {{($row->is_required===1)?"required":''}}>
                                                </div>
                                            @endif
                                            @if($row->type==="dropdown")
                                                <div class="form-group col-sm-12 col-md-4">
                                                    <label>{{ ucwords(str_replace('_', ' ', $row->name)) }} {!! ($row->is_required) ? ' <span class="text-danger">*</span></label>': '' !!}</label></label>
                                                    <select name="mother_{{ $row->name }}" class="form-control" {{($row->is_required===1)?"required":''}}>
                                                        <option value="">Please Select</option>
                                                        @foreach(json_decode($row->default_values) as $options)
                                                            @if($options != null)
                                                                <option value="{{$options}}">{{ucfirst(str_replace('_', ' ', $options))}}</option>
                                                            @endif
                                                        @endforeach
                                                    </select>
                                                </div>
                                            @endif
                                            @if($row->type==="radio")
                                                <div class="form-group col-sm-12 col-md-4">
                                                    <label>{{ ucwords(str_replace('_', ' ', $row->name)) }} {!! ($row->is_required) ? ' <span class="text-danger">*</span></label>': '' !!}</label></label>
                                                    <br>
                                                    <div class="d-flex">
                                                        @foreach(json_decode($row->default_values) as $options)
                                                            @if($options != null)
                                                                <div class="form-check form-check-inline">
                                                                    <label class="form-check-label">
                                                                        <input type="radio" name="mother_{{$row->name}}" value="{{$options}}" {{($row->is_required===1)?"required":''}}>
                                                                        {{ ucfirst($options) }}
                                                                    </label>
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif
                                            @if($row->type==="checkbox")
                                                <div class="form-group col-sm-12 col-md-4">
                                                    <label>{{ ucwords(str_replace('_', ' ', $row->name)) }} {!! ($row->is_required) ? ' <span class="text-danger">*</span></label>': '' !!}</label>
                                                    <br>
                                                    <div class="col-md-10" id="{{$row->name}}">
                                                        @foreach(json_decode($row->default_values) as $options)
                                                            @if($options != null)
                                                                <div class="checkbox form-check form-check-inline"  {{($row->is_required===1)?"required":''}}>
                                                                    <label class="form-check-label">
                                                                        <input type="checkbox" name="mother_{{ 'checkbox[' . $row->name . '][' . $options . ']' }}" value="{{$options}}"> {{ ucfirst(str_replace('_', ' ', $options)) }}
                                                                    </label>
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                    </div>

                                                </div>
                                            @endif
                                            @if($row->type==="textarea")
                                                <div class="form-group col-sm-12 col-md-4">
                                                    <label>{{ ucwords(str_replace('_', ' ', $row->name)) }} {!! ($row->is_required) ? ' <span class="text-danger">*</span></label>': '' !!}</label></label>
                                                    <textarea name="mother_{{$row->name}}" cols="10" rows="3" placeholder="{{ ucwords(str_replace('_', ' ', $row->name)) }}" class="form-control" {{($row->is_required===1)?"required":''}}></textarea>
                                                </div>
                                            @endif
                                            @if($row->type==="file")
                                                <div class="form-group col-sm-12 col-md-4">
                                                    <label>{{ ucwords(str_replace('_', ' ', $row->name)) }} {!! ($row->is_required) ? ' <span class="text-danger">*</span></label>': '' !!}</label>
                                                        <input type="file" name="mother_{{$row->name}}" class="file-upload-default" {{($row->is_required===1)?"required":''}}/>
                                                        <div class="input-group col-xs-12">
                                                            <input type="text" class="form-control file-upload-info" disabled="" placeholder="{{ ucwords(str_replace('_', ' ', $row->name)) }}" required />
                                                            <span class="input-group-append">
                                                                <button class="file-upload-browse btn btn-theme" type="button">{{ __('upload') }}</button>
                                                            </span>
                                                        </div>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>

                            </div>

                            <div class="row" id="guardian_div" style="display:none;">
                                <div class="form-group col-sm-12 col-md-12">
                                    <label>{{ __('guardian') . ' ' . __('email') }} <span class="text-danger">*</span></label>
                                    <select class="guardian-search form-control" id="guardian_email" name="guardian_email"></select>

                                </div>

                                <div class="form-group col-sm-12 col-md-4">
                                    <label>{{ __('guardian') . ' ' . __('first_name') }} <span class="text-danger">*</span></label>
                                    {!! Form::text('guardian_first_name', null, ['placeholder' => __('guardian') . ' ' . __('first_name'), 'class' => 'form-control', 'id' => 'guardian_first_name']) !!}
                                </div>

                                <div class="form-group col-sm-12 col-md-4">
                                    <label>{{ __('guardian') . ' ' . __('last_name') }} <span class="text-danger">*</span></label>
                                    {!! Form::text('guardian_last_name', null, ['placeholder' => __('guardian') . ' ' . __('last_name'), 'class' => 'form-control', 'id' => 'guardian_last_name']) !!}
                                </div>
                                <div class="form-group col-sm-12 col-md-4">
                                    <label>{{ __('guardian') . ' ' . __('mobile') }} <span class="text-danger">*</span></label>
                                    {!! Form::number('guardian_mobile', null, ['placeholder' => __('guardian') . ' ' . __('mobile'), 'class' => 'form-control', 'id' => 'guardian_mobile' , 'min' => 0]) !!}
                                </div>
                                <div class="form-group col-sm-12 col-md-4">
                                    <label> Guardian Password <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control" name="guardian_password" >
                                </div>
                                <div class="form-group col-sm-12 col-md-12">
                                    <label>{{ __('gender') }} <span class="text-danger">*</span></label><br>
                                    <div class="d-flex">
                                        <div class="form-check form-check-inline">
                                            <label class="form-check-label">
                                                <input type="radio" name="guardian_gender" value="male" id="guardian_male">
                                                {{ __('male') }}
                                            </label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <label class="form-check-label">
                                                <input type="radio" name="guardian_gender" value="female" id="guardian_female">
                                                {{ __('female') }}
                                            </label>
                                        </div>
                                    </div>
                                </div>


                                <div class="col-12">
                                    <div class="row guardian-extra-div">
                                        @foreach ($parentFields as $row)
                                            @if($row->type==="text" || $row->type==="number")
                                                <div class="form-group col-sm-12 col-md-4">
                                                    <label>{{ ucwords(str_replace('_', ' ', $row->name)) }} {!! ($row->is_required) ? ' <span class="text-danger">*</span></label>': '' !!}</label></label>
                                                    <input type="{{$row->type}}" name="guardian_{{$row->name}}" id="guardian_{{$row->name}}" placeholder="{{ ucwords(str_replace('_', ' ', $row->name)) }}" class="form-control" {{($row->is_required===1)?"required":''}}>
                                                </div>
                                            @endif
                                            @if($row->type==="dropdown")
                                                <div class="form-group col-sm-12 col-md-4">
                                                    <label>{{ ucwords(str_replace('_', ' ', $row->name)) }} {!! ($row->is_required) ? ' <span class="text-danger">*</span></label>': '' !!}</label></label>
                                                    <select name="guardian_{{ $row->name }}" id="guardian_{{$row->name}}" class="form-control" {{($row->is_required===1)?"required":''}}>
                                                        <option value="">Please Select</option>
                                                        @foreach(json_decode($row->default_values) as $options)
                                                            @if($options != null)
                                                                <option value="{{$options}}">{{ucfirst(str_replace('_', ' ', $options))}}</option>
                                                            @endif
                                                        @endforeach
                                                    </select>
                                                </div>
                                            @endif
                                            @if($row->type==="radio")
                                                <div class="form-group col-sm-12 col-md-4">
                                                    <label>{{ ucwords(str_replace('_', ' ', $row->name)) }} {!! ($row->is_required) ? ' <span class="text-danger">*</span></label>': '' !!}</label></label>
                                                    <br>
                                                    <div class="d-flex">
                                                        @foreach(json_decode($row->default_values) as $options)
                                                            @if($options != null)
                                                                <div class="form-check form-check-inline">
                                                                    <label class="form-check-label">
                                                                        <input type="radio" name="guardian_{{$row->name}}" id="guardian_{{$row->name}}" value="{{$options}}" {{($row->is_required===1)?"required":''}}>
                                                                        {{ ucfirst($options) }}
                                                                    </label>
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif
                                            @if($row->type==="checkbox")
                                                <div class="form-group col-sm-12 col-md-4">
                                                    <label>{{ ucwords(str_replace('_', ' ', $row->name)) }} {!! ($row->is_required) ? ' <span class="text-danger">*</span></label>': '' !!}</label>
                                                    <br>
                                                    <div class="col-md-10" id="{{$row->name}}">
                                                        @foreach(json_decode($row->default_values) as $options)
                                                            @if($options != null)
                                                                <div class="checkbox form-check form-check-inline"  {{($row->is_required===1)?"required":''}}>
                                                                    <label class="form-check-label">
                                                                        <input type="checkbox" id="guardian_{{$row->name}}" name="guardian_{{ 'checkbox[' . $row->name . '][' . $options . ']' }}" value="{{$options}}"> {{ ucfirst(str_replace('_', ' ', $options)) }}
                                                                    </label>
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                    </div>

                                                </div>
                                            @endif
                                            @if($row->type==="textarea")
                                                <div class="form-group col-sm-12 col-md-4">
                                                    <label>{{ ucwords(str_replace('_', ' ', $row->name)) }} {!! ($row->is_required) ? ' <span class="text-danger">*</span></label>': '' !!}</label></label>
                                                    <textarea name="guardian_{{$row->name}}" id="guardian_{{$row->name}}" cols="10" rows="3" placeholder="{{ ucwords(str_replace('_', ' ', $row->name)) }}" class="form-control" {{($row->is_required===1)?"required":''}}></textarea>
                                                </div>
                                            @endif
                                            @if($row->type==="file")
                                                <div class="form-group col-sm-12 col-md-4">
                                                    <label>{{ ucwords(str_replace('_', ' ', $row->name)) }} {!! ($row->is_required) ? ' <span class="text-danger">*</span></label>': '' !!}</label>
                                                        <input type="file" name="guardian_{{$row->name}}" class="file-upload-default" {{($row->is_required===1)?"required":''}}/>
                                                        <div class="input-group col-xs-12">
                                                            <input type="text" class="form-control file-upload-info" disabled="" placeholder="{{ ucwords(str_replace('_', ' ', $row->name)) }}" required />
                                                            <span class="input-group-append">
                                                                <button class="file-upload-browse btn btn-theme" type="button">{{ __('upload') }}</button>
                                                            </span>
                                                        </div>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>

                            </div>

                            <input class="btn btn-theme" type="submit" value={{ __('submit') }}>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection