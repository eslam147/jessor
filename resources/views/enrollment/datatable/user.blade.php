{{-- <a href="{{ route('students.show', $user->id) }}"> --}}
@if ($user)
    <a href="#" data-student-id="{{ $user->id }}">
        {{ $user->first_name }} {{ $user->last_name }}
    </a>
@else
    NA
@endif
