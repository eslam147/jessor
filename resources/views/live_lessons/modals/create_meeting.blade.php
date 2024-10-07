<div class="modal fade" id="connectMeetingModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-capitalize" id="exampleModalLabel">
                    {{ __('create_meeting') }}
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="pt-3 assign-meeting-form" id="assign_meeting_modal" action="#" novalidate="novalidate">
                <input type="hidden" name="live_lesson_id" id="live_lesson_id" value="" />
                <div class="modal-body py-0">
                    <div class="row">
                        <div class="form-group col-sm-12">
                            <label>{{ __('services') }} <span class="text-danger">*</span></label>
                            <select name="service" id="meeting_service" class="meeting_service form-control">
                                <option value="">--{{ __('select') }}--</option>
                                @foreach ($services as $service)
                                    <option value="{{ $service }}">
                                        {{ str()->ucfirst($service) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-sm-12">
                            <label>{{ __('start_date') }} <span class="text-danger">*</span></label>
                            <input type="text" id="meeting_start_date" class="form-control" disabled>
                        </div>
                        <div class="form-group col-sm-12">
                            <label>{{ __('duration') }} <span class="text-danger">*</span></label>
                            <input type="number" id="meeting_duration" class="form-control" disabled>
                        </div>
                        <div class="form-group col-sm-12">
                            <label>{{ __('password') }}</label>
                            <input type="text" id="password" name="password" placeholder="{{ __('password') }}"
                                class="form-control" />
                        </div>
                        <div class="form-group col-sm-12">
                            <div class="form-check">
                                <label class="form-check-label">
                                    <input type="checkbox" class="form-check-input" name="autorecord" id="autorecord"
                                        value="1">
                                    Auto Record
                                </label>
                            </div>
                        </div>
                    </div>
                    <hr class="mt-0">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('close') }}</button>
                    <button type="submit" id="assign_meeting_btn" class="btn btn-theme">{{ __('create') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
