@if (settingByType('custom_browser') === 'enabled' && !checkUserAgentCustomBrowser())
    <div class="bg-bubbles-white p-100 text-center" style="margin-top: 30px; margin-bottom: 130px;">
        <p dir="rtl" style="font-family: cairo; font-size: 30px; margin-bottom: 13px;">
            هذا المتصفح غير مصرح به. يرجى استخدام
            المتصفح المسموح.
        </p>
        <a href="{{ !empty(settingByType('browser_url')) ? settingByType('browser_url') : '' }}" target="_blank"
            rel="noopener noreferrer" class="btn btn-primary m-auto d-inline-block p-0 b-0">
            <button type="button" class="btn btn-primary"
                style="border: solid 5px; padding: 10px 20px; font-size: 18px;">
                <i class="fas fa-download"></i> Download
            </button>
        </a>
    </div>
@else
    @yield('lesson_content')
@endif
