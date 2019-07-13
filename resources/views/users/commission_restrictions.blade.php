@extends('adminlte::page')

@section('title', 'Commission Restricted Clients - Inter Web Ltd')

@section('content_header')
<h1>Commission Restricted Clients</h1>
@stop

@section('content')
<div class="box box-info">
    <div class="box-header with-border">
        <h3 class="box-title">Commission Restricted Clients</h3>
        <div class="pull-right">
            <a href="#" data-target="#modal_restrict_referal" data-toggle="modal" class="btn btn-primary"
                data-backdrop="static" data-keyboard="false"><i class="fa fa-plus"></i> Restrict Referal </a>
        </div>
    </div>

    <div class="box-body">
        <div class="table-responsive">
            <table id="example1" class="table table-hover" style="font-size:12px">
                <thead>
                    <tr>
                        <th>S/N</th>
                        <th>Client Name</th>
                        <th>ID Number</th>
                        <th>Phone Number</th>
                        <th>Account Number</th>
                        <th>Commission Earnings</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($clients as $count=> $row)
                    <tr>
                        <td><b>{{$count + 1 }}</b></td>
                        <td>{{$row->name}}</td>
                        <td>{{$row->id_no}}</td>
                        <td>{{$row->phone}}</td>
                        <td>{{$row->account_no}}</td>
                        <td>{{$row->comm_times}}</td>
                        <td>
                            <a href="" data-backdrop="static" data-keyboard="false" data-toggle="modal"
                                data-target="#modal_edit_restriction_{{$row->rest_id}}"
                                class="btn btn-xs btn-success"><i class="glyphicon glyphicon-edit"></i> Edit</a>
                        </td>
                    </tr>
                    @include('modals.users.modal_edit_restriction')
                    @endforeach
                </tbody>

            </table>
        </div>
    </div>
    <!-- /.box-body -->
</div>
@include('modals.users.modal_restrict_referal')
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
