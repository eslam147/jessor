@if($comments->total() > 0)
    @foreach ($comments as  $comment)
        <div class="box">
            <div class="media bb-1 border-fade">
                <img class="avatar avatar-lg" src="{{ !empty($comment->commentator->getRawOriginal('image')) ? $comment->commentator->image : global_asset('student/images/avatar/avatar-12.png') }}" alt="...">
                <div class="media-body">
                    <p>
                        <strong>{{ $comment->commentator->full_name }}</strong>
                        <i class="drop-action float-end fas fa-ellipsis-v cursor-pointer"></i>
                        <time class="timeago float-end text-fade mr-3" datetime="{{ $comment->created_at }}"></time>
                    </p>
                    @if(auth()->check() && auth()->user()->id == $comment->commentator->id)
                        <ul class="action-list dropdown-menu">
                            <li><a class="dropdown-item" href="javascript:0;">Edit</a></li>
                            <li><a data-id="1" class="dropdown-item delete" href="javascript:;">Delete</a></li>
                        </ul>
                    @endif
                </div>
            </div>
            <div class="box-body bb-1 border-fade">
                <p class="lead">
                    @if(!empty($comment->image))
                        <img class="img-comment img-thumbnail" src="{{ tenant_asset($comment->image) }}">
                    @endif
                    {!! $comment->comment !!}
                </p>
                <div class="gap-items-4 mt-10">
                    <a class="text-fade hover-light" href="#">
                        <i class="fa fa-thumbs-up me-1"></i> 1254
                    </a>
                    <a data-id="{{ $comment->id }}" class="replay text-fade hover-light" href="#">
                        <i class="fa fa-comment me-1"></i> 25
                    </a>
                </div>
            </div>
            <div class="get_replay_comment">
                <div class="media-list media-list-divided">
                    <form data-id="{{ $comment->id }}" class="replay_comment replay_comment_{{ $comment->id }} publisher pl-0 bt-1 border-fade">
                        @csrf
                        <img class="avatar avatar-sm" src="{{ !empty($comment->commentator->getRawOriginal('image')) ? $comment->commentator->image : global_asset('student/images/avatar/avatar-12.png') }}" alt="...">
                        <div class="emoji-container">
                            <div class="input_form input_form_{{ $comment->id }}">
                                <div class="mytext mytext_{{ $comment->id }}" contenteditable="true"></div>
                                <!-- منطقة الصور -->
                                <div class="image-preview image-preview_{{ $comment->id }}" style="margin-top: 10px;"></div>
                            </div>
                        </div>
                        <a data-id="{{ $comment->id }}" href="#" class="emoji-trigger publisher-btn" id="emoji-trigger"><i class="fa fa-smile-o"></i></a>
                        <div class="emoji-picker emoji-picker_{{ $comment->id }}">
                            <textarea data-id="{{ $comment->id }}" class="emoji-wysiwyg-editor emoji-wysiwyg-editor_{{ $comment->id }}" rows="1"></textarea>
                        </div>
                        <span class="publisher-btn file-group">
                            <i class="fa fa-camera file-browser"></i>
                            <input data-id="{{ $comment->id }}" type="file" name="image" class="image image_{{ $comment->id }}">
                        </span>
                        <button type="submit" class="publisher-btn"><i class="fa fa-paper-plane"></i></button>
                    </form>          
                    <div data-id="{{ $comment->id }}" class="get_replaies_comment get_replaies_comment_{{ $comment->id }}"></div>
                    <br>
                    <a class="publisher ml-1" href="javascript:void(0)">view more...</a>                
                </div>
                {{-- @if($comment->direct_replies->count() > 0)
                @endif --}}
            </div>
        </div>
    @endforeach
@endif