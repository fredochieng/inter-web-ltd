@extends('adminlte::page')

@section('title', 'Topups - Inter Web Ltd')

@section('content_header')
<h1>All Topups</h1>
@stop

@section('content')
<div class="box box-info">
    <div class="box-header with-border">
        <h3 class="box-title">All Topups</h3>
    </div>

    <div class="box-body">
        <div class="table-responsive">
            <table id="example1" class="table table-hover" style="font-size:12px">
                <thead>
                    <tr>
                        {{-- <th>S/N</th> --}}
                        <th>Account No</th>
                        <th>Client Name</th>
                        <th>ID Number</th>
                        <th>Phone Number</th>
                        <th>Amount(Kshs)</th>
                        <th>Topup Mode</th>
                        <th>Served By</th>
                        <th>Topup Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($topups as $count=> $row)
                    <tr>
                        {{-- <td><b>{{$count + 1 }}</b></td> --}}
                        <td>{{$row->account_no}}</td>
                        <td><a href="/client/{{$row->id}}/edit">{{$row->name}}</a></td>
                        <td>{{$row->id_no}}</td>
                        <td>{{$row->telephone}}</td>
                        <td>Kshs {{ number_format($row->topup_amount, 2, '.', ',')}}</td>
                        <td>{{$row->inv_mode}}</td>
                        <td>{{$row->served_by_name}}</td>
                        <td>{{$row->topped_date}}</td>
                        <td>
                            <a class="viewModal btn btn-info btn-sm" title="View Topup" href="#" data-toggle="modal"
                                data-target="#modal-view-topup_{{$row->topup_id}}" data-backdrop="static"
                                data-keyboard="false"><i class="fa fa-eye"></i></a> <a class="btn btn-danger btn-sm"
                                title="Delete Transaction" href=""><i class="fa fa-trash"></i></a> <a
                                class="btn bg-olive btn-sm subsPopup" title="View Client"
                                href="/client/{{$row->user_id}}/edit" data-href="/customer/{{$row->id}}"><i
                                    class="fa fa-user"></i></a></td>
                    </tr>
                    @include('modals.topups.modal-view-topup')
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
