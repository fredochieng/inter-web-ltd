@extends('adminlte::page')

@section('title', 'New Client - Inter Web Ltd')

@section('content_header')
<h1>Investors<small>add new client</small></h1>
@stop

@section('content')
<div class="box box-success">
    <div class="box-body">
        @if(count($errors)>0)
        <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            @foreach($errors->all() as $error)
            {{$error}}<br>
            @endforeach
        </div>
        @endif
        <div class="row">
            {!! Form::open(['url' => action('UserController@store'), 'method' => 'post', 'id' => 'user_add_form'
            ]) !!}
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('Full Name *') !!}
                    {!! Form::text('name', null, ['class' => 'form-control', 'required']); !!}
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
                <div class="input-group">
                    {!! Form::label('Date of Birth *') !!}
                    {{Form::text('dob', null, ['class' => 'form-control dob', 'id' => 'dob', 'required' ])}}

                </div>
            </div>
            <div class="clearfix"></div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('ID Number *') !!}
                    {!! Form::text('id_no', null, ['class' => 'form-control', 'required']); !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label(' Account Number *') !!}
                    {!! Form::text('account_no', $generated_account, ['class' => 'form-control' ]); !!}
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
            <div class="clearfix"></div>
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
            <div class="col-md-3">
                <div class="form-group">
                    {{Form::label('Name')}}
                    <div class="form-group">
                        {{Form::text('referer_name', '',['class'=>'form-control', 'readonly', 'id' => 'referer_name'])}}
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
            {{--  <div class="col-md-3">
                <div class="form-group required">
                    {{Form::label('Payment Mode')}} <br />
            <label>
                {!! Form::checkbox('checkbox', 1, false,
                [ 'class' => 'flat-red', 'value'=>'1', 'id' => 'mpesa_payment_id', 'name' => 'pay_mode_id']); !!}
                {{ 'MPESA' }}
            </label>
            <label>
                {!! Form::checkbox('checkbox', 2, false,
                [ 'class' => 'flat-red', 'value'=>'2', 'id' => 'bank_payment_id', 'name' => 'pay_mode_id']); !!}
                {{ 'BANK ACCOUNT' }}
            </label>
            {{-- <label>
                        <input type="checkbox" id='mpesa_payment_id' name="pay_mode_id" value="1" class="flat-red">
                    </label>
                    <label>
                        <input type="checkbox" id='bank_payment_id' name="pay_mode_id" value="2" class="flat-red">
                    </label> --}}
            {{--  </div>
            </div>   --}}
            <div class="col-md-3">
                {{Form::label('Payment Mode ')}}
                <div class="form-group">
                    <select class="form-control select2" id="pay_mode_id" name="pay_mode_id" style="width: 100%;"
                        tabindex="-1" aria-hidden="true">
                        <option selected="selected" value="0">Select payment mode</option>
                        @foreach($payment_mode as $item)
                        <option value="{{ $item->method_id }}">{{ $item->method_name }}</option>
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
                    <select class="form-control select2" name="pay_bank_id" id="pay_bank_id" style="width: 100%;" tabindex="-1"
                        aria-hidden="true">
                        <option value"0">Select payment bank</option>
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
            <div class="col-md-3">
                <div class="form-group">
                    {{Form::label('Investment Amount *')}}
                    <div class="form-group">
                        {{Form::text('inv_amount', '',['class'=>'form-control', 'required'])}}
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {{Form::label('Investment Duration *')}}
                    <div class="input-group">
                        {{Form::number('inv_duration', '',['class'=>'form-control', 'min' => '1', 'required'])}}
                        <span class="input-group-addon">Months</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                {{Form::label('Investment Type ')}}
                <div class="form-group">
                    <select class="form-control select2" name="inv_type_id" required style="width: 100%;" tabindex="-1"
                        aria-hidden="true">
                        <option value="">Select investment type</option>
                        @foreach($inv_types as $item)
                        <option value='{{ $item->inv_id }}'>{{ $item->inv_type }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="clearfix"></div>
            <div class="col-md-3">
                {{Form::label('Investment Mode ')}}
                <div class="form-group">
                    <select class="form-control select2" id="inv_mode_id" name="inv_mode_id" style="width: 100%;" tabindex="-1"
                        aria-hidden="true">
                        <option selected="selected" value="0">Select investment mode</option>
                        @foreach($inv_modes as $item)
                        <option value="{{ $item->id }}">{{ $item->inv_mode }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-3 hide mpesa_inv_div" id="mpesa_inv_div">
                <div class="form-group">
                    {{Form::label('MPESA Transaction Code *')}}
                    <div class="form-group">
                        {{Form::text('mpesa_trans_code', '',['class'=>'form-control'])}}
                    </div>
                </div>
            </div>
            <div class="col-md-3 hide bank_inv_div" id="bank_inv_div">
                {{Form::label('Bank ')}}
                <div class="form-group">
                    <select class="form-control select2" id="inv_bank_id" name="inv_bank_id" style="width: 100%;" tabindex="-1"
                        aria-hidden="true">
                        <option selected="selected">Select investment bank</option>
                        @foreach($banks as $item)
                        <option value="{{ $item->bank_id }}">{{ $item->bank_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-3 hide bank_inv_div" id="inv_bank_trans_id">
                <div class="form-group">
                    {{Form::label('Bank Transaction Code')}}
                    <div class="form-group">
                        {{Form::text('bank_trans_code', '',['class'=>'form-control'])}}
                    </div>
                </div>
            </div>
            <div class="col-md-3 hide cheq_inv_div" id="cheq_inv_div">
                {{Form::label('Bank ')}}
                <div class="form-group">
                    <select class="form-control select2" id="inv_cheq_bank_id" name="inv_cheq_bank_id" style="width: 100%;" tabindex="-1"
                        aria-hidden="true">
                        <option selected="selected">Select investment bank</option>
                        @foreach($banks as $item)
                        <option value='{{ $item->bank_id }}'>{{ $item->bank_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-3 hide cheq_inv_div" id="cheq_no_inv_div">
                <div class="form-group">
                    {{Form::label('Cheque Number')}}
                    <div class="form-group">
                        {{Form::text('cheque_no', '',['class'=>'form-control'])}}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="box-footer clearfix">
        <button type="submit" class="pull-right btn btn-primary"><i class="fa fa-check"></i> ADD</button>
    </div>

    {!! Form::close() !!}

</div><!-- /.modal-content -->
@stop
@section('css')
<link rel="stylesheet" href="/css/admin_custom.css">
<link rel="stylesheet" href="/css/bootstrap-datepicker.min.css">
<link rel="stylesheet" href="/iCheck/all.css">
@stop
@section('js')

<script src="/js/bootstrap-datepicker.min.js"></script>
<script src="/js/select2.full.min.js"></script>
<script src="/iCheck/icheck.min.js"></script>

<script>
$(document).ready(function(){

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
        }else{
        $("#mpesa_inv_div").addClass("hide");
        }
        if (val == 2 ) {
        $("#bank_inv_div").removeClass("hide");
        // $("#bank_payment_acc").removeClass("hide");
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
 });

</script>
@stop
