@if (!empty($status))
    <span class="badge badge-{{ $status->color() }}">{{ $status->translatedName() }}</span>
@endif
