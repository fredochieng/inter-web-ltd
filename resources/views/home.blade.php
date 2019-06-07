@extends('adminlte::page')

@section('title', 'Dashboard - Inter Web Ltd')

@section('content_header')
    <h1>Dashboard</h1>
@stop

@section('content')
    <div class="row">
    <a href="investments">
        <div class="col-md-3 col-sm-6 col-xs-12">
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
        <div class="col-md-3 col-sm-6 col-xs-12">
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
        <div class="col-md-3 col-sm-6 col-xs-12">
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
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="info-box bg-blue">
                <span class="info-box-icon"><i class="ion ion-person-add"></i></i></span>
                <div class="info-box-content">
                    <span class="info-box-text"><b>Total Customers</b></span>
                    <span class="info-box-number">{{ $total_customers }}</span>
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
        </div>
</div>
@stop
