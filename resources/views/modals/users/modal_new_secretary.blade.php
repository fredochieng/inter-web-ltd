<div class="modal fade in" id="modal_new_secretary" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            {!!
            Form::open(['action'=>'SecretaryController@store','method'=>'POST','class'=>'form','enctype'=>'multipart/form-data'])
            !!}
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    Add New Secretary
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            {{Form::label('Name *')}}<br>
                            <div class="form-group">
                                {{Form::text('name', '',['class'=>'form-control', 'required', 'placeholder'=>''])}}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {{Form::label('Email Address *')}}<br>
                            <div class="form-group">
                                {{Form::email('email', '',['class'=>'form-control', 'required', 'placeholder'=>''])}}
                            </div>
                        </div>
                    </div>
                </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-flat" aria-label="Close" data-dismiss="modal"><i
                        class="fa fa-times"></i>Cancel</button>
                <button type="submit" class="btn btn-primary btn-flat"><i class="fa fa-check"></i> Create New
                    Secretary</button>
            </div>
            {!! Form::close() !!}

        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
