@extends('student_dashboard.files.inc.use_private_browser')
@section('lesson_content')
    @if ($file->isExternalLink() || $file->isFileUpload())
        <iframe class="w-p100" style="width: 100%;height: 100vh;" src="{{ $file->file_url }}"></iframe>
    @endif
@endsection