@extends('adminlte::page')

@section('title', 'Payments - Inter Web Ltd')

@section('content_header')
<h1>All Payments</h1>
@stop

@section('content')
<div class="box box-info">
    <div class="box-header with-border">
        <h3 class="box-title">All Payments</h3>
    </div>
    <div class="box-body">
        <div class="table-responsive">
            <table id="example1" class="table table-hover" style="font-size:12px">
                <thead>
                    <tr>
                        <th>S/N</th>
                        <th>Transaction ID</th>
                        <th>Client Name</th>
                        <th>ID Number</th>
                        <th>Phone No</th>
                        <th>Account No</th>
                        <th>Amount(Kshs)</th>
                        <th>Payment Date</th>
                        <th>Comments</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($payments as $count=> $row)
                    <tr>
                        <td>{{$count + 1}}</td>
                        <td><b>{{$row->trans_id }}</b></td>
                        <td><a href="/client/{{$row->id}}/edit">{{$row->name}}</a></td>
                        <td>{{$row->id_no}}</td>
                        <td>{{$row->telephone}}</td>
                        <td>{{$row->account_no}}</td>
                        <td>Kshs {{ number_format($row->payment_amount, 2, '.', ',')}}</td>
                        <td>{{ $row->payment_date}}</td>
                        <td><a href="" data-toggle="modal"
                                data-target="#modal-show-payment-comments_{{$row->payment_id}}"><strong>
                                    <center>View</center>
                                </strong></a></p>
                        </td>
                        <td>
                            <a class="viewModal btn btn-info btn-sm" title="View Payment" href="#" data-toggle="modal"
                                data-target="#modal-view-payment_{{$row->payment_id}}" data-backdrop="static"
                                data-keyboard="false"><i class="fa fa-eye"></i></a> <a
                                class="btn bg-olive btn-sm subsPopup" title="View Client"
                                href="/client/{{$row->user_id}}/edit" data-href="/customer/{{$row->id}}"><i
                                    class="fa fa-user"></i></a></td>
                    </tr>
                    @include('modals.payments.modal-view-payment')
                    @include('modals.payments.modal-show-payment-comments')
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <!-- /.box-body -->
</div>
@stop
@section('css')

<link rel="stylesheet" href="/css/bootstrap-datepicker.min.css">
@stop
@section('js')

<script src="/js/bootstrap-datepicker.min.js"></script>
<script src="/js/select2.full.min.js"></script>
<script src="/js/numberformat.js"></script>
<script>
    $(function() {
        $(".select2").select2()
        $('#example1').DataTable()
    })
</script>
@stop
