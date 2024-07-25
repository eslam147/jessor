@if ($row->is_paid)
    <span class="badge badge-success">{{ __('paid') }}</span>
@else
    <span class="badge badge-primary">{{ __('free') }}</span>
@endif
