@extends('adminlte::page')

@section('title', 'Subscribers')

@section('content_header')
    <h1>Subscribers</h1>
@stop

@section('content')

<div class="box box-default color-palette-box">
        <div class="box-header with-border">
          <h3 class="box-title"> Search Subscriber</h3>
        </div>
        <div class="box-body">
             {!! Form::open(['action'=>'\Modules\Subs\Http\Controllers\SubsController@index','method'=>'get','class'=>'form form-horizontal subscriber_form','enctype'=>'multipart/form-data']) !!}
             <div class="col-md-5">
                <?php
                    $array_subcribers_by=array(
                      'subscriber_id'=>'Subscriber ID',
                      //'national_id'=>'National ID',
                      //'passport'=>'Passport',
                      'phone_no'=>'Phone Number',
                      'email'=>'Email',
                      'first_name'=>'FirstName',
                      'last_name'=>'LastName',
                      //'status'=>'Status',
                  );
?>

           <?php
				$find_subscriber_by='';	$find_value='';
				if(isset($_GET['find_subscriber_by'])){ $find_subscriber_by=$_GET['find_subscriber_by']; }
				if(isset($_GET['find_value'])){ $find_value=$_GET['find_value']; }
			 ?>

          <div class="form-group"> {{Form::label('find_subscriber_by', 'Search Subs By',['class'=>'col-sm-4 control-label'])}}
            <div class="col-sm-8">
            {{ Form::select('find_subscriber_by', $array_subcribers_by,$find_subscriber_by, ['class' => 'form-control','style'=>'width:100%']) }} </div>
          </div>
        </div>


         <div class="col-md-5">
          <div class="form-group"> {{Form::label('find_value', 'Subscriber ID',['class'=>'col-sm-4 control-label find_value_label'])}}
            <div class="col-sm-8"> {{Form::text('find_value', $find_value,['class'=>'form-control','placeholder'=>'Enter Subscriber ID for the Subscriber'])}}</div>
          </div>
        </div>?>

        <?php
        $find_subscriber_by='';	$find_value='';
        if(isset($_GET['find_subscriber_by'])){ $find_subscriber_by=$_GET['find_subscriber_by']; }
        if(isset($_GET['find_value'])){ $find_value=$_GET['find_value']; }
     ?>

  <div class="form-group"> {{Form::label('find_subscriber_by', 'Search Subs By',['class'=>'col-sm-4 control-label'])}}
    <div class="col-sm-8">
    {{ Form::select('find_subscriber_by', $array_subcribers_by,$find_subscriber_by, ['class' => 'form-control','style'=>'width:100%']) }} </div>
  </div>
</div>


 <div class="col-md-5">
  <div class="form-group"> {{Form::label('find_value', 'Subscriber ID',['class'=>'col-sm-4 control-label find_value_label'])}}
    <div class="col-sm-8"> {{Form::text('find_value', $find_value,['class'=>'form-control','placeholder'=>'Enter Subscriber ID for the Subscriber'])}}</div>
  </div>
  <div class="col-md-2">
    <button type="submit" class="btn btn-block btn-info" name="find_sub"><strong><i class="fa fa-fw fa-search"></i> SEARCH</strong></button>

    </div>


         {!! Form::close() !!}


    </div>
</div>


@if(count($subscribers) > 0)

  <div class="box box-default color-palette-box">
       <div class="box-header with-border">
         <h3 class="box-title"> Subscribers List</h3>
       </div>
       <div class="box-body">
       <p>{{count($subscribers)}} Records found matching your search query</p>
         <table class="table table-bordered table-striped" id="records">
             <thead>
               <tr>
                   <th>#</th>
                   <th>SUB ID</th>
                   <th>FULL NAMES</th>
                   <th>TELEPHONE</th>
                   <th>TOWN</th>
                   <th>FRANCHISE</th>
                   <th>STATUS</th>
                   <th></th>

               </tr>

           </thead>

           <tbody>

               @foreach($subscribers as $key=>$subscriber)
                   <tr>
                       <td>{{$key+1}}</td>
                       <td><a href="subs/{{$subscriber->SUBS}}/edit">{{$subscriber->SUBS}}</a></td>
                       <td>{{$subscriber->FIRSTNAME}} {{$subscriber->MIDDLENAME}} {{$subscriber->LASTNAME}}</td>
                       <td>
                           <?php $subs_contacts=\Modules\Subs\Models\Subs::get_sub_contacts($subscriber->SUBS,2030,0);
                               foreach($subs_contacts as $contact){
                                 echo $contact->PHONE."<br> ";
                                }

                                ?>

                                 <?php $subs_contacts=\Modules\Subs\Models\Subs::get_sub_contacts($subscriber->SUBS,2031,0);
                                    foreach($subs_contacts as $contact){
                                      echo $contact->PHONE."<br> ";
                                    }

                                ?>

                                <?php $subs_contacts=\Modules\Subs\Models\Subs::get_sub_contacts($subscriber->SUBS,116,0);
                                foreach($subs_contacts as $contact){
                                  echo $contact->PHONE."<br> ";
                                }

                            ?>

                        </td>
                        <td>{{$subscriber->town_name}}</td>
                        <td>{{$subscriber->franch_name}}</td>
                        <td>{{$subscriber->customer_status}}</td>
                        <td><a href="subs/{{$subscriber->SUBS}}/edit" class="btn btn-xs btn-info btn-flat"><strong>VIEW</strong></a></td>
                    </tr>
                @endforeach
            </tbody>

          </table>


         </div>
   </div>
@endif


@stop

@section('css')
<link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
<script>

$(document).ready(function() {
	 $("#records").DataTable();
	$('#find_subscriber_by').change(function() {
		//Use $option (with the "$") to see that the variable is a jQuery object
		var $option = $(this).find('option:selected');
		//Added with the EDIT
		var value = $option.val();//to get content of "value" attrib
		var text = $option.text();//to get <option>Text</option> content

		//alert(value);
		//alert(text);

		$(".find_value_label").html(text);
		$("#find_value").attr("placeholder", "Enter "+text+" for the Subscriber");
	});
});
</script>
@stop

