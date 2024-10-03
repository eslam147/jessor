<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12" style="margin-bottom:24px;">

    <div class="card">
        <div class="card-header">
            <h3>معلومات التواصل</h3>
        </div>
        <div class="card-body">

            <div class="row">

                <form action="{{ route('central.profile.admin-update-info') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="form-group col-md-6 mb-2">
                            <label for="name">
                                الاسم 
                            </label>
                            <input type="text" class="form-control text-dark" name="name"
                                value="{{ old('name',auth()->user()->name) }}">
                            @error('name')
                                <p class="text-danger">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
       

                        <div class="form-group col-md-6 mb-2">
                            <label for="email">
                                الاميل
                            </label>
                            <input type="email" class="form-control text-dark" name="email"
                                value="{{ old('email',auth()->user()->email) }}">
                            @error('email')
                                <p class="text-danger">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>


                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-success mt-3">
                            حفظ
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>
</div>
