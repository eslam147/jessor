<div class="modal fade delete_comment delete_comment_{{ $commentId }}" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('delete_comment') }}</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form data-id="{{ $commentId }}" class="remove_comment">
                @csrf
                <div class="modal-body">
                    <p>{{ __('are_you_sure_you_want_to_delete_this_comment') }}</p>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-danger"><i class="fa fa-trash"></i> {{ __('yes') }}</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('no') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
