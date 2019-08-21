<div class="modal fade" id="modal_edit_referral">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            {!! Form::open(['url' => action('ReferalsController@updateUserReferal'), 'method' => 'post']) !!}
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Edit Referall Information - <strong>{{$customer_data->name}}</strong></h4>

            </div>
            <div class="modal-body">
                <div class="row">
                    <input type="hidden" name="user_id" value="{{$customer_data->user_id}}">
                    <input type="hidden" name="accnt_no_id" value="{{$customer_data->accnt_id}}">
                    <input type="hidden" name="initial_inv" value="{{$customer_data->initial_inv}}">
                    <div class="col-md-5">
                        {{Form::label('Referer Phone Number')}}
                        <div class="form-group">
                            <select class="form-control select2" name="referer_phone_id" id="phone_no_id" required
                                style="width: 100%;" tabindex="-1" aria-hidden="true">
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3 hide">
                        <div class="form-group">
                            {{Form::label('Referer ID')}}
                            <div class="form-group">
                                {{Form::text('referer_id', '',['class'=>'form-control', 'readonly', 'id' => 'referer_id1'])}}
                            </div>
                        </div>
                    </div>
                    <input type="hidden" id="referer_phone" name="referer_phone">

                    <div class="col-md-6">
                        <div class="form-group">
                            {{Form::label('Referer Name')}}
                            <div class="form-group">
                                {{Form::text('referer_name', '',['class'=>'form-control', 'readonly', 'id' => 'referer_name1'])}}
                            </div>
                        </div>
                    </div>
                </div>

                <br>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                <button class="btn btn-success" type="submit"><i class="fa fa-check"></i>
                    Save Changes</button>
            </div>
            {!! Form::close() !!}
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
