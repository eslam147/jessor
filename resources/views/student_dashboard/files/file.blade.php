@if (trim(request()->userAgent()) == 'semi_browser_by_adel')
    @if ($file->isExternalLink())
        <iframe style="width: 100%;height: 100vh;" src="{{ $file->file_url }}"></iframe>
    @endif
@else
    <div class="bg-bubbles-white p-100" style="text-align: center; margin-top: 30px; margin-bottom: 130px;">
        <p class="mb-1" style="font-family: cairo; font-size: 30px;">
            هذا المتصفح غير مصرح به. يرجى استخدام
            المتصفح المسموح.
        </p>
        <a href="https://infinityschool.net/tenancy/assets/browser/InfinitySchoolV1.exe" target="_blank"
            rel="noopener noreferrer" class="btn btn-primary m-auto d-inline-block border-0 p-0">
            <button type="button" class="btn btn-primary"
                style="border: solid 5px; padding: 10px 20px; font-size: 18px;">
                <i class="fas fa-download"></i> Download
            </button>
        </a>
    </div>
@endif
