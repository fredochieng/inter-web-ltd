@extends('adminlte::page')

@section('title', 'Payments - Inter Web Ltd')

@section('content_header')
<h1>All Payments</h1>
@stop

@section('content')
<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">All Payments</h3>
        <div class="box-tools">
            <a href="#" data-target="#modal_new_payment" data-toggle="modal" class="btn btn-block btn-primary" data-backdrop="static" data-keyboard="false"><i class="fa fa-plus"></i> NEW PAYMENT </a>
        </div>
        <!-- <div class="box-tools">
            <a href="/payments/create" data-toggle="modal" class="btn btn-block btn-primary"><i
                    class="fa fa-plus"></i> NEW PAYMENT </a>
        </div> -->
    </div>
    <div class="box-body">
        <div class="table-responsive">
            <table id="example1" class="table table-no-margin" style="font-size:12px">
                <thead>
                    <tr>
                        <th>S/N</th>
                        <th>Transaction ID</th>
                        <th>Account No</th>
                        <th>Payment Amount(Kshs)</th>
                        <th>Payment Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($payments as $count=> $row)
                    <tr>
                        <td>{{$count + 1}}</td>
                        <td><b>{{$row->trans_id }}</b></td>
                        <td>{{$row->account_no}}</td>
                        <td>Kshs {{ number_format($row->payment_amount, 2, '.', ',')}}</td>
                        <td>{{ $row->payment_date}}</td>
                        <td>
                            <a href="" data-toggle="modal" data-target="#modal-edit-customer_{{$row->id}}" class="btn btn-xs btn-success"><i class="glyphicon glyphicon-edit"></i> Edit</a>
                            {{Form::hidden('_method','DELETE')}}
                            <a href="" data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#modal-delete-user_{{$row->id}}" class="btn btn-xs btn-danger delete_user_button">
                                <i class="glyphicon glyphicon-trash"></i> Delete</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <!-- /.box-body -->
</div>
@include('modals.payments.modal_new_payment')
@include('modals.payments.modal_search_client')
@stop
@section('css')

<link rel="stylesheet" href="/css/bootstrap-datepicker.min.css">
@stop
@section('js')

<script src="/js/bootstrap-datepicker.min.js"></script>
<script src="/js/select2.full.min.js"></script>
<script src="/js/numberformat.js"></script>

<script>
    $(document).ready(function() {
        $("#clients-list").on('click', '.action-select-client', function(e) {
            var client = JSON.parse($(this).attr('data-row'));
            $("#acc").val(client.account_no);
            $("#account_id").val(client.account_no_id);
            $("#name").val(client.name);
            $("#user_id").val(client.user_id);
            $("#id_no").val(client.id_no);
            $("#telephone").val(client.telephone);
            $("#method_name").val(client.method_name);

            if(client.method_name == 'BANK ACCOUNT'){
            //   console.log('Yeaaaaaaaahhh....it is bank');
              $("#bank_name_div").removeClass("hide");
                    $("#bank_account_div").removeClass("hide");
            }else{
                // console.log('Its is MPESA');
                $("#bank_name_div").addClass("hide");
                    $("#bank_account_div").addClass("hide");
                    $("#pay_mpesa_no_div").removeClass("hide");
            }
            $("#user_date").val(client.user_date);
            $("#pay_mpesa_no").val(client.pay_mpesa_no);
            $("#bank_name").val(client.bank_name);
            $("#pay_bank_acc").val(client.pay_bank_acc);

            $("#tot_payable_amnt").val('Kshs ' + number_format(client.tot_payable_amnt, 2));
            if(client.monthly_amount == 0.00){
                $("#monthly_amount").val('Kshs ' + number_format(client.next_pay_comp, 2));
             }else{
                $("#monthly_amount").val('Kshs ' + number_format(client.monthly_amount, 2));
             }
            $('#modal_search_client').modal('hide')
        });
    });
</script>
<script>
    $(function() {
        $(".select2").select2()
        $('#example1').DataTable()
        $('#example2').DataTable()
    })
</script>
@stop
