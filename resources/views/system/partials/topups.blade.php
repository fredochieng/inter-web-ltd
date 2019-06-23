<div class="pos-tab-content">
    <div class="row">
        <div class="col-sm-4">
            <div class="form-group">
                <label for="name">Investment Type 1:</label>
                <input class="form-control" required="" placeholder="" name="b_name" type="text" value="" id="name">
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                <label for="name">Investment Type 2:</label>
                <input class="form-control" required="" placeholder="" name="b_name" type="text" value="" id="name">
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                <label for="name">Investment Type 3:</label>
                <input class="form-control" required="" placeholder="" name="b_name" type="text" value="" id="name">
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="col-sm-4">
            <div class="form-group">
                {{Form::label('Default Interest Percentage')}}
                <div class="input-group">
                    {{Form::number('int_per', '',['class'=>'form-control', 'min' => '1', 'max'=>'100', 'required'])}}
                    <span class="input-group-addon">
                        Percent
                    </span>
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                {{Form::label('Minimum Investment Amount')}}
                <div class="input-group">
                    {{Form::number('min_inv_amount', '',['class'=>'form-control', 'min' => '1', 'required'])}}
                    <span class="input-group-addon">
                        Kshs
                    </span>
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                {{Form::label('Minimum Investment Duration')}}
                <div class="input-group">
                    {{Form::number('min_inv_duration', '',['class'=>'form-control', 'min' => '1', 'required'])}}
                    <span class="input-group-addon">
                        Months
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
