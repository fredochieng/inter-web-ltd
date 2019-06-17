@extends('adminlte::page')

@section('title', 'Edit Client - Inter Web Ltd')

@section('content_header')
<h1>MANAGE CLIENT - {{$customer_data->account_no}}</h1>
@stop

@section('content')
<div class="box box-info">
    <div class="box-header with-border">
        <h3 class="box-title">MANAGE CLIENT</h3>
        <p class="pull-right">
            <a href="" data-toggle="modal" data-target="#modal_new_topup" data-backdrop="static" data-keyboard="false"
                class="btn btn-info btn-sm ad-click-event">NEW TOP UP</a>
            <a href="" data-toggle="modal" data-target="#modal_add_payment" data-backdrop="static" data-keyboard="false"
                class="btn btn-warning btn-sm ad-click-event">NEW PAYMENT</a>
        </p>
    </div>
    <!-- /.box-header -->
    <div class="box-body  with-border">
        <table class="table table-no-margin">
            <tbody style="font-size:12px">
                <tr>
                    <td style=""><strong>ACCOUNT NO :</strong>{{$customer_data->account_no}}</td>
                    <td style=""><strong>FULL NAME: </strong> {{$customer_data->name}}</td>
                    <td style=""><strong>ID NUMBER:</strong>{{$customer_data->id_no}}</td>
                    <td style=""><strong>PHONE NUMBER: </strong> {{$customer_data->telephone}}</td>
                    @if(empty($next_pay_date))
                    <td>N/A</td>
                    @else
                    <td style=""><strong>NEXT PAY DATE: </strong>{{$next_pay_date}}</td>
                    @endif
                    @if($customer_data->inv_type_id == 2)
                    <td style=""><strong>PAY DATE: </strong>{{$next_pay_date}}</td>
                    @endif
                </tr>
                <tr>
                    <td style=""><strong> INVESTMENTS :</strong>Kshs
                        {{ number_format($customer_investments->user_sum,2,'.',',')}}</td>
                    {{-- <td style=""><strong> PAYABLE AMOUNT :</strong>Kshs
                        {{ number_format($tot_payable->user_tot_payable,2,'.',',')}}</td> --}}
                    <td style=""><strong> PAYMENTS MADE :</strong>Kshs
                        {{ number_format($customer_payments->total_payments_made,2,'.',',')}}</td>
                    <td style=""><strong> DUE PAYMENTS :</strong>Kshs
                        {{ number_format($tot_due_payments->total_due_payments,2,'.',',')}}</td>
                    @if($customer_data->inv_type_id == 1 && $customer_data->topped_up==0)
                    <td style=""><strong> NEXT PAYMENT :</strong>Kshs {{ number_format($monthly_amnt,2,'.',',')}}</td>
                    @endif
                    @if($customer_data->inv_type_id == 1 && $customer_data->topped_up==1)
                    <td style=""><strong> NEXT PAYMENT :</strong>Kshs
                        {{ number_format($updated_monthly_amnt,2,'.',',')}}</td>
                    @endif
                    @if($customer_data->inv_type_id == 2)
                    <td style=""><strong> COMPOUND PAYMENT :</strong>Kshs
                        {{ number_format($comp_payable_amout,2,'.',',')}}</td>
                    @endif
                    @if($customer_data->inv_type_id == 3)
                    <td style=""><strong> NEXT PAYMENT :</strong>Kshs
                        {{ number_format($tot_payable->monthly_amount,2,'.',',')}}</td>
                    @endif
                </tr>
                <tr>
                    @if($customer_data->inv_type_id == 3)
                    <td style=""><strong> COMPOUND AMOUNT :</strong>Kshs {{ number_format($tot_comp_amount,2,'.',',')}}
                    </td>
                    <td style=""><strong> COMPOUND PAYMENT DATE :</strong>{{$customer_investments->last_pay_date}}</td>
                    @endif
                    {{--  <td style=""><strong> COMMISSION EARNED :</strong>Kshs 30,000.00</td>  --}}
                </tr>
            </tbody>
        </table>
    </div>
</div>
<div class="nav-tabs-custom">
    <ul class="nav nav-tabs">
        <li class="active"><a href="#basic_details" data-toggle="tab" aria-expanded="true"><strong><i
                        class="fa fa-fw fa-user"></i> BASIC DETAILS</strong></a></li>
        <li class=""><a href="#investments" data-toggle="tab" aria-expanded="false"><strong><i class="fa fa-money"></i>
                    INVESTMENTS</strong></a></li>
        <li class=""><a href="#topups" data-toggle="tab" aria-expanded="false"><strong><i class="ion ion-cash"></i> TOP
                    UPS</strong></a></li>
        <li class=""><a href="#payments" data-toggle="tab" aria-expanded="false"><strong><i class="fa fa-dollar"></i>
                    PAYMENTS</strong></a></li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane active" id="basic_details">
            <div class="box-body">
                {{-- {!! Form::open(['url' => action('UserController@update'), 'method' => 'post', 'id' => 'AddClientForm'
                ]) !!} --}}
                <div class="tab-content">
                    <div class="tab-pane active" id="info-tab">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="row">
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            {!! Form::label('Full Name') !!}
                                            {!! Form::text('name', $customer_data->name, ['class' => 'form-control',
                                            'required']); !!}
                                        </div>
                                    </div>
                                    <!-- <div class="col-md-1"></div> -->
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            {!! Form::label('Email Address') !!}
                                            {!! Form::email('email', $customer_data->email, ['class' => 'form-control',
                                            'required' ]); !!}
                                        </div>
                                    </div>
                                    <!-- <div class="col-md-1"></div> -->
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            {!! Form::label(' Phone Number') !!}
                                            {!! Form::text('telephone', $customer_data->telephone, ['class' =>
                                            'form-control', 'required']); !!}
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="input-group">
                                            {!! Form::label('Date of Birth') !!}
                                            {{Form::text('dob', $customer_data->dob, ['class' => 'form-control dob', 'id' => 'dob', 'required' ])}}

                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            {!! Form::label('ID Number') !!}
                                            {!! Form::text('id_no', $customer_data->id_no, ['class' => 'form-control',
                                            'required']); !!}
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            {!! Form::label(' Account Number') !!}
                                            {!! Form::text('account_no', $customer_data->account_no, ['class' =>
                                            'form-control' ]); !!}
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            {!! Form::label('Home Town') !!}
                                            {!! Form::text('home_town', $customer_data->home_town, ['class' =>
                                            'form-control', 'required']); !!}
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            {!! Form::label(' Home Address') !!}
                                            {!! Form::text('home_address', $customer_data->home_address, ['class' =>
                                            'form-control', 'required' ]); !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            {!! Form::label('Next of Kin') !!}
                                            {!! Form::text('kin_name', $customer_data->kin_name, ['class' =>
                                            'form-control', 'required']); !!}
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            {!! Form::label('Next of Kin Phone Number') !!}
                                            {!! Form::text('kin_telephone', $customer_data->kin_telephone, ['class' =>
                                            'form-control', 'required']); !!}
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            {{Form::label('Refered By')}}
                                            <div class="form-group">
                                                {{Form::text('referer_name', '',['class'=>'form-control', 'readonly', 'id' => 'referer_name'])}}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            {{Form::label('Payment Mode')}}
                                            <div class="form-group">
                                                {{Form::text('referer_name',  $customer_data->method_name,['class'=>'form-control', 'readonly', 'id' => 'referer_name'])}}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">

                                    @if($customer_data->method_name == 'MPESA')
                                    <div class="col-md-3 mpesa_number_div" id="mpesa_number_div">
                                        <div class="form-group">
                                            {{Form::label('MPESA Number')}}
                                            <div class="form-group">
                                                {{Form::text('pay_mpesa_no', $customer_data->pay_mpesa_no,['class'=>'form-control', 'id' => 'mpesa_number'])}}
                                            </div>
                                        </div>
                                    </div>
                                    @else
                                    <div class="col-md-3 bank_payment_acc" id="bank_payment_acc">
                                        <div class="form-group">
                                            {{Form::label('Bank Name')}}
                                            <div class="form-group">
                                                {{Form::text('pay_bank_acc', $customer_data->bank_name,['class'=>'form-control'])}}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 bank_payment_acc" id="bank_payment_acc">
                                        <div class="form-group">
                                            {{Form::label('Bank Account')}}
                                            <div class="form-group">
                                                {{Form::text('pay_bank_acc', $customer_data->pay_bank_acc,['class'=>'form-control'])}}
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                {{--  <div class="col-md-12">
                <div class="form-group"> <br>
                    <input type="hidden" name="subs_id" value="900004">
                    <button type="submit" class="btn btn-info btn-flat add_sub_part_1"><strong>UPDATE BASIC
                            DETAILS</strong></button>
                </div>
            </div>  --}}

                {!! Form::close() !!}
                <div style="clear:both"></div>

                <input name="_method" type="hidden" value="PUT">
                </form>
            </div>
        </div>
        <!-- /.tab-pane -->
        <div class="tab-pane" id="investments">
            <div class="box-body">
                <br>
                <h4>All investments related to this client &nbsp;
                    <div style="clear:both"></div>
                </h4>
                <div style="clear:both"></div>
                <div class="table-responsive">
                    <table id="example1" class="table table-no-margin" style="font-size:12px">
                        <thead>
                            <tr>
                                <th>Transaction ID</th>
                                <th>Investment Date</th>
                                <th>Duration</th>
                                <th>Investment Type</th>
                                <th>Invested Amount</th>
                                {{-- <th>Payable Amount</th> --}}
                                <th>Monthly Payment</th>
                                <th>Last Payment Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($customer_trans as $count=> $row)
                            <tr>
                                <!-- <td>{{ $count + 1 }}</td> -->
                                <td><b>{{$row->trans_id }}</b></td>
                                <td>{{ date('Y-m-d', strtotime($row->inv_date))}}</td>
                                <td>{{$row->investment_duration }} Months</td>
                                <td>{{$row->inv_type }}</td>
                                <td>Kshs {{ number_format($row->investment_amount, 2, '.', ',')}}</td>
                                <td>Kshs {{ number_format($row->tot_payable_amnt, 2, '.', ',') }}</td>
                                {{-- <td>Kshs {{ number_format($row->monthly_amount, 2, '.', ',') }}</td> --}}
                                <td>{{ date('Y-m-d', strtotime($row->last_pay_date))}}</td>
                                <td><a class="viewModal btn btn-info btn-sm" title="View Investment" href="#"
                                        data-toggle="modal" data-target="#modal-view-investment_{{$row->investment_id}}"
                                        data-backdrop="static" data-keyboard="false"><i class="fa fa-eye"></i> View
                                        Investment</a></td>
                            </tr>
                            @include('modals.investments.modal-view-investment')
                            @endforeach
                    </table>
                </div>
            </div>


        </div>
        <div class="tab-pane" id="topups">
            <div class="box-body">
                <br>
                <h4>All investment topups related to this client &nbsp;
                    <div style="clear:both"></div>
                </h4>
                <div style="clear:both"></div>
                <div class="table-responsive">
                    <table id="example1" class="table table-no-margin" style="font-size:12px">
                        <thead>
                            <tr>
                                <th>S/N</th>
                                <th>Topup Amount</th>
                                <th>Topup Date</th>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($client_topups as $count=> $row)
                            <tr>
                                <td>{{ $count + 1 }}</td>
                                <td>Ksh {{$row->topup_amount }}</td>
                                <td>{{$row->created_at }}</td>
                            </tr>
                            @endforeach
                    </table>
                </div>
            </div>
        </div>
        <div class="tab-pane" id="payments">
            <div class="box-body">
                <br>
                <h4>All payments related to this client &nbsp;
                    <div style="clear:both"></div>
                </h4>
                <div style="clear:both"></div>
                <div class="table-responsive">
                    <table id="example1" class="table table-no-margin" style="font-size:12px">
                        <thead>
                            <tr>
                                <th>S/N</td>
                                <th>Transaction ID</th>
                                <th>Payment Date</th>
                                <th>Payment Amount</th>
                                <th>Paid At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($client_payments as $count=> $row)
                            <tr>
                                <td>{{ $count + 1 }}</td>
                                <td><b>{{$row->trans_id }}</b></td>
                                <td>{{ date('Y-m-d', strtotime($row->user_pay_date))}}</td>
                                <td>Kshs {{ number_format($row->payment_amount, 2, '.', ',')}}</td>
                                <td>{{ $row->payment_created_at}}</td>
                            </tr>
                            @endforeach
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /.tab-content -->
@include('modals.topups.modal_new_topup')
@include('modals.payments.modal_add_payment')
</div>

@stop
@section('css')
<link rel="stylesheet" href="/css/admin_custom.css">
<link rel="stylesheet" href="/css/bootstrap-datepicker.min.css">
@stop
@section('js')

<script src="/js/bootstrap-datepicker.min.js"></script>
<script src="/js/select2.full.min.js"></script>

<script>
    $(function() {

        $("#pay_mode_id").change(function() {
                    var val = $(this).val();
                    if (val == 1 ) {
                    $("#mpesa_number_div").removeClass("hide");
                    }else{
                    $("#mpesa_number_div").addClass("hide");
                    }
                    if (val == 2 ) {
                    $("#bank_payment_div").removeClass("hide");
                // $("#bank_payment_acc").removeClass("hide");
                    }
                    else{
                    $("#bank_payment_div").addClass("hide");
                //    $("#bank_payment_acc").addClass("hide");
                    }
        });


        $("#inv_bank_id").change(function() {
            var value = $(this).val();
            if (value != 0 ) {
            $("#inv_bank_trans_id").removeClass("hide");
            }
            else{
            $("#inv_bank_trans_id").addClass("hide");
            }
            });

            $("#inv_cheq_bank_id").change(function() {
            var value = $(this).val();
            if (value != 0 ) {
            $("#cheq_no_inv_div").removeClass("hide");
            }
            else{
            $("#cheq_no_inv_div").addClass("hide");
            }
            });
          // Investments Modes Selection
          $("#inv_mode_id").change(function() {
            var val = $(this).val();
                if (val == 1 ) {
                $("#mpesa_inv_div").removeClass("hide");
                $("#inv_bank_trans_id").addClass("hide");
                $("#cheq_no_inv_div").addClass("hide");
                }else{
                $("#mpesa_inv_div").addClass("hide");
                }
            if (val == 2 ) {
            $("#bank_inv_div").removeClass("hide");
            $("#bank_payment_acc").removeClass("hide");
            $("#cheq_no_inv_div").addClass("hide");
            }
            else{
            $("#bank_inv_div").addClass("hide");
            // $("#bank_payment_acc").addClass("hide");
            }
            if (val == 3 ) {
            $("#cheq_inv_div").removeClass("hide");
            // $("#bank_payment_acc").removeClass("hide");
            }
            else{
            $("#cheq_inv_div").addClass("hide");
            // $("#bank_payment_acc").addClass("hide");
            }
            if(val == 4){
                $("#mpesa_inv_div").addClass("hide");
                $("#bank_inv_div").addClass("hide");
                $("#inv_bank_trans_id").addClass("hide");
                $("#cheq_inv_div").addClass("hide");
            }
            });
        $(".select2").select2()
        $('#example1').DataTable()
    })
</script>


@stop
