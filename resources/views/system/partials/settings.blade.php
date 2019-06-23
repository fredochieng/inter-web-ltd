<div class="pos-tab-content active">
    <div class="row">
        <div class="col-sm-4">
            <div class="form-group">
                <label for="name">Business Name:*</label>
                <input class="form-control" required="" placeholder="Business Name" name="b_name" type="text" value=""
                    id="name">
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                <label for="start_date">Start Date:</label>
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                    </span>
                    <input class="form-control start-date-picker" placeholder="Start Date" readonly="" name="start_date"
                        type="text" value="" id="start_date">
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                <label for="business_logo">Upload Logo:</label>
                <input accept="image/*" name="business_logo" type="file" id="business_logo">
                <p class="help-block"><i> Previous logo (if exists) will be replaced</i></p>
            </div>
        </div>
        <div class="clearfix"></div>
    </div>
</div>
