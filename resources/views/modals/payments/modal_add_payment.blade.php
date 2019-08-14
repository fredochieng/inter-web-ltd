<div class="modal fade in" tabindex="-1" id="modal_add_payment">
    <div class="modal-dialog modal-lg" style="width:90%">
        <div class="modal-content">
            {!! Form::open(['url' => action('PaymentController@store'), 'id'=>'confirm_payment_form', 'method' =>
            'post']) !!}
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span
                        aria-hidden="true">×</span></button>
                <h4 class="modal-title">Confirm Client Payment</h4>
            </div>
            <div class="modal-body">
                <input type="hidden" name="account_id" value="{{$customer_data->accnt_id}}">
                <input type="hidden" name="inv_type" value="{{$customer_data->inv_type_id}}">
                <input type="hidden" name="pay_times" value="{{$customer_data->payment_times}}">
                <input type="hidden" name="inv_type" value="{{$customer_data->inv_type_id}}">
                <input type="hidden" name="user_id" value="{{$customer_data->user_id}}">
                <input type="hidden" name="comm_paid" value="{{$tot_comm}}">
                {{--  First row  --}}
                <div class="row">
                    {{--  // row 1  --}}
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
                    {{--  // end row 1  --}}
                </div>
                {{--  end first row  --}}
                <br />

                <div class="row">
                    {{--  //row 2  --}}
                    <div class="col-sm-3">
                        <div class="form-group">
                            {!! Form::label('Payment Date') !!}
                            {!! Form::text('user_date', $next_pay_date, ['class' => 'form-control'
                            ]); !!}
                        </div>
                    </div>

                    @if($customer_data->inv_type_id == 1 || $customer_data->inv_type ==3)
                    <div class="col-sm-3">
                        <div class="form-group">
                            {!! Form::label('Mothly Payment Amount') !!}
                            {!! Form::text('monthly_pay', number_format($next_amount,2,'.',','), ['class' =>
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
                    </div>
                    @endif

                    <div class="col-sm-3">
                        <div class="form-group">
                            {!! Form::label('Mpesa/Bank Confirmation Code') !!}
                            {!! Form::text('conf_code', '', ['class'=>'form-control', 'required'
                            ]); !!}
                        </div>
                    </div>
                </div>
                {{--  // end row 2  --}}

                <br />
                <div class="row">
                    {{--  //row 3  --}}
                    <div class="col-md-6">
                        <div class="box box-default">
                            <div class="box-header with-border">
                                <h3 class="box-title">PAYMENT MODES</h3>
                                <p class="pull-right">
                                    <button type="button" class="btn btn-info btn-xs pull-right add_co"
                                        data-toggle="modal" data-target="#modal_view_payment_info" data-keyboard="false"
                                        data-backdrop="static"> <i class="fa fa-fw fa-plus"></i> NEW PAYMENT MODE
                                    </button>
                                </p>
                            </div>
                            <!-- /.box-header -->

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
                            <p>Click new payment mode to add new payment mode</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Payment Comments</label>
                            <textarea id="" cols="" rows="9" name="comments" class="form-control" required
                                placeholder="Enter payments comments (mode of payment)"></textarea>

                        </div>
                    </div>
                    {{--  // end row 3  --}}
                </div>

            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                @if($fully_paid =='N')
                <button class="btn btn-primary" id="confirmPayment" type="submit"><i class="fa fa-check"></i>
                    CONFIRM PAYMENT</button>
                @else
                <button class="btn btn-primary" disabled id="confirmPayment" type="submit"><i class="fa fa-check"></i>
                    CONFIRM PAYMENT</button>
                @endif

            </div>
            {!! Form::close() !!}
        </div>

        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
