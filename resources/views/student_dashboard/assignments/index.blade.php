@extends('student_dashboard.layout.app')
@section('content')
    <div class="content-wrapper">
        <div class="container-full">
            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-12">
                        <div class="box bg-transparent no-shadow mb-0">
                            <div class="box-header px-0">
                                <h4 class="box-title">Assignments</h4>
                            </div>
                        </div>
                        <div class="box">
                            <div class="box-body py-10">
                                <div class="table-responsive">
                                    <table class="table no-border mb-0">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Name</th>
                                                <th>Instructions</th>
                                                <th>Points</th>
                                                <th>Resubmission</th>
                                                <th>Subject</th>
                                                <th>Due Date</th>
                                                <th>File</th>
                                                <th>Show Submission Files</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($assignments as $assignment)
                                                <tr>
                                                    <td>
                                                        <div class="bg-danger h-50 w-50 l-h-50 rounded text-center">
                                                            <p class="mb-0 fs-20 fw-600">{{ 'A' . $assignment->id }}
                                                            </p>
                                                        </div>
                                                    </td>
                                                    <td class="fw-600">
                                                        <h5>{{ $assignment->name }} </h5>
                                                    </td>
                                                    <td class="fw-600">
                                                        <p>{{ $assignment->instructions }} </p>
                                                    </td>
                                                    <td class="fw-600">
                                                        {{ $assignment->points }}
                                                    </td>
                                                    <td class="fw-600">
                                                        @if ($assignment->resubmission)
                                                            <span class="badge badge-sm badge-dot badge-primary">
                                                                {{ $assignment->extra_days_for_resubmission }} days
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td class="fw-600">{{ $assignment->subject->name }}</td>
                                                    <td class="fw-600">{{ $assignment->due_date }}</td>
                                                    <td class="text-fade">
                                                        @if ($assignment->file)
                                                            @foreach ($assignment->file as $file)
                                                                <a href="{{ $file->file_url }}" target="_blank">
                                                                    {{ $loop->iteration }} -
                                                                    {{ str($file->file_name)->limit(5) }}
                                                                </a>
                                                            @endforeach
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (empty($assignment->submission))
                                                            {{-- || ( $assignment->resubmission ) --}}
                                                            <a data-href="{{ route('student_dashboard.assignments.submit', $assignment->id) }}"
                                                                class="btn btn-icon btn-light btn-sm submit-assignment">
                                                                <span class="icon-Arrow-right fs-14"><span
                                                                        class="path1"></span><span
                                                                        class="path2"></span></span>
                                                                Submit
                                                            </a>
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
                </div>
            </section>
        </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="submitAssignmentModal" tabindex="-1" role="dialog"
        aria-labelledby="submitAssignmentModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">Submit Assignment</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="#" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="files">Upload Files</label>
                            <input type="file" name="files[]" class="form-control" multiple required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Submit Assignment</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        $('.submit-assignment').click(function(e) {
            e.preventDefault()
            console.log($(this).data('href'));

            let url = $(this).data('href')
            $('#submitAssignmentModal form').attr('action', url)
            $('#submitAssignmentModal').modal('show');
        })
    </script>
@endsection
