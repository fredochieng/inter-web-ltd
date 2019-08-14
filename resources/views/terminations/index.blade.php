@extends('adminlte::page')

@section('title', 'Terminations - Inter Web Ltd')

@section('content_header')
<h1>All Terminations</h1>
@stop

@section('content')
<div class="box box-info">
    <div class="box-header with-border">
        <h3 class="box-title">All Terminations</h3>
    </div>

    <div class="box-body">
        <div class="table-responsive">
            <table id="example1" class="table table-hover" style="font-size:12px">
                <thead>
                    <tr>
                        <th>S/N</th>
                        <th>Client</th>
                        <th>Account No</th>
                        <th>Before Termination (Kshs)</th>
                        <th>Amount Terminated (Kshs)</th>
                        <th>After Termination (Kshs)</th>
                        <th>Termination Type</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($terminations as $count=> $row)
                    <tr>
                        <td><b>{{$count + 1 }}</b></td>
                        <td>{{$row->name}}</td>
                        <td>{{$row->account_no}}</td>
                        <td>Kshs {{ number_format($row->before_ter, 2, '.', ',')}}</td>
                        <td>Kshs {{ number_format($row->amount_ter, 2, '.', ',') }}</td>
                        <td>Kshs {{ number_format($row->after_ter, 2, '.', ',') }}</td>
                        @if($row->termination_type == 1)
                        <td>Partial Termination</td>
                        @else
                        <td>Partial Termination</td>
                        @endif
                        <td>{{$row->termination_date}}</td>
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
