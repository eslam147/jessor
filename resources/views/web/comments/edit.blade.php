<form data-id="{{ $comment->id }}" class="update_comment update_comment_{{ $comment->id }} publisher pl-0 bt-1 border-fade">
    @csrf
    <img class="avatar avatar-sm" src="{{ !empty($comment->commentator->getRawOriginal('image')) ? $comment->commentator->image : global_asset('student/images/avatar/avatar-12.png') }}" alt="...">
    <div class="emoji-container">
        <div class="input_form input_form_{{ $comment->id }}">
            <div class="mytext mytext_{{ $comment->id }}" contenteditable="true">{!! $comment->comment !!}</div>
            <!-- منطقة الصور -->
            <div class="image-preview image-preview_{{ $comment->id }} @if(!empty($comment->image)) show @endif" style="margin-top: 10px;">
                @if(!empty($comment->image))
                    <div class="image-container">
                        <img class="myimg" src="{{ tenant_asset($comment->image) }}" alt="{{ $comment->image }}" />
                        <span class="remove-image" data-id="{{ $comment->id }}">X</span>
                    </div>
                @endif
            </div>
        </div>
    </div>
    <a data-id="{{ $comment->id }}" href="#" class="emoji-trigger-edit publisher-btn" id="emoji-trigger"><i class="fa fa-smile-o"></i></a>
    <div class="emoji-picker emoji-picker_edit_{{ $comment->id }}">
        <textarea data-id="{{ $comment->id }}" class="emoji-wysiwyg-editor edit-emoji-wysiwyg-editor_{{ $comment->id }}" rows="1"></textarea>
    </div>
    <span class="publisher-btn file-group">
        <i class="fa fa-camera file-browser"></i>
        <input data-id="{{ $comment->id }}" type="file" name="image" class="image image_{{ $comment->id }}">
    </span>
    <button type="submit" class="publisher-btn"><i class="fa fa-paper-plane"></i></button>
</form>