@php
    $privateBrowserEnabled = settingByType('custom_browser');
    $privateBrowserUrl = settingByType('browser_url');
@endphp
@if (isset($privateBrowserEnabled) && $privateBrowserEnabled == 'enabled')
    @if (trim(request()->userAgent()) == 'semi_browser_by_adel_v2')
        @if ($file->isExternalLink())
            <iframe style="width: 100%;height: 100vh;" src="{{ $file->file_url }}"></iframe>
        @endif
    @else
        <div class="bg-bubbles-white p-100" style="text-align: center; margin-top: 30px; margin-bottom: 130px;">
            <p style="direction: rtl; font-family: cairo; font-size: 30px; margin-bottom: 13px;">
                هذا المتصفح غير مصرح به. يرجى استخدام
                المتصفح المسموح.
            </p>
            <a href="{{ !empty($privateBrowserUrl) ? $privateBrowserUrl : '' }}" target="_blank" rel="noopener noreferrer"
                class="btn btn-primary" style="margin: auto; display: inline-block; border: 0; padding: 0;">
                <button type="button" class="btn btn-primary"
                    style="border: solid 5px; padding: 10px 20px; font-size: 18px;">
                    <i class="fas fa-download"></i> Download
                </button>
            </a>
        </div>
    @endif
@else
    @if ($file->isExternalLink())
        <iframe style="width: 100%;height: 100vh;" src="{{ $file->file_url }}"></iframe>
    @endif
@endif
