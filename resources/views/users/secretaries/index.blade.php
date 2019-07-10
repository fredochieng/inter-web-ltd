@extends('adminlte::page')
@section('title', 'Secretaries - Inter Web Ltd')
@section('content_header')
<h1>Manage Secretaries</h1>

@stop
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">Manage Secretaries</h3>
                <div class="box-tools">
                    <a href="#" data-target="#modal_new_secretary" data-toggle="modal" class="btn btn-block btn-primary"
                        data-backdrop="static" data-keyboard="false"><i class="fa fa-plus"></i> ADD SECRETARY </a>
                </div>
            </div>
            <div class="box-body">
                <div class="table-responsive">
                    <table class="table table-no-margin" id="example1">
                        <thead>
                            <tr>
                                <th>S/N #</th>
                                <th>Name</th>
                                <th>Email Address</th>
                                <th>ID Number</th>
                                <th>Phone Number</th>
                                <th>Registration Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($secretaries as $key=> $row)
                            <tr>
                                <td>{{$key + 1}}</td>
                                <td>{{$row->name}}</td>
                                <td>{{$row->email}}</td>
                                <td>{{$row->id_no}}</td>
                                <td>{{$row->telephone}}</td>
                                <td>{{$row->created_at}}</td>
                                <td>
                                    <a href="" data-backdrop="static" data-keyboard="false" data-toggle="modal"
                                        data-target="#modal_edit_user_{{$row->sec_id}}"
                                        class="btn btn-xs btn-success"><i class="glyphicon glyphicon-edit"></i> Edit</a>
                                    {{Form::hidden('_method','DELETE')}}
                                    <a href="" data-backdrop="static" data-keyboard="false" data-toggle="modal"
                                        data-target="#modal_delete_user_{{$row->sec_id}}"
                                        class="btn btn-xs btn-danger delete_user_button"><i
                                            class="glyphicon glyphicon-trash"></i> Delete</a>
                                </td>
                            </tr>
                            @include('modals.users.modal_edit_user')
                            @include('modals.users.modal_delete_user')
                            @endforeach

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@include('modals.users.modal_new_secretary')

@stop
