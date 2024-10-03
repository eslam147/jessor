<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12" style="margin-bottom:24px;">


    <div class="card">
        <div class="card-header">
            <h3>تغيير كلمة المرور</h3>
        </div>
        <div class="card-body">

            <form action="{{ route('central.profile.admin-update-password') }}" method="post">
                @method('PUT')
                @csrf
                <div class="row">

                    <div class="row">
                        <div class="form-group col-md-12 mb-2">
                            <label for="current-password">
                                كلمة المرور الحالية
                            </label>
                            <input type="password" class="form-control text-dark" name="current_password" id="current-password">
                            @error('current_password')
                                <div class="error text-danger">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="form-group col-md-6 mb-2">
                            <label for="new-password">
                                كلمة المرور الجديدة
                            </label>
                            <input type="password" class="form-control text-dark" name="new_password" id="new-password">
                            @error('new_password')
                                <div class="error text-danger">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="form-group col-md-6 mb-2">
                            <label for="new-confirm-password">
                                تاكيد كلمة المرور الجديدة
                            </label>
                            <input type="password" class="form-control text-dark" name="new_confirm_password" id="new-confirm-password">
                            @error('new_confirm_password')
                                <div class="error text-danger">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>


                    </div>


                    <div class="card-footer">
                        <button type="submit" class="btn btn-success mt-3">
                            تغيير كلمة المرور
                        </button>
                    </div>
                </div>
            </form>

        </div>
    </div>
</div>
