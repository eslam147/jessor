<div class="row">

    <div class="row">
        <div class="form-group col-md-6 mb-2">
            <label for="first_name">
                الاسم الاول
            </label>
            <input type="text" class="form-control" name="first_name"
                value="{{ old('first_name', isset($admin) ? $admin->first_name : '') }}" placeholder="ادخل الاسم الاول"
                id="first_name">
            @error('first_name')
                <p class="text-danger">{{ $message }}</p>
            @enderror
        </div>
        <div class="form-group col-md-6 mb-2">
            <label for="last_name">
                الاسم الاخير
            </label>
            <input type="text" class="form-control" name="last_name"
                value="{{ old('last_name', isset($admin) ? $admin->last_name : '') }}" placeholder="ادخل الاسم الثاني"
                id="last_name">
            @error('last_name')
                <p class="text-danger">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group col-md-6 mb-2">
            <label for="phone">
                الهاتف
            </label>
            <input type="text" class="form-control" name="phone"
                value="{{ old('phone', isset($admin) ? $admin->phone : '') }}" placeholder="ادخل رقم الهاتف"
                id="phone">
            @error('phone')
                <p class="text-danger">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group col-md-6 mb-2">
            <label for="email">
                البريد الالكتروني
            </label>
            <input type="email" class="form-control" name="email"
                value="{{ old('email', isset($admin) ? $admin->email : '') }}" placeholder="ادخل البريد الالكتروني"
                id="email">
            @error('email')
                <p class="text-danger">{{ $message }}</p>
            @enderror
        </div>
        @if (empty($adminRole) || empty($adminRole->is_default))
            <div class="form-group col-md-12 mb-2">
                <label for="role">الدور</label>
                <select class="form-control" name="role_id" id="role" required>

                    @foreach ($roles as $id => $name)
                        <option value="{{ $id }}"
                            {{ old('role_id', !empty($adminRole) ? $adminRole->id : '') == $id ? 'selected' : '' }}>
                            {{ $name }}
                        </option>
                    @endforeach
                </select>
                @error('role_id')
                    <p class="text-danger">{{ $message }}</p>
                @enderror
            </div>
        @endif

        @empty($admin)
            <div class="form-group col-md-6 mb-2">
                <label for="password">
                    كلمة السر
                </label>
                <input type="password" class="form-control" name="password" placeholder="ادخل كلمة السر" id="password">
                @error('password')
                    <p class="text-danger">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group col-md-6 mb-2">
                <label for="password_confirmation">تأكيد كلمة السر</label>
                <input id="password_confirmation" type="password"
                    class="form-control @error('password_confirmation') is-invalid @enderror" name="password_confirmation"
                    required>
                @error('password_confirmation')
                    <p class="text-danger">{{ $message }}</p>
                @enderror
            </div>
        @endempty

    </div>


</div>
