@extends('student_dashboard.layout.app')
@section('content')
    <div class="content-wrapper">
        <div class="container-full">
            <!-- Main content -->
            <h3 class="box-title mb-20 fw-500"> Latest Coupons </h3>
            <div class="box-body no-padding">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <tbody>
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Coupon Code</th>
                                    <th>Left Days</th>
                                    <th>Coupon Max Price</th>
                                    <th>Left Usage Amount</th>
                                    <th>Usage Left Count</th>
                                    <th>Count Of Usages</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            @foreach ($usagesCoupons as $coupon)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $coupon->code }}
                                        <a href="#" class="bg-transparent btn btn-sm copy-btn text-primary"
                                            data-clipboard-text="{{ $coupon->code }}" target="_blank">
                                            <i class="fa fa-clipboard" aria-hidden="true"></i>
                                        </a>
                                    </td>

                                    <td>
                                        <span @class([
                                            'badge',
                                            'badge-success' => $coupon->left_days_count > 7,
                                            'badge-warning' =>
                                                $coupon->left_days_count <= 7 && $coupon->left_days_count > 3,
                                            'badge-danger' =>
                                                $coupon->left_days_count <= 3 && $coupon->left_days_count > 0,
                                        ])>
                                            @if ($coupon->expiry_date->isFuture())
                                                {{ $coupon->expiry_date->diffForHumans() }}
                                            @else
                                                Expired
                                            @endif
                                        </span>
                                    </td>
                                    <td>{{ $coupon->price }}</td>

                                    <td>{{ $coupon->price - $coupon->usages->sum('price') }}</td>
                                    <td>{{ $coupon->maximum_usage - $coupon->usages->count() }}</td>
                                    <td>{{ $coupon->usages->count() }}</td>
                                    <td>
                                        @if (
                                            $coupon->is_disabled ||
                                                $coupon->expiry_date->isPast() ||
                                                $coupon->maximum_usage <= $coupon->usages->count() ||
                                                $coupon->price <= $coupon->usages->sum('price'))
                                            <span class="badge badge-danger">Expired</span>
                                        @else
                                            <span class="badge badge-success">Active</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        $(document).ready(function() {
            $('.copy-btn').on('click', function() {
                var copyText = $(this).data('clipboard-text');
                navigator.clipboard.writeText(copyText);
                let text = $(this).text();
                $(this).off()
                $(this).text('Copied!');
                setTimeout(function() {
                    $(this).text(text);
                    $(this).on()

                }, 2000);
            });
        });
    </script>
@endsection
