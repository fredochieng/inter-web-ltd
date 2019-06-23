<div class="modal fade" id="modal-approve-client_{{ $row->id }}">
    <div class="modal-dialog">
        <div class="modal-content">
            {{Form::text('id',$row->id,['class'=>'form-control hidden'])}}
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Approve Client</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <p>Are you sure you want to approve <span style="font-weight:bold">{{$row->id}}</span>?</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-flat" data-dismiss="modal"><i class="fa fa-times"></i>
                    No</button>
                <button type="submit" class="btn btn-primary btn-flat"><i class="fa fa-check"></i> Yes</button>
            </div>
            {!! Form::close() !!}
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
