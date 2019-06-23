<div class="modal fade" id="modal_new_topup">
    <div class="modal-dialog modal-lg">
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
                    <input type="hidden" name="account_no_id" value="{{$customer_data->accnt_id}}">
                    <input type="hidden" name="inv_id" value="{{$customer_data->investment_id}}">
                    <input type="hidden" name="user_id" value="{{$customer_data->user_id}}">
                    @if($customer_data->inv_type_id ==3)
                    <div class="col-md-4">
                        @else
                        <div class="col-md-6">
                            @endif
                            <div class="form-group">
                                {!! Form::label('Topup Amount *') !!}
                                {!! Form::number('topup_amount', '', ['class' => 'form-control', 'min'=> '100',
                                'required'
                                ]); !!}
                            </div>
                        </div>
                        @if($customer_data->inv_type_id ==3)
                        <div class="col-md-4" id="inv_type_id">
                            {{Form::label('Investment Plan')}}
                            <div class="form-group">
                                <select class="form-control select2" required id="inv_subtype_id" name="inv_subtype_id"
                                    style="width: 100%;" tabindex="-1" aria-hidden="true">
                                    <option selected="selected" value="">Select investment plan to topup</option>
                                    @foreach($inv_types as $item)
                                    <option value="{{ $item->inv_id }}">{{ $item->inv_type }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        @endif

                        @if($customer_data->inv_type_id ==3)
                        <div class="col-md-4">
                            @else
                            <div class="col-md-6">
                                @endif
                                {{Form::label('Investment Mode')}}
                                <div class="form-group">
                                    <select class="form-control select2" required id="inv_mode_id" name="inv_mode_id"
                                        style="width: 100%;" tabindex="-1" aria-hidden="true">
                                        <option selected="selected" value="">Select investment mode</option>
                                        @foreach($inv_modes as $item)
                                        <option value="{{ $item->id }}">{{ $item->inv_mode }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 hide mpesa_inv_div" id="mpesa_div">
                                <div class="form-group">
                                    {{Form::label('MPESA Transaction Code *')}}
                                    <div class="form-group">
                                        {{Form::text('mpesa_trans_code', '',['class'=>'form-control'])}}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 hide bank_inv_div" id="bank_id_div">
                                {{Form::label('Bank ')}}
                                <div class="form-group">
                                    <select class="form-control select2" id="inv_bank_id" name="inv_bank_id"
                                        style="width: 100%;" tabindex="-1" aria-hidden="true">
                                        <option selected="selected">Select investment bank</option>
                                        @foreach($banks as $item)
                                        <option value="{{ $item->bank_id }}">{{ $item->bank_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6 hide bank_inv_div" id="bank_trans_div">
                                <div class="form-group">
                                    {{Form::label('Bank Transaction Code')}}
                                    <div class="form-group">
                                        {{Form::text('bank_trans_code', '',['class'=>'form-control'])}}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 hide cheq_inv_div" id="cheq_bank_div">
                                {{Form::label('Bank ')}}
                                <div class="form-group">
                                    <select class="form-control select2" id="inv_cheq_bank_id" name="inv_cheq_bank_id"
                                        style="width: 100%;" tabindex="-1" aria-hidden="true">
                                        <option selected="selected">Select investment bank</option>
                                        @foreach($banks as $item)
                                        <option value='{{ $item->bank_id }}'>{{ $item->bank_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6 hide cheq_inv_div" id="cheq_no_div">
                                <div class="form-group">
                                    {{Form::label('Cheque Number')}}
                                    <div class="form-group">
                                        {{Form::text('cheque_no', '',['class'=>'form-control'])}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default btn-flat" data-dismiss="modal"><i
                                class="fa fa-times"></i>
                            No</button>
                        <button type="submit" class="btn btn-info btn-flat"><i class="fa fa-check"></i> Topup
                            Account</button>
                    </div>
                    {!! Form::close() !!}
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
