@if (!empty($row->meeting))
    <a href="{{ $row->meeting->start_url }}" target="_blank">
        <i class="fa fa-video-camera"></i>
        Open Meeting
    </a>
@endif