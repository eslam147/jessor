@if ($row->isFree())
    <span class="badge badge-primary">{{ __('free') }}</span>
@else
    <span class="badge badge-success">{{ __('paid') }}</span>
@endif
