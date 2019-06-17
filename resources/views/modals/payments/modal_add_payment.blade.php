<div class="modal fade in" id="modal_add_payment">
    <div class="modal-dialog modal-lg" style="width:90%">
        <div class="modal-content">
            {!! Form::open(['url' => action('PaymentController@store'), 'method' => 'post']) !!}

            {{-- <input type="hidden" id="account_id" name="account_id"> --}}

            {{-- <input type="hidden" id="inv_type" name="inv_type">
            <input type="hidden" id="user_id" name="user_id">
            <input type="hidden" id="pay_times" name="pay_times"> --}}
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span
                        aria-hidden="true">×</span></button>
                <h4 class="modal-title">New Payment</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <input type="hidden" name="account_id" value="{{$customer_data->accnt_id}}">
                    <input type="hidden" name="inv_type" value="{{$customer_data->inv_type_id}}">
                    <input type="hidden" name="pay_times" value="{{$customer_data->payment_times}}">

                    <div class="col-sm-3">
                        <div class="form-group">
                            {!! Form::label('Account Number') !!}
                            {!! Form::text('account_no', $customer_data->account_no, ['class' => 'form-control',
                            'min'=> '100', 'required'
                            ]); !!}
                        </div>
                    </div>

                    <div class="col-sm-3">
                        <div class="form-group">
                            {!! Form::label('Client Name') !!}
                            {!! Form::text('account_no', $customer_data->name, ['class' => 'form-control',
                            'min'=> '100', 'required'
                            ]); !!}
                        </div>
                    </div>

                    <div class="col-sm-3">
                        <div class="form-group">
                            {!! Form::label('ID Number') !!}
                            {!! Form::text('phone', $customer_data->id_no, ['class' => 'form-control',
                            'min'=> '100', 'required'
                            ]); !!}
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            {!! Form::label('Phone Number') !!}
                            {!! Form::text('phone', $customer_data->telephone, ['class' => 'form-control'
                            ]); !!}
                        </div>
                    </div>

                </div>

                <br />
                <div class="row">
                    <div class="col-sm-3">
                        <div class="form-group">
                            {!! Form::label('Total Due Payments') !!}
                            {!! Form::text('tot_payable_amnt',
                            number_format($tot_due_payments->total_due_payments,2,'.',','),
                            ['class' => 'form-control'
                            ]); !!}
                        </div>
                    </div>

                    <div class="col-sm-3">
                        <div class="form-group">
                            {!! Form::label('Payment Date') !!}
                            {!! Form::text('user_date', $next_pay_date, ['class' => 'form-control'
                            ]); !!}
                        </div>
                    </div>

                    @if($customer_data->inv_type_id == 1 && $customer_data->topped_up==0 || $customer_data->inv_type
                    ==3)
                    <div class="col-sm-3">
                        <div class="form-group">
                            {!! Form::label('Mothly Payment Amount') !!}
                            {!! Form::text('monthly_pay', number_format($monthly_amnt,2,'.',','), ['class' =>
                            'form-control'
                            ]); !!}
                        </div>
                    </div>

                    @elseif($customer_data->inv_type_id == 1 && $customer_data->topped_up==1)
                    <div class="col-sm-3">
                        <div class="form-group">
                            {!! Form::label('Mothly Payment Amount') !!}
                            {!! Form::text('updated_monthly_pay', number_format($updated_monthly_amnt,2,'.',','),
                            ['class' =>
                            'form-control'
                            ]); !!}
                        </div>
                    </div>
                    @elseif($customer_data->inv_type_id == 2)
                    <div class="col-sm-3">
                        <div class="form-group">
                            {!! Form::label('Compound Payment Amount') !!}
                            {!! Form::text('comp_pay_amount', number_format($comp_payable_amout,2,'.',','), ['class'
                            =>
                            'form-control'
                            ]); !!}
                        </div>
                    </div>

                    @endif
                    @if($customer_data->inv_type_id == 3 && $comp_pay_date == 1)
                    <div class="col-sm-3">
                        <div class="form-group">
                            {!! Form::label('Total Compound Amount') !!}
                            {!! Form::text('tot_comp_amount', number_format($tot_comp_amount,2,'.',','), ['class' =>
                            'form-control'
                            ]); !!}
                        </div>
                    </div @endif </div> </div> <div class="row">
                    @if($customer_data->inv_type_id == 3)
                    <div class="col-md-3">
                        <label for="franch" class="col-sm-12 control-label label-left">Compound Payment Date</label>
                        <div class="col-sm-12">
                            <input id="monthly_amount" class="form-control" placeholder="" readonly="readonly"
                                name="comp_pay_date" type="text" value="{{$customer_investments->last_pay_date}}">

                        </div>
                    </div>
                    @endif

                </div>

                <br />
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                    <button class="btn btn-primary" id="transactionPost" type="submit"><i class="fa fa-check"></i>
                        MAKE PAYMENT</button>
                </div>
                {!! Form::close() !!}
            </div>

        </div>

        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
