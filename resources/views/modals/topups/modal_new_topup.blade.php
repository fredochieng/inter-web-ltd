<div class="modal fade" id="modal_new_topup">
        <div class="modal-dialog">
            <div class="modal-content">
                    {!! Form::open(['url' => action('TopupController@store'), 'method' => 'post']) !!}
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Topup Account</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                    <input type="hidden" name="inv_type_id" value="{{$customer_data->inv_type_id}}">
                    <input type="hidden" name="account_no_id" value="{{$customer_data->account_no_id}}">
                    <input type="hidden" name="inv_id" value="{{$customer_data->investment_id}}">
                        <div class="col-sm-12">
                            <div class="form-group">
                                {!! Form::label('Topup Amount *') !!}
                                {!! Form::number('topup_amount', '', ['class' => 'form-control', 'min'=> '10000', 'required' ]); !!}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-flat" data-dismiss="modal"><i class="fa fa-times"></i>
                        No</button>
                    <button type="submit" class="btn btn-info btn-flat"><i class="fa fa-check"></i> Topup Account</button>
                </div>
                {!! Form::close() !!}
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
