<div class="col-md-12">
    <div class="row">
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    {{Form::label('Investment Amount *')}}
                    <div class="form-group">
                        {{Form::number('inv_amount', '',['class'=>'form-control', 'id'=>'total_inv_amount', 'placeholder'=>'Minimum investments amount(Kshs 100,000.00)', 'min'=>'1', 'required'])}}
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('Investment Date *') !!}
                    {{Form::text('inv_date', null, ['class' => 'form-control inv_date', 'id' => 'inv_date', 'required' ])}}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    {{Form::label('Investment Duration(Months) *')}}
                    <div class="form-group">
                        {{Form::number('inv_duration', '',['class'=>'form-control', 'min' => '1', 'required'])}}
                        {{--  <span class="input-group-addon">Months</span>  --}}
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
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
            <div class="col-md-2 hide" id="monthly_inv_amount_div">
                <div class="form-group">
                    {{Form::label('Monthly Amount *')}}
                    <div class="form-group">
                        {{Form::number('monthly_inv_amount', '',['class'=>'form-control', 'id'=>'monthly_inv_amount', 'min'=>'1', 'required'])}}
                    </div>
                </div>
            </div>

            <div class="col-md-2 hide" id="monthly_inv_duration_div">
                <div class="form-group">
                    {{Form::label('Duration *')}}
                    <div class="form-group">
                        {{Form::number('monthly_inv_duration', '',['class'=>'form-control', 'min' => '1', 'required'])}}
                        {{--  <span class="input-group-addon">Months</span>  --}}
                    </div>
                </div>
            </div>
            <div class="col-md-2 hide" id="compounded_inv_amount_div">
                <div class="form-group">
                    {{Form::label('Compounded Amount *')}}
                    <div class="form-group">
                        {{Form::number('compounded_inv_amount', '',['class'=>'form-control', 'id'=>'compounded_inv_amount'])}}
                    </div>
                </div>
            </div>

            <div class="col-md-2 hide" id="compounded_inv_duration_div">
                <div class="form-group">
                    {{Form::label('Duration(Months) *')}}
                    <div class="form-group">
                        {{Form::number('compounded_inv_duration', '',['class'=>'form-control', 'min' => '1'])}}
                        {{--  <span class="input-group-addon">Months</span>  --}}
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                {{Form::label('Investment Mode ')}}
                <div class="form-group">
                    <select class="form-control select2" id="inv_mode_id" name="inv_mode_id" style="width: 100%;"
                        tabindex="-1" aria-hidden="true">
                        <option selected="selected" value="0">Select investment mode</option>
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

        <br>
        <div class="row">
            <ul class="list-inline pull-right">
                <li><button type="button" class="btn btn-default prev-step">Previous</button></li>
                <li><button type="submit" class="btn btn-primary next-step">Save Client</button></li>
            </ul>
        </div>
    </div>

</div>

<script>
</script>
