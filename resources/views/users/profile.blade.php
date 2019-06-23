@extends('adminlte::page')
@section('title', 'Profile - Inter Web')
@section('content_header')
<h1 class="pull-left">My Profile</h1>
<div style="clear:both"></div>
@stop
@section('content')
<div class="row">
    <div class="col-xs-12">
        <div class="box box-info">
            <div class="box-body">
                <div class="container-fluid">
                    <div class="row">
                        {!!
                        Form::open(['action'=>['UserController@updateUserProfile',$user],'method'=>'POST','class'=>'form','enctype'=>'multipart/form-data'])
                        !!}

                        <div class="col-md-6">
                            {{Form::label('name', 'Full Name')}}<br>
                            <div class="form-group">
                                {{Form::text('name', $auth_users->name, ['class'=>'form-control', 'placeholder'=>''])}}
                            </div>
                            {{Form::label('email', 'Email')}}<br>
                            <div class="form-group">
                                {{Form::text('email', $auth_users->email,['class'=>'form-control', 'readonly'=>''])}}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="password">Change Password</label>
                                <input type="password" class="form-control" id="password" name="password"
                                    placeholder="Enter only if you want to change">
                            </div>
                            {{Form::label('password', 'Password *')}}<br>
                            <div class="form-group">
                                {{Form::password('confirm_password', ['class'=>'form-control', 'required'])}}
                            </div>
                            <p class="text-red">Enter you current password to make changes to you profile</p>

                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Save
                                    Changes</button>
                            </div>
                        </div>

                        <input type="hidden" name="id" value="1">
                        </form>
                        <!-- /.form -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop
