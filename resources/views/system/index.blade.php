@extends('adminlte::page')

@section('title', 'System - BManager')

@section('content_header')
<h1>System<small>system configuration</small></h1>
@stop

@section('content')
<div class="row">
    <div class="col-xs-12">
        <!--  <pos-tab-container> -->
        <div class="col-xs-12 pos-tab-container">
            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 pos-tab-menu">
                <div class="list-group">
                    <a href="#" class="list-group-item text-center active">Business</a>
                    <a href="#" class="list-group-item text-center">Settings</a>
                    <a href="#" class="list-group-item text-center">Sale</a>
                    <a href="#" class="list-group-item text-center">Purchases</a>
                    <a href="#" class="list-group-item text-center">Dashboard</a>
                    <a href="#" class="list-group-item text-center">System</a>
                    <a href="#" class="list-group-item text-center">Prefixes</a>
                    <a href="#" class="list-group-item text-center">Email Settings</a>
                </div>
            </div>
            <div class="col-lg-10 col-md-10 col-sm-10 col-xs-10 pos-tab">
                <!-- tab 1 start -->
              @include('system.partials.settings')
              {{--  @include('system.partials.settings_tax')  --}}
            </div>
        </div>
        <!--  </pos-tab-container> -->
    </div>
</div>
<div class="row">
    <div class="col-sm-12">
        <button class="btn btn-danger pull-right" type="submit">Update Settings</button>
    </div>
</div>

@stop
@section('css')
<link rel="stylesheet" href="/css/custom.css">
@stop
@section('js')
<script src="/js/custom.js"></script>
<script>
    $(document).ready(function(){
     $("div.pos-tab-menu>div.list-group>a").click(function(e) {
        e.preventDefault();
        $(this).siblings('a.active').removeClass("active");
        $(this).addClass("active");
        var index = $(this).index();
        $("div.pos-tab>div.pos-tab-content").removeClass("active");
        $("div.pos-tab>div.pos-tab-content").eq(index).addClass("active");
        });

    });
</script>
@stop
