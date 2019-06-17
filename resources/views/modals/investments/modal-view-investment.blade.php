<div class="modal fade in" tabindex="-1" id="modal-view-investment_{{$row->investment_id}}">
    <div class="modal-dialog modal-lg" style="width:90%">
        <div class="modal-content">

            <form>
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span
                            aria-hidden="true">×</span></button>
                    <h4 class="modal-title">View Investment <b>{{$row->trans_id}}</b></h4>

                </div>
                <div class="modal-body">

                    <div class="alert alert-danger message" style="display:none"></div>
                    <div class="row">
                        <div class="col-md-3">
                            <label for="date_today" class="col-sm-12 control-label label-left">Investment Date</label>
                            <div class="col-sm-12">
                                <input id="date_today" class="form-control" name="date_today" type="text"
                                    value="{{$row->inv_date}}">
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
                            <label for="date_today" class="col-sm-12 control-label label-left">Investment Amount</label>
                            <div class="col-sm-12">
                                <input id="date_today" class="form-control" name="date_today" type="text"
                                    value="Kshs {{ number_format($row->investment_amount, 2, '.', ',') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label for="franch" class="col-sm-12 control-label label-left">Investment Duration</label>
                            <div class="col-sm-12">
                                <input id="franch" class="form-control" name="franch" type="text"
                                    value="{{$row->investment_duration}} Months">

                            </div>
                        </div>

                        <div class="col-md-3">
                            <label for="franch" class="col-sm-12 control-label label-left">Investment Type</label>
                            <div class="col-sm-12">
                                <input id="franch" class="form-control" name="franch" type="text"
                                    value="{{$row->inv_type}}">

                            </div>
                        </div>
                        <div class="col-md-3">
                            <label for="franch" class="col-sm-12 control-label label-left">Last Payment Date</label>
                            <div class="col-sm-12">
                                <input id="franch" class="form-control" placeholder="Franch" name="franch" type="text"
                                    value="{{$row->last_pay_date}}">

                            </div>
                        </div>
                    </div>

                    <br />
                    <div class="row">
                        <div class="col-md-3">
                            <label for="franch" class="col-sm-12 control-label label-left">Total Payable Amount</label>
                            <div class="col-sm-12">
                                <input id="franch" class="form-control" name="franch" type="text"
                                    value="Kshs {{ number_format($row->tot_payable_amnt, 2, '.', ',') }}">

                            </div>
                        </div>
                        <div class="col-md-3">
                            <label for="franch" class="col-sm-12 control-label label-left">Monthly Payment</label>
                            <div class="col-sm-12">
                                <input id="franch" class="form-control" placeholder="Franch" name="franch" type="text"
                                    value="Kshs {{ number_format($row->monthly_amount, 2, '.', ',') }}">

                            </div>
                        </div>

                        <div class="col-md-3">
                            <label for="franch" class="col-sm-12 control-label label-left">Next Payment Date</label>
                            <div class="col-sm-12">
                                <input id="franch" class="form-control" placeholder="Franch" name="franch" type="text"
                                    value="{{$row->last_pay_date}}">

                            </div>
                        </div>
                        <div class="col-md-3">
                            <label for="franch" class="col-sm-12 control-label label-left">Number of Topups</label>
                            <div class="col-sm-12">
                                <input id="franch" class="form-control" placeholder="Franch" name="franch" type="text"
                                    value="0">

                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-md-3 " id="bckrow_from_bucket">
                            <div class="form-group">
                                <label for="from_bucket" class="col-sm-12 control-label label-left">Investment
                                    Mode</label>
                                <div class="col-sm-12">
                                    <input id="from_bucket" class="form-control" name="from_bucket" type="text"
                                        value="{{$row->inv_mode}}">
                                </div>
                            </div>
                        </div>
                        @if($row->inv_mode_id == 1)
                        <div class="col-md-3 " id="bckrow_from_before">
                            <div class="form-group">
                                <label for="from_before" class="col-sm-12 control-label label-left">MPESA Confirmtion
                                    Code</label>
                                <div class="col-sm-12">
                                    <input id="from_before" class="form-control" placeholder="Before"
                                        readonly="readonly" name="from_before" type="text"
                                        value="{{$row->mpesa_trans_code}}">
                                </div>
                            </div>
                        </div>
                        @elseif($row->inv_mode_id == 2)
                        <div class="col-md-3 " id="bckrow_from_after">
                            <div class="form-group">
                                <label for="from_after" class="col-sm-12 control-label label-left">Investment
                                    Bank</label>
                                <div class="col-sm-12">
                                    <input id="from_after" class="form-control" placeholder="After" name="from_after"
                                        type="text" value="{{$row->bank_name}}">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 " id="bckrow_before">
                            <div class="form-group">
                                <label for="before" class="col-sm-12 control-label label-left">Bank Transaction
                                    Code</label>
                                <div class="col-sm-12">
                                    <input id="before" class="form-control" placeholder="Before" name="before"
                                        type="text" value="{{$row->bank_trans_code}}">
                                </div>
                            </div>
                        </div>
                        @elseif($row->inv_mode_id == 3)
                        <div class="col-md-3 " id="bckrow_from_after">
                            <div class="form-group">
                                <label for="from_after" class="col-sm-12 control-label label-left">Bank Name</label>
                                <div class="col-sm-12">
                                    <input id="from_after" class="form-control" placeholder="After" name="from_after"
                                        type="text" value="{{$row->bank_name}}">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 " id="bckrow_before">
                            <div class="form-group">
                                <label for="before" class="col-sm-12 control-label label-left">Cheque Number</label>
                                <div class="col-sm-12">
                                    <input id="before" class="form-control" placeholder="Before" name="before"
                                        type="text" value="{{$row->cheque_no}}">
                                </div>
                            </div>
                        </div>
                        @endif
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="cust_status" class="col-sm-12 control-label label-left">Investment
                                    Status</label>
                                <div class="col-sm-12">
                                    <input id="cust_status" class="form-control" placeholder="Status" name="cust_status"
                                        type="text" value="Pending">
                                </div>
                            </div>
                        </div>
                    </div>
                    <br />
                    <div class="row">
                        <div class="col-md-3">
                            <label for="date_today" class="col-sm-12 control-label label-left">Served By</label>
                            <div class="col-sm-12">
                                <input id="date_today" class="form-control" name="date_today" type="text"
                                    value="{{$row->created_by_name}}">
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
                    <button class="btn btn-primary " data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>

        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
