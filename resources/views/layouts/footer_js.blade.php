<script src="{{ url('assets/js/vendor.bundle.base.js') }}"></script>
<script src="{{ url('assets/js/Chart.min.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.21.0/jquery.validate.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-toast-plugin/1.3.2/jquery.toast.min.js"></script>
<script src="{{ url('assets/select2/select2.min.js') }}"></script>

<script src="{{ url('assets/js/off-canvas.js') }}"></script>
<script src="{{ url('assets/js/hoverable-collapse.js') }}"></script>
<script src="{{ url('assets/js/misc.js') }}"></script>
<script src="{{ url('assets/js/settings.js') }}"></script>
<script src="{{ url('assets/js/todolist.js') }}"></script>
<script src="{{ url('assets/js/ekko-lightbox.min.js') }}"></script>


<script src="{{ url('assets/bootstrap-table/bootstrap-table.min.js') }}"></script>
<script src="{{ url('assets/bootstrap-table/bootstrap-table-mobile.js') }}"></script>
<script src="{{ url('assets/bootstrap-table/bootstrap-table-export.min.js') }}"></script>
<script src="{{ url('assets/bootstrap-table/fixed-columns.min.js') }}"></script>
<script src="{{ url('assets/bootstrap-table/tableExport.min.js') }}"></script>
<script src="{{ url('assets/bootstrap-table/jspdf.min.js') }}"></script>
<script src="{{ url('assets/bootstrap-table/jspdf.plugin.autotable.js') }}"></script>
<script src="{{ url('assets/bootstrap-table/jquery.tablednd.min.js') }}"></script>
<script src="{{ url('assets/bootstrap-table/reorder-rows.min.js') }}"></script>
<script src="{{ url('assets/bootstrap-table/loadash.min.js') }}"></script>


<script src="{{ url('assets/js/jquery.cookie.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.12.3/sweetalert2.all.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.30.1/moment.min.js"></script>
<script src="{{ url('assets/js/datepicker.min.js') }}"></script>
<script src="{{ url('assets/js/daterangepicker.js') }}"></script>
<script src="{{ url('assets/js/jquery.repeater.js') }}"></script>
<script src="{{ url('assets/tinymce/tinymce.min.js') }}"></script>

<script src="{{ url('assets/color-picker/jquery-asColor.min.js') }}"></script>
<script src="{{ url('assets/color-picker/color.min.js') }}"></script>

<script src="{{ url('assets/js/custom/validate.js') }}"></script>
<script src="{{ url('assets/js/jquery-additional-methods.min.js') }}"></script>
<script src="{{ url('assets/js/custom/function.js') }}?v=1.0.4""></script>
<script src="{{ url('assets/js/custom/custom.js') }}?v=1.0.5"></script>
<script src="{{ url('assets/js/custom/custom-bootstrap-table.js') }}?v=1.0.6"></script>

<script src="{{ url('assets/ckeditor-4/ckeditor.js') }}"></script>
<script src="{{ url('assets/ckeditor-4/adapters/jquery.js') }}" async></script>

@if ($errors->any())
    @foreach ($errors->all() as $error)
        <script type='text/javascript'>
            $.toast({
                text: '{{ $error }}',
                showHideTransition: 'slide',
                icon: 'error',
                loaderBg: '#f2a654',
                position: 'top-right'
            });
        </script>
    @endforeach
@endif

@if (Session::has('success'))
    <script>
        $.toast({
            text: '{{ Session::get('success') }}',
            showHideTransition: 'slide',
            icon: 'success',
            loaderBg: '#f96868',
            position: 'top-right'
        });
    </script>
@endif

<script>
    $(document).on('click', '.deletedata', function() {
        Swal.fire({
            title: "{{ __('delete_title') }}",
            text: "{{ __('confirm_message') }}",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: "{{ __('yes_delete') }}"
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: $(this).attr('data-url'),
                    type: "POST",
                    data:{
                        _token: "{{ csrf_token() }}",
                        _method: 'DELETE',
                    },
                    success: function(response) {
                        if (response['error'] == false) {
                            showSuccessToast(response['message']);
                            $('#table_list').bootstrapTable('refresh');
                        } else {
                            showErrorToast(response['message']);
                        }
                    }
                });
            }
        })
    });
</script>
<script>
    const lang_no = "{{ __('no') }}"
    const lang_yes = "{{ __('yes') }}"
    const lang_cannot_delete_beacuse_data_is_associated_with_other_data = "{{ __('cannot_delete_beacuse_data_is_associated_with_other_data') }}"
    const lang_delete_title = "{{ __('delete_title') }}"
    const lang_delete_warning = "{{ __('delete_warning') }}"
    const lang_yes_delete = "{{ __('yes_delete') }}"
    const lang_cancel = "{{ __('cancel') }}"
    const lang_no_data_found = "{{ __('no_data_found') }}"
    const lang_cash = "{{ __('cash') }}"
    const lang_cheque = "{{ __('cheque') }}"
    const lang_online = "{{ __('online') }}"
    const lang_success = "{{ __('success') }}"
    const lang_failed = "{{ __('failed') }}"
    const lang_pending = "{{ __('pending') }}"
    const lang_select_subject = "{{ __('select_subject') }}"
    const lang_option = "{{ __('option') }}"
    const lang_simple_question = "{{ __('simple_question') }}"
    const lang_equation_based = "{{ __('equation_based') }}"
    const lang_select_option = "{{ __('select') . ' ' . __('option') }}"
    const lang_enter_option = "{{ __('enter') . ' ' . __('option') }}"
    const lang_add_new_question = "{{ __('add_new_question') }}";
    const lang_yes_change_it = "{{ __('yes_change_it') }}"
    const lang_yes_uncheck = "{{ __('yes_unckeck') }}";
    const lang_partial_paid = "{{ __('partial_paid') }}";
    const lang_due_date_on = "{{ __('due_date_on') }}";
    const lang_charges = "{{ __('charges') }}";
    const lang_total_amount = "{{ __('total')}} {{__('amount')}}";
    const lang_paid_on = "{{ __('paid_on')}}";
    const lang_due_charges = "{{ __('due_charges')}}";
    const lang_date = "{{ __('date')}}";
    const lang_pay_in_installment = "{{__('pay_in_installment')}}";
    const lang_active = "{{ __('active') }}";
    const lang_inactive = "{{ __('inactive') }}";
    const lang_enable = "{{ __('enable') }}";
    const lang_disable = "{{ __('disable') }}";
</script>

