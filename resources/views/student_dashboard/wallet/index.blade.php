@extends('student_dashboard.layout.app')
@section('style')
    {{-- <style>
        .bg-green {
            background-color: #6AC977;
            color: #ffffff;
            border-radius: 15px;
        }

        .bg-green-br {
            border-color: #6ac977;
            color: #7E8FC1;
            background-color: transparent;
            border-radius: 15px;
        }
    </style> --}}
@endsection
@section('content')
    <div class="content-wrapper">
        <div class="container-full">
            <!-- Main content -->
            <section class="content">
                <div class="row">

                    <div class="col-12">
                        <div class="row">
                            <div class="col-xl-4 col-12">
                                <div class="box box-body">
                                    <div class="flexbox">
                                        <span class="icon-File text-primary fs-50"><span class="path1"></span><span
                                                class="path2"></span><span class="path3"></span></span>
                                        <span class="fs-40 fw-200">{{ Auth::user()->balance }} </span>
                                    </div>
                                    <div class="text-end">Balance</div>
                                </div>
                            </div>
                            <div class="col-xl-8 col-12">
                                <div class="box pull-up">
                                    <div class="box-body bg-img" style="background-image: url(../images/bg-5.png);"
                                        data-overlay-light="9">
                                        <div class="d-lg-flex align-items-center justify-content-between">
                                            <div class="d-md-flex align-items-center mb-30 mb-lg-0 w-p100">
                                                <img src="{{ global_asset('images/svg-icon/color-svg/custom-14.svg') }}"
                                                    class="img-fluid max-w-150" alt="">
                                                <div class="ms-30">
                                                    <h4 class="mb-10">Boost Your Wallet Balance to Unlock Premium Lessons!
                                                    </h4>
                                                    <p class="mb-0 text-fade">
                                                        Top up your wallet to access advanced lessons and take your learning
                                                        to the next level.
                                                        <br>Unlock exclusive content tailored to your goals.
                                                    </p>
                                                </div>
                                            </div>
                                            <div>
                                                <button type="button"
                                                    class="waves-effect waves-light w-p100 btn  btn-primary add_balance"
                                                    style="white-space: nowrap;">Add Balance!</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="box">
                            <div class="box-header with-border">
                                <h4 class="box-title">Latest Transactions</h4>
                                <div class="box-controls pull-right">
                                    <div class="lookup lookup-circle lookup-right">
                                        <input type="text" name="s">
                                    </div>
                                </div>
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body no-padding">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Invoice</th>
                                                <th>Date</th>
                                                <th>Description</th>
                                                <th>Amount</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($transactions as $transaction)
                                                <tr>
                                                    <td><a href="javascript:void(0)">{{ $transaction->id }}</a></td>
                                                    <td><span class="text-muted"><i class="fa fa-clock-o"></i>
                                                            {{ $transaction->created_at->format('d M, Y') }} </span> </td>
                                                
                                                    <td>
                                                        {{ $transaction->meta['description'] ?? '' }}
                                                    </td>
                                                    <td>
                                                        {{ ($transaction->type != 'deposit' ? '' : '+') . $transaction->amount }}
                                                    </td>
                                                    <td>
                                                        @if ($transaction->type == 'deposit')
                                                            <span
                                                                class="badge badge-pill text-capitalize badge-success">{{ trans('deposit') }}
                                                            </span>
                                                        @elseif ($transaction->type == 'withdraw')
                                                            <span
                                                                class="badge badge-pill text-capitalize badge-danger">{{ trans('withdraw') }}
                                                            </span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <!-- /.box-body -->
                        </div>
                        <!-- /.box -->
                    </div>
                </div>
            </section>
        </div>
    </div>
    <!-- Modal -->
    <div class="modal center-modal fade" id="add_balance_wallet_coupon" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Enter Coupon Code <i class="si-lock si"></i></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="purchaseForm" method="POST" action="{{ route('student_dashboard.wallet.applyCoupon') }}">
                        @csrf
                        <div class="form-group">
                            <label>Code</label>
                            <input type="text" name="coupon_code" class="form-control mt-5">
                        </div>
                    </form>
                </div>
                <div class="modal-footer modal-footer-uniform">
                    <button type="submit" form="purchaseForm" class="btn btn-success " style="width: 100%;">Submit</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        $(document).ready(function() {
            $(".add_balance").click(function(e) {
                e.preventDefault();
                $("#add_balance_wallet_coupon").modal('show');
            })
        });
    </script>
@endsection
