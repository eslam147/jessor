<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12" style="margin-bottom:24px;">

    <div class="card">
        <div class="card-header">
            <h3>صورة الحساب</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('central.profile.admin-update-image') }}" method="post" enctype="multipart/form-data">
                @method('PUT')
                @csrf

                <div class="row">
                    <div class="form-group col-md-6 mb-2">
                        <img src="{{ asset(auth()->user()->avatar) }}" class="rounded-circle shadow-4" style="width: 150px;" />
                    </div>


                    <div class="form-group col-md-6 mb-2 mt-5">
                        <label for="new-image">
                            رفع صوره جديده
                        </label>
                        <input type="file" class="form-control text-dark" name="image" id="new-image">
                        @error('image')
                            <p class="error text-danger">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-success mt-3">
                            تغيير صورة الحساب
                        </button>
                    </div>
                </div>
            </form>

        </div>
    </div>
</div>
