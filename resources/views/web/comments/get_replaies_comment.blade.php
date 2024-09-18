@foreach ($replies as $replay)
    <div class="media">
            <a class="avatar" href="#">
                <img class="avatar avatar-lg" src="{{ !empty($replay->commentator->getRawOriginal('image')) ? $replay->commentator->image : global_asset('student/images/avatar/avatar-12.png') }}" alt="...">
            </a>
        <div class="media-body">
            <p>
                <strong>{{ $replay->commentator->full_name }}</strong>
                <i class="drop-action float-end fas fa-ellipsis-v cursor-pointer"></i>
                <time class="timeago float-end text-fade mr-3" datetime="{{ $replay->created_at }}"></time>
            </p>
            @if(auth()->check() && auth()->user()->id == $comment->commentator->id)
                <ul class="action-list dropdown-menu">
                    <li><a data-id="{{ $replay->id }}" class="dropdown-item" href="javascript:0;">Edit</a></li>
                    <li><a data-id="{{ $replay->id }}" class="dropdown-item delete" href="javascript:;">Delete</a></li>
                </ul>
            @endif
            <p>
                @if(!empty($replay->image))
                    <img class="img-comment img-thumbnail" src="{{ tenant_asset($replay->image) }}">
                @endif
                {!! $replay->comment !!}
            </p>
            <div class="gap-items-4 mt-10">
                <a class="text-fade hover-light" href="#">
                    <i class="fa fa-thumbs-up me-1"></i> 1254
                </a>
                <a class="replay text-fade hover-light" href="#">
                    <i class="fa fa-comment me-1"></i> 25
                </a>
            </div>
            <div class="replay-media">
                {{-- @if($replies->nested_replies->count() > 0)
                    @include('web.comments.get_replaies_comment', ['nested_replies' => $replies->nested_replies])
                @endif --}}
                <br>
                <a href="javascript:void(0)">view more...</a>    
            </div>        
        </div>
    </div>
@endforeach
