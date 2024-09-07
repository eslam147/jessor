@if ($isLocked)
    <a data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="{{ $msg }}" href="#">
        <i class="fas fa-lock fa-fw"></i>
    </a>
@else
    <a href="{{ route('topics.files', $topic->id) }}">
        <span class="icon-Arrow-right fs-24">
            <span class="path1"></span>
            <span class="path2"></span>
        </span>
    </a>
@endif
