<div class="modal fade in" id="modal_add_payment">
    <div class="modal-dialog modal-lg" style="width:90%">
        <div class="modal-content">
            {!! Form::open(['url' => action('PaymentController@store'), 'method' => 'post']) !!}
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

                    {{-- // see if the logic applies to monthly + compounding inv too --}}
                    @if($customer_data->inv_type_id == 1 || $customer_data->inv_type
                    ==3)
                    <div class="col-sm-3">
                        <div class="form-group">
                            {!! Form::label('Mothly Payment Amount') !!}
                            {!! Form::text('monthly_pay', number_format($next_amount,2,'.',','), ['class' =>
                            'form-control'
                            ]); !!}
                        </div>
                    </div>

                    {{-- @elseif($customer_data->inv_type_id == 1 && $customer_data->topped_up==1)
                    <div class="col-sm-3">
                        <div class="form-group">
                            {!! Form::label('Mothly Payment Amount') !!}
                            {!! Form::text('updated_monthly_pay', number_format($updated_monthly_amnt,2,'.',','),
                            ['class' =>
                            'form-control'
                            ]); !!}
                        </div>
                    </div> --}}
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
                    </div>
                    @endif

                </div>
            </div>
            <div class="row">
                @if($customer_data->inv_type_id == 3)
                <div class="col-md-3">
                    <label for="franch" class="col-sm-12 control-label label-left">Compound Payment Date</label>
                    <div class="col-sm-12">
                        <input id="monthly_amount" class="form-control" placeholder="" readonly="readonly"
                            name="comp_pay_date" type="text" value="{{$customer_investments->last_pay_date}}">

                    </div>
                </div>
                @endif
                <!-- {{--  <div class="row">  --}} -->

                <div class="col-sm-6">
                    <h4>Payment Mode &nbsp;

                        <button type="button" class="btn btn-info btn-xs btn-flat pull-right add_co" data-toggle="modal"
                            data-target="#modal_view_payment_info" data-keyboard="false" data-backdrop="static"> <i
                                class="fa fa-fw fa-plus"></i> ADD PAYMENT MODE </button>
                    </h4>
                    <table class="table table-no-margin">
                        <thead>
                            <tr>
                                <th>S/N</th>
                                <th>Payment Mode</th>
                                <th>Bank Name</th>
                                <th>Bank Account</th>
                                <th>Mpesa Numner</th>
                                <th>Select</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($client_payment_modes as $count=> $item)
                            <tr>
                                <th>{{$count + 1}}</th>
                                <td>{{$item->method_name}}</td>

                                @if(empty($item->bank_name))
                                <td>N/A</td>
                                @else
                                <td>{{$item->bank_name}}</td>
                                @endif
                                @if(empty($item->pay_bank_acc))
                                <td>N/A</td>
                                @else
                                <td>{{$item->pay_bank_acc}}</td>
                                @endif
                                @if(empty($item->pay_mpesa_no))
                                <td>N/A</td>
                                @else
                                <td>{{$item->pay_mpesa_no}}</td>
                                @endif
                                <td><input type="checkbox" id="select_pay_mode" value="{{$item->pay_id}}"
                                        name="select_pay_mode" class="pay"> </td>
                            </tr>
                            @endforeach

                        </tbody>
                    </table>
                    <p>Click add payment mode to add new payment mode</p>
                </div>

                <div class="col-sm-3">
                    <div class="form-group">
                        {!! Form::label('Mpesa/Bank Confirmation Code') !!}
                        {!! Form::text('conf_code', '', ['class'
                        =>
                        'form-control'
                        ]); !!}
                    </div>
                </div>
                <div class="col-md-9">
                    <div class="form-group">
                        <label>Payment Comments</label>
                        <textarea id="" cols="" rows="3" name="comments" class="form-control"
                            placeholder="Enter payments comments (mode of payment)"></textarea>

                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                <button class="btn btn-primary" id="confirmPayment" type="submit"><i class="fa fa-check"></i>
                    CONFIRM PAYMENT</button>
            </div>
            {!! Form::close() !!}
        </div>



    </div>

    <!-- /.modal-content -->
</div>
<!-- /.modal-dialog -->
</div>