@can('enrollments-delete')
    <a href="{{ route('enrollment.destroy', $row->id) }}"
        class="btn btn-xs btn-gradient-danger btn-rounded btn-icon delete-form" data-id="{{ $row->id }}">
        <i class="fa fa-trash"></i>
    </a>
@endcan
@can('enrollments-edit')
    <a class="btn btn-xs btn-gradient-primary btn-rounded btn-icon edit-data" data-id="{{ $row->id }}" title="Edit"
        data-toggle="modal" data-target="#editModal">
        <i class="fa fa-edit"></i>
    </a>
@endcan
