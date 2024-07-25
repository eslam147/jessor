@can('enrollments-delete')
    <a href="{{ route('enrollment.destroy', $row->id) }}"
        class="btn btn-xs btn-gradient-danger btn-rounded btn-icon delete-form" data-id="{{ $row->id }}">
        <i class="fa fa-trash"></i>
    </a>
@endcan
