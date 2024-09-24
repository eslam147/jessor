<div class="modal fade" id="show_coupon_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">
                    {{ __('show') . ' ' . __('coupon') }}
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p><strong>Code:</strong> <span id="coupon-code"></span></p>
                <p><strong>Expiry Date:</strong> <span id="coupon-expiry-date"></span></p>
                <p><strong>Price:</strong> <span id="coupon-price"></span></p>
                <p><strong>Disabled:</strong> <span id="coupon-is-disabled"></span></p>
                <p><strong>Teacher:</strong> <span id="coupon-teacher"></span></p>
                <p><strong>Maximum Usage:</strong> <span id="coupon-maximum-usage"></span></p>
                <p><strong>Type:</strong> <span id="coupon-type"></span></p>
                <p><strong>Only Applied To:</strong> <span id="coupon-only-applied-to"></span></p>
                <p><strong>Created At:</strong> <span id="coupon-created_at"></span></p>
                <table id="coupon_usages" class="table table-responsive table-dark">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ __('student_name') }}</th>
                            <th>{{ __('email') }}</th>
                            <th>{{ __('phone_number') }}</th>
                            <th>{{ __('discount') }}</th>
                            <th>{{ __('applied') }}</th>
                            <th>{{ __('created_at') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="4">{{ __('no_data') }}</td>
                        </tr>
                    </tbody>
                </table>
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('close') }}</button>
            </div>
        </div>
    </div>
</div>
