<div class="modal fade" id="modal_terminate_investment_{{$customer_data->investment_id}}">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            {!!
            Form::open(['action'=>'InvestmentController@terminateInvestment','method'=>'POST','class'=>'form','enctype'=>'multipart/form-data'])
            !!}

            {{Form::text('investment_id',$customer_data->investment_id,['class'=>'form-control hidden'])}}
            {{Form::text('user_id',$customer_data->user_id,['class'=>'form-control hidden'])}}
            <input type="hidden" name="inv_type" id="inv_type" value="{{$customer_data->inv_type}}">
            <input type="hidden" name="total_investments" id="total_investments"
                value="{{$customer_data->investment_amount}}">
            @if($customer_data->inv_type_id ==3)
            <input type="hidden" name="total_investments31" id="total_investments31"
                value="{{$customer_data->monthly_inv}}">
            <input type="hidden" name="total_investments32" id="total_investments32"
                value="{{$customer_data->compounded_inv}}">
            @endif
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Terminate Investment</h4>
            </div>

            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        @if($customer_data->inv_type_id ==3)
                        <div class="col-md-3">
                            @else
                            <div class="col-md-4">
                                @endif
                                {{Form::label('Termination Type ')}}
                                <div class="form-group">
                                    <select class="form-control select2" id="termination_type" name="termination_type"
                                        required style="width: 100%;" tabindex="-1" aria-hidden="true">
                                        <option selected="selected" value="">Select termination type</option>
                                        <option value="1">Partial Termination</option>
                                        <option value="2">Full Termination</option>
                                    </select>
                                </div>
                            </div>
                            @if($customer_data->inv_type_id ==3)
                            <div class="col-md-3">
                                {{Form::label('Investment Plan')}}
                                <div class="form-group">
                                    <select class="form-control select2" id="inv_subtype" name="inv_subtype" required
                                        style="width: 100%;" tabindex="-1" aria-hidden="true">
                                        <option selected="selected" value="">Investment plan to terminate
                                        </option>
                                        @foreach($inv_types as $item)
                                        <option value="{{ $item->inv_id }}">{{ $item->inv_type }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            @endif
                            @if($customer_data->inv_type_id ==3)
                            <div class="col-md-3">
                                @else
                                <div class="col-md-4">
                                    @endif
                                    <label for="franch" class="col-sm-12 control-label label-left">Amount to
                                        terminate</label>
                                    <div class="col-sm-12">
                                        <input id="amount_terminated" class="form-control" name="amount_terminated"
                                            required type="number" value="">

                                    </div>
                                </div>
                                @if($customer_data->inv_type_id ==3)
                                <div class="col-md-3" id="amount_after_ter_div">
                                    @else
                                    <div class="col-md-4" id="amount_after_ter_div">
                                        @endif

                                        <label for="franch" class="col-sm-12 control-label label-left">Amount after
                                            ter
                                        </label>
                                        <div class="col-sm-12">
                                            <input id="amount_after_ter" class="form-control" name="amount_after_ter"
                                                type="text" value="">

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default btn-flat" data-dismiss="modal"><i
                                    class="fa fa-times"></i>
                                No</button>
                            <button type="submit" class="btn btn-danger btn-flat"><i class="fa fa-close"></i>
                                Terminate</button>
                        </div>
                        {!! Form::close() !!}
                    </div>
                    <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
            </div>
