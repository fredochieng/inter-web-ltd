@extends('adminlte::page')

@section('title', 'Investments - Inter Web Ltd')

@section('content_header')
<h1>All Investments</h1>
@stop

@section('content')
<div class="box box-info">
    <div class="box-header with-border">
        <h3 class="box-title">All Investments</h3>
    </div>

    <div class="box-body">
        <div class="table-responsive">
            <table id="example1" class="table table-hover" style="font-size:12px">
                <thead>
                    <tr>
                        <!-- <th>Transaction ID</th> -->
                        <th>Investment Date</th>
                        <!-- <th>Duration</th> -->
                        <th>Investment Type</th>
                        <th>Client</th>
                        <th>Account No</th>
                        <th>Amount(Kshs)</th>
                        <th>Status</th>
                        <th>Served By</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($investments as $count=> $row)
                    <tr>
                        <!-- <td><b>{{$row->trans_id }}</b></td> -->
                        <td>{{ date('Y-m-d', strtotime($row->inv_date))}}</td>
                        <!-- <td>{{ $row->investment_duration}} Months</td> -->
                        <td>{{$row->inv_type}}</td>
                        <td><a href="/client/{{$row->user_id}}/edit">{{$row->name}}</a></td>
                        <td>{{$row->account_no}}</td>
                        <!-- <td>{{$row->created_by_name}}</td> -->
                        <td>Kshs {{ number_format($row->investment_amount, 2, '.', ',')}}</td>
                        <!-- <td>Kshs {{ number_format($row->monthly_amount, 2, '.', ',') }}</td> -->
                        <!-- <td>Kshs {{ number_format($row->tot_payable_amnt, 2, '.', ',') }}</td> -->
                        <!-- <td>{{ date('Y-m-d', strtotime($row->last_pay_date))}}</td> -->
                        @if($row->inv_status_id == 0)
                        <td><span class="label label-warning">Pending</span></td>
                        @else
                        <td><span class="label label-success">Approved</span>
                        </td>
                        @endif
                        <td>{{$row->created_by_name}}</td>
                        <td>
                            <a class="viewModal btn btn-info btn-sm" title="View Investment" href="#"
                                data-toggle="modal" data-target="#modal-view-investment_{{$row->investment_id}}"
                                data-backdrop="static" data-keyboard="false"><i class="fa fa-eye"></i></a> <a
                                class="btn btn-primary btn-sm editTrans" title="Approve Investment" href="#"><i
                                    class="fa fa-pencil"></i></a> <a class="btn btn-danger btn-sm"
                                title="Delete Transaction" href=""><i class="fa fa-trash"></i></a> <a
                                class="btn bg-olive btn-sm subsPopup" title="View Client"
                                href="/client/{{$row->user_id}}/edit" data-href="/customer/{{$row->id}}"><i
                                    class="fa fa-user"></i></a></td>
                    </tr>
                    @include('modals.investments.modal-view-investment')
                    @endforeach
                </tbody>
                {{-- <tfoot>
                    <tr class="bg-gray font-17 footer-total text-center">
                        <td colspan="4" rowspan="1"><strong>Total:</strong></td>
                        <td rowspan="1" colspan="1"><span class="display_currency" id="footer_sale_total" data-currency_symbol="true">
                               <strong> Kshs {{ $sum_investments }}</strong></span></td>
                <td rowspan="1" colspan="3"><span class="display_currency" id="footer_total_paid"
                        data-currency_symbol="true">
                        <strong> Kshs {{ $sum_total_payout }}</span></td>
                <td rowspan="1" colspan="1"><span class="display_currency" id="footer_total_remaining"
                        data-currency_symbol="true"><strong></span></td>
                <td rowspan="1" colspan="2"></td>
                </tr>
                </tfoot> --}}
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
