<div class="media px-0 mt-20">
    <a class="avatar" href="#">
        <img src="../images/avatar/8.jpg" alt="...">
    </a>
    <div class="media-body">
        <p>
            <a href="#"><strong>Brock Lensar</strong></a>
            <i class="drop-action float-end fas fa-ellipsis-v cursor-pointer"></i>
            <time class="timeago float-end text-fade mr-3" datetime="2024-09-14T16:40:17"></time>
        </p>
        <ul class="action-list dropdown-menu">
            <li><a class="dropdown-item" href="javascript:0;">Edit</a></li>
            <li><a data-id="1" class="dropdown-item delete" href="javascript:;">Delete</a></li>
        </ul>    
        <p>Thank you for your nice comment.</p>
        <div class="gap-items-4 mt-10">
            <a class="text-fade hover-light" href="#">
                <i class="fa fa-thumbs-up me-1"></i> 1254
            </a>
            <a class="replay text-fade hover-light" href="#">
                <i class="fa fa-comment me-1"></i> 25
            </a>
        </div>
        @if($nested_replies->nested_replies->count() > 0)
            @include('web.comments.get_replaies', ['nested_replies' => $nested_replies->nested_replies])
        @endif
        <form class="publisher bt-1 pl-0 border-fade">
            <img class="avatar avatar-sm" src="../images/avatar/4.jpg" alt="...">
            <div class="emoji-container">
                <div class="input_form input_form_1">
                    <div class="mytext mytext_1" contenteditable="true"></div>
                    <!-- منطقة الصور -->
                    <div class="image-preview image-preview_1" style="margin-top: 10px;"></div>
                </div>
            </div>
            <a data-id="1" href="#" class="emoji-trigger publisher-btn" id="emoji-trigger"><i class="fa fa-smile-o"></i></a>
            <div class="emoji-picker emoji-picker_1">
                <textarea data-id="1" class="emoji-wysiwyg-editor emoji-wysiwyg-editor_1" rows="1"></textarea>
            </div>
            <span class="publisher-btn file-group">
                <i class="fa fa-camera file-browser"></i>
                <input data-id="1" type="file" name="image" class="image">
            </span>
            <button type="submit" class="publisher-btn"><i class="fa fa-paper-plane"></i></button>
        </form>                                            
    </div>
</div>
