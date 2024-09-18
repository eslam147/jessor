@extends('web.master')
@section('css')
    
    <link rel="stylesheet" href="{{ url('/student/assets/icons/font-awesome/css/v4-shims.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/emojionearea/3.4.2/emojionearea.min.css">
    <link rel="stylesheet" href="{{ url('/assets/css/frontend.css') }}">
@endsection
<!-- navbar ends here  -->
@section('content')
    <div class="main">
        <div class="container">
            <div class="content-header">
                <div class="d-flex align-items-center">
                    <div class="me-auto">
                        <h3 class="page-title">{{ $teacher->user->full_name }}</h3>
                        <div class="d-inline-block align-items-center">
                            <nav>
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ url('/') }}"><i class="fa fa-home"></i></a>
                                    </li>
                                    <li class="breadcrumb-item"><a href="{{ url('/teachers') }}">{{ __('teachers') }}</a>
                                    </li>
                                    <li class="breadcrumb-item">{{ $teacher->user->full_name }}</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
            <section teacher="{{ $teacher->id }}" class="content teacher">
                <div class="theme-primary container">
                    <div class="row">
                        <div class="col-12 col-lg-8 col-xl-8">
                            <div class="box">
                                <div class="box-header no-border">
                                    <h4 class="box-title">{{ __('related_subjects') }}</h4>
                                </div>
                                <div class="box-body no-border">
                                    <div class="row subject-filter">
                                        <div class="col-12 col-lg-6 col-xl-6">
                                            <label for="subject">{{ __('subjects') }}: -</label>
                                            <select class="form-control subject" name="subject">
                                                <option value="">{{ __('select_subject') }}</option>
                                                @foreach ($all_subjects as $subject)
                                                    <option value="{{ $subject->id }}">{{ $subject->name }} -
                                                        {{ $subject->type }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-12 col-lg-6 col-xl-6">
                                            <label for="class">{{ __('classes') }}: -</label>
                                            <select class="form-control calss_id" name="calss_id">
                                                <option value="">{{ __('select_class') }}</option>
                                                @foreach ($school_classes as $class)
                                                    <option value="{{ $class['id'] }}">{{ $class['name'] }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row get_subjects mt-3">
                                        @include('web.get_subjects')
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-lg-4 col-xl-4">
                            <div class="box box-widget widget-user">
                                <!-- Add the bg color to the header using any of the bg-* classes -->
                                <div class="widget-user-header bg-img bbsr-0 bber-0"
                                    style="background: url({{ global_asset('student/images/gallery/full/10.jpg') }}) center center;"
                                    data-overlay="5">
                                    <h3 class="widget-user-username text-white">{{ $teacher->user->full_name }}</h3>
                                </div>
                                <div class="widget-user-image">
                                    <img class="rounded-circle {{ $classes[array_rand($classes)] }}"
                                        src="{{ !empty($teacher->user->getRawOriginal('image')) ? $teacher->user->image : global_asset('student/images/avatar/avatar-12.png') }}"
                                        alt="{{ $teacher->user->full_name }}">
                                </div>
                                <div class="box-footer">
                                    <div class="row">
                                        <div class="text-center mt-3 mb-3">
                                            <span>
                                                <i class="fa fa-star text-warning"></i>
                                                <i class="fa fa-star text-warning"></i>
                                                <i class="fa fa-star text-warning"></i>
                                                <i class="fa fa-star text-warning"></i>
                                                <i class="fa fa-star-half text-warning"></i>
                                                <span class="text-muted ms-2">(12)</span>
                                            </span>
                                        </div>
                                        <div class="col-12 text-center">
                                            @if (auth()->check())
                                                <button data-id="{{ $teacher->id }}"
                                                    class="btn btn-info-light follow-btn @if ($follow) active @endif">
                                                    @if ($follow)
                                                        <i class="ti-minus"></i> {{ __('unfollow') }}
                                                    @else
                                                        <i class="ti-plus"></i> {{ __('follow') }}
                                                    @endif
                                                </button>
                                            @endif
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="description-block">
                                                <h5 class="description-header">{{ $teacher->students_count }}</h5>
                                                <span class="description-text">{{ __('students') }}</span>
                                            </div>
                                        </div>
                                        <div class="col-sm-3 be-1 bs-1">
                                            <div class="description-block">
                                                <h5 class="description-header">{{ $teacher->subjects_count }}</h5>
                                                <span class="description-text">{{ __('subjects') }}</span>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="description-block">
                                                <h5 class="description-header">{{ $teacher->lessons_teacher_count }}</h5>
                                                <span class="description-text">{{ __('lessons') }}</span>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="description-block">
                                                <h5 class="description-header">{{ $teacher->questions_count }}</h5>
                                                <span class="description-text">{{ __('questions') }}</span>
                                            </div>
                                        </div>
                                        <!-- /.col -->
                                    </div>
                                    <!-- /.row -->
                                </div>
                            </div>
                            <div class="box">
                                <div class="box-body box-profile">
                                    <div class="row">
                                        <div class="col-12">
                                            <div>
                                                <p>{{ __('qualification') }} :<span
                                                        class="text-gray ps-10">{{ $teacher->qualification }}</span>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- /.box-body -->
                            </div>
                            <div class="box">
                                <div class="box-body">
                                    <div class="flexbox align-items-baseline mb-20">
                                        <h6 class="text-uppercase ls-2">{{ __('students') }}</h6>
                                        <small>{{ $teacher->students_count }}</small>
                                    </div>
                                    <div class="gap-items-2 gap-y">
                                        @foreach ($teacher->students as $student)
                                            <a class="avatar" href="#"><img
                                                    class="rounded-circle {{ $classes[array_rand($classes)] }}"
                                                    src="{{ !empty($student->user->getRawOriginal('image')) ? $student->user->image : global_asset('student/images/avatar/' . $files[array_rand($files)]) }}"
                                                    alt="..."></a>
                                        @endforeach
                                        @if ($teacher->students_count > 7)
                                            <a class="avatar avatar-more"
                                                href="#">+{{ $teacher->students_count - 7 }}</a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-lg-12 col-xl-12">
                            <div class="box">
                                <form class="publisher comment bt-1 border-fade">
                                    @csrf
                                    <img class="avatar avatar-sm" src="../images/avatar/4.jpg" alt="...">
                                    <div class="emoji-container">
                                        <div class="input_form input_form_3">
                                            <div class="comment_text mytext mytext_3" contenteditable="true"></div>
                                            <!-- منطقة الصور -->
                                            <div class="image-preview image-preview_comment" style="margin-top: 10px;"></div>
                                        </div>
                                    </div>
                                    <a data-id="3" href="#" class="emoji-trigger publisher-btn" id="emoji-trigger"><i class="fa fa-smile-o"></i></a>
                                    <div class="emoji-picker emoji-picker_3">
                                        <textarea data-id="3" class="emoji-wysiwyg-editor emoji-wysiwyg-editor_3" rows="1"></textarea>
                                    </div>
                                    <span class="publisher-btn file-group">
                                        <i class="fa fa-camera file-browser"></i>
                                        <input data-id="comment" type="file" name="image" class="image">
                                    </span>
                                    <button type="submit" class="publisher-btn"><i class="fa fa-paper-plane"></i></button>
                                </form>
                            </div>
                            <div class="last_comment"></div>
                            <div class="comments_place">
                                @include('web.comments.comments', ['comments' => $comments])
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
@endsection
@section('js')
    <script src="{{ url('/student/assets/jquery-timeago/js/jquery.timeago.js') }}"></script>
    <script src="{{ url('/assets/js/teacher.js') }}"></script>
    <script src="{{ url('/student/assets/icons/font-awesome/js/config.min.js') }}"></script>
    <script src="{{ url('/student/assets/icons/font-awesome/js/util.min.js') }}"></script>
    <script src="{{ url('/student/assets/icons/font-awesome/js/jquery.emojiarea.min.js') }}"></script>
    <script src="{{ url('/student/assets/icons/font-awesome/js/emoji-picker.min.js') }}"></script>
    <script src="{{ url('/student/assets/emojis/emojionearea.js') }}"></script>
    <script src="{{ url('/student/assets/emojis/emojis.js') }}"></script>
@endsection