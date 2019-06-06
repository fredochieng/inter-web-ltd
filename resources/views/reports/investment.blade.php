@extends('adminlte::page')

@section('title', 'Investments Report - BManager')

@section('content_header')
<h1>Reports<small>Investment report</small></h1>
@stop

@section('content')
<div class="row">
   {{-- <div class="pull-right box-tools">
    <button type="button" class="btn btn-primary btn-sm daterange pull-right" data-toggle="tooltip" title="Date range">
        <i class="fa fa-calendar"></i></button>
    <button type="button" class="btn btn-primary btn-sm pull-right" data-widget="collapse" data-toggle="tooltip"
        title="Collapse" style="margin-right: 5px;">
        <i class="fa fa-minus"></i></button>
</div> --}}
</div>
<div class="row">
    {{-- <div class="col-md-3 col-md-offset-7 col-xs-6">
        <div class="input-group">
            <span class="input-group-addon bg-light-blue"><i class="fa fa-map-marker"></i></span>
            <select class="form-control select2" id="profit_loss_location_filter">
                {{-- @foreach($business_locations as $key => $value)
                <option value="{{ $key }}">{{ $value }}</option>
    @endforeach --}}
    {{-- </select>
        </div>
    </div> --}}
    {{-- <div class="col-md-12 col-xs-8">
        <div class="form-group pull-right">
            <div class="input-group">
                <button type="button" class="btn btn-primary" id="profit_loss_date_filter">
                    <span>
                        <i class="fa fa-calendar"></i> Filter by date
                    </span>
                    <i class="fa fa-caret-down"></i>
                </button>
            </div>
        </div>
    </div> --}}
</div>
<br>
<div class="row">
    <div class="col-sm-6">
        <div class="box box-solid">
            <div class="box-header with-border">
                <h3 class="box-title">Investments</h3>
            </div>

            <div class="box-body">
                <table class="table table-striped">
                    <tbody>
                        <tr>
                            <th>Total Investments:</th>
                            <td>
                                <span class="total_purchase">Kshs {{ $total_investments }}</span>
                            </td>
                        </tr>
                        <tr>
                            <th>Total Payout:</th>
                            <td>
                                <span class="purchase_inc_tax">Kshs {{ $sum_payout }}</span>
                            </td>
                        </tr>
                        <tr>
                            <th>Total Due Payments:</th>
                            <td>
                                <span class="purchase_return_inc_tax">Kshs {{ $due_payments }}</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-sm-6">
        <div class="box box-solid">
            <div class="box-header with-border">
                <h3 class="box-title">Payments</h3>
            </div>

            <div class="box-body">
                <table class="table table-striped">
                    <tbody>
                        <tr>
                            <th>Total Payments:</th>
                            <td>
                                <span class="total_sell">Kshs </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Payments Due Today:</th>
                            <td>
                                <span class="sell_inc_tax">Kshs </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-sm-12">
        <div class="box box-solid">
            <div class="box-header with-border">
                <h3 class="box-title">Today Investments & Payments <i class="fa fa-info-circle text-info hover-q "
                        aria-hidden="true" data-container="body" data-toggle="popover" data-placement="auto"
                        data-content="-ve value = Amount to pay <br>+ve Value = Amount to receive" data-html="true"
                        data-trigger="hover"></i></h3>
            </div>

            <div class="box-body">

                <h3 class="text-muted">
                    Total Investments Today:
                    <span class="sell_minus_purchase text-success">Kshs {{ $today_total_investments}}</span>
                </h3>
            </div>
        </div>
    </div>
</div>
@stop
@section('css')
<link rel="stylesheet" href="/css/admin_custom.css">
<link rel="stylesheet" href="/css/bootstrap-datepicker.min.css">
<link rel="stylesheet" href="/css/daterangepicker.css">
@stop
@section('js')

<script src="/js/bootstrap-datepicker.min.js"></script>
<script src="/js/jquery.min.js"></script>
<script src="/js/jquery-ui.min.js"></script>
<script src="/js/daterangepicker.js"></script>
<script src="/js/select2.full.min.js"></script>
<script src="/js/report.js"></script>

<script>
    $(document).ready( function(){
        if($('#profit_loss_date_filter').length == 1){
            $('#profit_loss_date_filter').daterangepicker(
            dateRangeSettings,
            function (start, end) {
            $('#profit_loss_date_filter span').html(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
            updateProfitLoss();
            }
            );
            $('#profit_loss_date_filter').on('cancel.daterangepicker', function(ev, picker) {
            $('#profit_loss_date_filter').html('<i class="fa fa-calendar"></i> ' + LANG.filter_by_date);
            });
            updateProfitLoss();
            }

function updateProfitLoss(){

var start = $('#profit_loss_date_filter').data('daterangepicker').startDate.format('YYYY-MM-DD');
var end = $('#profit_loss_date_filter').data('daterangepicker').endDate.format('YYYY-MM-DD');
var location_id = $('#profit_loss_location_filter').val();

var data = { start_date: start, end_date: end, location_id: location_id };

var loader = __fa_awesome();
$('.opening_stock, .total_transfer_shipping_charges, .closing_stock, .total_sell, .total_purchase, .total_expense,
.net_profit, .total_adjustment, .total_recovered, .total_sell_discount, .total_purchase_discount,
.total_purchase_return, .total_sell_return').html(loader);

$.ajax({
method: "GET",
url: '/reports/profit-loss',
dataType: "json",
data: data,
success: function(data){
$('.opening_stock').html(__currency_trans_from_en( data.opening_stock, true ));
$('.closing_stock').html(__currency_trans_from_en( data.closing_stock, true ));
$('.total_sell').html(__currency_trans_from_en( data.total_sell, true ));
$('.total_purchase').html(__currency_trans_from_en( data.total_purchase, true ));
$('.total_expense').html(__currency_trans_from_en( data.total_expense, true ));
$('.net_profit').html(__currency_trans_from_en( data.net_profit, true ));
$('.total_adjustment').html(__currency_trans_from_en( data.total_adjustment, true ));
$('.total_recovered').html(__currency_trans_from_en( data.total_recovered, true ));
$('.total_purchase_return').html(__currency_trans_from_en( data.total_purchase_return, true ));
$('.total_transfer_shipping_charges').html(__currency_trans_from_en( data.total_transfer_shipping_charges, true ));
$('.total_purchase_discount').html(__currency_trans_from_en( data.total_purchase_discount, true ));
$('.total_sell_discount').html(__currency_trans_from_en( data.total_sell_discount, true ));
$('.total_sell_return').html(__currency_trans_from_en( data.total_sell_return, true ));
__highlight(data.net_profit, $('.net_profit'));
}
});
}
 });
</script>

@stop
