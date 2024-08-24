{{-- <a href="{{ route('students.show', $user->id) }}"> --}}
@if ($user)
    <a href="#">
        {{ $user->first_name }} {{ $user->last_name }}
    </a>
@else
    NA
@endif
