<div class="modal fade" id="modal_view_payment_info">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            {!! Form::open(['url' => action('PaymentModeController@store'), 'method' => 'post']) !!}
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Add new mode of payment - <strong>{{$customer_data->name}}</strong></h4>

            </div>
            <div class="modal-body">
                <div class="row">
                    <input type="hidden" name="user_id" value="{{$customer_data->user_id}}">

                    <div class="col-md-4 bank_payment_div" id="pay_mode_id_div">
                        {{Form::label('Payment Mode ')}}
                        <div class="form-group">
                            <select class="form-control select2" name="pay_mode_id" id="pay_mode_id" required
                                style="width: 100%;" tabindex="-1" aria-hidden="true">
                                <option value"">Select payment mode</option>
                                @foreach($payment_mode as $item)
                                <option value="{{ $item->method_id }}">{{ $item->method_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-8 hide mpesa_number_div" id="mpesa_number_div">
                        <div class="form-group">
                            {{Form::label('MPESA Number')}}
                            <div class="form-group">
                                {{Form::text('pay_mpesa_no', '',['class'=>'form-control', 'id' => 'mpesa_number'])}}
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 hide bank_payment_div" id="bank_payment_div">
                        {{Form::label('Payment Bank ')}}
                        <div class="form-group">
                            <select class="form-control select2" name="pay_bank_id" id="pay_bank_id"
                                style="width: 100%;" tabindex="-1" aria-hidden="true">
                                <option value=''>Select payment bank</option>
                                @foreach($banks as $item)
                                <option value="{{ $item->bank_id }}">{{ $item->bank_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4 hide" id="bank_acc_div">
                        <div class="form-group">
                            {{Form::label('Bank Account')}}
                            <div class="form-group">
                                {{Form::text('pay_bank_acc', '',['class'=>'form-control'])}}
                            </div>
                        </div>
                    </div>
                </div>

                <br>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                <button class="btn btn-primary" id="confirmPayment" type="submit"><i class="fa fa-check"></i>
                    ADD PAYMENT MODE</button>
            </div>
            {!! Form::close() !!}
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
