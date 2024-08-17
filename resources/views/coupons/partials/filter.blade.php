<div id="coupon_filter">
    <div class="row">
        <div class="col-lg-3 col-md-4  my-1 col-12">
            <select name="coupon_filter_by_medium" id="coupon_filter_by_medium" class="form-control">
                <option value="">{{ __('select_medium') }}</option>
                @foreach ($mediums as $medium)
                    <option value="{{ $medium->id }}">
                        {{ $medium->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-lg-3 col-md-4  my-1 col-12">
            <select name="coupon_filter_class_id" id="coupon_filter_class_id" class="form-control">
                <option value="">{{ __('select_class_section') }}</option>
                @foreach ($classes as $class)
                    <option value="{{ $class->id }}">
                        {{ $class->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-lg-3 col-md-4  my-1 col-12">
            <select name="coupon_filter_subject_id" id="coupon_filter_subject_id" class="form-control">
                <option value="">{{ __('select_subject') }}</option>
                @foreach ($classSubjects->pluck('subject') as $subject)
                    <option value="{{ $subject->id }}">
                        {{ $subject->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-lg-3 col-md-4  my-1 col-12">
            <select name="coupon_filter_teacher_id" id="coupon_filter_teacher_id" class="form-control">
                <option value="">{{ __('select_teacher') }}</option>
                @foreach ($teachers as $teacher)
                    <option value="{{ $teacher->id }}">
                        {{ $teacher->user->first_name . ' ' . $teacher->user->last_name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-lg-3 col-md-4  my-1 col-12">
            <select name="coupon_filter_lesson_id" id="coupon_filter_lesson_id" class="form-control">
                <option value="">{{ __('select_lesson') }}</option>
                @foreach ($lessons as $lesson)
                    <option value="{{ $lesson->id }}">
                        {{ $lesson->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-lg-3 col-md-4  my-1 col-12">
            <input type="text" name="coupon_filter_tags" id="coupon_filter_tags" class="form-control"
                placeholder="{{ __('enter_tags') }}">
        </div>
        <div class="col-lg-3 col-md-4  my-1 col-12">
            <input type="text" name="coupon_filter_price" id="coupon_filter_price" class="form-control"
                placeholder="{{ __('enter_price') }}">
        </div>
        {{-- <!-- Date Range Filters -->
        <div class="row col-12">
            <div class="col-lg-6 my-1 col-12">
                <input type="date" name="coupon_filter_start_date" id="coupon_filter_start_date" class="form-control"
                    placeholder="{{ __('start_date') }}">
            </div>
            <div class="col-lg-6 my-1 col-12">
                <input type="date" name="coupon_filter_end_date" id="coupon_filter_end_date" class="form-control"
                    placeholder="{{ __('end_date') }}">
            </div>
        </div> --}}
        <div class="row col-12">
            <div class="col-lg-6  my-1 col-12">
                <div class="form-check form-check-inline">
                    <label class="form-check-label">
                        <input type="checkbox" name="coupon_filter_used" value="true" @checked(!empty(request('purchased'))) class="form-check-input filter_used"
                            id="coupon_filter_used">Show Used
                        <i class="input-helper"></i></label>
                </div>
            </div>
            <div class="col-lg-6  my-1 col-12">
                <select name="filter_status" id="coupon_filter_status" class="form-control">
                    <option value="">{{ __('select_status') }}</option>
                    <option value="active">{{ __('active') }}</option>
                    <option value="inactive">{{ __('disabled') }}</option>
                </select>
            </div>
        </div>
        <div class="my-1 col-12">
            <button type="button" id="search_btn" class="btn btn-primary text-capitalize">{{ __('search') }}</button>
            <button type="button" id="export_button" class="btn btn-secondary text-capitalize">{{ __('export') }}</button>
        </div>
        <div class="col-12">
            <hr>
        </div>
    </div>
</div>
