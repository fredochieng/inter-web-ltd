@extends('adminlte::page')

@section('title', 'New Client - Inter Web Ltd')

@section('content_header')
<h1>Clients<small>add new client</small></h1>
@stop

@section('content')

<div class="box box-info">
    <div class="box-header with-border">
        <h3 class="box-title">ADD NEW CLIENT</h3>
    </div>
    <!-- /.box-header -->
    <div class="box-body ">
        <div class="nav-tabs-custom">
            <div class="wizard">
                <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation" class="active">
                        <a href="#step1" data-toggle="tab" aria-controls="step1" role="tab" title="PERSONAL DETAILS">
                            <strong><i class="fa fa-fw fa-user"></i> PERSONAL DETAILS</strong>
                        </a>
                    </li>

                    <li role="presentation" class="disabled">
                        <a href="#step2" data-toggle="tab" aria-controls="step2" role="tab" title="PAYMENT MODE">
                            <strong><i class="fa fa-fw fa-calendar-check-o"></i>PAYMENT MODE</strong>
                        </a>
                    </li>
                    <li role="presentation" class="disabled">
                        <a href="#step3" data-toggle="tab" aria-controls="step3" role="tab" title="INVESTMENT">
                            <strong><i class="fa fa-fw fa-desktop"></i> INVESTMENT</strong>
                        </a>
                    </li>
                </ul>

               {!! Form::open(['url' => action('UserController@store'), 'method' => 'post', 'id' => 'AddClientForm'
                ]) !!}

                <div class="tab-content">
                    <div class="tab-pane active" role="tabpanel" id="step1"> @include('users.partials.details')
                    </div>
                    <!-- /.tab-pane -->
                    <div class="tab-pane" role="tabpanel" id="step2"> @include('users.partials.payment')
                    </div>
                     <div class="tab-pane" role="tabpanel" id="step3">@include('users.partials.investment') </div>

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
<link rel="stylesheet" href="/css/admin_custom.css">
<link rel="stylesheet" href="/css/bootstrap-datepicker.min.css">
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
@stop
@section('js')
<script src="/js/bootstrap-datepicker.min.js"></script>
<script src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.13.1/jquery.validate.js"></script>
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

	 $('.inv_date').datepicker( {
	 	format: 'yyyy-mm-dd',
		orientation: "bottom",
		autoclose: true,
         showDropdowns: true,
         todayHighlight: true,
         toggleActive: true,
         clearBtn: true,
     })
     $(".select2").select2()

        })
</script>
<script>
    $(function () {
        $("#phone_no_id").select2({
          ajax: {
            url: "/phones/get_numbers",
        	type:'GET',
            dataType: 'json',
            delay: 250,
            data: function (params) {
        		console.log(params);
              return {
                q: params.term, // search term
                page: params.page
              };
            },
            processResults: function (data, params) {
              params.page = params.page || 1;
        	   var retVal = [];
              $.each(data, function(index, element) {
        			 var lineObj = {
        				  id: element.id,
                          text: element.text,
                          referer_name: element.referer_name
        			}
                retVal.push(lineObj);
        		});

              return {
                results: retVal,
                pagination: {
                  more: (params.page * 30) < data.total_count
                }
              };
            },
            cache: true
          },
          placeholder: 'Search referer by phone number',
          escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
          minimumInputLength: 4,
          templateResult: formatRepo,
          templateSelection: formatRepoSelection
        }).on('select2:select', function(e) {
            var data = e.params.data;
            $("#referer_name").val(data.referer_name);
            $("#referer_id").val(data.id);
            console.log();
        });
        function formatRepo (repo) {
         if (repo.loading) {
            return repo.text;
          }

          var markup =repo.text;

          return markup;
        }

        function formatRepoSelection (repo) {
          return repo.text ;
        }

        $("#pay_bank_id").change(function() {
            var value = $(this).val();
            if (value != 0 ) {
            $("#bank_payment_acc").removeClass("hide");
            }
            else{
            $("#bank_payment_acc").addClass("hide");
            }
    });

        $("#pay_mode_id").change(function() {
                    var val = $(this).val();
                    if (val == 1 ) {
                    $("#mpesa_number_div").removeClass("hide");
                    $("#bank_payment_acc").addClass("hide");
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
        });

        // SELECTION OF MONTHLY + COMPOUNDED INVESTMENT TYPE
        $("#inv_type_id").change(function() {
        var val = $(this).val();
            if (val == 3 ) {
            $("#monthly_inv_amount_div").removeClass("hide");
            $("#monthly_inv_duration_div").removeClass("hide");
            $("#compounded_inv_amount_div").removeClass("hide");
            $("#compounded_inv_duration_div").removeClass("hide");
            // $("#inv_bank_trans_id").addClass("hide");
            // $("#cheq_no_inv_div").addClass("hide");
            }else{
            $("#monthly_inv_amount_div").addClass("hide");
            $("#monthly_inv_duration_div").addClass("hide");
            $("#compounded_inv_amount_div").addClass("hide");
            $("#compounded_inv_duration_div").addClass("hide");

            }
        });
 })
</script>
<script type="text/javascript">
    $(document).ready(function(){
      $('#nextButton').click(function(e){
      e.preventDefault();
        $('#listMenu a[href="#second"]').addClass('active');
    });

      $(".next-step").click(function (e) {

        var form = $("#AddClientForm");
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
