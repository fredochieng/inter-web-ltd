@extends('adminlte::page')

@section('title', 'Customer Report - BManager')

@section('content_header')
<h1>Reports<small>Customer report</small></h1>
@stop

@section('content')
<div class="box box-primary" style="font-family: Source Sans Pro','Helvetica Neue',Helvetica,Arial,sans-serif; font-weight:400">
    <div class="box-header with-border">
        <h3 class="box-title">Customer Report</h3>
    </div>

    <div class="box-body">
        <div class="table-responsive">

            <table id="example1" class="table table-bordered table-striped dataTable" style="font-size:12px">
                <thead>
                    <tr>
                        <th>S/N</th>
                        <th>Customer</th>
                        <th>Account No</th>
                        <th>Total Investments(Kshs)</th>
                        <th>Due Interests(Kshs)</th>
                        <th>Due Payments(Kshs)</th>
                    </tr>
                </thead>
                <tbody>
                     @foreach ($records as $count=> $row)
                    <tr>
                        <td>{{ $count + 1 }}</td>
                        <td><b><a href="/customer/{{$row->id}}">{{ $row->name }}</a></b></td>
                        <td>{{$row->account_no}}</td>
                        <td>Kshs {{ number_format($row->user_sum, 2, '.', ',')}}</td>
                        <td>Kshs {{ number_format($row->user_payout, 2, '.', ',')}}</td>
                        <td>Kshs {{ number_format($row->total_due_payments, 2, '.', ',')}}</td>
                    </tr>
                    @endforeach
                </tbody>
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
