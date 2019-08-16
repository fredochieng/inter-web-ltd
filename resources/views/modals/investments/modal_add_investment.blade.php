<div class="modal fade in" id="modal_add_investment">
    <div class="modal-dialog modal-lg" style="width:90%">
        <div class="modal-content">
            {!! Form::open(['url' => action('InvestmentController@store'), 'method' => 'post', 'class' =>
            'addClientForm'])
            !!}
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span
                        aria-hidden="true">×</span></button>
                <h4 class="modal-title">New Investment - <strong>{{$customer_data->name}}</strong></h4>
            </div>
            <div class="modal-body">
                <input type="hidden" name="account_id" value="{{$customer_data->accnt_id}}">
                <input type="hidden" name="user_id" value="{{$customer_data->user_id}}">

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('Investment Date *') !!}
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </span>
                                {{Form::text('inv_date', null, ['class' => 'form-control inv_date', 'id' => 'inv_date', 'required' ])}}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {{Form::label('Investment Amount *')}}
                            <div class="form-group">
                                {{Form::number('inv_amount', '',['class'=>'form-control', 'id'=>'total_inv_amount1', 'placeholder'=>'Minimum amount(Kshs 100,000.00)', 'min'=>'1', 'required'])}}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            {{Form::label('Investment Duration(Months) *')}}
                            <div class="input-group">
                                {{Form::number('inv_duration', '',['class'=>'form-control', 'min' => '1', 'required'])}}
                                <span class="input-group-addon">
                                    Months
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">

                    <div class="col-md-4">
                        {{Form::label('Investment Type ')}}
                        <div class="form-group">
                            <select class="form-control select2" name="inv_type_id" id="inv_type_id" required
                                style="width: 100%;" tabindex="-1" aria-hidden="true">
                                <option value="">Select investment type</option>
                                @foreach($investment_types as $item)
                                <option value='{{ $item->inv_id }}'>{{ $item->inv_type }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                </div>
                <div class="row">
                    <div class="col-md-3 hide" id="monthly_inv_amount_div">
                        <div class="form-group">
                            {{Form::label('Monthly Amount *')}}
                            <div class="form-group">
                                {{Form::number('monthly_inv_amount', '',['class'=>'form-control', 'id'=>'monthly_inv_amount', 'min'=>'1'])}}
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 hide" id="monthly_inv_duration_div">
                        <div class="form-group">
                            {{Form::label('Duration *')}}
                            <div class="form-group">
                                {{Form::number('monthly_inv_duration', '',['class'=>'form-control', 'min' => '1'])}}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 hide" id="compounded_inv_amount_div">
                        <div class="form-group">
                            {{Form::label('Compounded Amount *')}}
                            <div class="form-group">
                                {{Form::number('compounded_inv_amount', '',['class'=>'form-control', 'id'=>'compounded_inv_amount'])}}
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 hide" id="compounded_inv_duration_div">
                        <div class="form-group">
                            {{Form::label('Duration(Months) *')}}
                            <div class="form-group">
                                {{Form::number('compounded_inv_duration', '',['class'=>'form-control', 'min' => '1'])}}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        {{Form::label('Payment Mode ')}}
                        <div class="form-group">
                            <select class="form-control select2" name="pay_mode_id" id="pay_mode_id2" required
                                style="width: 100%;" tabindex="-1" aria-hidden="true">
                                <option value="">Select payment mode</option>
                                @foreach($payment_mode as $item)
                                <option value='{{ $item->method_id }}'>{{ $item->method_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4 hide mpesa_number_div1" id="mpesa_number_div1">
                        <div class="form-group">
                            {{Form::label('MPESA Number')}}
                            <div class="form-group">
                                {{Form::text('pay_mpesa_no', '',['class'=>'form-control', 'id' => 'mpesa_number'])}}
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 hide bank_payment_div1" id="bank_payment_div1">
                        {{Form::label('Payment Bank ')}}
                        <div class="form-group">
                            <select class="form-control select2" name="pay_bank_id" id="pay_bank_id1"
                                style="width: 100%;" tabindex="-1" aria-hidden="true">
                                <option value"">Select payment bank</option>
                                @foreach($banks as $item)
                                <option value="{{ $item->bank_id }}">{{ $item->bank_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4 hide bank_payment_acc2" id="bank_payment_acc2">
                        <div class="form-group">
                            {{Form::label('Bank Account')}}
                            <div class="form-group">
                                {{Form::text('pay_bank_acc', '',['class'=>'form-control'])}}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        {{Form::label('Investment Mode ')}}
                        <div class="form-group">
                            <select class="form-control select2" id="inv_mode_id1" name="inv_mode_id" required
                                style="width: 100%;" tabindex="-1" aria-hidden="true">
                                <option selected="selected" value="">Select investment mode</option>
                                @foreach($inv_modes as $item)
                                <option value="{{ $item->id }}">{{ $item->inv_mode }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4 hide mpesa_inv_div" id="mpesa_inv_div">
                        <div class="form-group">
                            {{Form::label('MPESA Transaction Code *')}}
                            <div class="form-group">
                                {{Form::text('mpesa_trans_code', '',['class'=>'form-control'])}}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 hide bank_inv_div" id="bank_inv_div">
                        {{Form::label('Bank ')}}
                        <div class="form-group">
                            <select class="form-control select2" id="inv_bank_id1" name="inv_bank_id"
                                style="width: 100%;" tabindex="-1" aria-hidden="true">
                                <option selected="selected">Select investment bank</option>
                                @foreach($banks as $item)
                                <option value="{{ $item->bank_id }}">{{ $item->bank_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4 hide bank_inv_div" id="inv_bank_trans_id1">
                        <div class="form-group">
                            {{Form::label('Bank Transaction Code')}}
                            <div class="form-group">
                                {{Form::text('bank_trans_code', '',['class'=>'form-control'])}}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 hide cheq_inv_div" id="cheq_inv_div">
                        {{Form::label('Bank ')}}
                        <div class="form-group">
                            <select class="form-control select2" id="inv_cheq_bank_id1" name="inv_cheq_bank_id"
                                style="width: 100%;" tabindex="-1" aria-hidden="true">
                                <option selected="selected">Select investment bank</option>
                                @foreach($banks as $item)
                                <option value='{{ $item->bank_id }}'>{{ $item->bank_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4 hide cheq_inv_div" id="cheq_no_inv_div">
                        <div class="form-group">
                            {{Form::label('Cheque Number')}}
                            <div class="form-group">
                                {{Form::text('cheque_no', '',['class'=>'form-control'])}}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                    <button class="btn btn-primary" id="confirmPayment" type="submit"><i class="fa fa-check"></i>
                        ADD INVESTMENT</button>
                </div>
                {!! Form::close() !!}
            </div>

            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
</div>
