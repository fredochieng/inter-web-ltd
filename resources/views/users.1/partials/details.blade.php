<div class="col-md-12"><br/>
    <div class="row">
        <div class="row">
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

        <br>
        <div class="row">
            <div class="col-md-offset-10 col-md-2">

                <ul class="list-inline pull-right">
                    <li><button type="button" class="btn btn-primary next-step" id="BasicButton">Next</button></li>
                </ul>

                {{-- <a href="#franchises" data-toggle="tab" class="btn btn-primary btn-sm" id="nextButton"><i class="fa fa-arrow-circle-right"></i> next</a> --}}

            </div>
        </div>



    </div>

</div>
