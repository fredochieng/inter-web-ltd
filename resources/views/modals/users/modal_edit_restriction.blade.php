<div class="modal fade in" id="modal_edit_restriction_{{$row->rest_id}}" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            {!!
            Form::open(['action'=>['ReferalsController@update',$row->rest_id],'method'=>'PATCH','class'=>'form','enctype'=>'multipart/form-data'])
            !!}
            <input type="hidden" name='rest' value="{{$row->rest_id}}">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    Edit Restriction - <strong>{{$row->name}}</strong>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            {{Form::label('Client Name')}}<br>
                            <div class="form-group">
                                {{Form::text('name', $row->name,['class'=>'form-control', 'readonly', 'placeholder'=>''])}}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {{Form::label('ID Number')}}<br>
                            <div class="form-group">
                                {{Form::text('id_no', $row->id_no,['class'=>'form-control', 'placeholder'=>''])}}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {{Form::label('Phone Number')}}<br>
                            <div class="form-group">
                                {{Form::text('phone_no', $row->phone,['class'=>'form-control', 'placeholder'=>''])}}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {{Form::label('Commission Earning Times')}}<br>
                            <div class="form-group">
                                {{Form::text('comm_times', $row->comm_times,['class'=>'form-control', 'placeholder'=>''])}}
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
