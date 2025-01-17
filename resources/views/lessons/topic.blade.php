@extends('layouts.master')

@section('title')
    {{ __('manage') . ' ' . __('topic') }}
@endsection
@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/css/dropify.min.css">
@endsection
@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('manage') . ' ' . __('topic') }}
            </h3>
        </div>
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">
                            {{ __('create') . ' ' . __('topic') }}
                        </h4>
                        <form class="pt-3 add-topic-form"  action="{{ route('lesson-topic.store') }}"
                            method="POST" novalidate="novalidate">
                            <div class="row">
                                <div class="form-group col-sm-12 col-md-6">
                                    <label>{{ __('class') . ' ' . __('section') }} <span
                                            class="text-danger">*</span></label>
                                    <select name="class_section_id" id="topic_class_section_id"
                                        class="class_section_id form-control">
                                        <option value="">{{ __('select') . ' ' . __('class_section') }}</option>
                                        @foreach ($class_section as $section)
                                            <option value="{{ $section->id }}" data-class="{{ $section->class->id }}">
                                                {{ $section->class->name . ' ' . $section->section->name . ' - ' . $section->class->medium->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group col-sm-12 col-md-6">
                                    <label>{{ __('subject') }} <span class="text-danger">*</span></label>
                                    <select name="subject_id" id="topic_subject_id" class="subject_id form-control">
                                        <option value="">--{{ __('select_subject') }}--</option>
                                        @foreach ($subjects as $subject)
                                            <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-sm-12 col-md-6">
                                    <label>{{ __('lesson') }} <span class="text-danger">*</span></label>
                                    <select name="lesson_id" id="topic_lesson_id" class="form-control">
                                        <option value="">--{{ __('select_lesson') }}--</option>
                                    </select>
                                </div>

                                <div class="form-group col-sm-12 col-md-6">
                                    <label>{{ __('topic_name') }} <span class="text-danger">*</span></label>
                                    <input type="text" id="name" name="name"
                                        placeholder="{{ __('topic_name') }}" class="form-control" />
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-sm-12 col-md-6">
                                    <label>{{ __('topic_description') }} <span class="text-danger">*</span></label>
                                    <textarea id="description" name="description" placeholder="{{ __('topic_description') }}" class="form-control"></textarea>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-sm-12 col-md-6">
                                    <label>{{ __('thumbnail') }} <span class="text-danger">*</span><small
                                            class="text-info">({{ __('preferred_size', ['w' => '300', 'h' => '300']) }})</small></label>
                                    <input type="file" name="thumbnail" class="dropify" id="thumbnail">
                                </div>
                            </div>

                            <h4 class="mb-3">{{ __('files') }}</h4>

                            <div class="form-group">
                                <div class="row file_type_div" id="file_type_div">
                                    <div class="form-group col-md-2">
                                        <label>{{ __('type') }}</label>
                                        <select id="file_type" name="file[0][type]" class="form-control file_type">
                                            <option value="">--{{ __('select') }}--</option>
                                            <optgroup label="Files">
                                                <option value="file_upload">{{ __('file_upload') }}</option>
                                                <option value="youtube_link">{{ __('youtube_link') }}</option>
                                                <option value="video_upload">{{ __('video_upload') }}</option>
                                                <option value="video_corner_link">{{ __('video_corner_link') }}</option>
                                                <option value="external_link">{{ __('external_link') }}</option>
                                            </optgroup>
                                            <optgroup label="Other">
                                                <option value="online_exam">{{ __('online_exam') }}</option>
                                                <option value="assignment">{{ __('assignment') }}</option>
                                            </optgroup>
                                        </select>
                                    </div>
                                    <div class="form-group input_file_content col-md-3" id="file_name_div"
                                        style="display: none">
                                        <label>{{ __('file_name') }} <span class="text-danger">*</span></label>
                                        <input type="text" name="file[0][name]" class="form-control"
                                            placeholder="{{ __('file_name') }}" required>
                                    </div>
                                    <div class="form-group input_file_content col-md-3" id="file_div"
                                        style="display: none">
                                        <label>{{ __('file_upload') }} <span class="text-danger">*</span></label>
                                        <input type="file" name="file[0][file]" class="form-control" placeholder=""
                                            required>
                                    </div>
                                    <div class="form-group input_file_content col-md-3" id="video_corner_url_div"
                                        style="display: none">
                                        <label>{{ __('link') }} <span class="text-danger">*</span></label>
                                        <input type="text" name="file[0][video_corner_url]"
                                            class="video_corner_url form-control" placeholder="{{ __('link') }}"
                                            required>
                                    </div>
                                    <div class="form-group input_file_content col-md-3" id="video_corner_url_div"
                                        style="display: none">
                                        <label>{{ __('download_link') }} <span class="text-danger">*</span></label>
                                        <input type="text" name="file[0][download_link]"
                                            class="download_link form-control" placeholder="{{ __('download_link') }}"
                                            required>
                                    </div>
                                    <div class="form-group input_file_content col-md-3" id="file_link_div"
                                        style="display: none">
                                        <label>{{ __('link') }} <span class="text-danger">*</span></label>
                                        <input type="text" name="file[0][link]" class="form-control"
                                            placeholder="{{ __('link') }}" required>
                                    </div>
                                    <div class="form-group input_file_content col-md-3" id="external_link_div"
                                        style="display: none">
                                        <label>{{ __('link') }} <span class="text-danger">*</span></label>
                                        <input type="text" name="file[0][external_link]" class="form-control"
                                            placeholder="{{ __('link') }}" required>
                                    </div>
                                    <div class="form-group input_file_content col-md-3" id="quizzes"
                                        style="display: none">
                                        <label>{{ __('online_exam') }} <span class="text-danger">*</span></label>
                                        <select name="file[0][online_exam]" disabled class="form-control quizzes" placeholder="{{ __('online_exam') }}" required>
                                            <option value="">{{ __('select_online_exam') }}</option>
                                        </select>
                                    </div>
                                    <div class="form-group input_file_content col-md-3" id="assignments"
                                        style="display: none">
                                        <label>{{ __('assignments') }} <span class="text-danger">*</span></label>
                                        <select name="file[0][assignments]" disabled class="form-control assignments" placeholder="{{ __('assignments') }}" required>
                                            <option value="">{{ __('select_assignment') }}</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-1 col-md-1 pl-0 mt-4">
                                        <button type="button" class="btn btn-inverse-success btn-icon add-lesson-file">
                                            <i class="fa fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="mt-3 extra-files"></div>
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
                            {{ __('list') . ' ' . __('topic') }}
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
                                                {{ $class->class->name . '-' . $class->section->name }}
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
                            data-url="{{ route('lesson-topic.show', 1) }}" data-click-to-select="true"
                            data-side-pagination="server" data-pagination="true"
                            data-page-list="[5, 10, 20, 50, 100, 200, All]" data-search="true" data-toolbar="#toolbar"
                            data-show-columns="true" data-show-refresh="true" data-fixed-columns="true"
                            data-fixed-number="2" data-fixed-right-number="1" data-trim-on-search="false"
                            data-mobile-responsive="true" data-sort-name="id" data-sort-order="desc"
                            data-maintain-selected="true" data-export-types='["txt","excel"]'
                            data-query-params="CreateTopicQueryParams"
                            data-export-options='{ "fileName": "topic-list-{{ date('d-m-y') }}" ,"ignoreColumn":
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
                                    <th scope="col" data-field="lesson_name" data-sortable="true">
                                        {{ __('lesson') }}</th>
                                    <th scope="col" data-field="file" data-formatter="fileFormatter"
                                        data-sortable="true">{{ __('file') }}</th>
                                    <th scope="col" data-field="created_at" data-sortable="true"
                                        data-visible="false"> {{ __('created_at') }}</th>
                                    <th scope="col" data-field="updated_at" data-sortable="true"
                                        data-visible="false"> {{ __('updated_at') }}</th>
                                    <th scope="col" data-field="operate" data-sortable="false"
                                        data-events="topicEvents">{{ __('action') }}</th>
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
                                {{ __('edit') . ' ' . __('topic') }}
                            </h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form class="pt-3 edit-topic-form" id="edit-form" action="{{ url('lesson-topic') }}"
                            novalidate="novalidate">
                            <input type="hidden" name="edit_id" id="edit_id" value="" />
                            <div class="modal-body">

                                <div class="row">
                                    <div class="form-group col-sm-12 col-md-6">
                                        <label>{{ __('class') . ' ' . __('section') }} <span
                                                class="text-danger">*</span></label>
                                        <select name="class_section_id" id="edit_topic_class_section_id"
                                            class="class_section_id form-control">
                                            <option value="">--{{ __('select') }}--</option>
                                            @foreach ($class_section as $section)
                                                <option value="{{ $section->id }}">{{ $section->class->name }}
                                                    {{ $section->section->name }} - {{ $section->class->medium->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group col-sm-12 col-md-6">
                                        <label>{{ __('subject') }} <span class="text-danger">*</span></label>
                                        <select name="subject_id" id="edit_topic_subject_id"
                                            class="subject_id form-control">
                                            <option value="">--{{ __('select_subject') }}--</option>
                                            @foreach ($subjects as $subject)
                                                <option value="{{ $subject->id }}">{{ $subject->name }} -
                                                    {{ $subject->type }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-group col-sm-12 col-md-6">
                                        <label>{{ __('lesson') }} <span class="text-danger">*</span></label>
                                        <select name="lesson_id" id="edit_topic_lesson_id" class="form-control">
                                            <option value="">--{{ __('select_lesson') }}--</option>
                                        </select>
                                    </div>

                                    <div class="form-group col-sm-12 col-md-6">
                                        <label>{{ __('topic_name') }} <span class="text-danger">*</span></label>
                                        <input type="text" id="edit_name" name="name"
                                            placeholder="{{ __('topic_name') }}" class="form-control" />
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-group col-sm-12 col-md-6">
                                        <label>{{ __('topic_description') }} <span class="text-danger">*</span></label>
                                        <textarea id="edit_description" name="description" placeholder="{{ __('topic_description') }}"
                                            class="form-control"></textarea>
                                    </div>
                                </div>

                                <h4 class="mb-3">{{ __('files') }}</h4>
                                <div class="row edit_clone_file_type_div" id="edit_clone_file_type_div"
                                    style="display: none;">
                                    <input type="hidden" id="edit_file_id" name="edit_file[0][id]" />
                                    <div class="form-group col-md-2">
                                        <label>{{ __('type') }}</label>
                                        <select id="edit_file_type" name="edit_file[0][type]"
                                            class="form-control file_type">
                                            <option value="">--{{ __('select') }}--</option>
                                            <option value="file_upload">{{ __('file_upload') }}</option>
                                            <option value="youtube_link">{{ __('youtube_link') }}</option>
                                            <option value="video_upload">{{ __('video_upload') }}</option>
                                            <option value="video_corner_link">{{ __('video_corner_link') }}</option>
                                            <option value="external_link">{{ __('external_link') }}</option>
                                            <option value="online_exam">{{ __('online_exam') }}</option>
                                            <option value="assignment">{{ __('assignment') }}</option>
                                        </select>
                                    </div>
                                    <div class="form-group input_file_content col-md-3" id="file_name_div"
                                        style="display: none">
                                        <label>{{ __('file_name') }} <span class="text-danger">*</span></label>
                                        <input type="text" name="edit_file[0][name]" class="file_name form-control"
                                            placeholder="{{ __('file_name') }}" required>
                                    </div>
                                    <div class="form-group input_file_content col-md-3" id="file_div"
                                        style="display: none">
                                        <label>{{ __('file_upload') }} </label>
                                        <input type="file" name="edit_file[0][file]" class="file form-control"
                                            placeholder="">
                                        <a href="" target="_blank" id="file_preview" class="w-100"></a>
                                    </div>
                                    <div class="form-group input_file_content col-md-3" id="video_corner_url_div"
                                        style="display: none">
                                        <label>{{ __('link') }} <span class="text-danger">*</span></label>
                                        <input type="text" name="edit_file[0][video_corner_url]"
                                            class="video_corner_url form-control" placeholder="{{ __('link') }}"
                                            required>
                                    </div>
                                    <div class="form-group input_file_content col-md-3" id="video_corner_url_div"
                                        style="display: none">
                                        <label>{{ __('download_link') }} <span class="text-danger">*</span><small
                                                class="text-info">(Required for Show In Mobile App Player)</small></label>
                                        <input type="text" name="edit_file[0][download_link]"
                                            class="download_link form-control" placeholder="{{ __('download_link') }}"
                                            required>
                                    </div>
                                    <div class="form-group input_file_content col-md-3" id="file_link_div"
                                        style="display: none">
                                        <label>{{ __('link') }} <span class="text-danger">*</span></label>
                                        <input type="text" name="edit_file[0][link]" class="file_link form-control"
                                            placeholder="{{ __('link') }}" required>
                                    </div>
                                    <div class="form-group input_file_content col-md-3" id="external_link_div"
                                        style="display: none">
                                        <label>{{ __('link') }} <span class="text-danger">*</span></label>
                                        <input type="text" name="edit_file[0][external_link]"
                                            class="external_link form-control" placeholder="{{ __('link') }}"
                                            required>
                                    </div>
                                    <div class="form-group input_file_content col-md-3" id="edit_quizzes"
                                        style="display: none">
                                        <label>{{ __('online_exam') }} <span class="text-danger">*</span></label>
                                        <select name="edit_file[0][online_exam]" class="form-control quizzes" placeholder="{{ __('online_exam') }}" required>
                                            <option value="">{{ __('select_online_exam') }}</option>
                                        </select>
                                    </div>
                                    <div class="form-group input_file_content col-md-3" id="edit_assignments"
                                        style="display: none">
                                        <label>{{ __('assignments') }} <span class="text-danger">*</span></label>
                                        <select name="edit_file[0][assignments]" class="form-control assignments" placeholder="{{ __('assignments') }}" required>
                                            <option value="">{{ __('select_assignment') }}</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-1 pl-0 mt-4">
                                        <button type="button" class="btn btn-icon btn-inverse-danger remove-lesson-file">
                                            <i class="fa fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="mt-3 edit-extra-files"></div>
                                <div>
                                    <div class="form-group pl-0 mt-4">
                                        <button type="button" class="col-md-3 btn btn-inverse-success edit-lesson-file">
                                            {{ __('add_new_files') }} <i class="fa fa-plus"></i>
                                        </button>
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
@section('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/js/dropify.min.js"></script>
    <script>
        $('.dropify').dropify();
    </script>
@endsection
