<a href="#" class="btn btn-sm btn-info btn-rounded show_users" data-id="{{ $row->id }}"
    title="Show Students Enrolled">
    <i class="fa fa-users"></i>
    Show Students
</a>
@empty($row->meetings->count())
    <a href="#" class="btn btn-sm btn-success btn-rounded connect_meeting" data-id="{{ $row->id }}"
        title="Create Meeting">
        <i class="fa fa-video-camera"></i>
        Create Meeting
    </a>
@else
    @if ($row->status->isStarted())
        <a href="{{ '#' }}" class="btn btn-sm btn-danger btn-rounded stop_meeting">
            <i class="fa fa-stop"></i>
            Stop
        </a>
    @elseif($row->status->isScheduled())
        <a href="{{ '#' }}" data-id="{{ $row->id }}" class="btn btn-sm btn-facebook btn-rounded start_meeting">
            <i class="fa fa-video-camera"></i>
            Start
        </a>
    @endif
    <a href='{{ route('live_lessons.edit', $row->id) }}' class="btn btn-sm btn-gradient-primary btn-rounded edit-data"
        data-id='{{ $row->id }}' title="Edit" href="{{ route('coupons.edit', $row->id) }}">
        <i class="fa fa-edit"></i>
        Edit
    </a>
@endempty
