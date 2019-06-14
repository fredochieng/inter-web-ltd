$(document).ready(function() {
    //Sell Payment Report
    sell_payment_report = $("table#sell_payment_report_table").DataTable({
        processing: true,
        serverSide: true,
        aaSorting: [[2, "desc"]],
        ajax: {
            url: "/reports/due-payments",
            data: function(d) {
                d.pay_mode_id = $("select#pay_mode_id").val();
                d.bank_id = $("select#bank_id").val();
                alert(d.bank_id);

                var start = "";
                var end = "";
                if ($("input#daterange-btn").val()) {
                    start = $("input#daterange-btn")
                        .data("daterangepicker")
                        .startDate.format("YYYY-MM-DD");
                    end = $("input#daterange-btn")
                        .data("daterangepicker")
                        .endDate.format("YYYY-MM-DD");
                }
                d.start_date = start;
                d.end_date = end;
                alert(start);
            }
        },
        columns: [
            {
                orderable: false,
                searchable: false,
                data: null,
                defaultContent: ""
            },
            { data: "Account", name: "account" },
            { data: "Name", name: "name" },
            { data: "ID Number", name: "id_no" }
        ],
        createdRow: function(row, data, dataIndex) {
            if (!data.transaction_id) {
                $(row)
                    .find("td:eq(0)")
                    .addClass("details-control");
            }
        }
    });
    // Array to track the ids of the details displayed rows
    var spr_detail_rows = [];

    $("#sell_payment_report_table tbody").on(
        "click",
        "tr td.details-control",
        function() {
            var tr = $(this).closest("tr");
            var row = sell_payment_report.row(tr);
            var idx = $.inArray(tr.attr("id"), spr_detail_rows);

            if (row.child.isShown()) {
                tr.removeClass("details");
                row.child.hide();

                // Remove from the 'open' array
                spr_detail_rows.splice(idx, 1);
            } else {
                tr.addClass("details");

                row.child(show_child_payments(row.data())).show();

                // Add to the 'open' array
                if (idx === -1) {
                    spr_detail_rows.push(tr.attr("id"));
                }
            }
        }
    );

    // On each draw, loop over the `detailRows` array and show any child rows
    sell_payment_report.on("draw", function() {
        $.each(spr_detail_rows, function(i, id) {
            $("#" + id + " td.details-control").trigger("click");
        });
    });

    if ($("#daterange-btn").length == 1) {
        $("#daterange-btn").daterangepicker(dateRangeSettings, function(
            start,
            end
        ) {
            $("#daterange-btn span").val(
                start.format(moment_date_format) +
                    " ~ " +
                    end.format(moment_date_format)
            );
            sell_payment_report.ajax.reload();
        });
        $("#daterange-btn").on("cancel.daterangepicker", function(ev, picker) {
            $("#daterange-btn").val("");
            sell_payment_report.ajax.reload();
        });
    }

    $(
        "#sell_payment_report_form #location_id, #sell_payment_report_form #customer_id"
    ).change(function() {
        sell_payment_report.ajax.reload();
    });
});
