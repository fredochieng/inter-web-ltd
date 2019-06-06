@extends('adminlte::page')

@section('title', 'Clients - Inter Web Ltd')

@section('content_header')
<h1>Client<small>Client Information</small></h1>
@stop

@section('content')
<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title">
            <i class="fa fa-user margin-r-5"></i>
            Customer Information (<b>{{ $customer_data->name }}</b>) </h3>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-sm-3">
                <div class="well well-sm">
                    <strong> Account Number</strong>
                    <p class="text-muted">{{ $customer_data->account_no }}</p>
                    <strong> ID Number</strong>
                    <p class="text-muted">{{ $customer_data->id_no }}</p>
                    <strong>Phone Number</strong>
                    <p class="text-muted">{{ $customer_data->telephone }}</p>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="well well-sm">
                    <strong> Home Address</strong>
                    <p class="text-muted">{{ $customer_data->home_address }}</p>
                    <strong> Next of kin</strong>
                    <p class="text-muted">{{ $customer_data->kin_name }}</p>
                    <strong> Next of kin telephone</strong>
                    <p class="text-muted">{{ $customer_data->kin_telephone }}</p>
                </div>
            </div>
            <div class="col-sm-3">
               <div class="well well-sm">
                <strong> Payment Mode</strong>
                <p class="text-muted">{{ $customer_data->method_name }}</p>
                @if($customer_data->method_id==2)
                <strong> Bank Name</strong>
                <p class="text-muted">{{ $customer_data->bank_name }}</p>
                <strong> Bank Account</strong>
                <p class="text-muted">{{ $customer_data->pay_bank_acc }}</p>
                @elseif($customer_data->method_id==1)
                <strong> MPESA Number</strong>
                <p class="text-muted">{{ $customer_data->pay_mpesa_no }}</p>
                @endif
            </div>
            </div>
            <div class="col-sm-3">
                <div class="well well-sm">
                    <strong>Total Investments</strong>
                    <p class="text-muted">
                        <span class="display_currency" data-currency_symbol="true">Kshs {{ number_format($customer_investments->user_sum,2,'.',',') }}</span>
                    </p>
                    <strong>Due Payment Amount</strong>
                    <p class="text-muted">
                        <span class="display_currency" data-currency_symbol="true">Kshs {{ number_format($customer_investments->user_total_payout,2,'.',',') }}</span>
                    </p>
                      <strong>Total Payments Made</strong>
                    <p class="text-muted">
                        <span class="display_currency" data-currency_symbol="true">Kshs
                            {{ number_format($customer_payments->total_payments_made,2,'.',',') }}</span>
                    </p>
                </div>
            </div>
           {{--  <div class="col-sm-3">
            <div class="well well-sm">
                <strong>Total Payments Made</strong>
                <p class="text-muted">
                    <span class="display_currency" data-currency_symbol="true">Kshs
                        {{ number_format($customer_payments->total_payments_made,2,'.',',') }}</span>
                </p>
                {{--  <strong>Total Due Interests</strong>
                <p class="text-muted">
                    <span class="display_currency" data-currency_symbol="true">Kshs
                        {{ number_format($customer_investments->user_payout,2,'.',',') }}</span>
                </p>  --}}
                {{--  <strong>Total Due Payments</strong>
                <p class="text-muted">
                    <span class="display_currency" data-currency_symbol="true">Kshs
                        {{ number_format($customer_investments->total_due_payments,2,'.',',') }}</span>
                </p>
            </div>  --}}
        {{--  </div>   --}}
            <div class="clearfix"></div>
            <div class="col-sm-12">
            </div>
        </div>
    </div>
</div>
<!-- /.box-body -->

<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title">
            <i class="fa fa-money margin-r-5"></i>
            All investments related to this customer </h3>
    </div>
    {{--  <div class="box-body">
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group">
                    <div class="input-group">
                        <button type="button" class="btn btn-primary" id="daterange-btn">
                            <span>
                                <i class="fa fa-calendar"></i> Filter by date
                            </span>
                            <i class="fa fa-caret-down"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>  --}}
    <div class="box-body">
        <div class="table-responsive">
            <table id="example1" class="table table-bordered table-striped dataTable" style="font-size:12px">
                <thead>
                    <tr>
                        {{-- <th>Transaction ID</th> --}}
                        <th>S/N</th>
                        <th>Investment Date</th>
                        <th>Amount(Kshs)</th>
                        <th>Interest Rate(%)</th>
                        <th>Due Interests(Kshs)</th>
                        <th>Due Payments(Kshs)</th>
                        <th>Maturity Date</th>
                    </tr>
                </thead>
                <tbody>
                     @foreach ($customer_trans as $count=> $row)
                    <tr>
                        <td>{{ $count + 1 }}</td>
                        {{-- <td><b>{{$row->trans_id }}</b></td> --}}
                         <td>{{ date('Y-m-d', strtotime($row->created_at))}}</td>
                        <td>Kshs {{ number_format($row->investment_amount, 2, '.', ',')}}</td>
                        <td>{{$row->interest_rate}}</td>
                        <td>Kshs {{ number_format($row->payout, 2, '.', ',')}}</td>
                        <td>Kshs {{ number_format($row->total_payout, 2, '.', ',') }}</td>
                        <td>{{ date('Y-m-d', strtotime($row->maturity_date))}}</td>
                     </tr>
                    @endforeach
                </tbody>
                {{--  <tfoot>
                    <tr class="bg-gray font-17 footer-total text-center">
                        <td colspan="2" rowspan="1"><strong>Total:</strong></td>
                        <td rowspan="1" colspan="1"><span class="display_currency" id="footer_sale_total"
                                data-currency_symbol="true">
                                <strong> KSHS {{ $sum_investments }}</strong></span></td>
                        <td rowspan="1" colspan="1"><span class="display_currency" id="footer_sale_total"
                                data-currency_symbol="true">
                            </span></td>
                        <td rowspan="1" colspan="1"><span class="display_currency" id="footer_total_paid"
                                data-currency_symbol="true">
                                <strong> KSHS {{ $sum_payout }}</span></td>
                        <td rowspan="1" colspan="1"><span class="display_currency" id="footer_total_remaining"
                                data-currency_symbol="true"><strong> KSHS {{ $sum_payout }}</span></td>
                        <td rowspan="1" colspan="2"></td>
                    </tr>
                </tfoot>  --}}
            </table>
        </div>
    </div>
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
    $(function () {
	 $('.dob').datepicker( {
	 	format: 'yyyy-mm-dd',
		orientation: "bottom",
		autoclose: true,
         showDropdowns: true,
         todayHighlight: true,
         toggleActive: true,
         clearBtn: true,
     })
          $(".select2").select2()
          $('#example1').DataTable()
 })
</script>
@stop
