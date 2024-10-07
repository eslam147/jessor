@extends('centeral.admin.layouts.master')

@push('title')
    Show Admin
@endpush


@section('content')
    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12" style="margin-bottom:24px;">
            <div class="card">
                <div class="card-header">
                    <h3>عرض بيانات الادمن - {{ $admin->full_name }} </h3>
                </div>
                <div class="card-body">
                    <div class="row">

                        <div class="row">
                            <div class="form-group col-md-6 mb-2">
                                <label for="first_name">
                                    الاسم الاول
                                </label>
                                <input readonly type="text" class="form-control" name="first_name"
                                    value="{{ old('first_name', isset($admin) ? $admin->first_name : '') }}" placeholder="ادخل الاسم الاول"
                                    id="first_name">
                              
                            </div>
                            <div class="form-group col-md-6 mb-2">
                                <label for="last_name">
                                    الاسم الاخير
                                </label>
                                <input readonly type="text" class="form-control" name="last_name"
                                    value="{{ old('last_name', isset($admin) ? $admin->last_name : '') }}" placeholder="ادخل الاسم الثاني"
                                    id="last_name">
                           
                            </div>
                    
                            <div class="form-group col-md-6 mb-2">
                                <label for="phone">
                                    الهاتف
                                </label>
                                <input readonly type="text" class="form-control" name="phone"
                                    value="{{ old('phone', isset($admin) ? $admin->phone : '') }}" placeholder="ادخل رقم الهاتف"
                                    id="phone">
                          
                            </div>
                    
                            <div class="form-group col-md-6 mb-2">
                                <label for="email">
                                    البريد الالكتروني
                                </label>
                                <input readonly type="email" class="form-control" name="email"
                                    value="{{ old('email', isset($admin) ? $admin->email : '') }}" placeholder="ادخل البريد الالكتروني"
                                    id="email">
                              
                            </div>
                            <div class="form-group col-md-12 mb-2">
                                <label for="role">الدور</label>
                                <select readonly class="form-control" name="role_id" id="role" required>
                                    @foreach ($roles as $id => $name)
                                        <option value="{{ $id }}" {{ old('role_id',(!empty($adminRole)? $adminRole->id : '')) == $id ? 'selected' : '' }}>
                                            {{ $name }}
                                        </option>
                                    @endforeach
                                </select>
                         
                            </div>
                          
                          
                        </div>
                    
                    
                    </div>
                    
                </div>
            </div>
    </div>
@endsection


