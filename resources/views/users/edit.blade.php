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
            @if($approved =='Y' && $fully_paid == 'N')
            <button data-toggle="modal" data-target="#modal_new_topup" data-backdrop="static" data-keyboard="false"
                class="btn bg-aqua margin"><i class="fa fa-plus"></i> NEW TOPUP</button>
            @elseif($approved =='N' && $fully_paid == 'N')
            <button data-toggle="modal" disabled data-target="#modal_new_topup" data-backdrop="static"
                data-keyboard="false" class="btn bg-aqua margin"><i class="fa fa-plus"></i> NEW TOPUP</button>
            @elseif($approved =='Y' && $fully_paid == 'Y')
            <button data-toggle="modal" disabled data-target="#modal_new_topup" data-backdrop="static"
                data-keyboard="false" class="btn bg-aqua margin"><i class="fa fa-plus"></i> NEW TOPUP</button>
            @endif
            @if((auth()->user()->can('payments.manage')) && $approved =='Y' && $fully_paid == 'N')
            <button data-toggle="modal" data-target="#modal_add_payment" data-backdrop="static" data-keyboard="false"
                class="btn bg-purple margin"><i class="fa fa-check"></i> CONFIRM PAYMENT</button>
            @elseif((auth()->user()->can('payments.manage')) && $approved =='N' && $fully_paid == 'N')
            <button data-toggle="modal" disabled data-target="#modal_add_payment" data-backdrop="static"
                data-keyboard="false" class="btn bg-purple margin"><i class="fa fa-check"></i> CONFIRM PAYMENT</button>
            @elseif((auth()->user()->can('payments.manage')) && $approved =='Y' && $fully_paid == 'Y')
            <button data-toggle="modal" disabled data-target="#modal_add_payment" data-backdrop="static"
                data-keyboard="false" class="btn bg-purple margin"><i class="fa fa-check"></i> CONFIRM PAYMENT</button>
            @endif
        </p>
    </div>
    <!-- /.box-header -->
    <div class="box-body  with-border">
        <table class="table table-no-margin">
            <tbody style="font-size:12px">
                <tr>
                    <td style=""><strong>ACCOUNT # :</strong>{{$customer_data->account_no}}</td>
                    <td style=""><strong>FULL NAME: </strong> {{$customer_data->name}}</td>
                    <td style=""><strong>ID NUMBER:</strong>{{$customer_data->id_no}}</td>
                    <td style=""><strong>PHONE NUMBER: </strong> {{$customer_data->telephone}}</td>
                    @if($customer_data->inv_status_id == 0)
                    <td><strong>INVESTMENT STATUS: <span class="label label-warning"> PENDING</span></strong></td>
                    @else
                    <td><strong> INVETSMENT STATUS: <span class="label label-success"> APPROVED</span></strong></td>
                    @endif
                </tr>
                <tr>
                    @if(empty($next_pay_date))
                    <td>N/A</td>
                    @elseif(!empty($next_pay_date) && $approved == 'Y')
                    <td style=""><strong>NEXT PAY DATE: </strong>{{$next_pay_date}}</td>
                    @elseif(!empty($next_pay_date) && $approved == 'N')
                    <td style=""><strong>NEXT PAY DATE: </strong>N/A</td>
                    @endif
                    @if($approved == 'Y')
                    <td style=""><strong> INVESTMENTS :</strong>Kshs
                        {{ number_format($real_tot_inv,2,'.',',')}}</td>
                    @else
                    <td style=""><strong> INVESTMENTS :</strong>
                        N/A</td>
                    @endif
                    @if($approved == 'Y')
                    <td style=""><strong> PAYMENTS MADE :</strong>Kshs
                        {{ number_format($customer_payments->total_payments_made,2,'.',',')}}</td>
                    @else
                    <td style=""><strong> PAYMENTS MADE :</strong>
                        N/A</td>
                    @endif
                    @if($approved == 'Y' && $fully_paid == 'N')
                    <td style=""><strong> DUE PAYMENTS :</strong>Kshs
                        {{ number_format($tot_due_payments->total_due_payments,2,'.',',')}}</td>
                    @elseif($approved == 'Y' && $fully_paid == 'Y')
                    <td style=""><strong> DUE PAYMENTS :</strong>FULLY PAID</td>
                    @else
                    <td style=""><strong> DUE PAYMENTS :</strong>
                        N/A</td>
                    @endif
                    @if($customer_data->inv_type_id == 1 && $approved == 'Y' && $fully_paid =='N')
                    <td style=""><strong> NEXT PAYMENT :</strong>Kshs {{ number_format($next_amount,2,'.',',')}}</td>
                    @elseif($customer_data->inv_type_id == 1 && $approved == 'Y' && $fully_paid =='Y')
                    <td style=""><strong> NEXT PAYMENT :</strong>FULLY PAID</td>
                    @elseif($customer_data->inv_type_id == 1 && $approved == 'N')
                    <td style=""><strong> NEXT PAYMENT :</strong>N/A</td>
                    @endif
                    @if($customer_data->inv_type_id ==2 && $approved == 'Y' && $fully_paid == 'N')
                    <td style=""><strong> COMPOUND PAYMENT :</strong>Kshs
                        {{ number_format($comp_payable_amout,2,'.',',')}}</td>
                    @elseif($customer_data->inv_type_id ==2 && $approved == 'Y' && $fully_paid == 'Y')
                    <td style=""><strong> COMPOUND PAYMENT :</strong>FULLY PAID</td>
                    @elseif($customer_data->inv_type_id ==2 && $approved == 'N')
                    <td style=""><strong> COMPOUND PAYMENT :</strong>N/A</td>
                    @endif
                    @if($customer_data->inv_type_id == 3 && $approved == 'Y')
                    <td style=""><strong> NEXT PAYMENT :</strong>Kshs
                        {{ number_format($next_amount,2,'.',',')}}</td>
                    @elseif ($customer_data->inv_type_id == 3 && $approved == 'N')
                    <td style=""><strong> NEXT PAYMENT :</strong>N/A</td>
                    @endif
                </tr>
                <tr>
                    @if($customer_data->inv_type_id == 3 && $approved == 'Y')
                    <td style=""><strong> COMPOUND AMOUNT :</strong>Kshs {{ number_format($tot_comp_amount,2,'.',',')}}
                    </td>
                    <td style=""><strong> COMPOUND PAYMENT DATE :</strong>{{$customer_investments->last_pay_date}}</td>
                    @elseif ($customer_data->inv_type_id == 3 && $approved == 'N')
                    <td style=""><strong> COMPOUND AMOUNT :</strong>N/A
                    </td>
                    <td style=""><strong> COMPOUND PAYMENT DATE :</strong>N/A</td>
                    @endif
                </tr>
                <tr>
                    @if($tot_comm !='')
                    <td style=""><strong> COMMISSION AMOUNT :</strong>Kshs {{ number_format($tot_comm,2,'.',',')}}
                    </td>
                    @else
                    <td style=""><strong> COMMISSION AMOUNT :</strong>Kshs 0.00
                        @endif
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
        <li class=""><a href="#referals" data-toggle="tab" aria-expanded="false"><strong><i class="fa fa-users"></i>
                    REFERRED CLIENTS</strong></a></li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane active" id="basic_details">
            <div class="box-body">
                {{-- {!! Form::open(['url' => action('UserController@update'), 'method' => 'post', 'id' => 'AddClientForm'
                ]) !!} --}}

                {!!
                Form::open(['action'=>['UserController@update',$customer_data->user_id],'method'=>'PATCH','class'=>'form','enctype'=>'multipart/form-data'])
                !!}

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
                                            'form-control', 'readonly' ]); !!}
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
                                    <input type="hidden" name="referee_id" value="{{$customer_data->refered_by}}">
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
                                                {{Form::text('pay_mode_id',  $customer_data->method_name,['class'=>'form-control', 'readonly', 'id' => 'referer_name'])}}
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
                                                {{Form::text('pay_mpesa_no', $customer_data->pay_mpesa_no,['class'=>'form-control', 'id' => 'mpesa_number','readonly'])}}
                                            </div>
                                        </div>
                                    </div>
                                    @else
                                    <div class="col-md-3 bank_payment_acc" id="bank_payment_acc">
                                        <div class="form-group">
                                            {{Form::label('Bank Name')}}
                                            <div class="form-group">
                                                {{Form::text('pay_bank_acc', $customer_data->bank_name,['class'=>'form-control','readonly'])}}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 bank_payment_acc" id="bank_payment_acc">
                                        <div class="form-group">
                                            {{Form::label('Bank Account')}}
                                            <div class="form-group">
                                                {{Form::text('pay_bank_acc', $customer_data->pay_bank_acc,['class'=>'form-control','readonly'])}}
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group"> <br>
                        <input type="hidden" name="subs_id" value="900004">
                        <button type="submit" class="btn btn-info btn-flat add_sub_part_1"><strong>UPDATE BASIC
                                DETAILS</strong></button>
                    </div>
                </div>

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
                                <th>S/N</td>
                                <th>Transaction ID</th>
                                <th>Investment Date</th>
                                <th>Duration</th>
                                <th>Investment Type</th>
                                <th>Invested Amount</th>
                                @if($customer_data->inv_type==2)
                                <th>Payment Date</th>
                                @endif
                                <th>Status</th>
                                <th>View Investment</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{--  {{ $client_payment_modes}} --}}
                            @foreach ($customer_trans as $count=> $row)
                            <tr>
                                <td>{{ $count + 1 }}</td>
                                <td><b>{{$row->trans_id }}</b></td>
                                <td>{{ date('Y-m-d', strtotime($row->inv_date))}}</td>
                                <td>{{$row->investment_duration }} Months</td>
                                <td>{{$row->inv_type }}</td>
                                <td>Kshs {{ number_format($real_tot_inv, 2, '.', ',')}}</td>
                                @if($customer_data->inv_type==2)
                                <td>{{ date('Y-m-d', strtotime($row->last_pay_date))}}</td>
                                @endif
                                @if($row->inv_status_id == 0)
                                <td><span class="label label-warning">Pending</span></td>
                                @else
                                <td><span class="label label-success">Approved</span>
                                </td>
                                @endif
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
            {!! Form::close() !!}
        </div>
        <div class="tab-pane" id="topups">
            <div class="box-body">
                <br>
                <h4>All topups related to this client &nbsp;
                    <div style="clear:both"></div>
                </h4>
                <div style="clear:both"></div>
                <div class="table-responsive">
                    <table id="example1" class="table table-no-margin" style="font-size:12px">
                        <thead>
                            <tr>
                                <th>S/N</th>
                                <th>Topup Amount</th>
                                <th>Topup Mode</th>
                                <th>Served By</th>
                                <th>Topup Date</th>
                                <th>View Topup</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($client_topups as $count=> $row)
                            <tr>
                                <td>{{ $count + 1 }}</td>
                                <td>Ksh {{$row->topup_amount }}</td>
                                <td>{{$row->inv_mode }}</td>
                                <td>{{$row->served_by_name }}</td>
                                <td>{{$row->topped_at}}</td>
                                <td><a class="viewModal btn btn-info btn-sm" title="View Topup" href="#"
                                        data-toggle="modal" data-target="#modal-view-topup_{{$row->topup_id}}"
                                        data-backdrop="static" data-keyboard="false"><i class="fa fa-eye"></i> View
                                        Topup</a></td>
                            </tr>
                            @include('modals.topups.modal-view-topup')
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
                                <th>Payment Mode</th>
                                <th>Mpesa/Bank Trans Code</th>
                                <th>Paid At</th>
                                <th>Served By</th>
                                <th>Comments</th>
                                <th>View Payment</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($client_payments as $count=> $row)
                            <tr>
                                <td>{{ $count + 1 }}</td>
                                <td><b>{{$row->trans_id }}</b></td>
                                <td>{{ date('Y-m-d', strtotime($row->user_pay_date))}}</td>
                                <td>Kshs {{ number_format($row->payment_amount, 2, '.', ',')}}</td>
                                <td>{{ $row->method_name}}</td>
                                <td><b>{{$row->conf_code }}</b></td>
                                <td>{{ $row->payment_created_at}}</td>
                                <td>{{ $row->served_by_name}}</td>
                                <td><a href="" data-toggle="modal"
                                        data-target="#modal-show-payment-comments_{{$row->payment_id}}"><strong>
                                            <center>View</center>
                                        </strong></a></p>
                                </td>
                                <td><a class="viewModal btn btn-info btn-sm" title="View Payment" href="#"
                                        data-toggle="modal" data-target="#modal-view-payment_{{$row->payment_id}}"
                                        data-backdrop="static" data-keyboard="false"><i class="fa fa-eye"></i> View
                                        Payment</a></td>
                            </tr>
                            @include('modals.payments.modal-view-payment')
                            @include('modals.payments.modal-show-payment-comments')
                            @endforeach
                    </table>
                </div>
            </div>
        </div>
        <div class="tab-pane" id="referals">
            <div class="box-body">
                <br>
                <h4>All clients refered &nbsp;
                    <div style="clear:both"></div>
                </h4>
                <div style="clear:both"></div>
                <div class="table-responsive">
                    <table id="example1" class="table table-no-margin" style="font-size:12px">
                        <thead>
                            <tr>
                                <th>S/N</td>
                                <th>Name</th>
                                <th>Email</th>
                                <th>ID Number</th>
                                <th>Phone Number</th>
                                <th>Account #</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($referer as $count=> $row)
                            <tr>
                                <td>{{ $count + 1 }}</td>
                                <td><b>{{$row->name}}</b></td>
                                <td>{{ $row->email}}</td>
                                <td><b>{{$row->id_no }}</b></td>
                                <td>{{ $row->telephone}}</td>
                                <td>{{ $row->account_no}}</td>
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
@include('modals.payments.modal_view_payment_mode')
</div>

@stop
@section('css')
<link rel="stylesheet" href="/css/bootstrap-datepicker.min.css">
<link rel="stylesheet" href="/plugins/iCheck/all.css">
@stop
@section('js')

<script src="/js/bootstrap-datepicker.min.js"></script>
<script src="/js/select2.full.min.js"></script>
<script src="/plugins/iCheck/icheck.min.js"></script>
</script>
<script>
    $(function() {
        var form = document.getElementById("confirm_payment_form");
        form.reset();
        $("#pay_mode_id").change(function() {
                    var val = $(this).val();
                    if (val == 1 ) {

                    $("#mpesa_number_div").removeClass("hide");
                    $("#bank_acc_div").addClass("hide");
                    $("#bank_payment_div").addClass("hide");
                    }else{
                    $("#mpesa_number_div").addClass("hide");
                    }
                    if (val == 2 ) {

                    $("#bank_payment_div").removeClass("hide");
                    $("#bank_acc_div").removeClass("hide");
                    }
                    else{
                    $("#bank_payment_div").addClass("hide");
                    $("#bank_acc_div").addClass("hide");
                    }
        });
           // CONFIRM PAYMENT

        $(".select2").select2()
        $('#example1').DataTable()

        // TOPUP MODE

        $("#inv_mode_id").change(function() {
            var val = $(this).val();
            if (val == 1 ) {

            $("#mpesa_div").removeClass("hide");
            $("#bank_id_div").addClass("hide");
            $("#bank_trans_div").addClass("hide");
            $("#cheq_bank_div").addClass("hide");
            $("#cheq_no_div").addClass("hide");
            }else{
            $("#mpesa_div").addClass("hide");
            }
            if (val == 2 ) {

            $("#bank_id_div").removeClass("hide");
            $("#mpesa_div").addClass("hide");
            $("#bank_trans_div").removeClass("hide");
            $("#cheq_bank_div").addClass("hide");
            $("#cheq_no_div").addClass("hide");
            }
            else{
            $("#bank_id_div").addClass("hide");
            $("#bank_trans_div").addClass("hide");
            }

            if(val == 3 ){
                $("#cheq_bank_div").removeClass("hide");
                $("#cheq_no_div").removeClass("hide");
                $("#bank_id_div").addClass("hide");
                $("#mpesa_div").addClass("hide");
                $("#bank_trans_div").addClass("hide");
            }else{
                $("#cheq_bank_div").addClass("hide");
                $("#cheq_no_div").addClass("hide");
            }

});

$('input').keyup(function(){
    var totalInvestment  = Number($('#total_inv_amount').val());
   var monthlyInvestment = Number($('#monthly_inv_amount').val());
  var compoundedInvestment = totalInvestment - monthlyInvestment;
 document.getElementById('compounded_inv_amount').value = compoundedInvestment;
});
    })
</script>
<script>
    $(document).ready(function (){
        var max_limit = 1; // Max Limit
        $(".pay:input:checkbox").each(function (index){
            this.checked = (".pay:input:checkbox" < max_limit);
        }).change(function (){
            if ($(".pay:input:checkbox:checked").length > max_limit){
                this.checked = false;
            }
        });

        $('#confirmPayment').click(function() {
            checked = $("input[type=checkbox]:checked").length;

            if(!checked) {
              alert("You must check at least one payment mode.");
             // toast('No client found matching your entry', 'warning', 'top-right');
              return false;
            }

          });

          $('.topup_date').datepicker( {
            format: 'yyyy-mm-dd',
           orientation: "bottom",
           autoclose: true,
            showDropdowns: true,
            todayHighlight: true,
            toggleActive: true,
            clearBtn: true,
        })
    });
</script>

@stop
