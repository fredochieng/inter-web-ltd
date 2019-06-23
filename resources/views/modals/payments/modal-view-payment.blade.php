<div class="modal fade in" tabindex="-1" id="modal-view-payment_{{$row->payment_id}}">
    <div class="modal-dialog modal-lg" style="width:90%">
        <div class="modal-content">
            <form>
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span
                            aria-hidden="true">×</span></button>
                    <h4 class="modal-title">Payment Details <b>{{$row->trans_id}}</b></h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-3">
                            <label for="date_today" class="col-sm-12 control-label label-left">Payment Date</label>
                            <div class="col-sm-12">
                                <input id="date_today" class="form-control" name="date_today" type="text"
                                    value="{{$row->user_pay_date}}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label for="franch" class="col-sm-12 control-label label-left">Account Number</label>
                            <div class="col-sm-12">
                                <input id="franch" class="form-control" name="franch" type="text"
                                    value="{{$row->account_no}}">

                            </div>
                        </div>

                        <div class="col-md-3">
                            <label for="franch" class="col-sm-12 control-label label-left">Client Full Name</label>
                            <div class="col-sm-12">
                                <input id="franch" class="form-control" name="franch" type="text"
                                    value="{{$row->name}}">

                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="franch" class="col-sm-12 control-label label-left">Client ID Number</label>
                                <div class="col-sm-12">
                                    <input id="franch" class="form-control" placeholder="Franch" name="franch"
                                        type="text" value="{{$row->id_no}}">

                                </div>
                            </div>
                        </div>
                    </div>
                    <br />

                    <div class="row">
                        <div class="col-md-3">
                            <label for="date_today" class="col-sm-12 control-label label-left">Payment Amount</label>
                            <div class="col-sm-12">
                                <input id="date_today" class="form-control" name="date_today" type="text"
                                    value="Kshs {{ number_format($row->payment_amount, 2, '.', ',') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label for="franch" class="col-sm-12 control-label label-left">Payment Mode</label>
                            <div class="col-sm-12">
                                <input id="franch" class="form-control" name="franch" type="text"
                                    value="{{$row->method_name}}">

                            </div>
                        </div>
                        @if($row->pay_mode_id == '2')
                        <div class="col-md-3">
                            <label for="franch" class="col-sm-12 control-label label-left">Bank Name</label>
                            <div class="col-sm-12">
                                <input id="franch" class="form-control" name="franch" type="text"
                                    value="{{$row->bank_name}}">

                            </div>
                        </div>
                        <div class="col-md-3">
                            <label for="franch" class="col-sm-12 control-label label-left">Bank Account</label>
                            <div class="col-sm-12">
                                <input id="franch" class="form-control" placeholder="" name="franch" type="text"
                                    value="{{$row->pay_bank_acc}}">

                            </div>
                        </div>
                        @else
                        <div class="col-md-6">
                            <label for="franch" class="col-sm-12 control-label label-left">Mpesa Number</label>
                            <div class="col-sm-12">
                                <input id="franch" class="form-control" name="franch" type="text"
                                    value="{{$row->pay_mpesa_no}}">

                            </div>
                        </div>
                        @endif
                    </div>
                    <br />
                    <div class="row">
                        <div class="col-md-3">
                            <label for="franch" class="col-sm-12 control-label label-left">Mpesa/Bank Code</label>
                            <div class="col-sm-12">
                                <input id="franch" class="form-control" name="franch" type="text"
                                    value="{{$row->conf_code}}">

                            </div>
                        </div>
                        <div class="col-md-3">
                            <label for="date_today" class="col-sm-12 control-label label-left">Served By</label>
                            <div class="col-sm-12">
                                <input id="date_today" class="form-control" name="date_today" type="text"
                                    value="{{$row->served_by_name}}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label for="date_today" class="col-sm-12 control-label label-left">Initiated On</label>
                            <div class="col-sm-12">
                                <input id="date_today" class="form-control" name="date_today" type="text"
                                    value="{{$row->created_at}}">
                            </div>
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-right" data-dismiss="modal">Close</button>

                </div>
                {!! Form::close() !!}
        </div>

        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
