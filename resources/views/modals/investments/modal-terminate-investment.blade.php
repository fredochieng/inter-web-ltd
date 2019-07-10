<div class="modal fade" id="modal_terminate_investment_{{$customer_data->investment_id}}">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            {!!
            Form::open(['action'=>'InvestmentController@terminateInvestment','method'=>'POST','class'=>'form','enctype'=>'multipart/form-data'])
            !!}

            {{Form::text('investment_id',$customer_data->investment_id,['class'=>'form-control hidden'])}}

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Terminate Investment</h4>
            </div>

            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="col-md-4">
                            {{Form::label('Termination TYpe ')}}
                            <div class="form-group">
                                <select class="form-control select2" id="termination_type" name="termination_type"
                                    required style="width: 100%;" tabindex="-1" aria-hidden="true">
                                    <option selected="selected" value="">Select termination type</option>
                                    <option value="1">Partial Termination</option>
                                    <option value="2">Full Termination</option>

                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label for="franch" class="col-sm-12 control-label label-left">Amount to terminate</label>
                            <div class="col-sm-12">
                                <input id="amount_terminated" class="form-control" name="amount_terminated" type="text"
                                    value="">

                            </div>
                        </div>
                        <div class="col-md-4 hide" id="amount_after_ter_div">
                            <label for="franch" class="col-sm-12 control-label label-left">Amount after
                                termination</label>
                            <div class="col-sm-12">
                                <input id="amount_after_ter" class="form-control" name="amount_after_ter" type="text"
                                    value="">

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-flat" data-dismiss="modal"><i class="fa fa-times"></i>
                    No</button>
                <button type="submit" class="btn btn-danger btn-flat"><i class="fa fa-close"></i> Terminate</button>
            </div>
            {!! Form::close() !!}
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
