@extends('adminlte::page')
@section('title', 'Due Payments Report - Inter Web Ltd')
@section('content_header')
<h1>Due Payments Report</h1>
@stop
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="box box-info" id="accordion">
            <div class="box-header with-border">
                <h3 class="box-title">
                    <a data-toggle="collapse" data-parent="#accordion" href="#collapseFilter">
                        <i class="fa fa-filter" aria-hidden="true"></i> Filters
                    </a>
                </h3>
            </div>
            <div id="collapseFilter" class="panel-collapse active collapse in" aria-expanded="true">
                <div class="box-body">
                    {!!
                    Form::open(['action'=>['ReportController@showDuePaymentsReports'],
                    'method'=>'GET','class'=>'form','enctype'=>'multipart/form-data'])
                    !!}
                    <div class="col-md-12">
                        <div class="col-md-3">
                            {{Form::label('Payment Mode ')}}
                            <div class="form-group">
                                <select class="form-control select2" id="pay_mode_id" name="pay_mode_id"
                                    style="width: 100%;" tabindex="-1" aria-hidden="true">
                                    <option selected="selected" value="">Select payment mode</option>
                                    @foreach($payment_modes as $payment_mode)
                                    <option value="{{ $payment_mode->method_id }}">{{ $payment_mode->method_name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3 hide" id="pay_bank_id">
                            {{Form::label('Payment Bank ')}}
                            <div class="form-group">
                                <select class="form-control select2" id="bank_id" name="bank_id" style="width: 100%;"
                                    tabindex="-1" aria-hidden="true">
                                    <option selected="selected" value="">Select payment bank</option>
                                    @foreach($banks as $bank)
                                    <option value="{{ $bank->bank_id }}">{{ $bank->bank_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('Payment Date') !!}
                                {!! Form::text('date_range', null, ['placeholder' => 'Select date range', 'class' =>
                                'form-control', 'id' => 'daterange-btn', 'readonly']); !!}
                            </div>
                        </div>

                        <div class="col-md-2">
                            <button type="submit" style="margin-top:25px;" class="btn btn-block btn-info"><strong><i
                                        class="fa fa-fw fa-search"></i>Generate Report</strong></button>
                        </div>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="box box-info">
            <div class="box-body">
                <div class="table-responsive">
                    <table class="table table-no-margin" id="sell_payment_report_table">
                        <table>
                            <thead>
                                <tr>
                                    <th>Account #</th>
                                    <th>Name</th>
                                    <th>ID Number</th>
                                    <th>Mode of Payment</th>
                                    <th>Bank</th>
                                    <th>Account No</th>
                                    <th>MPesa No</th>
                                    <th>Amount</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($due_payments_report as $item)
                                <tr>
                                    <td>{{$item->account_no}}</td>
                                    <td><a href="/client/{{$item->id}}/edit">{{$item->name}}</a></td>
                                    <td>{{$item->id_no}}</td>
                                    <td>{{$item->method_name}}</td>
                                    @if($item->bank_name !='')
                                    <td>{{$item->bank_name}}</td>
                                    @else
                                    <td>N/A</td>
                                    @endif
                                    @if($item->pay_bank_acc !='')
                                    <td>{{$item->pay_bank_acc}}</td>
                                    @else
                                    <td>N/A</td>
                                    @endif
                                    @if($item->pay_mpesa_no !='')
                                    <td>{{$item->pay_mpesa_no}}</td>
                                    @else
                                    <td>N/A</td>
                                    @endif
                                    @if($item->inv_type_id ==1)
                                    <td>Kshs {{ number_format($item->to_be_paid, 2, '.', ',')}}</td>
                                    @elseif ($item->inv_type_id ==2)
                                    <td>Kshs {{ number_format($item->to_be_paid, 2, '.', ',')}}</td>
                                    @endif
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <tfoot>
                            <tr class="bg-gray font-17 footer-total text-center">
                                <td colspan="7"><strong>Total:</strong></td>
                                <td><span class="display_currency" id="footer_total_amount"
                                        data-currency_symbol="true">4555</span></td>
                                <td colspan="4"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@stop
@section('css')
<link rel="stylesheet" href="/plugins/bootstrap-daterangepicker/daterangepicker.css">
@stop
@section('js')
<script src="/plugins/jquery/dist/jquery.js"></script>
<script src="/plugins/moment/min/moment.min.js"></script>
<script src="/plugins/bootstrap-daterangepicker/daterangepicker.js"></script>
<script src="/js/select2.full.min.js"></script>
{{-- <script src="/js/report.js"></script> --}}
<script>
    $(function () {

     //Initialize Select2 Elements
     $('.select2').select2()
     //Date range as a button
     $('#daterange-btn').daterangepicker(
       {
         ranges   : {
           'Today'       : [moment(), moment()],
           'Yesterday'   : [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
           'Last 7 Days' : [moment().subtract(6, 'days'), moment()],
           'Last 30 Days': [moment().subtract(29, 'days'), moment()],
           'This Month'  : [moment().startOf('month'), moment().endOf('month')],
           'Last Month'  : [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
         },
         startDate: moment().subtract(29, 'days'),
         endDate  : moment()
       },
       function (start, end) {
         //$('#daterange-btn span').html(start.format('Y-m-d') + ' - ' + end.format('YYYY-MM-DD'))
       }
     )

     var start = "";
     var end = "";
     if ($("input#daterange-btn").val()) {
         start = $("input#daterange-btn")
             .data("daterangepicker")
             .startDate.format("YYYY-MM-DD");
         end = $("input#daterange-btn")
             .data("daterangepicker")
             .endDate.format("YYYY-MM-DD");
     }

     start_date = start;
     end_date = end;

     $("#pay_mode_id").change(function() {
        var val = $(this).val();
        if (val == 2 ) {
        $("#pay_bank_id").removeClass("hide");
        }
        else{
        $("#pay_bank_id").addClass("hide");
        }
});

   })
</script>
@stop
