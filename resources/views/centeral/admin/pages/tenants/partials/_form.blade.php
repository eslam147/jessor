<div class="row">
    <div class="col-12">
        <div class="form-group">
            <label>الدومين</label>
            <input type="text" name="domain" class="form-control mt-3" placeholder="مثال: sampledomain.com"
                value="{{ old('domain') }}">
            @error('domain')
                <p class="text-danger" role="alert">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="form-group col-md-6 col-sm-12 mb-2">
        <label>اسم المدرسة</label>
        <input name="school_name" value="{{ old('school_name') }}" type="text" required placeholder="اسم المدرسة"
            class="form-control" />
        @error('school_name')
            <p class="text-danger" role="alert">{{ $message }}</p>
        @enderror
    </div>

    <div class="form-group col-md-6 col-sm-12 mb-2">
        <label>البريد الإلكتروني للمدرسة</label>
        <input name="school_email" value="{{ old('school_email') }}" type="email" required
            placeholder="البريد الإلكتروني للمدرسة" class="form-control" />
        @error('school_email')
            <p class="text-danger" role="alert">{{ $message }}</p>
        @enderror
    </div>

    <div class="form-group col-md-6 col-sm-12 mb-2">
        <label>هاتف المدرسة</label>
        <input name="school_phone" value="{{ old('school_phone') }}" type="text" required placeholder="هاتف المدرسة"
            class="form-control" />
        @error('school_phone')
            <p class="text-danger" role="alert">{{ $message }}</p>
        @enderror
    </div>

    <div class="form-group col-md-4 col-sm-12 mb-2">
        <label>المنطقة الزمنية</label>
        <select name="time_zone" required class="form-control" style="width:100%">
            @foreach ($getTimezoneList as $timezone)
                <option value="{{ $timezone[2] }}" @selected(old('time_zone', 'Africa/Cairo') == $timezone[2])>
                    {{ $timezone[2] }} - GMT {{ $timezone[1] }} - {{ $timezone[0] }}
                </option>
            @endforeach
        </select>
        @error('time_zone')
            <p class="text-danger" role="alert">{{ $message }}</p>
        @enderror
    </div>

    <div class="form-group col-md-4 col-sm-12 mb-2">
        <label>تنسيق التاريخ</label>
        <select name="date_formate" required class="form-control">
            @foreach ($getDateFormat as $key => $dateformate)
                <option value="{{ $key }}" @selected(old('date_formate') == $key)>
                    {{ $dateformate }}
                </option>
            @endforeach
        </select>
        @error('date_formate')
            <p class="text-danger" role="alert">{{ $message }}</p>
        @enderror
    </div>

    <div class="form-group col-md-4 col-sm-12 mb-2">
        <label>تنسيق الوقت</label>
        <select name="time_formate" required class="form-control">
            @foreach ($getTimeFormat as $key => $timeformate)
                <option value="{{ $key }}" @selected(old('time_formate') == $key)>
                    {{ $timeformate }}
                </option>
            @endforeach
        </select>
        @error('time_formate')
            <p class="text-danger" role="alert">{{ $message }}</p>
        @enderror
    </div>
</div>
<hr>
