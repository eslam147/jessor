<table class="table">
    <thead>
        <tr>
            <th scope="col">ID</th>
            <th >Code</th>
            <th>Teacher</th>
            <th>Class</th>
            <th>Subject</th>
            <th>Maximum Usage</th>
            <th>Price</th>
            <th>Expiry Date</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($coupons as $coupon)
            <tr>
                <td scope="row">{{ $coupon->id }}</td>
                <td>{{ $coupon->code }}</td>
                <td>{{ optional($coupon->teacher)->name }}</td>
                <td>{{ optional($coupon->classModel)?->name ?? 'N/A' }}</td>
                <td>{{ optional($coupon->subject)?->name ?? 'N/A' }}</td>
                <td>{{ $coupon->maximum_usage }}</td>
                <td>{{ !is_null($coupon->price) ? number_format($coupon->price, 2) : 'N/A' }}</td>
                <td>{{ $coupon->expiry_date->toDateString() }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
