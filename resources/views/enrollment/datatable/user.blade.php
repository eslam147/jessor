{{-- <a href="{{ route('students.show', $user->id) }}"> --}}
@if ($user)
    <a href="#" data-user-id="{{ $user->id }}">
        {{ $user->full_name }}
    </a>
@else
    NA
@endif
