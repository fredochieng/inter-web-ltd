@extends('adminlte::page')

@section('title', 'Dashboard - Inter Web Ltd')

@section('content_header')
<h1>Dashboard</h1>
@stop

@section('content')
@if(auth()->user()->can('dashboard.data'))
<div class="row">
    <a href="investments">
        <div class="col-md-4 col-sm-6 col-xs-12">
            <div class="info-box bg-green">
                <span class="info-box-icon"><i class="fa fa-money"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text"><b>Invested Amount</b></span>
                    <span class="info-box-number">Kshs {{ $sum_investments }}</span>
                    <div class="progress">
                        <div class="progress-bar" style="width:  78.260869565217%"></div>
                    </div>
                    <span class="progress-description">

                        More info
                    </span>
                </div>
                <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
        </div>
    </a>
    <!-- /.col -->
    <div class="col-md-4 col-sm-6 col-xs-12">
        <div class="info-box bg-yellow">
            <span class="info-box-icon"><i class="ion ion-cash"></i></i></span>
            <div class="info-box-content">
                <span class="info-box-text"><b>Payments Made</b></span>
                <span class="info-box-number">Kshs {{ $sum_tot_payments }}</span>
                <div class="progress">
                    <div class="progress-bar" style="width: 17.391304347826%"></div>
                </div>
                <span class="progress-description">
                    More info
                </span>
            </div>
            <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
    </div>
    <!-- /.col -->
    <div class="col-md-4 col-sm-6 col-xs-12">
        <div class="info-box bg-aqua">
            <span class="info-box-icon"><i class="fa fa-dollar"></i><i class="fa fa-exclamation"></i></i></span>
            <div class="info-box-content">
                <span class="info-box-text"><b>Due Payments</b></span>
                <span class="info-box-number">Kshs {{ $sum_tot_due_payments }}</span>
                <div class="progress">
                    <div class="progress-bar" style="width: 0%"></div>
                </div>
                <span class="progress-description">
                    More info
                </span>
            </div>
            <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
    </div>
    <!-- /.col -->
    {{-- <div class="col-md-3 col-sm-6 col-xs-12">
        <div class="info-box bg-blue">
            <span class="info-box-icon"><i class="ion ion-cash"></i></i></span>
            <div class="info-box-content">
                <span class="info-box-text"><b>Total Topups</b></span>
                <span class="info-box-number">{{$sum_tot_topups }}</span>
    <div class="progress">
        <div class="progress-bar" style="width: 4.3478260869565%"></div>
    </div>
    <span class="progress-description">
        More info
    </span>
</div>
<!-- /.info-box-content -->
</div>
<!-- /.info-box -->
</div> --}}
</div>
<input type="hidden" id="tot_investments" value="{{ $sum_investments1 }}">
<input type="hidden" id="sum_tot_payments" value="{{ $sum_tot_payments1 }}">
<input type="hidden" id="tot_due_payments" value="{{ $sum_tot_due_payments1 }}">
<input type="hidden" id="sum_tot_topups" value="{{ $sum_tot_topups1 }}">
<input type="hidden" name="clients" value="{{ $total_customers}}" id="tot_clients">
<div class="row">
    <div class="col-md-8">
        <!-- AREA CHART -->
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">Transactions Recap Chart</h3>

                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                    </button>
                    <button type="button" class="btn btn-box-tool" data-widget="remove"><i
                            class="fa fa-times"></i></button>
                </div>
            </div>
            <div class="box-body">
                <div class="chart">
                    <canvas id="myChart" style="height:337px"></canvas>
                </div>
            </div>
            <!-- /.box-body -->
        </div>
        <!-- /.box -->
    </div>
    <!-- /.col (LEFT) -->
    <div class="col-md-4">
        <!-- Info Boxes Style 2 -->
        <div class="info-box bg-yellow">
            <span class="info-box-icon"><i class="fa fa-eur"></i></span>

            <div class="info-box-content">
                <span class="info-box-text">Total Monthly Investments</span>
                <span class="info-box-number">KShs
                    {{number_format($total_monthly_investments->tot_monthly_inv, 2, '.', ',')}}</span>

                <div class="progress">
                    <div class="progress-bar" style="width: 50%"></div>
                </div>
                <span class="progress-description">

                </span>
            </div>
            <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
        <div class="info-box bg-green">
            <span class="info-box-icon"><i class="fa fa-usd"></i></span>

            <div class="info-box-content">
                <span class="info-box-text">Total Compounded Investments</span>
                <span class="info-box-number">Kshs
                    {{number_format($total_compounded_investments->tot_comp_inv, 2, '.', ',')}}</span>

                <div class="progress">
                    <div class="progress-bar" style="width: 20%"></div>
                </div>
                <span class="progress-description">

                </span>
            </div>
            <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
        <div class="info-box bg-red">
            <span class="info-box-icon"><i class="ion ion-cash"></i></span>

            <div class="info-box-content">
                <span class="info-box-text">Monthly/Comp. Investments</span>
                <span class="info-box-number">Kshs
                    {{number_format($total_monthly_comp_investments->tot_monthly_comp_inv, 2, '.', ',')}}</span>

                <div class="progress">
                    <div class="progress-bar" style="width: 70%"></div>
                </div>
                <span class="progress-description">

                </span>
            </div>
            <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
        <div class="info-box bg-aqua">
            <span class="info-box-icon"><i class="ion ion-ios-people-outline"></i></span>

            <div class="info-box-content">
                <span class="info-box-text">Clients</span>
                <div class="pull-right" style="margin-top:-20px;"><span class="info-box-text">Secretaries</span>
                </div>
                <span class="info-box-number">{{$total_customers}}</span>
                <div class="pull-right" style="margin-top:-20px;"> <span
                        class="info-box-number">{{$total_secretaries}}</span></div>

                <div class="progress">
                    <div class="progress-bar" style="width: 40%"></div>
                </div>
                <span class="progress-description">

                </span>
            </div>
            <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
    </div>
    <!-- /.col (RIGHT) -->
</div>
{{-- <div class="row">
    <div class="col-md-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Monthly Recap Report</h3>

                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                    </button>
                    <div class="btn-group">
                        <button type="button" class="btn btn-box-tool dropdown-toggle" data-toggle="dropdown">
                            <i class="fa fa-wrench"></i></button>
                        <ul class="dropdown-menu" role="menu">
                            <li><a href="#">Action</a></li>
                            <li><a href="#">Another action</a></li>
                            <li><a href="#">Something else here</a></li>
                            <li class="divider"></li>
                            <li><a href="#">Separated link</a></li>
                        </ul>
                    </div>
                    <button type="button" class="btn btn-box-tool" data-widget="remove"><i
                            class="fa fa-times"></i></button>
                </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
                <div class="row">
                    <div class="col-md-12">
                        <p class="text-center">
                            <strong>Sales: 1 Jan, 2014 - 30 Jul, 2014</strong>
                        </p>

                        <div class="chart">
                            <!-- Sales Chart Canvas -->
                            <canvas id="chartJSContainer" style="height:400px; width:600px"></canvas>
                        </div>
                        <!-- /.chart-responsive -->
                    </div>
                </div>
                <!-- /.row -->
            </div>
            <!-- ./box-body -->
        </div>
        <!-- /.box -->
    </div>
    <!-- /.col -->
</div> --}}
</div>
@endif
@stop
@section('css')
@stop @section('js')
<script>
    Chart.defaults.doughnutLabels = Chart.helpers.clone(Chart.defaults.doughnut);

var helpers = Chart.helpers;
var defaults = Chart.defaults;

Chart.controllers.doughnutLabels = Chart.controllers.doughnut.extend({
updateElement: function(arc, index, reset) {
var _this = this;
var chart = _this.chart,
chartArea = chart.chartArea,
opts = chart.options,
animationOpts = opts.animation,
arcOpts = opts.elements.arc,
centerX = (chartArea.left + chartArea.right) / 2,
centerY = (chartArea.top + chartArea.bottom) / 2,
startAngle = opts.rotation, // non reset case handled later
endAngle = opts.rotation, // non reset case handled later
dataset = _this.getDataset(),
circumference = reset && animationOpts.animateRotate ? 0 : arc.hidden ? 0 :
_this.calculateCircumference(dataset.data[index]) * (opts.circumference / (2.0 * Math.PI)),
innerRadius = reset && animationOpts.animateScale ? 0 : _this.innerRadius,
outerRadius = reset && animationOpts.animateScale ? 0 : _this.outerRadius,
custom = arc.custom || {},
valueAtIndexOrDefault = helpers.getValueAtIndexOrDefault;

helpers.extend(arc, {
// Utility
_datasetIndex: _this.index,
_index: index,

// Desired view properties
_model: {
x: centerX + chart.offsetX,
y: centerY + chart.offsetY,
startAngle: startAngle,
endAngle: endAngle,
circumference: circumference,
outerRadius: outerRadius,
innerRadius: innerRadius,
label: valueAtIndexOrDefault(dataset.label, index, chart.data.labels[index])
},

draw: function () {
var ctx = this._chart.ctx,
vm = this._view,
sA = vm.startAngle,
eA = vm.endAngle,
opts = this._chart.config.options;

var labelPos = this.tooltipPosition();
var segmentLabel = vm.circumference / opts.circumference * 100;

ctx.beginPath();

ctx.arc(vm.x, vm.y, vm.outerRadius, sA, eA);
ctx.arc(vm.x, vm.y, vm.innerRadius, eA, sA, true);

ctx.closePath();
ctx.strokeStyle = vm.borderColor;
ctx.lineWidth = vm.borderWidth;

ctx.fillStyle = vm.backgroundColor;

ctx.fill();
ctx.lineJoin = 'bevel';

if (vm.borderWidth) {
ctx.stroke();
}

if (vm.circumference > 0.15) { // Trying to hide label when it doesn't fit in segment
ctx.beginPath();
ctx.font = helpers.fontString(opts.defaultFontSize, opts.defaultFontStyle, opts.defaultFontFamily);
ctx.fillStyle = "#fff";
ctx.textBaseline = "top";
ctx.textAlign = "center";

// Round percentage in a way that it always adds up to 100%
ctx.fillText(segmentLabel.toFixed(0) + "%", labelPos.x, labelPos.y);
}
}
});

var model = arc._model;
model.backgroundColor = custom.backgroundColor ? custom.backgroundColor : valueAtIndexOrDefault(dataset.backgroundColor,
index, arcOpts.backgroundColor);
model.hoverBackgroundColor = custom.hoverBackgroundColor ? custom.hoverBackgroundColor :
valueAtIndexOrDefault(dataset.hoverBackgroundColor, index, arcOpts.hoverBackgroundColor);
model.borderWidth = custom.borderWidth ? custom.borderWidth : valueAtIndexOrDefault(dataset.borderWidth, index,
arcOpts.borderWidth);
model.borderColor = custom.borderColor ? custom.borderColor : valueAtIndexOrDefault(dataset.borderColor, index,
arcOpts.borderColor);

// Set correct angles if not resetting
if (!reset || !animationOpts.animateRotate) {
if (index === 0) {
model.startAngle = opts.rotation;
} else {
model.startAngle = _this.getMeta().data[index - 1]._model.endAngle;
}

model.endAngle = model.startAngle + model.circumference;
}

arc.pivot();
}
});

var tot_investments = $('#tot_investments');
var tot_due_payments = $('#tot_due_payments');
var sum_tot_payments = $('#sum_tot_payments');
var sum_tot_topups = $('#sum_tot_topups');

var tot_investments = tot_investments.val();
var tot_due_payments = tot_due_payments.val();
var sum_tot_payments = sum_tot_payments.val();
var sum_tot_topups = sum_tot_topups.val();

var tot_investments = tot_investments;
var tot_due_payments = tot_due_payments;
var sum_tot_payments = sum_tot_payments;
var sum_tot_topups = sum_tot_topups;

var config = {
type: 'doughnutLabels',
data: {
datasets: [{
data: [
tot_investments.valueOf(),
tot_due_payments.valueOf(),
sum_tot_payments.valueOf(),
sum_tot_topups.valueOf(),
],
backgroundColor: [
"#00a65a",
"#00c0ef",
"#f39c12",
"#3c8dbc"
],
label: 'Dataset 1'
}],
labels: [
"Investments",
"Due Payments",
"Payments Made",
"Topups"
]
},
options: {
responsive: true,
legend: {
position: 'top',
},
title: {
display: false,
text: 'Investments Chart'
},
animation: {
animateScale: true,
animateRotate: true
}
}
};

var ctx = document.getElementById("myChart").getContext("2d");
new Chart(ctx, config);

var options = {
    type: 'line',
    data: {
    labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun"],
datasets: [
{
    label               : 'Electronics',
    fillColor           : 'rgba(210, 214, 222, 1)',
    strokeColor         : 'rgba(210, 214, 222, 1)',
    pointColor          : 'rgba(210, 214, 222, 1)',
    pointStrokeColor    : '#c1c7d1',
    pointHighlightFill  : '#fff',
    pointHighlightStroke: 'rgba(220,220,220,1)',
    data                : [65, 59, 80, 81, 56, 55, 40]
},
{
    label               : 'Digital Goods',
    fillColor           : 'rgba(60,141,188,0.9)',
    strokeColor         : 'rgba(60,141,188,0.8)',
    pointColor          : '#3b8bba',
    pointStrokeColor    : 'rgba(60,141,188,1)',
    pointHighlightFill  : '#fff',
    pointHighlightStroke: 'rgba(60,141,188,1)',
    data                : [28, 48, 40, 19, 86, 27, 90]
}
]
},
    options: {
        scaleGridLineColor      : 'rgba(red)',
    scales: {
    yAxes: [{

        ticks: {
        reverse: false,
        display: false
        }
}]

}
}
}

var ctx = document.getElementById('chartJSContainer').getContext('2d');
new Chart(ctx, options);

</script>
@stop
