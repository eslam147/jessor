@extends('layouts.master')

@section('title')
    {{ __('manage') . ' ' . __('lesson') }}
@endsection
@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/css/dropify.min.css">
@endsection
@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('manage') . ' ' . __('lesson') }}
            </h3>
        </div>
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card search-container">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">
                            {{ __('create') . ' ' . __('lesson') }}
                        </h4>
                        <form class="pt-3 add-lesson-form" id="create-form" action="{{ route('lesson.store') }}"
                            method="POST" novalidate="novalidate" enctype="multipart/form-data">
                            <div class="row">
                                <div class="form-group col-sm-12 col-md-6">
                                    <label>{{ __('class') . ' ' . __('section') }} <span
                                            class="text-danger">*</span></label>
                                    <select name="class_section_id" id="class_section_id"
                                        class="class_section_id form-control">
                                        <option value="">--{{ __('select') }}--</option>
                                        @foreach ($class_section as $section)
                                            <option value="{{ $section->id }}" data-class="{{ $section->class->id }}">
                                                {{ $section->class->name . ' ' . $section->section->name . ' - ' . $section->class->medium->name }}
                                                {{ $section->class->streams->name ?? '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group col-sm-12 col-md-6">
                                    <label>{{ __('subject') }} <span class="text-danger">*</span></label>
                                    <select name="subject_id" id="subject_id" class="subject_id form-control">
                                        <option value="">--{{ __('select') }}--</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-sm-12 col-md-6">
                                    <label>{{ __('lesson_name') }} <span class="text-danger">*</span></label>
                                    <input type="text" id="name" name="name"
                                        placeholder="{{ __('lesson_name') }}" class="form-control" />
                                </div>

                                <div class="form-group col-sm-12 col-md-6">
                                    <label>{{ __('lesson_description') }} <span class="text-danger">*</span></label>
                                    <textarea id="description" name="description" placeholder="{{ __('lesson_description') }}" class="form-control"></textarea>
                                </div>
                                <div class="form-group col-sm-12 col-md-6">
                                    <label>{{ __('paid_or_free') }} <span class="text-danger">*</span></label>
                                    <div class="d-flex">
                                        <div class="form-check form-check-inline">
                                            <label class="form-check-label">
                                                <input name="payment_status" class="payment_status" type="radio" value="0">
                                                Free
                                                <i class="input-helper"></i></label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <label class="form-check-label">
                                                <input name="payment_status" class="payment_status" type="radio" value="1">
                                                Paid
                                                <i class="input-helper"></i></label>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group col-sm-12 col-md-6 price_row">
                                    <label>{{ __('price') }} <span class="text-danger">*</span></label>
                                    <input type="number" min="1" step="0.01" id="price" disabled name="price"
                                        placeholder="{{ __('price') }}" class="form-control" />
                                </div>

                                <div class="form-group col-sm-12 col-md-6">
                                    <label>{{ __('status') }} <span class="text-danger">*</span></label>
                                    <div class="d-flex">
                                        <div class="form-check form-check-inline">
                                            <label class="form-check-label">
                                                <input name="status" type="radio" value="draft">
                                                Draft
                                                <i class="input-helper"></i></label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <label class="form-check-label">
                                                <input name="status" type="radio" value="published">
                                                Published
                                                <i class="input-helper"></i></label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <label class="form-check-label">
                                                <input name="status" type="radio" value="archived">
                                                Archived
                                                <i class="input-helper"></i></label>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group col-sm-12 col-md-6">
                                    <label>{{ __('lesson_thumbnail') }} <span
                                            class="text-danger">*</span><small class="text-info">({{ __('preferred_size', ['w' => '300', 'h' => '300']) }})</small></label>
                                    <input type="file" name="lesson_thumbnail" class="dropify" id="lesson_thumbnail">
                                </div>
                            </div>
                            <hr>
                            <div class="text-center">
                                <input class="btn btn-theme" id="create-btn" type="submit" value={{ __('submit') }}>

                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">
                            {{ __('list') . ' ' . __('lesson') }}
                        </h4>
                        <div id="toolbar">
                            <div class="row">
                                <div class="col">
                                    <select name="filter_subject_id" id="filter_subject_id" class="form-control">
                                        <option value="">{{ __('all') }}</option>
                                        @foreach ($subjects as $subject)
                                            <option value="{{ $subject->id }}">
                                                {{ $subject->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col">
                                    <select name="filter_class_section_id" id="filter_class_section_id"
                                        class="form-control">
                                        <option value="">{{ __('all') }}</option>
                                        @foreach ($class_section as $class)
                                            <option value="{{ $class->id }}">
                                                {{ $class->class->name . '-' . $class->section->name . ' ' . $class->class->medium->name }}
                                                {{ $class->class->streams->name ?? '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col">
                                    <select name="filter_lesson_id" id="filter_lesson_id" class="form-control">
                                        <option value="">{{ __('all') }}</option>
                                        @foreach ($lessons as $lesson)
                                            <option value="{{ $lesson->id }}">
                                                {{ $lesson->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                        </div>

                        <table aria-describedby="mydesc" class='table' id='table_list' data-toggle="table"
                            data-url="{{ route('lesson.show', 1) }}" data-click-to-select="true"
                            data-side-pagination="server" data-pagination="true"
                            data-page-list="[5, 10, 20, 50, 100, 200, All]" data-search="true" data-toolbar="#toolbar"
                            data-show-columns="true" data-show-refresh="true" data-fixed-columns="true"
                            data-fixed-number="2" data-fixed-right-number="1" data-trim-on-search="false"
                            data-mobile-responsive="true" data-sort-name="id" data-sort-order="desc"
                            data-maintain-selected="true" data-export-types='["txt","excel"]'
                            data-query-params="CreateLessionQueryParams"
                            data-export-options='{ "fileName": "lesson-list-{{ date('d-m-y') }}" ,"ignoreColumn":
                            ["operate"]}'
                            data-show-export="true">
                            <thead>
                                <tr>
                                    <th scope="col" data-field="id" data-sortable="true" data-visible="false">
                                        {{ __('id') }}</th>
                                    <th scope="col" data-field="no" data-sortable="false">{{ __('no.') }}</th>
                                    <th scope="col" data-field="name" data-sortable="true">{{ __('name') }}</th>
                                    <th scope="col" data-field="description" data-sortable="true">
                                        {{ __('description') }}</th>
                                    <th scope="col" data-field="class_section_name" data-sortable="true">
                                        {{ __('class_section') }}</th>
                                    <th scope="col" data-field="subject_name" data-sortable="true">
                                        {{ __('subject') }}</th>
                                    <th scope="col" data-field="file" data-formatter="fileFormatter"
                                        data-sortable="true">{{ __('file') }}</th>
                                    <th scope="col" data-field="purchased_count" data-sortable="true">
                                        {{ __('purchased_count') }}</th>
                                    <th scope="col" data-field="payment_status" data-sortable="false">
                                        {{ __('payment_status') }}</th>
                                    <th scope="col" data-field="status_name" data-sortable="false">
                                        {{ __('status') }}</th>
                                    <th scope="col" data-field="created_at" data-sortable="true"
                                        data-visible="false"> {{ __('created_at') }}</th>
                                    <th scope="col" data-field="updated_at" data-sortable="true"
                                        data-visible="false"> {{ __('updated_at') }}</th>
                                    <th scope="col" data-field="operate" data-sortable="false"
                                        data-events="lessonEvents">{{ __('action') }}</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Modal -->
            <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-xl" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">
                                {{ __('edit') . ' ' . __('lesson') }}
                            </h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form class="pt-3 edit-lesson-form" id="edit-form" action="{{ url('lesson') }}"
                            novalidate="novalidate">
                            <input type="hidden" name="edit_id" id="edit_id" value="" />
                            <div class="modal-body">
                                <div class="row">
                                    <div class="form-group col-sm-12 col-md-6">
                                        <label>{{ __('class') . ' ' . __('section') }} <span
                                                class="text-danger">*</span></label>
                                        <select name="class_section_id" id="edit_class_section_id"
                                            class="class_section_id form-control">
                                            <option value="">--{{ __('select') }}--</option>
                                            @foreach ($class_section as $section)
                                                <option value="{{ $section->id }}"
                                                    data-class="{{ $section->class->id }}">
                                                    {{ $section->class->name . ' ' . $section->section->name . ' - ' . $section->class->medium->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group col-sm-12 col-md-6">
                                        <label>{{ __('subject') }} <span class="text-danger">*</span></label>
                                        <select name="subject_id" id="edit_subject_id" class="subject_id form-control">
                                            <option value="">--{{ __('select') }}--</option>
                                            @foreach ($subjects as $subject)
                                                <option value="{{ $subject->id }}">
                                                    {{ $subject->name . ' - ' . $subject->type }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-group col-sm-12 col-md-6">
                                        <label>{{ __('lesson_name') }} <span class="text-danger">*</span></label>
                                        <input type="text" id="edit_name" name="name"
                                            placeholder="{{ __('lesson_name') }}" class="form-control" />
                                    </div>

                                    <div class="form-group col-sm-12 col-md-6">
                                        <label>{{ __('lesson_description') }} <span class="text-danger">*</span></label>
                                        <textarea id="edit_description" name="description" placeholder="{{ __('lesson_description') }}"
                                            class="form-control"></textarea>
                                    </div>
                                    <div class="form-group col-sm-12 col-md-6">
                                        <label>{{ __('paid_or_free') }} <span class="text-danger">*</span></label>
                                        <div class="d-flex">
                                            <div class="form-check form-check-inline">
                                                <label class="form-check-label">
                                                    <input name="payment_status" class="payment_status free" type="radio" value="0">
                                                    Free
                                                    <i class="input-helper"></i></label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <label class="form-check-label">
                                                    <input name="payment_status" class="payment_status paid" type="radio" value="1">
                                                    Paid
                                                    <i class="input-helper"></i></label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group col-sm-12 col-md-6 price_row">
                                        <label>{{ __('price') }} <span class="text-danger">*</span></label>
                                        <input type="number" min="1" step="0.01" id="price" disabled name="price"
                                            placeholder="{{ __('price') }}" class="form-control" />
                                    </div>
                                    <div class="form-group col-sm-12 col-md-6">
                                        <label>{{ __('status') }} <span class="text-danger">*</span></label>
                                        <div class="d-flex">
                                            <div class="form-check form-check-inline">
                                                <label class="form-check-label">
                                                    <input name="status" type="radio" value="draft">
                                                    Draft
                                                    <i class="input-helper"></i></label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <label class="form-check-label">
                                                    <input name="status" type="radio" value="published">
                                                    Published
                                                    <i class="input-helper"></i></label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <label class="form-check-label">
                                                    <input name="status" type="radio" value="archived">
                                                    Archived
                                                    <i class="input-helper"></i></label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group col-sm-12 col-md-6">
                                        <label>{{ __('lesson_thumbnail') }} <span
                                                class="text-danger">*</span><small class="text-info">({{ __('preferred_size', ['w' => '300', 'h' => '300']) }})</small></label>
                                        <input type="file" name="lesson_thumbnail" data-show-remove="false" data-default-file="{{ global_asset('images/default-image.png') }}" class="dropify" id="lesson_thumbnail">
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary"
                                    data-dismiss="modal">{{ __('close') }}</button>
                                <input class="btn btn-theme" type="submit" value={{ __('edit') }} />
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/js/dropify.min.js"></script>
    <script>
        $('.dropify').dropify();
        $('.payment_status').change(function() {
            let $this = $(this);
            if($this.val() == 1) {
                $('.price_row input').removeAttr('disabled');
            }else{
                $('.price_row input').attr('disabled','');
            }
        })
    </script>
@endsection
