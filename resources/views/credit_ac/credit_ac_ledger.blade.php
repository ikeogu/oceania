@extends('common.web')
@include('common.header')

@section('styles')
    <style>

        .dataTables_wrapper .dataTables_length, .dataTables_wrapper .dataTables_filter,
        .dataTables_wrapper .dataTables_info, .dataTables_wrapper .dataTables_processing,
        .dataTables_wrapper .dataTables_paginate {
            color: black;
        }

        th, td {
            vertical-align: middle !important;
            text-align: center
        }

        label, .dataTables_info,
        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter,
        .dataTables_wrapper .dataTables_info,
        .dataTables_wrapper .dataTables_processing,
        .dataTables_wrapper .dataTables_paginate {
            color: #000 !important;
        }

        .active_button {
            color: #ccc;
            border: 1px #ccc solid;
        }

        .active_button:hover, .active_button:active, .active_button_activated {
            background: transparent !important;
            color: #34dabb !important;
            border: 1px #34dabb solid !important;
            font-weight: bold;
        }

        .active_button_activated {
            background: transparent;
            color: #34dabb;
            border: 1px #34dabb solid;
            font-weight: bold;
        }

        .slim-cell {
            padding-top: 2px !important;
            padding-bottom: 2px !important;
        }

        .pd_column {
            padding-top: 10px;
        }

        tr {
            height: 40px
        }
        a:hover, a:visited, a:link, a:active{
            text-decoration: none;
        }


.modal-inside .row {
    padding: 0px;
    margin: 0px;
    color: #000;
}

.modal-body {
    position: relative;
    flex: 1 1 auto;
    padding: 0px !important;
}

    </style>
@endsection

@include('common.menubuttons')

@section('content')
    <div class="container-fluid">
        <div class="d-flex mt-0 p-0"
             style="width:100%; margin-top:5px !important;margin-bottom:5px !important;height: 70px;">
            <div style="padding:0" class="align-self-center col-sm-8">
                <h2 class="mb-0">Credit Account Ledger</h2>
            </div>

            {{--<div class="col-sm-4 pl-0">
                <div class="row mb-0" style="float:right;">

                     <span class="col-auto " style="margin-top: 10%">
                               New Franchise Location
                           </span>
                    <button onclick=""
                            class="btn  sellerbutton mb-0 mr-0"
                            style="padding:0px;float:right;margin-left:5px;
						border-radius:10px; background-color: darkgray; color: white" id="addproduct_btn">Confirm
                    </button>
                </div>
            </div>--}}
        </div>

        <div class="col-sm-12 pl-0 pr-0" style="">
            <table id="tableFLocaltionProduct" class="table table-bordered">
                <thead class="thead-dark">
                <tr style="">
                    <th class="text-center" style="text-align: center;">No</th>
                    <th class="text-center" style="text-align: center;">Date</th>
                    <th class="text-left" style="">Document No</th>
                    <th class="text-center" style="" nowrap>Amount</th>
                </tr>
                </thead>
                <tbody id="shows">
                </tbody>
            </table>
        </div>
    </div>

@endsection

<div class="modal fade" id="evReceiptDetailModal" tabindex="-1" role="dialog">
    <div class="modal-dialog  modal-dialog-centered"
        style="width: 366px; margin-top: 0!important;margin-bottom: 0!important;">

        <!-- Modal content-->
        <div class="modal-content  modal-inside detail_view">
        </div>
    </div>
</div>

@section('script')

    <script>
        $.ajaxSetup({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
        });

        var tableData = {};
        var table = $('#tableFLocaltionProduct').DataTable({
            "processing": false,
            "serverSide": true,
            "autoWidth": false,
            "language": {
                "emptyTable": "No data available in table"
            },
            "ajax": {
                /* This is just a sample route */
                "url": "{{route('creditaccount.listledger',request()->systemid)}}",
                "type": "POST",
                data: function (d) {
                    return $.extend(d, tableData);
                },
                'headers': {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
            },
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                {data: 'date', name: 'date'},
                {data: 'sysid', name: 'sysid', render: function (data, type, row, meta) {
                //data = JSON.parse(data);
                return "<a href='#' onClick='getFuelReceiptlist("+row['receipt_id']+")' style='text-decoration: none;'>"+data+"</a>"
                }
                },
                {data: 'amount', name: 'amount'},

            ],
            createdRow: (row, data, dataIndex, cells) => {
                if(data['amount'] == '0.00'){
                    $(cells[3]).css('background-color', 'red');
                }
            },
           
            "columnDefs": [
                {"width": "30px", "targets": [0]},
                {"width": "120px", "targets": [1]},
                {"width": "120px", "targets": [3]},
                {"className": "dt-left vt_middle", "targets": [ 2, ]},
                {"className": "dt-right vt_middle", "targets": [ 3, ]},
                {orderable: false, targets: [-1]},
            ],
        });


function getFuelReceiptlist(data) {
    $.ajax({
            method: "post",
            url: "{{ route('fuel.envReceipt') }}",
            data: {
                id: data
            }
        }).done((data) => {
            $(".detail_view").html(data);
            $("#evReceiptDetailModal").modal('show');
        })
        .fail((data) => {
            console.log("data", data)
        });
}

    </script>
@endsection
@include('common.footer')
