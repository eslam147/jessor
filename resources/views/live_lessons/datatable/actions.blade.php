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
{{-- @can('coupons-edit') --}}
    <a href='{{ route('coupons.edit', $row->id) }}' class="btn btn-sm btn-gradient-primary btn-rounded edit-data"
        data-id='{{ $row->id }}' title="Edit" href="{{ route('coupons.edit', $row->id) }}">
        <i class="fa fa-edit"></i>
        Edit
    </a>
{{-- @endcan --}}
{{-- @can('coupons-list')
    <a href='{{ route('coupons.show', $row->id) }}' class="btn btn-xs btn-gradient-info btn-rounded btn-icon view_coupon"
        data-id='{{ $row->id }}'>
        <i class="fa fa-eye"></i>
    </a>
@endcan --}}