@foreach ($teachers as $teacher)                    
    <div class="col-xs-12 col-sm-12 col-md-4 col-xl-4 col-lg-4">
        <div class="box box-widget widget-user-4">
            <div class="widget-user-header {{ $headers[array_rand($headers)] }}">
                <div class="box-body text-white">
                    <div class="widget-user-image">
                        <img class="rounded-circle {{ $classes[array_rand($classes)] }}" src="{{ !empty($teacher->user->getRawOriginal('image')) ? $teacher->user->image : global_asset('student/images/avatar/avatar-12.png') }}"
                            alt="{{ $teacher->user->full_name }}">
                    </div>
                    <h3 class="widget-user-username">{{ $teacher->user->full_name }}</h3>
                </div>
            </div>
            <div class="box-footer">
                <div class="row">
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
                    <div class="col-sm-12 text-center">
                        <a class="btn btn-primary-light text-center p-10" href="{{ route('instructor.profile', $teacher->id) }}">
                            {{ __('view_profile') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endforeach
{{ $teachers->links('vendor.pagination.bootstrap-4') }}