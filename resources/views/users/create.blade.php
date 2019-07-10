@extends('adminlte::page')

@section('title', 'New Client - Inter Web Ltd')

@section('content_header')
<h1>Clients<small>add new client</small></h1>
@stop

@section('content')

<div class="box box-info">
    <div class="box-header with-border">
        {{--  <h3 class="box-title">ADD NEW CLIENT</h3>  --}}
        <div class="pull-left">
            <a href="#" data-target="#modal_restrict_referal" data-toggle="modal" class="btn btn-primary"
                data-backdrop="static" data-keyboard="false"><i class="fa fa-plus"></i> Restrict Referal </a>
        </div>
        <div class="box-tools">
            <a href="#" data-target="#modal_blacklist_client" data-toggle="modal" class="btn btn-block btn-primary"
                data-backdrop="static" data-keyboard="false"><i class="fa fa-plus"></i> Blacklist Phone/ID Number </a>
        </div>
    </div>
    <div class="box-body">
        {!! Form::open(['url' => action('UserController@store'), 'method' => 'post', 'class' => 'addClientForm']) !!}
        <div class="col-md-12">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('Full Name *') !!}
                        {!! Form::text('name', null, ['class' => 'form-control']); !!}
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('Email Address *') !!}
                        {!! Form::email('email', null, ['class' => 'form-control', 'required' ]); !!}
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label(' Phone Number *') !!}
                        {!! Form::text('telephone', null, ['class' => 'form-control', 'required']); !!}
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('Date of Birth *') !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </span>
                            {{Form::text('dob', null, ['class' => 'form-control dob', 'id' => 'dob', 'required' ])}}
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('ID Number *') !!}
                        {!! Form::text('id_no', null, ['class' => 'form-control', 'required']); !!}
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label(' Account Number *') !!}
                        {!! Form::text('account_no', $generated_account, ['class' => 'form-control', 'readonly' ]); !!}
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('Home Town*') !!}
                        {!! Form::text('home_town', null, ['class' => 'form-control', 'required']); !!}
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label(' Home Address*') !!}
                        {!! Form::text('home_address', null, ['class' => 'form-control', 'required' ]); !!}
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('Next of Kin *') !!}
                        {!! Form::text('kin_name', null, ['class' => 'form-control', 'required']); !!}
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('Next of Kin Phone Number *') !!}
                        {!! Form::text('kin_telephone', null, ['class' => 'form-control', 'required']); !!}
                    </div>
                </div>
                <div class="col-md-3">
                    {{ Form::label('Refered by (Optional) ')}}
                    <div class="form-group">
                        <select id="phone_no_id" class=" col-md-12 " name="referer_phone_id"> </select>
                    </div>
                </div>
                <input type="hidden" id="referer_phone" name="referer_phone">

                <div class="col-md-3">
                    <div class="form-group">
                        {{Form::label('Name')}}
                        <div class="form-group">
                            {{Form::text('referer_name', '',['class'=>'form-control', 'readonly', 'id' => 'referer_name'])}}
                        </div>
                    </div>
                </div>
                <div class="col-md-3 hide">
                    <div class="form-group">
                        {{Form::label('Referer ID')}}
                        <div class="form-group">
                            {{Form::text('referer_id', '',['class'=>'form-control', 'readonly', 'id' => 'referer_id'])}}
                        </div>
                    </div>
                </div>

            </div>

            <div class="row">
                <div class="col-md-3">
                    {{Form::label('Payment Mode ')}}
                    <div class="form-group">
                        <select class="form-control select2" name="pay_mode_id" id="pay_mode_id" required
                            style="width: 100%;" tabindex="-1" aria-hidden="true">
                            <option value="">Select payment mode</option>
                            @foreach($payment_mode as $item)
                            <option value='{{ $item->method_id }}'>{{ $item->method_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-md-3 hide mpesa_number_div" id="mpesa_number_div">
                    <div class="form-group">
                        {{Form::label('MPESA Number')}}
                        <div class="form-group">
                            {{Form::text('pay_mpesa_no', '',['class'=>'form-control', 'id' => 'mpesa_number'])}}
                        </div>
                    </div>
                </div>

                <div class="col-md-3 hide bank_payment_div" id="bank_payment_div">
                    {{Form::label('Payment Bank ')}}
                    <div class="form-group">
                        <select class="form-control select2" name="pay_bank_id" id="pay_bank_id" style="width: 100%;"
                            tabindex="-1" aria-hidden="true">
                            <option value"">Select payment bank</option>
                            @foreach($banks as $item)
                            <option value="{{ $item->bank_id }}">{{ $item->bank_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3 hide bank_payment_acc" id="bank_payment_acc">
                    <div class="form-group">
                        {{Form::label('Bank Account')}}
                        <div class="form-group">
                            {{Form::text('pay_bank_acc', '',['class'=>'form-control'])}}
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('Investment Date *') !!}
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </span>
                            {{Form::text('inv_date', null, ['class' => 'form-control inv_date', 'id' => 'inv_date', 'required' ])}}
                        </div>
                    </div>
                </div>

            </div>
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        {{Form::label('Investment Amount *')}}
                        <div class="form-group">
                            {{Form::number('inv_amount', '',['class'=>'form-control', 'id'=>'total_inv_amount', 'placeholder'=>'Minimum amount(Kshs 100,000.00)', 'min'=>'1', 'required'])}}
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        {{Form::label('Investment Duration(Months) *')}}
                        <div class="input-group">
                            {{Form::number('inv_duration', '',['class'=>'form-control', 'min' => '1', 'required'])}}
                            <span class="input-group-addon">
                                Months
                            </span>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    {{Form::label('Investment Type ')}}
                    <div class="form-group">
                        <select class="form-control select2" name="inv_type_id" id="inv_type_id" required
                            style="width: 100%;" tabindex="-1" aria-hidden="true">
                            <option value="">Select investment type</option>
                            @foreach($inv_types as $item)
                            <option value='{{ $item->inv_id }}'>{{ $item->inv_type }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3 hide" id="monthly_inv_amount_div">
                    <div class="form-group">
                        {{Form::label('Monthly Amount *')}}
                        <div class="form-group">
                            {{Form::number('monthly_inv_amount', '',['class'=>'form-control', 'id'=>'monthly_inv_amount', 'min'=>'1'])}}
                        </div>
                    </div>
                </div>

                <div class="col-md-3 hide" id="monthly_inv_duration_div">
                    <div class="form-group">
                        {{Form::label('Duration *')}}
                        <div class="form-group">
                            {{Form::number('monthly_inv_duration', '',['class'=>'form-control', 'min' => '1'])}}
                        </div>
                    </div>
                </div>
                <div class="col-md-3 hide" id="compounded_inv_amount_div">
                    <div class="form-group">
                        {{Form::label('Compounded Amount *')}}
                        <div class="form-group">
                            {{Form::number('compounded_inv_amount', '',['class'=>'form-control', 'id'=>'compounded_inv_amount'])}}
                        </div>
                    </div>
                </div>

                <div class="col-md-3 hide" id="compounded_inv_duration_div">
                    <div class="form-group">
                        {{Form::label('Duration(Months) *')}}
                        <div class="form-group">
                            {{Form::number('compounded_inv_duration', '',['class'=>'form-control', 'min' => '1'])}}
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    {{Form::label('Investment Mode ')}}
                    <div class="form-group">
                        <select class="form-control select2" id="inv_mode_id" name="inv_mode_id" required
                            style="width: 100%;" tabindex="-1" aria-hidden="true">
                            <option selected="selected" value="">Select investment mode</option>
                            @foreach($inv_modes as $item)
                            <option value="{{ $item->id }}">{{ $item->inv_mode }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-4 hide mpesa_inv_div" id="mpesa_inv_div">
                    <div class="form-group">
                        {{Form::label('MPESA Transaction Code *')}}
                        <div class="form-group">
                            {{Form::text('mpesa_trans_code', '',['class'=>'form-control'])}}
                        </div>
                    </div>
                </div>
                <div class="col-md-4 hide bank_inv_div" id="bank_inv_div">
                    {{Form::label('Bank ')}}
                    <div class="form-group">
                        <select class="form-control select2" id="inv_bank_id" name="inv_bank_id" style="width: 100%;"
                            tabindex="-1" aria-hidden="true">
                            <option selected="selected">Select investment bank</option>
                            @foreach($banks as $item)
                            <option value="{{ $item->bank_id }}">{{ $item->bank_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-4 hide bank_inv_div" id="inv_bank_trans_id">
                    <div class="form-group">
                        {{Form::label('Bank Transaction Code')}}
                        <div class="form-group">
                            {{Form::text('bank_trans_code', '',['class'=>'form-control'])}}
                        </div>
                    </div>
                </div>
                <div class="col-md-4 hide cheq_inv_div" id="cheq_inv_div">
                    {{Form::label('Bank ')}}
                    <div class="form-group">
                        <select class="form-control select2" id="inv_cheq_bank_id" name="inv_cheq_bank_id"
                            style="width: 100%;" tabindex="-1" aria-hidden="true">
                            <option selected="selected">Select investment bank</option>
                            @foreach($banks as $item)
                            <option value='{{ $item->bank_id }}'>{{ $item->bank_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-4 hide cheq_inv_div" id="cheq_no_inv_div">
                    <div class="form-group">
                        {{Form::label('Cheque Number')}}
                        <div class="form-group">
                            {{Form::text('cheque_no', '',['class'=>'form-control'])}}
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-offset-10 col-md-2">
                    <ul class="list-inline pull-right">
                        <button type="submit" class="btn btn-primary next-step">ADD NEW CLIENT</button>
                    </ul>
                </div>
            </div>
        </div>
        {!! Form::close() !!}
    </div>
</div>
@include('modals.users.modal_blacklist_client')
@include('modals.users.modal_restrict_referal')

@stop
@section('css')
<link rel="stylesheet" href="/css/bootstrap-datepicker.min.css">

@stop
@section('js')
<script src="/js/bootstrap-datepicker.min.js"></script>
<script src="/js/select2.full.min.js"></script>
<script src="https://oss.maxcdn.com/jquery.bootstrapvalidator/0.5.2/js/bootstrapValidator.min.js"></script>

<script>
    $(document).ready(function() {
$('.addClientForm').bootstrapValidator({
    message: 'This value is not valid',
    feedbackIcons: {
    valid: 'glyphicon glyphicon-ok',
    invalid: 'glyphicon glyphicon-remove',
    validating: 'glyphicon glyphicon-refresh'
},
fields: {
    name: {
    message: 'The username is not valid',
    validators: {
    notEmpty: {
    message: 'The username is required'
},
    stringLength: {
    min: 6,
    max: 30,
    message: 'The name must be more than 6 and less than 30 characters long'
},
    regexp: {
        regexp: /^[a-z\s]+$/i,
    message: 'The name can only consist of alphabetical letters'
}
}
},
    telephone: {
    message: 'The phone number is not valid',
    validators: {
    notEmpty: {
    message: 'The phone number is required'
},
    stringLength: {
    min: 10,
    max: 10,
    message: 'The phone number must be 10 characters long'
},
    regexp: {
     regexp: /^[0-9][0-9]{0,15}$/,
    message: 'Phone number can only consist of numbers'
}
}
},
    kin_telephone: {
    message: 'The phone number is not valid',
    validators: {
    notEmpty: {
    message: 'The phone number is required'
},
    stringLength: {
    min: 10,
    max: 10,
    message: 'The phone number must be 10 characters long'
},
    regexp: {
     regexp: /^[0-9][0-9]{0,15}$/,
    message: 'Phone number can only consist of numbers'
}
}
},
    id_no: {
    message: 'The ID number is not valid',
    validators: {
    notEmpty: {
    message: 'The ID number is required'
},
    stringLength: {
    min: 7,
    max: 8,
    message: 'The ID number must be 7 or 8 characters long'
},
    regexp: {
     regexp: /^[0-9][0-9]{0,15}$/,
    message: 'ID number can only consist of numbers'
}
}
},
email: {
    validators: {
    notEmpty: {
    message: 'The email is required and cannot be empty'
},
emailAddress: {
message: 'The input is not a valid email address'
}
}
}
}
});
});
</script>
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
            $("#referer_phone").val(data.text);
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
        if(val == 4){
            $("#mpesa_inv_div").addClass("hide");
            $("#bank_inv_div").addClass("hide");
            $("#inv_bank_trans_id").addClass("hide");
            $("#cheq_inv_div").addClass("hide");
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
       $('input').keyup(function(){
            var totalInvestment  = Number($('#total_inv_amount').val());
           var monthlyInvestment = Number($('#monthly_inv_amount').val());
          var compoundedInvestment = totalInvestment - monthlyInvestment;
         document.getElementById('compounded_inv_amount').value = compoundedInvestment;
        });

        //var phone  = $('#referer_phone').val();
       // alert(phone);

        var form = document.getElementById("addClientForm");
        form.reset();
 })
</script>
@stop
