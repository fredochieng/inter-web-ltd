<div class="modal fade in" tabindex="-1" id="modal-view-topup_{{$row->topup_id}}">
    <div class="modal-dialog modal-lg" style="width:90%">
        <div class="modal-content">
            <form>
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span
                            aria-hidden="true">×</span></button>
                    <h4 class="modal-title">Topup Details - <strong>{{$row->name}}</strong></h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-3">
                            <label for="date_today" class="col-sm-12 control-label label-left">Topup Date</label>
                            <div class="col-sm-12">
                                <input id="date_today" class="form-control" name="date_today" type="text"
                                    value="{{$row->topped_date}}">
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
                            <label for="franch" class="col-sm-12 control-label label-left">Phone Number</label>
                            <div class="col-sm-12">
                                <input id="franch" class="form-control" name="franch" type="text"
                                    value="{{$row->telephone}}">

                            </div>
                        </div>
                        <div class="col-md-3">
                            <label for="date_today" class="col-sm-12 control-label label-left">Topup Amount</label>
                            <div class="col-sm-12">
                                <input id="date_today" class="form-control" name="date_today" type="text"
                                    value="Kshs {{ number_format($row->topup_amount, 2, '.', ',') }}">
                            </div>
                        </div>
                        @if($row->inv_mode_id == 2)
                        <div class="col-md-2">
                            @elseif($row->inv_mode_id == 1)
                            <div class="col-md-3">
                                @elseif($row->inv_mode_id == 3)
                                <div class="col-md-2">
                                    @elseif($row->inv_mode_id == 4)
                                    <div class="col-md-3">
                                        @endif
                                        <label for="franch" class="col-sm-12 control-label label-left">Topup
                                            Mode</label>
                                        <div class="col-sm-12">
                                            <input id="franch" class="form-control" name="franch" type="text"
                                                value="{{$row->inv_mode}}">

                                        </div>
                                    </div>
                                    {{-- </div>
                        </div> --}}
                                    @if($row->inv_mode_id == 1)
                                    <div class="col-md-3">
                                        <label for="franch" class="col-sm-12 control-label label-left">Mpesa Transaction
                                            Code</label>
                                        <div class="col-sm-12">
                                            <input id="franch" class="form-control" name="franch" type="text"
                                                value="{{$row->mpesa_trans_code}}">

                                        </div>
                                    </div>
                                    @elseif($row->inv_mode_id == 2)
                                    <div class="col-md-2">
                                        <label for="franch" class="col-sm-12 control-label label-left">Bank
                                            Name</label></label>
                                        <div class="col-sm-12">
                                            <input id="franch" class="form-control" name="franch" type="text"
                                                value="{{$row->bank_name}}">

                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <label for="franch" class="col-sm-12 control-label label-left">Bank Transaction
                                            Code</label></label>
                                        <div class="col-sm-12">
                                            <input id="franch" class="form-control" name="franch" type="text"
                                                value="{{$row->bank_trans_code}}">

                                        </div>
                                    </div>
                                    @elseif($row->inv_mode_id == 3)
                                    <div class="col-md-2">
                                        <label for="franch" class="col-sm-12 control-label label-left">Bank
                                            Name</label></label>
                                        <div class="col-sm-12">
                                            <input id="franch" class="form-control" name="franch" type="text"
                                                value="{{$row->bank_name}}">

                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <label for="franch" class="col-sm-12 control-label label-left">Cheque
                                            Number</label></label>
                                        <div class="col-sm-12">
                                            <input id="franch" class="form-control" name="franch" type="text"
                                                value="{{$row->cheque_no}}">

                                        </div>
                                    </div>
                                    @endif
                                </div>
                                <br />
                                <div class="row">
                                    <div class="col-md-3">
                                        <label for="date_today" class="col-sm-12 control-label label-left">Served
                                            By</label>
                                        <div class="col-sm-12">
                                            <input id="date_today" class="form-control" name="date_today" type="text"
                                                value="{{$row->served_by_name}}">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="date_today" class="col-sm-12 control-label label-left">Initiated
                                            On</label>
                                        <div class="col-sm-12">
                                            <input id="date_today" class="form-control" name="date_today" type="text"
                                                value="{{$row->topped_date}}">
                                        </div>
                                    </div>
                                </div>


                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default pull-right"
                                        data-dismiss="modal">Close</button>

                                </div>
            </form>
        </div>

        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
