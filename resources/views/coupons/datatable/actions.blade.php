<a href='{{ route('coupons.edit', $row->id) }}' class="btn btn-xs btn-gradient-primary btn-rounded btn-icon edit-data"
    data-id='{{ $row->id }}' title="Edit" href="{{ route('coupons.edit', $row->id) }}">
    <i class="fa fa-edit"></i>
</a>
<a href='{{ route('coupons.destroy', $row->id) }}' class="btn btn-xs btn-gradient-danger btn-rounded btn-icon delete-form"
    data-id='{{ $row->id }}'>
    <i class="fa fa-trash"></i>
</a>
