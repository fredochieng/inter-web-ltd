@extends('adminlte::page')

@section('title', 'Payments - BManager')

@section('content_header')
<h1>All Payments</h1>
@stop

@section('content')
<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">All Payments</h3>
        <div class="box-tools">
            <a href="/payments/create" data-toggle="modal" class="btn btn-block btn-primary"><i
                    class="fa fa-plus"></i> ADD </a>
        </div>
    </div>

    <div class="box-body">
        <div class="table-responsive">
            <table id="example1" class="table table-bordered table-striped dataTable" style="font-size:12px">
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
                            <a href="" data-toggle="modal" data-target="#modal-edit-customer_{{$row->id}}"
                                class="btn btn-xs btn-success"><i class="glyphicon glyphicon-edit"></i> Edit</a>
                            {{Form::hidden('_method','DELETE')}}
                            <a href="" data-backdrop="static" data-keyboard="false" data-toggle="modal"
                                data-target="#modal-delete-user_{{$row->id}}"
                                class="btn btn-xs btn-danger delete_user_button">
                                <i class="glyphicon glyphicon-trash"></i> Delete</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                {{--  <tfoot>
                    <tr class="bg-gray font-17 footer-total text-center">
                        <td colspan="2" rowspan="1"><strong>Total:</strong></td>
                        <td rowspan="1" colspan="1"><span class="display_currency" id="footer_sale_total"
                                data-currency_symbol="true">
                                <strong> Kshs {{ $sum_investments }}</strong></span></td>
                        <td rowspan="1" colspan="1"><span class="display_currency" id="footer_sale_total"
                                data-currency_symbol="true">
                            </span></td>
                        <td rowspan="1" colspan="1"><span class="display_currency" id="footer_total_paid"
                                data-currency_symbol="true">
                                <strong> Kshs {{ $sum_payout }}</span></td>
                        <td rowspan="1" colspan="1"><span class="display_currency" id="footer_total_remaining"
                                data-currency_symbol="true"><strong> Kshs {{ $sum_total_payout }}</span></td>
                        <td rowspan="1" colspan="2"></td>
                    </tr>
                </tfoot>  --}}
            </table>
        </div>
    </div>
    <!-- /.box-body -->
</div>
@stop
@section('css')
<link rel="stylesheet" href="/css/admin_custom.css">
<link rel="stylesheet" href="/css/bootstrap-datepicker.min.css">
@stop
@section('js')

<script src="/js/bootstrap-datepicker.min.js"></script>
<script src="/js/select2.full.min.js"></script>

<script>
    $(function () {
          $(".select2").select2()
          $('#example1').DataTable()
 })
</script>

@stop
