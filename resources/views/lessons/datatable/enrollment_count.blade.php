<a href="{{ route('enroll.index', ['lesson_id' => $row->id]) }}" class="btn btn-xs btn-gradient-primary btn-rounded"
    title="Show Enrollments">
    {{ trans('enrollments_count', ['count' => $row->enrollments_count]) }}
</a>
