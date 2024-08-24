@extends('layouts.master')

@section('title')
    {{ __('wallets') }}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('manage') . ' ' . __('wallets') }}
            </h3>
        </div>
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">
                            {{ __('add_balance') . ' ' . __('to_student_wallet') }}
                        </h4>
                        <form class="pt-3 wallet-create-form" id="create-form" action="{{ route('wallet.updateBalance','deposit') }}"
                            method="POST" novalidate="novalidate">
                            <div class="row">
                                <div class="form-group col-sm-6 col-md-12">
                                    <label>{{ __('select_student') }} <span class="text-danger">*</span></label>
                                    {!! Form::select('user_id', $students->pluck('full_name', 'id'), null, [
                                        'required',
                                        'placeholder' => __('select_student'),
                                        'class' => 'form-control user_id',
                                    ]) !!}
                                    {{-- <input name="amount" type="number" placeholder="{{ __('amount') }}"
                                        class="form-control" required /> --}}
                                </div>
                                <div class="form-group col-sm-6 col-md-12">
                                    <label>{{ __('amount') }} <span class="text-danger">*</span></label>
                                    <input name="amount" type="number" placeholder="{{ __('amount') }}"
                                        class="form-control" required />
                                </div>
                            </div>
                            <input class="btn btn-theme" id="create-btn" type="submit" value="{{ __('submit') }}">
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">
                            {{ __('list') . ' ' . __('wallets') }}
                        </h4>
                        <table aria-describedby="mydesc" class='table' id='table_list' data-toggle="table"
                            data-url="{{ route('wallet.list') }}" data-click-to-select="true" data-side-pagination="server"
                            data-pagination="true" data-page-list="[5, 10, 20, 50, 100, 200]" data-search="true"
                            data-toolbar="#toolbar" data-show-columns="true" data-show-refresh="true"
                            data-fixed-columns="true" data-fixed-number="2" data-fixed-right-number="1"
                            data-trim-on-search="false" data-mobile-responsive="true" data-sort-name="id"
                            data-sort-order="desc" data-maintain-selected="true" data-query-params="queryParams"
                            data-show-export="true"
                            data-export-options='{"fileName": "wallet-list-{{ date('d-m-y') }}","ignoreColumn": ["operate"]}'>
                            <thead>
                                <tr>
                                    <th scope="col" data-field="id" data-sortable="true" data-visible="false">
                                        {{ __('id') }}</th>
                                    <th scope="col" data-field="no" data-sortable="false">{{ __('no.') }}</th>
                                    <th scope="col" data-field="name" data-sortable="false">{{ __('name') }}</th>
                                    <th scope="col" data-field="balance" data-sortable="false">{{ __('balance') }}</th>
                                    <th scope="col" data-field="created_at" data-sortable="true" data-visible="false">
                                        {{ __('created_at') }}</th>
                                    <th scope="col" data-field="updated_at" data-sortable="true" data-visible="false">
                                        {{ __('updated_at') }}</th>
                                    <th scope="col" data-field="operate" data-sortable="false"
                                        data-events="actionEvents">{{ __('action') }}</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
            <!-- Modal -->
            <div class="modal fade" id="addBalanceModal" data-backdrop="static" data-keyboard="false" tabindex="-1"
                role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">{{ __('add_balance') }}</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form class="pt-3 wallet-form wallet_balance_update" id="wallet-form" action="#"
                            novalidate="novalidate">
                            @csrf
                            <input type="hidden" name="user_id" id="user_id" value="" />
                            <input type="hidden" name="type" class="type" value="deposit" />
                            <input type="hidden" disabled id="wallet_balance" value="" />
                            <div class="modal-body">
                                <div class="row">
                                    <div class="form-group col-sm-6 col-md-12">
                                        <label for="student">{{ __('student') }}</label>
                                        <input name="student" id="student_name" type="text"
                                            placeholder="{{ __('student') }}" class="form-control" disabled />
                                    </div>
                                    <div class="form-group col-sm-6 col-md-12">
                                        <label for="amount">{{ __('amount') }} <span
                                                class="text-danger">*</span></label>
                                        <input name="amount" id="amount" type="number" value="1"
                                            min="1" step="0.01" placeholder="{{ __('balance_amount') }}"
                                            class="form-control amount" required />
                                    </div>
                                    <div class="form-group col-sm-6 col-md-12">
                                        <label for="new_balance">{{ __('balance_will_be') }} </label>
                                        <input id="new_balance" type="number" class="form-control" disabled />
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary"
                                    data-dismiss="modal">{{ __('close') }}</button>
                                <input class="btn btn-theme" type="submit" value="{{ __('add_money') }}" />
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="withdrawBalanceModal" data-backdrop="static" data-keyboard="false" tabindex="-1"
                role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">{{ __('withdraw_balance') }}</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form class="pt-3 withdraw-wallet-form wallet_balance_update" id="withdraw-wallet-form" action="{{ route('wallet.updateBalance','withdraw') }}"
                            novalidate="novalidate">
                            @csrf
                            <input type="hidden" name="user_id" id="user_id" value="" />
                            <input type="hidden" name="type" class="type" value="withdraw" />

                            <input type="hidden" disabled id="wallet_balance" value="" />
                            <div class="modal-body">
                                <div class="row">
                                    <div class="form-group col-sm-6 col-md-12">
                                        <label for="student">{{ __('student') }}</label>
                                        <input name="student" id="student_name" type="text"
                                            placeholder="{{ __('student') }}" class="form-control" disabled />
                                    </div>
                                    <div class="form-group col-sm-6 col-md-12">
                                        <label for="amount">{{ __('amount') }} <span
                                                class="text-danger">*</span></label>
                                        <input name="amount" id="amount" type="number" value="1"
                                            min="1" step="0.01" placeholder="{{ __('balance_amount') }}"
                                            class="form-control amount" required />
                                    </div>
                                    <div class="form-group col-sm-6 col-md-12">
                                        <label for="new_balance">{{ __('balance_will_be') }} </label>
                                        <input id="new_balance" type="number" class="form-control" disabled />
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary"
                                    data-dismiss="modal">{{ __('close') }}</button>
                                <input class="btn btn-theme" type="submit" value="{{ __('withdraw_money') }}" />
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script type="text/javascript">
        $('.wallet_balance_update').submit(function(e) {
            e.preventDefault();
            let form = $(this);
            // #TODO: Add Translation
            Swal.fire({
                title: "Are you sure?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, delete it!",
            }).then((result) => {
                if (result.isConfirmed) {
                    let url = `{{ route('wallet.updateBalance',':type') }}`.replace(':type', form.find('.type').val());
                    let data = new FormData(form[0]);

                    function successCallback(response) {
                        form.closest('.modal').modal('hide');
                        $("#table_list").bootstrapTable("refresh");
                        showSuccessToast(response.message);
                    }
                    function errorCallback(response) {
                        showErrorToast(response.message);
                    }
                    ajaxRequest(
                        "POST",
                        url,
                        data,
                        null,
                        successCallback,
                        errorCallback
                    );
                }
            });
        });
        window.actionEvents = {
            'click .wallet_deposit': function(e, value, row, index) {
                console.log('clicked');

                $('#addBalanceModal #user_id').val(row.id);
                $('#student_name').val(row.name);
                $('#wallet_balance').val(row.balance);
                $('#new_balance').val(row.balance);
                $('#addBalanceModal').modal('show');
            },
            'click .wallet_withdraw': function(e, value, row, index) {
                $('#withdrawBalanceModal #user_id').val(row.id);
                $('#student_name').val(row.name);
                $('#new_balance,#wallet_balance').val(row.balance);
                $('#withdrawBalanceModal').modal('show');
            },
        };
        $('#addBalanceModal .amount').change(function() {
            let amount = parseFloat($(this).val());
            let walletBalance = parseFloat($('#wallet_balance').val());
            $('#new_balance').val(amount + walletBalance);
        });
        $('#withdrawBalanceModal .amount').change(function() {
            let amount = parseFloat($(this).val());
            let walletBalance = parseFloat($('#wallet_balance').val());
            $('#new_balance').val(amount + walletBalance);
        });

        $('.user_id').select2()
        function queryParams(p) {
            return {
                limit: p.limit,
                sort: p.sort,
                order: p.order,
                offset: p.offset,
                search: p.search
            };
        }
    </script>
@endsection
