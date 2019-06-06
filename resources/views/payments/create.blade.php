@extends('adminlte::page')

@section('title', 'Create Payment - BManager')

@section('content_header')
<h1>Payments<small>create new payment</small></h1>
@stop

@section('content')
<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">New Payment</h3>
    </div>

    <div class="box-body">
        {!!
        Form::open(['action'=>'PaymentController@store','method'=>'POST','class'=>'form','enctype'=>'multipart/form-data'])
        !!}
        <div class="row">
            <div class="col-md-4">
                {{Form::label('Account Number* ')}}
                <div class="form-group">
                    <select id="account_no_id" class=" col-md-12 " required name="account_no_id"> </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    {{Form::label('Name')}}
                    <div class="form-group">
                        {{Form::text('name', '',['class'=>'form-control', 'readonly', 'placeholder'=>'Select account number first', 'id' => 'account_user_name'])}}
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    {{Form::label('Telephone')}}
                    <div class="form-group">
                        {{Form::text('telephone', '',['class'=>'form-control', 'readonly', 'placeholder'=>'Select account number first', 'id' => 'account_user_telephone'])}}
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    {{Form::label('Total Due Payments *')}}
                    <div class="form-group">
                        {{Form::text('due_payments', '',['class'=>'form-control', 'id'=>'user_total_due_payments', 'min' => '0', 'max'=>'100', 'placeholder'=>'Select Account Number first'])}}
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    {{Form::label('Amount *')}}
                    <div class="form-group">
                        {{Form::text('payment_amount', '',['class'=>'form-control', 'required', 'placeholder'=>'Enter the amount you want to pay'])}}
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label>Payment Method</label>
                    <select class="form-control select2" name="payment_method_id" id="payment_method" style="width: 100%;" tabindex="-1"
                        aria-hidden="true">
                        <option selected="selected">Select payment method</option>
                        @foreach($payment_methods as $p_method)
                        <option value='{{ $p_method->method_id }}'>{{ $p_method->method_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>
    <!-- /.box-body -->
    <div class="box-footer clearfix">
        <button type="submit" class="pull-right btn btn-primary"><i class="fa fa-check"></i> Make
            Payment</button>
    </div>
    {!! Form::close() !!}
</div>
@stop
@section('css')
<link rel="stylesheet" href="/css/admin_custom.css">
<link rel="stylesheet" href="/css/bootstrap-datepicker.min.css">
@stop
@section('js')

<script src="/js/bootstrap-datepicker.min.js"></script>
<script src="/js/select2.full.min.js"></script>

<script>
    $(function () {
    //Initialize Select2 Elements
$(".select2").select2();
$("#account_no_id").select2({
  ajax: {
    url: "/accounts/get_accounts",
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
                  user_name: element.user_name,
                  user_telephone: element.user_telephone,
                  user_total_payout: element.user_total_payout
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
  placeholder: 'Select account number first',
  escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
  minimumInputLength: 2,
  templateResult: formatRepo,
  templateSelection: formatRepoSelection
}).on('select2:select', function(e) {
    var data = e.params.data;
    $("#account_user_name").val(data.user_name);
    $("#account_user_telephone").val(data.user_telephone);
    $("#user_total_due_payments").val(data.user_total_payout);
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
