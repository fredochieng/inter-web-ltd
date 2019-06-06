<div class="col-md-12">
    <div class="row">
        <div class="row">
           {{--  <div class="col-md-4">
            {{Form::label('Payment Mode ')}}
            <div class="form-group">
                <select class="form-control select2" id="pay_mode_id" name="pay_mode_id" required style="width: 100%;" tabindex="-1"
                    aria-hidden="true">
                    <option selected="selected" value="0">Select payment mode</option>
                    @foreach($payment_mode as $item)
                    <option value="{{ $item->method_id }}">{{ $item->method_name }}</option>
                    @endforeach
                </select>
            </div>
        </div>  --}}
        <div class="col-md-4" id="">
            <div class="form-group">
                <div class="form-group col-md-12">
                    {{ Form::label('','Payment Mode',['class'=>'control-label label-left']) }}
                    {{ Form::select('pay_mode_id', $payment_mode->pluck('method_name','method_id'), null,
                          ['class'=>'form-control', 'id'=> 'pay_mode_id', 'placeholder'=>'Select payment mode' , 'required'=>'required'] ) }}
                </div>
            </div>
        </div>
        <div class="col-md-4 hide mpesa_number_div" id="mpesa_number_div">
            <div class="form-group">
                {{Form::label('MPESA Number')}}
                <div class="form-group">
                    {{Form::text('pay_mpesa_no', '',['class'=>'form-control', 'id' => 'mpesa_number'])}}
                </div>
            </div>
        </div>

        <div class="col-md-4 hide bank_payment_div" id="bank_payment_div">
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
        <div class="col-md-4 hide bank_payment_acc" id="bank_payment_acc">
            <div class="form-group">
                {{Form::label('Bank Account')}}
                <div class="form-group">
                    {{Form::text('pay_bank_acc', '',['class'=>'form-control'])}}
                </div>
            </div>
        </div>
        </div>

        <br>
       <div class="row">
        <ul class="list-inline pull-right">
            <li><button type="button" class="btn btn-default prev-step">Previous</button></li>
            <li><button type="button" class="btn btn-primary next-step" id="BasicButton">Next</button></li>
        </ul>
    </div>



    </div>

</div>

<script>






</script>
