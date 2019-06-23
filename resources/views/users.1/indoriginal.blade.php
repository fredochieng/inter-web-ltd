@extends('adminlte::page')

@section('title', 'Clients - Inter Web Ltd')

@section('content_header')
<h1>Clients<small>Client Management</small></h1>
@stop
@section('content')
<div class="row">
    <div class="col-md-12">
        <!-- Custom Tabs (Pulled to the right) -->
        <div class="nav-tabs-custom">
            <div class="tab-content">
                <div class="tab-pane active" id="tab-active-clients">
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="box box-success">
                                <div class="box-body">
                                    <div class="table-responsive">
                                       <table id="example1" class="table no-margin">
                                            <thead>
                                                <tr>
                                                    <th>S/N</th>
                                                    <th>Name</th>
                                                    <th>Email</th>
                                                    <th>Telephone</th>
                                                    <th>ID Number</th>
                                                    <th>Account No</th>
                                                    <th>Registered At</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($clients as $count=> $row)
                                                <tr>
                                                    <td>{{$count + 1}}</td>
                                                    <td><a href="/client/{{$row->user_id}}/edit">{{$row->name}}</a></td>
                                                    <td><a href="">{{$row->email}}</a></td>
                                                    <td>{{$row->telephone}}</td>
                                                    <td>{{$row->id_no}}</td>
                                                    <td>{{ $row->account_no }}</td>
                                                    <td>{{ $row->created_at}}</td>
                                                    <td>
                                                        <a href="" data-toggle="modal" data-target="#modal-edit-customer_{{$row->id}}"
                                                            class="btn btn-xs btn-success"><i class="glyphicon glyphicon-edit"></i> Edit</a>
                                                        {{Form::hidden('_method','DELETE')}}
                                                        <a href="" data-backdrop="static" data-keyboard="false" data-toggle="modal"
                                                            data-target="#modal-delete-user_{{$row->id}}" class="btn btn-xs btn-danger delete_user_button">
                                                            <i class="glyphicon glyphicon-trash"></i> Delete</a>
                                                    </td>
                                                </tr>
                                                @include('modals.users.modal-edit-customer')
                                                @include('modals.users.modal-delete-user')
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <!-- /.tab-content -->
        </div>
        <!-- nav-tabs-custom -->
    </div>
    <!-- /.col -->
</div>
@stop
@section('css')
<link rel="stylesheet" href="/css/admin_custom.css">
<link rel="stylesheet" href="/css/bootstrap-datepicker.min.css">
@stop
@section('js')

<script src="/js/bootstrap-datepicker.min.js"></script>
<script src="/js/bootbox.min.js"></script>
<script>
    $(document).ready(function(){

    $("#pay_bank_id").change(function() {
            var value = $(this).val();
            if (value != 0 ) {
            $("#bank_payment_acc").removeClass("hide");
            }
            else{
            $("#bank_payment_acc").addClass("hide");
            }
    });

        $("#pay_mode_id").change(function() {
                    var val = $(this).val();
                    if (val == 1 ) {
                    $("#mpesa_number_div").removeClass("hide");
                    }
                    else{
                    $("#mpesa_number_div").addClass("hide");
                    }

                    if (val == 2 ) {
                    $("#bank_payment_div").removeClass("hide");
                   // $("#bank_payment_acc").removeClass("hide");
                    }
                    else{
                    $("#bank_payment_div").addClass("hide");
                //    $("#bank_payment_acc").addClass("hide");
                    }
        });
});

</script>
<script>
    $(function ()
    {
    $(".select2").select2()
    $('#example1').DataTable()
    $('#example2').DataTable()
    $('#example3').DataTable()
    $('#example4').DataTable()

    $('.dob').datepicker( {
    format: 'yyyy-mm-dd',
    orientation: "bottom",
    autoclose: true,
    showDropdowns: true,
    todayHighlight: true,
    toggleActive: true,
    clearBtn: true,
    })
});

</script>
@stop
