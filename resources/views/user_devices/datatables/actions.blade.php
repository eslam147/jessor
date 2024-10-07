@can('user-devices-delete')
    <a class="btn btn-xs btn-gradient-danger btn-rounded btn-icon delete_device"  data-id="{{ $row->id }}"
        data-user_id="{{ $row->user_id }}" data-device-id="{{ $row->id }}" data-url='{{ route('user_devices.destroy', $row->id) }}' title="Delete">
        <i class="fa fa-trash"></i>
    </a>
@endcan