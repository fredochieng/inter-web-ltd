$("#pay_bank_id4").change(function () {
    var value = $(this).val();
    if (value != 0) {
        $("#bank_payment_acc4").removeClass("hide");
    } else {
        $("#bank_payment_acc4").addClass("hide");
    }
});
$("#pay_mode_id1").change(function () {
    var val = $(this).val();
    if (val == 1) {
        $("#mpesa_number_div4").removeClass("hide");
        $("#bank_payment_acc4").addClass("hide");
    } else {
        $("#mpesa_number_div4").addClass("hide");
    }
    if (val == 2) {
        $("#bank_payment_div4").removeClass("hide");
        // $("#bank_payment_acc").removeClass("hide");
    } else {
        $("#bank_payment_div4").addClass("hide");
        //    $("#bank_payment_acc").addClass("hide");
    }
});
