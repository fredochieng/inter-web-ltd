<div class="modal fade in" id="modal_new_payment">
    <div class="modal-dialog modal-lg" style="width:90%">
        <div class="modal-content">
            {!! Form::open(['url' => action('PaymentController@store'), 'method' => 'post']) !!}

            <input type="hidden" id="account_id" name="account_id">
            <input type="hidden" id="inv_type" name="inv_type">
            <input type="hidden" id="user_id" name="user_id">
            <input type="hidden" id="pay_times" name="pay_times">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span
                        aria-hidden="true">×</span></button>
                <h4 class="modal-title">New Payment</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-3">
                        <label for="franch" class="col-sm-12 control-label label-left">Account Number</label>
                        <div class="input-group">
                            <input id="acc" class="form-control" placeholder="Account Number" readonly="readonly"
                                required="required" name="acc" type="text" value="">
                            <span class="input-group-btn">
                                <a href="" class="btn btn-info btn-flat" data-toggle="modal"
                                    data-target="#modal_search_client"> <i class="fa fa-search"></i> </a>
                            </span>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <label for="franch" class="col-sm-12 control-label label-left">Client Full Name</label>
                        <div class="col-sm-12">
                            <input id="name" class="form-control" readonly="readonly" name="name" type="text" value="">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label for="franch" class="col-sm-12 control-label label-left">ID Number</label>
                        <div class="col-sm-12">
                            <input id="id_no" class="form-control" readonly="readonly" name="id_no" type="text"
                                value="">

                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="franch" class="col-sm-12 control-label label-left">Phone Number</label>
                            <div class="col-sm-12">
                                <input id="telephone" class="form-control" placeholder="" readonly="readonly"
                                    name="telephone" type="text" value="">

                            </div>
                        </div>
                    </div>
                </div>

                <br />
                <div class="row">
                    <div class="col-md-3">
                        <label for="franch" class="col-sm-12 control-label label-left">Total Due Payments</label>
                        <div class="col-sm-12">
                            <input id="tot_payable_amnt" class="form-control" readonly="readonly"
                                name="tot_payable_amnt" type="text" value="">

                        </div>
                    </div>

                    <div class="col-md-3">
                        <label for="franch" class="col-sm-12 control-label label-left">Payment Date</label>
                        <div class="col-sm-12">
                            <input id="user_date" class="form-control" placeholder="" readonly="readonly"
                                name="user_date" type="text" value="">

                        </div>
                    </div>

                    <div class="col-md-3">
                        <label for="franch" class="col-sm-12 control-label label-left">Mothly Payment Amount</label>
                        <div class="col-sm-12">
                            <input id="monthly_amount" class="form-control" placeholder="" readonly="readonly"
                                name="monthly_pay" type="text" value="">

                        </div>
                    </div>
                    <div class="col-md-3" id="monthly_amount_div">
                        <label for="franch" class="col-sm-12 control-label label-left">Total Compounded Amount</label>
                        <div class="col-sm-12">
                            <input id="tot_comp_amount" class="form-control" placeholder="" readonly="readonly"
                                name="comp_monthly_amount" type="text" value="">

                        </div>
                    </div>

                    {{-- <div class="col-md-3">
                        <label for="franch" class="col-sm-12 control-label label-left">Next Payment Date</label>
                        <div class="col-sm-12">
                            <input id="franch" class="form-control" placeholder="" name="franch" type="text" value="">

                        </div>
                    </div> --}}
                </div>
                <br />

                <div class="row">

                    <div class="col-md-3" id="tot_payment_div">
                        <label for="franch" class="col-sm-12 control-label label-left">Total Payment Amount</label>
                        <div class="col-sm-12">
                            <input id="tot_pay_amount" class="form-control" placeholder="" readonly="readonly"
                                name="tot_pay_amount" type="text" value="">

                        </div>
                    </div>
                </div>
                <br />
                <div class="row">
                    <div class="col-md-4">
                        <label for="date_today" class="col-sm-12 control-label label-left">Prefered Mode of
                            Payment</label>
                        <div class="col-sm-12">
                            <input id="method_name" class="form-control" readonly="readonly" name="method_name"
                                type="text" value="">
                        </div>
                    </div>

                    <div class="col-md-4 hide" id="bank_name_div">
                        <label for="franch" class="col-sm-12 control-label label-left">Bank Name</label>
                        <div class="col-sm-12">
                            <input id="bank_name" class="form-control" readonly="readonly" name=" bank_name" type="text"
                                value="">

                        </div>
                    </div>
                    <div class="col-md-4 hide" id="bank_account_div">
                        <label for="franch" class="col-sm-12 control-label label-left">Bank Account Number</label>
                        <div class="col-sm-12">
                            <input id="pay_bank_acc" class="form-control" readonly="readonly" placeholder=""
                                name="pay_bank_acc" type="text" value="">

                        </div>
                    </div>
                    <div class="col-md-4 hide" id="pay_mpesa_no_div">
                        <label for="franch" class="col-sm-12 control-label label-left">MPESA Number</label>
                        <div class="col-sm-12">
                            <input id="pay_mpesa_no" class="form-control" readonly="readonly" name="pay_mpesa_no"
                                type="text" value="">

                        </div>
                    </div>

                </div>

                <hr>
                <br />
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                <button class="btn btn-primary" id="transactionPost" type="submit"><i class="fa fa-check"></i> MAKE
                    PAYMENT</button>
            </div>

            </form>
        </div>

        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>

@push('js')

@endpush
