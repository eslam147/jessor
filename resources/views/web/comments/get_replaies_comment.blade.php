@foreach ($replies->take(10) as $replay)
    <div class="media media_{{ $replay->id }}">
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
                    <li><a data-id="{{ $replay->id }}" class="dropdown-item edit" href="javascript:0;">Edit</a></li>
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
                <a data-id="{{ $replay->id }}" class="replay text-fade hover-light" href="#">
                    <i class="fa fa-comment me-1"></i> {{ $replay->directReplies()->count() }}
                </a>
            </div>
            <div class="replay-media-{{ $replay->id }} replay-media">
                <form data-id="{{ $replay->id }}" class="replay_comment replay_comment_{{ $replay->id }} publisher pl-0 bt-1 border-fade">
                    @csrf
                    <img class="avatar avatar-sm" src="{{ !empty($comment->commentator->getRawOriginal('image')) ? $comment->commentator->image : global_asset('student/images/avatar/avatar-12.png') }}" alt="...">
                    <div class="emoji-container">
                        <div class="input_form input_form_{{ $replay->id }}">
                            <div class="mytext mytext_{{ $replay->id }}" contenteditable="true"></div>
                            <!-- منطقة الصور -->
                            <div class="image-preview image-preview_{{ $replay->id }}" style="margin-top: 10px;"></div>
                        </div>
                    </div>
                    <a data-id="{{ $replay->id }}" href="#" class="emoji-trigger publisher-btn" id="emoji-trigger"><i class="fa fa-smile-o"></i></a>
                    <div class="emoji-picker emoji-picker_{{ $replay->id }}">
                        <textarea data-id="{{ $replay->id }}" class="emoji-wysiwyg-editor emoji-wysiwyg-editor_{{ $replay->id }}" rows="1" style="display: none;"></textarea>
                    </div>
                    <span class="publisher-btn file-group">
                        <i class="fa fa-camera file-browser"></i>
                        <input data-id="{{ $replay->id }}" type="file" name="image" class="image image_{{ $replay->id }}">
                    </span>
                    <button type="submit" class="publisher-btn"><i class="fa fa-paper-plane"></i></button>
                </form>     
                <div data-id="{{ $replay->id }}" class="edit_comment edit_comment_{{ $replay->id }}"></div>
                <div class="replaies replaies_{{ $replay->id }}">
                    @if(count($replay->directReplies) > 0)
                        @include('web.comments.get_replaies_comment', ['replies' => $replay->directReplies])
                    @endif
                </div>
                @if($replay->directReplies()->count() > 10)
                    <br>
                    <a data-page="1" data-id="{{ $replay->id }}" class="show_more publisher ml-1" href="javascript:void(0)">view more...</a>                
                @endif
            </div>
        </div>
    </div>
@endforeach