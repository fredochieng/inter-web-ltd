<div class="modal fade in" id="modal-edit-customer_{{ $row->id }}" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            {!! Form::open(['action'=>['UserController@update',$row->id],'method'=>'PUT','class'=>'form','enctype'=>'multipart/form-data'])
            !!}
            <input type="hidden" name='customer' value="{{$row->id}}">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    Edit Customer Details
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            {{Form::label('Name *')}}<br>
                            <div class="form-group">
                                {{Form::text('name', $row->name,['class'=>'form-control', 'placeholder'=>''])}}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            {{Form::label('Email Address *')}}<br>
                            <div class="form-group">
                                {{Form::email('email', $row->email,['class'=>'form-control', 'readonly', 'placeholder'=>''])}}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            {{Form::label('Telephone *')}}<br>
                            <div class="form-group">
                                {{Form::text('telephone', $row->telephone,['class'=>'form-control', 'placeholder'=>''])}}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">

                    <div class="col-md-4">
                        <div class="form-group">
                            {{Form::label('ID Number *')}}<br>
                            <div class="form-group">
                                {{Form::text('id_no', $row->id_no,['class'=>'form-control', 'readonly', 'placeholder'=>''])}}
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
                                {{Form::text('account_no',['class'=>'form-control', 'readonly','placeholder'=>''])}}
                            </div>
                        </div>
                    </div> --}}
                </div>
                <div class="row">


                </div>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            {{Form::label('Home Address *')}}<br>
                            <div class="form-group">
                                {{Form::text('home_address', $row->home_address,['class'=>'form-control', 'required', 'placeholder'=>''])}}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {{Form::label('Home Town *')}}<br>
                            <div class="form-group">
                                {{Form::text('home_town', $row->home_town,['class'=>'form-control', 'placeholder'=>''])}}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {{Form::label('Next of Kin Name *')}}<br>
                            <div class="form-group">
                                {{Form::text('kin_name', $row->kin_name,['class'=>'form-control', 'placeholder'=>''])}}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {{Form::label('Next of Kin Telephone *')}}<br>
                            <div class="form-group">
                                {{Form::text('kin_telephone', $row->kin_telephone,['class'=>'form-control', 'required', 'placeholder'=>''])}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-flat" aria-label="Close" data-dismiss="modal"><i
                        class="fa fa-times"></i>Cancel</button>
                <button type="submit" class="btn btn-success btn-flat"><i class="fa fa-check"></i> Save Changes</button>
            </div>
            {!! Form::close() !!}

        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
