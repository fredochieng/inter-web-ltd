$(function () {

    $("#plan_type").change(function () {
        var val = $(this).val();
        if (val == 1) {
            $("#inv_duration_div1").removeClass("hide");
            $("#monthly_inv_duration_div").addClass("hide");
            $("#compounded_inv_duration_div").addClass("hide");

            document.getElementById('amount_transfered').readOnly = true;
            document.getElementById("amount_after_transfer").readOnly = true;

            var investment_type = $('#real_inv_type_id').val();
            // alert(investment_type);

            if (investment_type == 3) {

                $("#inv_subtype_id2").change(function () {
                    val2 = $(this).val();
                    if (val2 == 1) {
                        var totalInvestment = Number($('#monthly_total_investments').val());
                        var after_transfer = totalInvestment - totalInvestment;
                        document.getElementById('amount_transfered').value = totalInvestment;
                        document.getElementById('amount_after_transfer').value = after_transfer;
                    } else {
                        var totalInvestment = Number($('#comp_total_investments').val());
                        var after_transfer = totalInvestment - totalInvestment;
                        document.getElementById('amount_transfered').value = totalInvestment;
                        document.getElementById('amount_after_transfer').value = after_transfer;
                    }

                });

            } else {
                var totalInvestment = Number($('#total_investments').val());
                var after_transfer = totalInvestment - totalInvestment;
                document.getElementById('amount_transfered').value = totalInvestment;
                document.getElementById('amount_after_transfer').value = after_transfer;
            }
        } else {
            // Reset form bringing complications
            // document.getElementById('changePlanClientForm').reset();
            document.getElementById('amount_transfered').readOnly = false;
            $("#inv_duration_div1").addClass("hide");
            $("#monthly_inv_duration_div1").removeClass("hide");
            $("#compounded_inv_duration_div1").removeClass("hide");
        }

        // Partial transfers
        if (val == 2) {
            var investment_type = $('#real_inv_type_id').val();
            if (investment_type == 3) {

                $("#inv_subtype_id2").change(function () {
                    val2 = $(this).val();
                    if (val2 == 1) {
                        $('input').keyup(function () {
                            var totalInvestment31 = Number($('#monthly_total_investments').val());
                            var transfered31 = Number($('#amount_transfered').val());
                            var after_transfer31 = totalInvestment31 - transfered31;
                            document.getElementById('amount_after_transfer').value = after_transfer31;
                        });
                    } else {
                        $('input').keyup(function () {

                            var totalInvestment31 = Number($('#comp_total_investments').val());
                            var transfered31 = Number($('#amount_transfered').val());
                            var after_transfer31 = totalInvestment31 - transfered31;
                            document.getElementById('amount_after_transfer').value = after_transfer31;
                        });
                    }

                });

            } else {

                $('input').keyup(function () {
                    var totalInvestment31 = Number($('#total_investments').val());
                    var transfered31 = Number($('#amount_transfered').val());
                    var after_transfer31 = totalInvestment31 - transfered31;
                    document.getElementById('amount_after_transfer').value = after_transfer31;
                });
            }


            document.getElementById("amount_after_transfer").readOnly = true;
            $("#inv_duration_div1").addClass("hide");
            $("#monthly_inv_duration_div1").removeClass("hide");
            $("#compounded_inv_duration_div1").removeClass("hide");


        } else {
            // document.getElementById('changePlanClientForm').reset();
            // document.getElementById('amount_transfered').readOnly = false;

            $("#inv_duration_div1").removeClass("hide");
            $("#monthly_inv_duration_div1").addClass("hide");
            $("#compounded_inv_duration_div1").addClass("hide");
        }

    });

})
