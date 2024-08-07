@extends('student_dashboard.layout.app')
@section('style')

<style>
    .bg-green{
        background-color: #6AC977;
        color: #ffffff;
        border-radius: 15px;
    }

    .bg-green-br{
        border-color: #6ac977;
        color: #7E8FC1;
        background-color: transparent;
        border-radius: 15px;
    }
</style>
@endsection
@section('content')
<div class="content-wrapper">
    <div class="container-full">
      <!-- Main content -->
      <section class="content">
            <div class="row">
                @foreach ($lessons as $row)
                <div class="col-4" >
                    <div class="box pull-up">
						<div class="box-body">
							<p class="mb-0 fs-18"> {{ $row->name }} </p>
							<div class="d-flex justify-content-between mt-30">
								<div>
									<p class="mb-0 text-fade">lesson description</p>
									<p class="mb-0">{{ $row->description }}</p>
								</div>
								<div>
                                    @if ($row->isFree())
                                        <p class="mb-5 fw-600">55%</p>
                                        <div class="progress progress-sm mb-0 w-100">
                                            <div class="progress-bar progress-bar-primary" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: 55%">
                                            </div>
                                        </div>
                                    @else
                                        @if($row->enrollments_count > 0)
                                            <p class="mb-5 fw-600">55%</p>
                                            <div class="progress progress-sm mb-0 w-100">
                                                <div class="progress-bar progress-bar-primary" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: 55%">
                                                </div>
                                            </div>
                                        @else
                                        <i style="font-size: 25px;" class="si-lock si text-danger" ></i>
                                        @endif
                                    @endif
								</div>
							</div>
                            <div class="bg-primary mt-5 rounded">
                                @if($row->isFree())
                                    <a href="{{ route('topics.show',$row->id) }}" >
                                        <h5 class="text-white text-center p-10"> start now </h5>
                                    </a>
                                @else
                                @if($row->enrollments_count > 0)
                                        <a href="{{ route('topics.show',$row->id) }}" >
                                            <h5 class="text-white text-center p-10"> start now </h5>
                                        </a>
                                    @else
                                        <a href="javascript:void(0)" class="locked-btn" data-id="{{ $row->id }}" data-bs-toggle="modal" data-bs-target="#modal-center" data-animation="shake" >
                                            <h5 class="text-white text-center  p-10"> Locked Lesson </h5>
                                        </a>
                                    @endif
                                @endif
							</div>
						</div>
					</div>
                </div>
                @endforeach
            </div>
      </section>
    </div>
</div>
<!-- Modal -->
<div class="modal center-modal fade" id="modal-center" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Lesson Is Locked <i class="si-lock si" ></i></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="purchaseForm" method="POST" action="{{ route('enroll.store') }}" >
                    @csrf
                    <div class="form-group" >
                        <label>Purchase Code</label>
                        <input type="text" name="purchase_code" class="form-control mt-5" >
                    </div>
                    <input type="hidden" id="LessonId" name="lesson_id" value="">
                </form>
            </div>
            <div class="modal-footer modal-footer-uniform">
                <button type="submit" form="purchaseForm" class="btn btn-success" style="width: 100%;" >Unlock</button>
            </div>
        </div>
    </div>
</div>
<!-- /.modal -->
@include('sweetalert::alert')
@endsection
@section('script')
    <script>
        $(document).ready(function() {
            // Attach click event listener to buttons with class 'locked-btn'
            $('.locked-btn').on('click', function() {
                // Get the data-id attribute from the clicked button
                var id = $(this).data('id');

                // Pass the id to the hidden input field with the specified ID
                $('#LessonId').val(id);
            });
        });

    </script>
@endsection
