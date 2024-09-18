@foreach ($lessons as $lesson)
<div class="col-md-12 col-lg-3 col-xl-3">
    <a class="lesson-topics" href="{{ route('lesson.topics', $lesson->id) }}">
        <div class="card">
            <img style="width: 364px; height: 263px; margin-left:auto; margin-right:auto;" class="card-img-top" src="{{ !empty($lesson->thumbnail) ? $lesson->thumbnail : loadTenantMainAsset('logo1', global_asset('images/no_image_available.jpg')) }}" alt="{{ $lesson->name }}">
            <div class="card-body text-center">
                <h4 class="card-title">{{ $lesson->name }}</h4>
                <p>{{ str_length($lesson->description,90) }}</p>
            </div>
            <div class="card-footer">
                <div class="justify-content-between d-flex">
                    <span class="text-muted">{{ __('price') }} {{ number_format($lesson->price, 2) }}</span>
                    <span class="text-muted">{{ __('topics') }} {{ $lesson->topic_count }}</span>
                </div>
                @if (auth()->check() && auth()->user()->student)
                    @if(empty($lesson->studentActiveEnrollment))
                        @if ($lesson->is_lesson_free)
                            <button data-class-section-id ="{{ $lesson->class_section_id }}" data-id="{{ $lesson->id }}" class="btn btn-success locked-btn mt-3"<i class="fa fa-gift mx-2"></i>Enroll Lesson For Free!</button>
                        @else
                            <button data-class-section-id ="{{ $lesson->class_section_id }}" data-id="{{ $lesson->id }}" class="btn btn-success locked-btn mt-3" data-price="{{ $lesson->price }}" data-bs-toggle="modal" data-bs-target="#payment-methods"><i class="fa fa-shopping-cart mx-2"></i>Enroll Lesson!</button>
                        @endif
                    @endif
                @elseif(!auth()->check())
                    @if ($lesson->is_lesson_free)
                        <button data-class-section-id ="{{ $lesson->class_section_id }}" data-id="{{ $lesson->id }}" class="btn btn-success locked-btn mt-3"<i class="fa fa-gift mx-2"></i>Enroll Lesson For Free!</button>
                    @else
                        <button data-class-section-id ="{{ $lesson->class_section_id }}" data-id="{{ $lesson->id }}" class="btn btn-success locked-btn mt-3" data-price="{{ $lesson->price }}" data-bs-toggle="modal" data-bs-target="#payment-methods"><i class="fa fa-shopping-cart mx-2"></i>Enroll Lesson!</button>
                    @endif
                @endif
            </div>
        </div>
    </a>
</div>
@endforeach
{{ $lessons->links('vendor.pagination.bootstrap-4') }}