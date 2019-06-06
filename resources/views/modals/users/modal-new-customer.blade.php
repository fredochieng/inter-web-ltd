<div class="modal fade in" id="modal-new-customer" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            {!! 
            Form::open(['action'=>'UserController@store','method'=>'POST','class'=>'form','enctype'=>'multipart/form-data'])
            !!}
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    Add New Customer
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            {{Form::label('Name *')}}<br>
                            <div class="form-group">
                                {{Form::text('name', '',['class'=>'form-control', 'required', 'placeholder'=>''])}}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            {{Form::label('Email Address *')}}<br>
                            <div class="form-group">
                                {{Form::email('email', '',['class'=>'form-control', 'required', 'placeholder'=>''])}}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            {{Form::label('Telephone *')}}<br>
                            <div class="form-group">
                                {{Form::text('telephone', '',['class'=>'form-control', 'required', 'placeholder'=>''])}}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">

                    <div class="col-md-4">
                        <div class="form-group">
                            {{Form::label('ID Number *')}}<br>
                            <div class="form-group">
                                {{Form::text('id_no', '',['class'=>'form-control', 'required', 'placeholder'=>''])}}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="purchase_date">Date of Birth</label>
                            <div class="input-group">
                                <input type="text" class="form-control dob" id="dob" name="dob">
                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                            </div>
                        </div>
                    </div>
                    {{-- <div class="col-md-4">
                        <div class="form-group">
                            {{Form::label('Account Number *')}}<br>
                            <div class="form-group">
                                {{Form::text('account_no',{{ $generated_account_no }}['class'=>'form-control',
                                'required', 'placeholder'=>''])}}
                            </div>
                        </div>
                    </div> --}}
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="account_no">Account Number</label>
                            <div class="form-group">
                                <input type="text" class="form-control"  name="account_no" value="{{ $generated_account_no }}">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">


                </div>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            {{Form::label('Home Address *')}}<br>
                            <div class="form-group">
                                {{Form::text('home_address', '',['class'=>'form-control', 'required', 'placeholder'=>''])}}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {{Form::label('Home Town *')}}<br>
                            <div class="form-group">
                                {{Form::text('home_town', '',['class'=>'form-control', 'required', 'placeholder'=>''])}}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {{Form::label('Next of Kin Name *')}}<br>
                            <div class="form-group">
                                {{Form::text('kin_name', '',['class'=>'form-control', 'required', 'placeholder'=>''])}}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {{Form::label('Next of Kin Telephone *')}}<br>
                            <div class="form-group">
                                {{Form::text('kin_telephone', '',['class'=>'form-control', 'required', 'placeholder'=>''])}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-flat" aria-label="Close" data-dismiss="modal"><i
                        class="fa fa-times"></i>Cancel</button>
                <button type="submit" class="btn btn-primary btn-flat"><i class="fa fa-check"></i> Create New
                    Customer</button>
            </div>
            {!! Form::close() !!}

        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
