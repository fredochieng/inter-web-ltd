@extends('adminlte::page')

@section('title', 'System - BManager')

@section('content_header')
<h1>System<small>system configuration</small></h1>
@stop

@section('content')

<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title">PROMO DISCOUNTS</h3>
    </div>
    <!-- /.box-header -->
    <div class="box-body ">
        <div class="nav-tabs-custom">
            <div class="wizard">
                <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation" class="active">
                        <a href="#step1" data-toggle="tab" aria-controls="step1" role="tab" title="DETAILS">
                            <strong><i class="fa fa-fw fa-user"></i> DETAILS</strong>
                        </a>
                    </li>

                    <li role="presentation" class="disabled">
                        <a href="#step2" data-toggle="tab" aria-controls="step2" role="tab" title="FRANCHISES">
                            <strong><i class="fa fa-fw fa-calendar-check-o"></i>FRANCHISES</strong>
                        </a>
                    </li>
                    <li role="presentation" class="disabled">
                        <a href="#step3" data-toggle="tab" aria-controls="step3" role="tab" title="PACKAGES">
                            <strong><i class="fa fa-fw fa-desktop"></i> PACKAGES</strong>
                        </a>
                    </li>


                </ul>


                {{--  {{ Form::open( ['route' =>['promo_discounts.save'], 'method' => 'post' ,'class'=>'form ','enctype'=>'multipart/form-data', 'id' => 'AddPromoDiscount' ]) }}  --}}

                <div class="tab-content">
                    <div class="tab-pane active" role="tabpanel" id="step1"> @include('system.partials.details')
                    </div>
                    <!-- /.tab-pane -->
                      <div class="tab-pane" role="tabpanel" id="step2"> @include('system.partials.payment')
                    </div>
                    {{--  <div class="tab-pane" role="tabpanel" id="step3">@include('system::discounts.files.packages') </div>   --}}

                </div>
                <div class="clearfix"></div>
            </div>
            {!! Form::close() !!}
        </div>
        <!-- /.tab-content -->
    </div>
</div>

@stop
@section('css')
<!-- <link rel="stylesheet" href="/css/admin_custom.css"> -->
<style>
    .modal-actions {
        margin-top: 30px;
        margin-bottom: 10px;
    }

    .full-width {
        width: 100%;
    }

    .amtwarning {
        /* border-color: #fb0000; */
        color: #fb0000;
    }
</style>
{{-- <link rel="stylesheet" href="/css/systems_custom.css"> --}}
<link rel="stylesheet" href="/css/bootstrap-datepicker.min.css">
@stop
@section('js')
  <script type="text/javascript">
    $(document).ready(function(){

      //APPLY THE DATEPICKER COMPONENT TO ANY DOM ELEMENT WITH THE CLASS




      $('.datepicker').datepicker( {
                    format: 'dd-mm-yyyy',
                    orientation: "bottom",
                    autoclose: true,
                    showDropdowns: true,

                })
      //APPLY THE SELECT2 PLUGIN TO ANY SELECT ELEMENT WITH THE CLASS


      $('.select2').select2();
      $('#pkgsrv').select2({
          multiple:true,
      });

      $('#nextButton').click(function(e){
      e.preventDefault();



        $('#listMenu a[href="#second"]').addClass('active');
    });


//GET ALL FRANCHISE
   var franchTable =   $('.franchiseTable').DataTable({
        // "bScrollInfinite": true,
        // "bScrollCollapse": true,
        // "sScrollY":"200px",
        // "dom":'lfrtspi',
        // "paging": false,
        "rowCallback":function(r,d){
            $('td:eq(2) input', r).attr("disabled",false);

        },
        "iDisplayLength": -1,
         "bPaginate": true,
        "iCookieDuration": 60,
        "bStateSave": false,
        "bAutoWidth": false,
        //true
        "fixedHeader": true,
        "bScrollAutoCss": true,
        "bProcessing": true,
        "bRetrieve": true,
        "bJQueryUI": true,
        //"sDom": 't',
        "sDom": '<"H"CTrf>t<"F"lip>',
        "aLengthMenu": [[25, 50, 100, -1], [25, 50, 100, "All"]],
        //"sScrollY": "500px",
        //"sScrollX": "100%",
        "sScrollXInner": "110%",
        "fnInitComplete": function() {
            this.css("visibility", "visible");
        }

      });

      var tableId = 'franchTable';
        $('<div style="width: 100%; height: 300px; overflow: auto"></div>').append($('#' + tableId)).insertAfter($('#' + tableId + '_wrapper div').first());


    // //



//GET ALL PACKAGES
   var packageTable =   $('.packagesTable').DataTable({
        // "bScrollInfinite": true,
        // "bScrollCollapse": true,
        // "sScrollY":"200px",
        // "dom":'lfrtspi',
        // "paging": false,
        "rowCallback":function(r,d){
            $('td:eq(2) input', r).attr("disabled",false);

        },
        "iDisplayLength": -1,
        "bPaginate": true,
        "iCookieDuration": 60,
        "bStateSave": false,
        "bAutoWidth": false,
        //true
        "fixedHeader": true,
        "bScrollAutoCss": true,
        "bProcessing": true,
        "bRetrieve": true,
        "bJQueryUI": true,
        //"sDom": 't',
        "sDom": '<"H"CTrf>t<"F"lip>',
        "aLengthMenu": [[25, 50, 100, -1], [25, 50, 100, "All"]],
        //"sScrollY": "500px",
        //"sScrollX": "100%",
        "sScrollXInner": "110%",
        "fnInitComplete": function() {
            this.css("visibility", "visible");
        }

      });

      var tableId = 'packageTable';
        $('<div style="width: 100%; height: 300px; overflow: auto"></div>').append($('#' + tableId)).insertAfter($('#' + tableId + '_wrapper div').first());

        $('#box').css( 'display', 'block' );
        franchTable.search( '' ).draw();




      $("#global_fr").change(function(){
          var $state = $(this).prop("checked");
          if($state){
              $('.frlistitem').attr('checked',true);
          }
          else{
              $('.frlistitem').attr('checked',false);
          }
      });

      $("#global_pkg").change(function(){
          var $state = $(this).prop("checked");
          if($state){
              $('.pkglistitem').attr('checked',true);
          }
          else{
              $('.pkglistitem').attr('checked',false);
          }
      });

      $('#pkgsrv').change(function(){
          var $srv = $(this).val();
          if($srv.length===0){
              alert("You must indicate at least one service for the package");
              return false;
          }
          else{// GET SERVICE INFO AND DISPLAY IT IN THE SERVICES TABLE.
              $.ajax({
                  type:'POST',
                  url:"{{ url('system/packages/srvList') }}",
                  data:{"services":$srv,"_token":"{{ csrf_token() }}" },
                  beforeSend:function(data){
                  },
                  success:function(response,txtstat,xhr){
                      if(xhr.status===200){
                          $("#srvlistbl tbody").html("");
                          for (const r of response){
                              // COMPILE NEW ROW
                              var $trow = "<tr>";
                              $trow += "<td>"+r.SRVID+"</td>";
                              $trow += "<td>"+r.SRVCODE+"</td>";
                              $trow += "<td>"+r.SRVNAME+"</td>";
                               if(r.ADDRESSABLE===1){
                                  $trow += "<td>Yes</td>";
                              }
                              else{
                                  $trow += "<td>No</td>";
                              };
                              if(r.RECURRING===1){
                                  $trow += "<td>Yes</td>";
                              }
                              else{
                                  $trow += "<td>No</td>";
                              };
                              $trow += "</tr>";

                              // APPEND THE ROW TO THE TABLE
                              $("#srvlistbl tbody").append($trow);
                              console.log(r);
                          }
                      }
                      else{
                          alert("It has failed :-)");
                      }
                  },
                  error:function(response){
                      console.log(response);
                  }
              });
          }
      });


      //CODE FOR THE [ADD SERVICE] button

      $("#addsrv_btn").click(function(){
          $(this).preventDefault();
          var $d=$('#pkgsrv').select2('data');
          var srvs = "";
          console.log($d.length);
          $.each($d,function(i,v){
              console.log(v.id);
          });
      });

      //CODE FOR THE PROCEED BUTTON SEGMENT ONE

      $('a[data-toggle="tab"]').on('show.bs.tab', function (e) {

        franchTable.columns.adjust().draw();

        var $target = $(e.target);
        if ($target.parent().hasClass('disabled')) {
            return false;
        }

      });

      $(".next-step").click(function (e) {

        var form = $("#AddPromoDiscount");
        form.validate();

        if (form.valid() === true){
                var $active = $('.nav-tabs li.active');
                $active.next().removeClass('disabled');
                nextTab($active);
                }
                else{

                    $('.error').addClass('amtwarning');
                }
			});

      $(".prev-step").click(function (e) {
        var $active = $('.nav-tabs li.active');
        prevTab($active);
        });

      //VALIDATE FORM

      $('#BasicButton').click(function(){ });

    $('form input').each(function() {
        $('.error').removeClass('amtwarning');


         });
  });

  function nextTab(elem) {
    $(elem).next().find('a[data-toggle="tab"]').click();
}
function prevTab(elem) {
    $(elem).prev().find('a[data-toggle="tab"]').click();
}
</script>
@stop
