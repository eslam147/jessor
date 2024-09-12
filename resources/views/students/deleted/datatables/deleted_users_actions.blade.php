@canany(['student-restore', 'student-force-delete'])
    @can('student-restore')
        <a class="btn btn-xs btn-gradient-success btn-rounded btn-icon restore_btn" data-id="{{ $row->id }}"
            data-user_id="{{ $row->user_id }}" data-url="{{ route('students.restore', $row->user_id) }}" title="Restore">
            <i class="fa fa-undo"></i>
        </a>
    @endcan
    @can('student-force-delete')
        <a class="btn btn-xs btn-gradient-danger btn-rounded btn-icon force_delete_btn" data-id='{{ $row->id }} '
            data-user_id='{{ $row->user_id }}' data-url='{{ route('students.permanent_delete', $row->user_id) }}' title="Delete">
            <i class="fa fa-trash"></i>
        </a>
    @endcan
@endcanany
