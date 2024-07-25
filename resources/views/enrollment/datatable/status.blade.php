{{-- @if ($row->is_disabled)
    <a href="{{ route('coupons.status', ['coupon' => $row->id]) }}"
        class="btn btn-xs btn-success btn-rounded btn-icon active_coupon" data-status="0" data-id='{{ $row->id }}'>
        <i class="fa fa-check"></i>
    </a>
@else
    <a href="{{ route('coupons.status', ['coupon' => $row->id]) }}"
        class="btn btn-xs btn-danger btn-rounded btn-icon disable_coupon" data-status="1" data-id='{{ $row->id }}'>
        <i class="fa fa-times"></i>
    </a>
@endif --}}
