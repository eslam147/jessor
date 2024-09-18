@foreach ($subjects as $subject)
    <div class="col-md-12 col-lg-6 col-xl-6">
        <a href="{{ route('subject.lessons', $subject->subject->id) }}">
            <div class="card">
                <img style="width: 364px; height: 263px; margin-left:auto; margin-right:auto;" class="card-img-top"
                    src="{{ !empty($subject->subject->image) ? $subject->subject->image : loadTenantMainAsset('logo1', global_asset('images/no_image_available.jpg')) }}"
                    alt="{{ $subject->subject->name }}">
                <div class="card-body text-center">
                    <h4 class="card-title">{{ $subject->subject->name }} | {{ $subject->class_section->class->name }}
                    </h4>
                </div>
                <div class="card-footer justify-content-between d-flex">
                    <span>
                        <i class="fa fa-star text-warning"></i>
                        <i class="fa fa-star text-warning"></i>
                        <i class="fa fa-star text-warning"></i>
                        <i class="fa fa-star text-warning"></i>
                        <i class="fa fa-star-half text-warning"></i>
                        <span class="text-muted ms-2">(12)</span>
                    </span>
                    <br>
                    <span class="text-muted">{{ __('lessons') }} {{ $subject->subject->lessons_count }}</span>
                    @if (auth()->check())
                        <span data-id="{{ $subject->subject->id }}" class="like like_{{ $subject->subject->id }}">
                            <i
                                class="@if (\App\Models\User::find(auth()->user()->id)->hasLiked(\App\Models\Subject::find($subject->subject->id))) fa-solid @else fa-regular @endif fa-thumbs-up"></i>
                        </span>
                        <span data-id="{{ $subject->subject->id }}"
                            class="dislike dislike_{{ $subject->subject->id }}">
                            <i
                                class="@if (\App\Models\User::find(auth()->user()->id)->hasDisliked(\App\Models\Subject::find($subject->subject->id))) fa-solid @else fa-regular @endif fa-thumbs-down"></i>
                        </span>
                    @endif
                </div>
            </div>
        </a>
    </div>
@endforeach
{{ $subjects->links('vendor.pagination.bootstrap-4') }}
