@extends('student_dashboard.layout.app')
@section('content')
<div class="content-wrapper">
    <div class="container-full">
        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="form-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="fw-700 fs-16 form-label">first Name</label>
                                <input type="text" class="form-control" disabled value="{{ Auth::user()->first_name }}">
                            </div>
                        </div>
                        <!--/span-->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="fw-700 fs-16 form-label">last name</label>
                                <input type="text" class="form-control" disabled value="{{ Auth::user()->last_name }}">
                            </div>
                        </div>
                        <!--/span-->
                    </div>
                    <!--/row-->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="fw-700 fs-16 form-label">email</label>
                                <div class="input-group">
                                    <div class="input-group-addon"><i class="ti-email"></i></div>
                                    <input type="text" class="form-control" value="{{ Auth::user()->email }}" >
                                </div>
                            </div>
                        </div>
                        <!--/span-->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="fw-700 fs-16 form-label">password</label>
                                <div class="input-group">
                                    <div class="input-group-addon"><i class="ti-key"></i></div>
                                    <input type="password" class="form-control"  >
                                </div>
                            </div>
                        </div>
                        <!--/span-->
                    </div>

                    <!--/row-->
                    <div class="row">
                        <div class="col-md-4">
                            <div class="product-img text-start">
                                <button class="btn btn-success">save</button>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <h4 class="box-title mt-40">Remove My Account</h4>
                        </div>
                    </div>
                </div>
                <div class="form-actions mt-10">
                    <form id="removeAccount" action="{{ route('student-settings.destroy', Auth::user()->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            Remove Account
                        </button>
                    </form>

                </div>
            </div>
        </section>
    </div>
</div>
@endsection
