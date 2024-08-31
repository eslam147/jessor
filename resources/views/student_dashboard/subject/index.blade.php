@extends('student_dashboard.layout.app')
@section('content')
    <div class="content-wrapper">
        <div class="container-full">
            <!-- Main content -->
            <section class="content">
                <div class="row">
                    @foreach ($subjects as $row)
                        <div class="col-lg-6 col-12">
                            <div class="box pull-up">
                                <div class="box-body bg-img"
                                    style="background-image: url({{ global_asset('student/images/bg-5.png') }});"
                                    data-overlay-light="9">
                                    <div class="d-lg-flex align-items-center justify-content-between">
                                        <div class="d-md-flex align-items-center mb-30 mb-lg-0 w-p100">
                                            <img style="background-color: {{ $row->bg_color }}" src="{{ !empty($row->image) ? $row->image : loadTenantMainAsset('logo1', global_asset('images/no_image_available.jpg')) }}"
                                                class="img-fluid object-fit-cover w-150 p-10" alt="">
                                            <div class="ms-30 w-75">
                                                <h4 class="mb-10 text-bold">{{ $row->name }}</h4>
                                                @if ($row->teachersViaSubject->count())
                                                    <div class="d-flex flex-column mt-10 w-75">
                                                        <div class="d-flex">
                                                            @foreach ($row->teachersViaSubject->take(4) as $teacher)
                                                                <a href="#" title="{{ $teacher->user->full_name }}"
                                                                    class="h-50 l-h-50 me-2 overflow-hidden rounded text-center w-50">
                                                                    <img src="{{ $teacher->user->image }}"
                                                                        class="align-self-end h-50 object-fit-cover"
                                                                        alt="">
                                                                </a>
                                                            @endforeach
                                                            @if ($row->teachersViaSubject->count() > 4)
                                                                <a href="#" title="More"
                                                                    class="h-50 l-h-50 overflow-hidden rounded text-center bg-body-secondary rounded-5 w-50">
                                                                    {{ $row->teachersViaSubject->count() - 4 }}+
                                                                </a>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div>
                                            <a href="{{ route('subjects.show', $row->id) }}"
                                                class="waves-effect waves-light w-p100 btn btn-primary"
                                                style="white-space: nowrap;">View All!</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        </div>
    </div>
@endsection
