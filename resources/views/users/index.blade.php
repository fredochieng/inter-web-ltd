@extends('adminlte::page')

@section('title', 'Clients - Inter Web Ltd')

@section('content_header')
<h1>Clients<small>Client Management</small></h1>
@stop
@section('content')
<div class="box box-info color-palette-box">
    <div class="box-header with-border">
        <h3 class="box-title"> Search Client</h3>
        <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
        </div>
    </div>
    <div class="box-body">

        {!! Form::open(['action'=>'UserController@index','method'=>'get','class'=>'form form-horizontal
        subscriber_form','enctype'=>'multipart/form-data']) !!}
        <div class="col-md-5">
            <?php
		  	$array_clients_by=array(
				'id_no'=>'ID Number',
				'account_no'=>'Account Number',
                'telephone'=>'Phone Number',
				'name'=>'Name',
			);

		  ?>

            <?php
				$find_client_by='';	$find_value='';
				if(isset($_GET['find_client_by'])){ $find_client_by=$_GET['find_client_by']; }
				if(isset($_GET['find_value'])){ $find_value=$_GET['find_value']; }
			 ?>

            <div class="form-group">
                {{Form::label('find_client_by', 'Search Clients By',['class'=>'col-sm-4 control-label'])}}
                <div class="col-sm-8">
                    {{ Form::select('find_client_by', $array_clients_by,$find_client_by, ['class' => 'form-control','style'=>'width:100%']) }}
                </div>
            </div>
        </div>

        <div class="col-md-5">
            <div class="form-group">
                {{Form::label('find_value', 'ID Number',['class'=>'col-sm-4 control-label find_value_label'])}}
                <div class="col-sm-8">
                    {{Form::text('find_value', $find_value,['class'=>'form-control','placeholder'=>'Enter ID Number of the client'])}}
                </div>
            </div>
        </div>

        <div class="col-md-2">
            <button type="submit" class="btn btn-block btn-info" name="find_client"><strong><i
                        class="fa fa-fw fa-search"></i> SEARCH</strong></button>

        </div>

        {!! Form::close() !!}

    </div>
</div>

@if(count($clients) > 0)

<div class="box box-info color-palette-box">
    <div class="box-header with-border">
        <h3 class="box-title"> Clients List</h3>
        <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
        </div>
    </div>
    <div class="box-body">
        <p>{{count($clients)}} Records found matching your search query</p>
        <table class="table table-no-margin" id="records">
            <thead>
                <tr>
                    <th>S/N</th>
                    <th>Name</th>
                    <th>Email</th>
                    {{-- <th>Telephone</th>
                <th>ID Number</th>
                <th>Account No</th> --}}
                    <th>Registered At</th>
                    <th>Action</th>

                </tr>

            </thead>

            <tbody>

                @foreach($clients as $key=>$row)
                <tr>
                    <td>{{$key + 1}}</td>
                    <td><a href="/client/{{$row->id}}/edit">{{$row->name}}</a></td>
                    <td><a href="">{{$row->email}}</a></td>
                    {{-- <td>{{$row->telephone}}</td>
                    <td>{{$row->id_no}}</td> --}}
                    {{-- <td>{{$row->account_no }}</td> --}}
                    <td>{{$row->created_at}}</td>
                    <td>
                        <a href="/client/{{$row->id}}/edit" class="btn btn-sm btn-info"><i
                                class="glyphicon glyphicon-eye"></i> View Client</a>
                    </td>
                </tr>
                @endforeach
            </tbody>

        </table>


    </div>
</div>
@endif
@stop
@section('css')
<link rel="stylesheet" href="/css/admin_custom.css">
<link rel="stylesheet" href="/css/bootstrap-datepicker.min.css">
@stop
@section('js')
<script>
    $(document).ready(function() {
             $("#records").DataTable();
            $('#find_client_by').change(function() {
                //Use $option (with the "$") to see that the variable is a jQuery object
                var $option = $(this).find('option:selected');
                //Added with the EDIT
                var value = $option.val();//to get content of "value" attrib
                var text = $option.text();//to get <option>Text</option> content

                //alert(value);
                //alert(text);

                $(".find_value_label").html(text);
                $("#find_value").attr("placeholder", "Enter "+text+" of the client");
            });
        });
</script>
@stop
