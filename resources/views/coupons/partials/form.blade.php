<div class="row">
    @unless(isset($coupon))
        <div class="form-group col-sm-12">
            <label>{{ __('coupons_count') }}<span class="text-danger">*</span></label>
            {!! Form::number('coupons_count', null, [
                'required',
                'min' => 1,
                'max' => 50,
                'step' => '1',
                'placeholder' => __('coupons_count'),
                'class' => 'form-control',
            ]) !!}
            @error('coupons_count')
                <p class="text-danger" role="alert">{{ $message }}</p>
            @enderror
        </div>
    @endunless

    <div class="form-group col-sm-12">
        <label>{{ __('usage_limit') }}<span class="text-danger">*</span></label>
        {!! Form::number('usage_limit', isset($coupon) ? $coupon->usage_limit : '', [
            'required',
            'min' => 1,
            'step' => '1',
            'placeholder' => __('usage_limit'),
            'class' => 'form-control',
        ]) !!}
        @error('usage_limit')
            <p class="text-danger" role="alert">{{ $message }}</p>
        @enderror
    </div>

    <div class="form-group col-sm-12">
        <label>{{ __('expiry_date') }}<span class="text-danger">*</span></label>
        {!! Form::date('expiry_date', isset($coupon) ? $coupon->expiry_date : '', [
            'required',
            'placeholder' => __('expiry_date'),
            'class' => 'form-control',
        ]) !!}
        @error('expiry_date')
            <p class="text-danger" role="alert">{{ $message }}</p>
        @enderror
    </div>
    @isset($coupon)
        <div class="form-group col-sm-12">
            <label>{{ __('expiry_time') }}<span class="text-danger">*</span></label>
            {!! Form::time('expiry_time', $coupon->expiry_date->format('H:i'), [
                'required',
                'placeholder' => __('expiry_time'),
                'class' => 'form-control',
            ]) !!}
            @error('expiry_time')
                <p class="text-danger" role="alert">{{ $message }}</p>
            @enderror
        </div>
    @endisset
    <div class="form-group col-sm-12">
        <label>{{ __('medium') }}</label>

        <select name="medium_id" id="medium_id" class="form-control">
            @foreach ($mediums as $medium)
                <option value="{{ $medium->id }}" @selected(old('medium_id', isset($coupon) ? $coupon->classModel->medium_id : '') == $medium->id)>
                    {{ $medium->name }}
                </option>
            @endforeach
        </select>
        @error('medium_id')
            <p class="text-danger" role="alert">{{ $message }}</p>
        @enderror
    </div>
    <div class="form-group col-sm-12">
        <label>{{ __('class') }}</label>
        <select name="class_id" id="class_m_id" readonly class="form-control"></select>
        @error('class_id')
            <p class="text-danger" role="alert">{{ $message }}</p>
        @enderror
    </div>
    <div class="form-group col-sm-12">
        <label>{{ __('subject') }}</label>
        {!! Form::select('subject_id', [], old('subject_id'), [
            'required',
            'readonly',
            'placeholder' => __('select_subject'),
            'class' => 'form-control',
            'id' => 'subject_id',
        ]) !!}
        @error('subject_id')
            <p class="text-danger" role="alert">{{ $message }}</p>
        @enderror
    </div>
    <div class="form-group col-sm-12">
        <label>{{ __('teacher') }}</label>
        {!! Form::select('teacher_id', [], old('teacher_id'), [
            'readonly',
        
            'placeholder' => __('select_teacher'),
            'class' => 'form-control',
            'id' => 'teacher_id',
        ]) !!}
        @error('teacher_id')
            <p class="text-danger" role="alert">{{ $message }}</p>
        @enderror
    </div>

    <div class="form-group col-sm-12">
        <label>{{ __('lesson') }}</label>
        <select name="lesson_id" id="lesson_id" class="form-control" readonly></select>
        @error('lesson_id')
            <p class="text-danger" role="alert">{{ $message }}</p>
        @enderror
    </div>
</div>
