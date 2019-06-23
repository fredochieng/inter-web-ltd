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
                    {!! Form::open(['url' => '#', 'method' => 'get', 'id' => 'sell_payment_report_form' ]) !!}
                    <div class="col-md-4">
                        {{Form::label('Payment Mode ')}}
                        <div class="form-group">
                            {!! Form::text('telephone', $pay_mode, ['class' =>
                            'form-control', 'readonly']); !!}
                        </div>
                    </div>
                    <div class="col-md-4">
                        {{Form::label('Payment Bank ')}}
                        <div class="form-group">
                            {!! Form::text('telephone', $pay_bank, ['class' =>
                            'form-control', 'readonly']); !!}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('Payment Date') !!}
                            {!! Form::text('date_range', $start_date .' - '. $end_date, ['placeholder' => '',
                            'class' =>
                            'form-control', 'id' => 'daterange-btn', 'readonly']); !!}
                        </div>
                    </div>
                    <div class="col-md-1">
                        <a href="/reports/due-payments" style="margin-top:25px;" class="btn bg-purple"><strong><i
                                    class="fa fa-arrow-left"></i> BACK</strong></a>

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
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            @if($type == 1)
                            <b>Due Payment Report for period</b>
                            @elseif($type == 2)
                            <b>{{$pay_mode}} Due Payment Report for period</b>
                            @else
                            <b>{{$pay_bank}} Due Payment Report for period</b>
                            @endif
                            {{$start_date}} - {{$end_date}}</h3>
                    </div>
                    <table class="table table-no-margin">
                        <div class="btn-group  btn-sm" style="margin-left:930px;">
                            <button type="button" class="btn btn-info btn-flat"><i class="fa fa-align-justify"></i>
                                Action</button>
                            <button type="button" class="btn btn-info btn-flat dropdown-toggle" data-toggle="dropdown"
                                aria-expanded="false">
                                <span class="caret"></span>
                                <span class="sr-only">Toggle Dropdown</span>
                            </button>
                            <ul class="dropdown-menu" role="menu">
                                <li><a href="/report/excel/generate"><i class="fa fa-file-o"></i> Export to CSV</a></li>
                                <li><a href="#"><i class="fa fa-file-excel-o"></i> Export to Excel</a></li>
                                <li><a href="#"><i class="fa fa-file-pdf-o"></i> Export to PDF</a></li>
                            </ul>
                        </div>

                        @if($type==1)
                        <thead>
                            <tr>
                                <th>Account #</th>
                                <th>Name</th>
                                <th>ID Number</th>
                                <th>Phone Number</th>
                                <th>Mode of Payment</th>
                                <th>Bank</th>
                                <th>Account No</th>
                                <th>MPesa No</th>
                                <th>Amount</th>
                                <th>Payment Date</th>
                            </tr>
                        </thead>
                        @elseif($type==2)
                        <thead>
                            <tr>
                                <th>Account #</th>
                                <th>Name</th>
                                <th>ID Number</th>
                                <th>Phone Number</th>
                                <th>Mode of Payment</th>
                                @if($pay_mode == 'BANK ACCOUNT')
                                <th>Bank</th>
                                <th>Account No</th>
                                @else
                                <th>MPesa No</th>
                                @endif
                                <th>Amount</th>
                                <th>Payment Date</th>
                            </tr>
                        </thead>
                        @elseif($type==3)
                        <thead>
                            <tr>
                                <th>Account #</th>
                                <th>Name</th>
                                <th>ID Number</th>
                                <th>Phone Number</th>
                                <th>Bank</th>
                                <th>Account No</th>
                                <th>Amount</th>
                                <th>Payment Date</th>
                            </tr>
                        </thead>
                        @endif
                        <tbody>
                            @if($type==1)
                            @foreach ($today_due_payment_report as $item)
                            <tr>
                                <td>{{$item->account_no}}</td>
                                <td><a href="/client/{{$item->client_id}}/edit">{{$item->name}}</a></td>
                                <td>{{$item->id_no}}</td>
                                <td>{{$item->telephone}}</td>
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
                                @elseif ($item->inv_type_id ==3)
                                <td>Kshs {{ number_format($item->to_be_paid, 2, '.', ',')}}</td>
                                @endif
                                <td>{{$item->next_pay_date}}</td>
                            </tr>
                            @endforeach
                            @elseif($type==2)
                            @foreach ($today_due_payment_report as $item)
                            <tr>
                                <td>{{$item->account_no}}</td>
                                <td><a href="/client/{{$item->client_id}}/edit">{{$item->name}}</a></td>
                                <td>{{$item->id_no}}</td>
                                <td>{{$item->telephone}}</td>
                                <td>{{$item->method_name}}</td>
                                @if($pay_mode == 'BANK ACCOUNT')
                                <td>{{$item->bank_name}}</td>
                                <td>{{$item->pay_bank_acc}}</td>
                                @else
                                <td>{{$item->pay_mpesa_no}}</td>
                                @endif

                                @if($item->inv_type_id ==1)
                                <td>Kshs {{ number_format($item->to_be_paid, 2, '.', ',')}}</td>
                                @elseif ($item->inv_type_id ==2)
                                <td>Kshs {{ number_format($item->to_be_paid, 2, '.', ',')}}</td>
                                @elseif ($item->inv_type_id ==3)
                                <td>Kshs {{ number_format($item->to_be_paid, 2, '.', ',')}}</td>
                                @endif
                                <td>{{$item->next_pay_date}}</td>
                            </tr>
                            @endforeach
                            @elseif($type==3)
                            @foreach ($today_due_payment_report as $item)
                            <tr>
                                <td>{{$item->account_no}}</td>
                                <td><a href="/client/{{$item->client_id}}/edit">{{$item->name}}</a></td>
                                <td>{{$item->id_no}}</td>
                                <td>{{$item->telephone}}</td>
                                <td>{{$item->bank_name}}</td>
                                <td>{{$item->pay_bank_acc}}</td>
                                @if($item->inv_type_id ==1)
                                <td>Kshs {{ number_format($item->to_be_paid, 2, '.', ',')}}</td>
                                @elseif ($item->inv_type_id ==2)
                                <td>Kshs {{ number_format($item->to_be_paid, 2, '.', ',')}}</td>
                                @endif
                                <td>{{$item->next_pay_date}}</td>
                            </tr>
                            @endforeach
                            @endif
                        </tbody>
                        {{-- <tfoot>
                            <tr class="bg-gray font-17 footer-total text-center">
                                <td colspan="7"><strong>Total:</strong></td>
                                <td><span class="display_currency" id="footer_total_amount"
                                        data-currency_symbol="true">4555</span></td>
                                <td colspan="4"></td>
                            </tr>
                        </tfoot> --}}
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
              $(".select2").select2()
             // $('#example1').DataTable()
     })
</script>
<script>
    $(function () {

     //Initialize Select2 Elements
     $('#example1').DataTable()
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
         $('#daterange-btn span').html(start.format('YYYY, MMMM, D') + ' - ' + end.format('YYYY, MMMM, D'))
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
   })
</script>
@stop
