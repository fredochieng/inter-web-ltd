<div class="modal fade in" tabindex="-1" role="dialog" id="modal_search_client">
    <div class="modal-dialog modal-lg" style="width: 60%" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span>
                </button>
                <h4 class="modal-title">Search Client</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 search-container">
                        <div class="input-group">
                            <div class="input-group-btn">
                                <select class="btn btn-default form-control search-by" id="search_client_by" data-live-search="true" style="width: 150px !important;">
                                    <option value="id_no">ID Number</option>
                                    <option value="account_no" selected="">Account Number</option>
                                    <option value="telephone">Phone Number</option>
                                </select>
                            </div>
                            <input type="text" class="form-control" id="search_value" placeholder="Search Client">
                            <span class="input-group-btn">
                                <button class="btn btn-info btn-flat" id="search_clients_action" type="button">
                                    <i class="fa fa-search">Search</i>
                                </button>
                          </span>
                        </div>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="table-responsive">
                                       <table id="clients-list" class="table no-margin">
                                            <thead>
                                                <tr>
                                                    <th>Account</th>
                                                    <th>Name</th>
                                                    <th>ID Number</th>
                                                    <th>Telephone</th>
                                                    <th></th>

                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                    </div>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>
@push('css')
    <style>
        .search-by {
            width: 250px !important;
        }
        #subscribers-list tr td {
            height: 20px;
            }
            .table>tbody>tr>td, .table>tbody>tr>th, .table>tfoot>tr>td, .table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>th {
                padding: 2px;
                line-height: 1.42857143;
                vertical-align: top;
                border-top: 1px solid #ddd;
            }
            .btn_tbl {
                    display: inline-block;
                    padding: 1px 12px;
                    margin-bottom: 0;
                    font-size: 11px;
                    font-weight: 400;
                    line-height: 1.42857143;

                }
    </style>
@endpush

@push('js')
    <script type="text/javascript">
        let clientsTable;
        $(document).ready(function () {

            $('#modal_search_client').on('shown.bs.modal', function (e) {
                clientsTable.columns.adjust().draw();

            });

            $("#search_clients_action").on('click', function () {
                clientsTable.ajax.reload(null, false);
            });


            clientsTable = $('#clients-list').DataTable({
                "processing": true,
                "sScrollY": "300px",
                "dom": 'lfrtip',
                'lengthChange': false,
                "sort": false,
                "paging": true,
                "serverSide": true,
                "initComplete": function () {

                },
                "deferRender": true,
                "ajax": {
                    url: "{{ url("/payments/client/search")  }}",
                    data: function (data) {
                        delete data.columns;
                        delete data.column;
                        data.search_by = $("#search_client_by").val();
                        console.log(search_client_by);
                        data.search = $("#search_value").val();
                    }
                },
                "columnDefs": [
                    {
                        "render": function (data, type, row) {
                            return row.account_no
                        },
                        "targets": 0
                    },
                    {
                        "render": function (data, type, row) {
                            return row.name
                        },
                        "targets": 1
                    },
                    {
                        "render": function (data, type, row) {
                            return row.id_no
                        },
                        "targets": 2
                    },
                    {
                        "render": function (data, type, row) {
                            return row.telephone
                        },
                        "targets": 3
                    },
                    {
                        "render": function (data, type, row) {
                            return "<div class='btn  btn-sm row-actions btn_tbl'>" +
                                "<button data-row='" + JSON.stringify(row) + "' class='action-select-client btn btn-primary' type='button'>" +
                                "<i class='fa fa-tick'></i> Select" +
                                "</button>" +
                                "</div>"
                        },
                        "targets": 4
                    },
                ],
                "searching": false,
                "rowCallback": function (r, d) {
                    //$('td:eq(2) input', r).attr("disabled",true);
                },
            });
        });
    </script>
@endpush






