@extends('adminlte::page')

@section('title', 'Blacklisted Clients - Inter Web Ltd')

@section('content_header')
<h1>Blacklisted Clients</h1>
@stop

@section('content')
<div class="box box-info">
    <div class="box-header with-border">
        <h3 class="box-title">Blacklisted Clients - Cannot be referred again</h3>
        <div class="pull-right">
            <a href="#" data-target="#modal_blacklist_client" data-toggle="modal" class="btn btn-primary"
                data-backdrop="static" data-keyboard="false"><i class="fa fa-plus"></i> Blacklist ID/Phone </a>
        </div>
    </div>

    <div class="box-body">
        <div class="table-responsive">
            <table id="example1" class="table table-hover" style="font-size:12px">
                <thead>
                    <tr>
                        <th>S/N</th>
                        <th>ID Number</th>
                        <th>Phone Number</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($blacklists as $count=> $row)
                    <tr>
                        <td><b>{{$count + 1 }}</b></td>
                        <td>{{$row->id_no}}</td>
                        <td>{{$row->phone}}</td>
                    </tr>
                    @endforeach
                </tbody>

            </table>
        </div>
    </div>
    <!-- /.box-body -->
</div>
@include('modals.users.modal_blacklist_client')
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
