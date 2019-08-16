<div class="modal fade in" id="modal_change_plan">
    <div class="modal-dialog modal-lg" style="width:90%">
        <div class="modal-content">
            {!! Form::open(['url' => action('InvestmentController@changePlan'), 'method' => 'post', 'id' =>
            'changePlanClientForm'])
            !!}
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span
                        aria-hidden="true">×</span></button>
                <h4 class="modal-title">Change Investment Plan - <strong>{{$customer_data->name}}</strong></h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <input type="hidden" name="account_no_id" value="{{$customer_data->accnt_id}}">
                    <input type="hidden" name="user_id" value="{{$customer_data->user_id}}">
                    <input type="hidden" name="real_inv_type_id" value="{{$customer_data->inv_type_id}}">
                    @if($customer_data->inv_type_id ==1)
                    <input type="hidden" name="inv_type_id" value="2">
                    @elseif($customer_data->inv_type_id ==2)
                    <input type="hidden" name="inv_type_id" value="1">
                    @endif
                    {{--  <input type="hidden" name="total_investments31" id="total_investments31"
                        value="{{$customer_data->monthly_inv}}"> --}}

                    <div class="col-md-4">
                        {{Form::label('Change Plan ')}}
                        <div class="form-group">
                            <select class="form-control select2" id="plan_type" name="plan_type" required
                                style="width: 100%;" tabindex="-1" aria-hidden="true">
                                <option selected="selected" value="0">Select plan type</option>
                                <option value="1">Full Transfer</option>
                                <option value="2">Partial Transfer</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label for="franch" class="col-sm-12 control-label label-left">Amount to
                            transfer</label>
                        <div class="col-sm-12">
                            <input id="amount_transfered" class="form-control" name="amount_transfered" required
                                type="number" value="">

                        </div>
                    </div>
                    <div class="col-md-4" id="amount_after_transfer_div">
                        <label for="franch" class="col-sm-12 control-label label-left">Amount after
                            transfer
                        </label>
                        <div class="col-sm-12">
                            <input id="amount_after_transfer" class="form-control" name="amount_after_transfer"
                                type="text" value="">

                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('Investment Date *') !!}
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </span>
                                {{Form::text('inv_date', null, ['class' => 'form-control topup_date', 'readonly', 'id' => 'topup_date', 'required' ])}}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group hide inv_duration_div" id="inv_duration_div1">
                            {{Form::label('Investment Duration(Months) *')}}
                            <div class="input-group">
                                {{Form::number('inv_duration', '',['class'=>'form-control', 'min' => '1'])}}
                                <span class="input-group-addon">
                                    Months
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 hide monthly_inv_duration_div" id="monthly_inv_duration_div1">
                        <div class="form-group">
                            {{Form::label('Monthly Duration *')}}
                            <div class="form-group">
                                {{Form::number('monthly_inv_duration', '',['class'=>'form-control', 'min' => '1'])}}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 hide compounded_inv_duration_div" id="compounded_inv_duration_div1">
                        <div class="form-group">
                            {{Form::label('Compounding Duration(Months) *')}}
                            <div class="form-group">
                                {{Form::number('compounded_inv_duration', '',['class'=>'form-control', 'min' => '1'])}}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default btn-flat" data-dismiss="modal"><i
                                class="fa fa-times"></i>
                            No</button>
                        <button type="submit" class="btn btn-primary btn-flat"><i class="fa fa-check"></i>
                            Change Plan</button>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>

            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
</div>
