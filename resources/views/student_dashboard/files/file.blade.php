@if ($file->isExternalLink())
    <iframe style="width: 100%;height: 100vh;" src="{{ $file->file_url }}" ></iframe>
@endif
