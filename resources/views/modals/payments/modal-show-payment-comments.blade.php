<div class="modal fade in" tabindex="-1" id="modal-show-payment-comments_{{$row->payment_id}}">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form>
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span
                            aria-hidden="true">×</span></button>
                    <h4 class="modal-title">Payment Comments <b>{{$row->trans_id}}</b></h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="form-group">
                                <textarea class="form-control summernoteTicket" value="" readonly rows="18.5"
                                    id="message" name="message">{{ $row->comments }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-right" data-dismiss="modal">Close</button>
                </div>
                {!! Form::close() !!}
        </div>

        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
