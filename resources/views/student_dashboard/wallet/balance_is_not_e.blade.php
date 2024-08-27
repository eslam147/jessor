<p>Your balance :{{ $user->balance }}. You don't have enough balance to unlock this lesson .
    <a href="{{ route('student_dashboard.wallet.index') }}" class="btn btn-primary-light w-p100">Add Balance Now</a>
</p>
