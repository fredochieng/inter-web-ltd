<div class="modal fade in" id="modal_edit_user_{{$row->sec_id}}" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            {!!
            Form::open(['action'=>['SecretaryController@update',$row->sec_id],'method'=>'PATCH','class'=>'form','enctype'=>'multipart/form-data'])
            !!}
            <input type="hidden" name='user' value="{{$row->sec_id}}">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    Edit Secretary - <strong>{{$row->name}}</strong>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            {{Form::label('Name')}}<br>
                            <div class="form-group">
                                {{Form::text('name', $row->name,['class'=>'form-control', 'placeholder'=>''])}}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {{Form::label('Email Address')}}<br>
                            <div class="form-group">
                                {{Form::email('email', $row->email,['class'=>'form-control', 'readonly', 'placeholder'=>''])}}
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-flat" aria-label="Close" data-dismiss="modal"><i
                        class="fa fa-times"></i>Cancel</button>
                <button type="submit" class="btn btn-success btn-flat"><i class="fa fa-save"></i> Save
                    Changes</button>
            </div>
            {!! Form::close() !!}

        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
