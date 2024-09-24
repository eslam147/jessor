@if (!empty($row->start_url))
    <a href="{{ $row->start_url }}">
        <i class="fa fa-video-camera"></i>
        Open Meeting
    </a>
@endif