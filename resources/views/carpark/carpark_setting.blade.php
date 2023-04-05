@extends('common.web')
@include('common.header')

@section('styles')
<style>
.dataTables_wrapper .dataTables_length,
.dataTables_wrapper .dataTables_filter,
.dataTables_wrapper .dataTables_info,
.dataTables_wrapper .dataTables_processing,
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

.typewriter {
   text-align: right;
   border-radius: 3px;
   overflow: hidden; /* Ensures the content is not revealed until the animation */
   /* border-right: .15em solid black;*/ /* The typwriter cursor */
   white-space: nowrap; /* Keeps the content on a single line */
   margin: 0 auto; /* Gives that scrolling effect as the typing happens */
   letter-spacing: .0em; /* Adjust as needed */
   /* animation:
	 typing 3.5s steps(40, end),
	 blink-caret .75s step-end infinite;*/
}


/* The typewriter cursor effect */
@keyframes blink-caret {
   from, to {
       border-color: transparent
   }
   50% {
       border-color: black;
   }
}
</style>
@endsection

@include('common.menubuttons')
@section('content')
    <div class="container-fluid">
        <div class="d-flex mt-0 p-0"
             style="width:100%; height: 75px;">
            <div style="padding:0; " class="align-self-center col-md-8">
                <h2 class="mb-0">Carpark Lot Setting</h2>
            </div>

            <div class="col-md-4 pl-0 align-items-center">
                <div class="row mb-0" style="float:right;">

                    <a href="javascript:void(0)" onclick="setDefaultKwh()"
						class="text-center"
						style="margin-top:10px !important;padding:0px;float:right;
						text-decoration: none; margin-right: 15px">
                        Default kWh<br>
						<span id="kwh_default"> 0.00</span>
                    </a>

                    <a href="javascript:void(0)" onclick="setDefaultRate()"
						class="text-center"
						style="margin-top:10px !important;padding:0px;float:right;
						text-decoration: none">
						Default /hour<br>
						<span id="rate_default"> 0.00</span>
                    </a>

                    <input type="hidden" name="rate_default_value"
						id="rate_default_value" value="">
                    <input type="hidden" name="kwh_default_value"
						id="kwh_default_value" value="">

                    <button onclick="save()"
						class="btn btn-success sellerbutton mb-0 mr-0"
						style="padding:0px;float:right;margin-left:5px;
						border-radius:10px; color: white" id="add">
						<span style="margin-left:-5px">+Lot</span>
                    </button>
                </div>
            </div>
        </div>

        <div class="col-md-12 pl-0 pr-0" style="">
            <table id="carparklot_table" class="table table-bordered">
                <thead class="thead-dark">
                <tr style="">
                    <th class="text-center" style="">No</th>
                    <th class="text-center" style="">Lot ID</th>
                    <th class="text-left" style="">Lot No</th>
                    <th class="text-center" style="">kWh</th>
                    <th class="text-center" style="">/hour</th>
                    <th class="text-center" style=""></th>
                    <th class="text-center" style=""></th>
                </tr>
                </thead>
                <tbody id="shows">

                </tbody>
            </table>
        </div>
    </div>

    <div class="modal fade" id="fillFields2" tabindex="-1" role="dialog">
        <div class="modal-dialog  modal-dialog-centered mw-75 w-50">

            <!-- Modal content-->
            <div class="modal-content  modal-inside bg-purplelobster">
                <div class="modal-header" style="border:none;">&nbsp;</div>
                <div class="modal-body text-center">
                    <h5 class="mb-0" id="return_data2">
                        Please fill all fields
                    </h5>
                </div>
                <div class="modal-footer" style="border: none;">&nbsp;</div>
            </div>

        </div>
    </div>

    <div class="modal fade" id="updateModal" tabindex="-1"
         role="dialog" aria-labelledby="staffNameLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered"
             role="document" style="width: 300px">
            <div class="modal-content ">

                <div class="modal-body bg-purplelobster">
                    <div class='text-center' style="margin:auto">
                        <input type="number" placeholder="1" id="value"
                               style="text-align:right"
                               class="form-control"/>
                        <input type="hidden" id="key"
                               style="text-align:right"
                               value="qty" class="form-control"
                               placeholder='0'/>
                        <input type="hidden" id="element"
                               style="text-align:right"
                               value="" class="form-control"
                               placeholder='0'/>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="normalRateModal" tabindex="-1"
         role="dialog" aria-labelledby="staffNameLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered"
             role="document" style="width: 300px">
            <div class="modal-content bg-purplelobster">
                <div class="modal-body ">
                    <div class='text-center ' style="margin:auto">

                        <!-- <div class="typewriter" id="retail_price_normal_fk_text"
                             style="padding: 6px 12px 6px 12px; background-color: white; color: #0c0c0c; ">0.00
                        </div> -->


                        <input type="text" id="retail_price_normal_fk" class="form-control"
                               placeholder='0.00' value="0" style="text-align:right;" />

                        <input type="hidden" id="retail_price_normal_fk_buffer"
                               style="text-align:right; " class="form-control"
                               placeholder='' value="0"/>
                        <input type="hidden" id='retail_price_normal'/>
                        <input type="hidden" id="element_price"
                               style="text-align:right" value="" class="form-control"
                               placeholder='0'/>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="deleteLotModal" tabindex="-1"
         role="dialog" aria-labelledby="staffNameLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered  mw-75 w-50"
             role="document">
            <div class="modal-content modal-inside bg-purplelobster">

                <div class="modal-header" style="border-width:0"></div>
                <div class="modal-body text-center">
                    <h5 class="modal-title text-white">
                        Do you want to permanently
                        delete this lot?
                    </h5>
                </div>
                <div class="modal-footer text-center" style="border-width:0">
                    <div class="row" style="width:100%;">
                        <input type="hidden" name="" id="lotId" value="">
                        <div class="col col-m-12 text-center">
                            <button class="btn btn-primary primary-button"
                                    onclick="yesDelete()">Yes
                            </button>
                            <button class="btn btn-danger primary-button"
                                    onclick="noDelete()">No
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            </ul>
        </div>
    </div>

@endsection
@section('script')
    <script src="{{asset('js/number_format.js')}}"></script>

    <script>
        $.ajaxSetup({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
        });

        var tableData = {};
        var table = $('#carparklot_table').DataTable({
            "processing": false,
            "serverSide": true,
            "autoWidth": false,
            "ajax": {
                /* This is just a sample route */
                "url": "{{route('car_park.list')}}",
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
                {data: 'systemid', name: 'systemid'},
                {
                    data: 'lot_no', name: 'lot_no', render: function (data) {
                        return "<a href='javascript:void(0)' style='text-decoration: none;' onclick='update(" + JSON.parse(data)["id"] + "," + JSON.parse(data)["lot_no"] + ")'>" + (JSON.parse(data)["lot_no"]) + "</a>"
                    }
                },
                {
					data: 'kwh', name: 'kwh', render: function (data) {
                        return "<a href='javascript:void(0)' style='text-decoration: none;' onclick='updateKwh(" + JSON.parse(data)["id"] + "," + JSON.parse(data)["kwh"] + ")'>" + number_format((JSON.parse(data)["kwh"] != null ? JSON.parse(data)["kwh"] : "0.00") / 100, 2) + "</a>"
					}
				},
                {
                    data: 'rate', name: 'rate', render: function (data) {
                        return "<a href='javascript:void(0)' style='text-decoration: none;' onclick='updateRate(" + JSON.parse(data)["id"] + "," + JSON.parse(data)["rate"] + ")'>" + number_format((JSON.parse(data)["rate"] != null ? JSON.parse(data)["rate"] : "0.00") / 100, 2) + "</a>"
                    }
                },
                {data: 'bluecrab', name: 'bluecrab'},
                {data: 'action', name: 'action'},
            ],
            "order": [0, 'desc'],
            "columnDefs": [
                {"width": "30px", "targets": [0,5,6]},
                {"width": "120px", "targets": [1]},
                {"width": "auto", "targets": [2]},
                {"width": "100px", "targets": [3,4]},
                {"className": "dt-left vt_middle", "targets": [2]},
                {"className": "dt-center vt_middle", "targets": [3,4,5,6]},
                {orderable: false, targets: [-1,-2]},
            ],
        });

        function deleteMe(id) {
            $("#deleteLotModal").modal("show");
            $("#lotId").attr("value", id);

        }

        function noDelete() {
            $("#deleteLotModal").modal("hide");
        }


        function yesDelete() {
            $.ajax({
                method: "post",
                url: "{{route('car_park.lotDelete')}}",
                data: {id: $("#lotId").val()}
            })
                .done((data) => {
                    $("#deleteLotModal").modal("hide");
                    table.ajax.reload();
                    $("#return_data2").html("Lot deleted successfully");
                    $("#fillFields2").modal('show');
                    setTimeout(function () {
                        $('#fillFields2').modal('hide');
                    }, 2000);
                })
                .fail((data) => {
                    $("#return_data2").html("Lot deleted error, try again");
                    $("#fillFields2").modal('show');
                    setTimeout(function () {
                        $('#fillFields2').modal('hide');
                    }, 2000);
                });
        }

        function save() {
            $.ajax({
                method: "get",
                url: "{{route('car_park.save')}}"
            })
                .done((data) => {
                    //console.log("data", data);
                    table.ajax.reload();
                    $("#return_data2").html("Lot added successfully");
                    $("#fillFields2").modal('show');
                    setTimeout(function () {
                        $('#fillFields2').modal('hide');
                    }, 2000);
                })
                .fail((data) => {
                    console.log("data", data)
                });
        }

        loadDefaultRate();

        function loadDefaultRate() {
            $.ajax({
                method: "get",
                url: "{{route('car_park.loadDefaultRate')}}"
            })
                .done((data) => {
                    console.log("data",data);
                    if (data.data == null) {
                        $("#rate_default").text("0.00");
                        $("#rate_default_value").attr("value", 0);
                    } else {

                        $("#rate_default").text(number_format(data.data.default_rate / 100, 2));
                        $("#rate_default_value").attr("value", data.data.default_rate);
                    }

                    if (data.carparksettingkwh== null) {
                        $("#kwh_default").text("0.00");
                        $("#kwh_default_value").attr("value", 0);
                    } else {

                        $("#kwh_default").text(number_format(data.carparksettingkwh.default_kwh / 100, 2));
                        $("#kwh_default_value").attr("value", data.carparksettingkwh.default_kwh);
                    }


                })
                .fail((data) => {
                    console.log("data", data)
                });
        }


        function setDefaultRate() {
            $("#normalRateModal").modal("show");
            $('#normalRateModal').on('shown.bs.modal', function (e) {
                $("#retail_price_normal_fk").focus();
            });
            let old_value = $("#rate_default_value").val();
            if (parseInt(old_value) > 0) {
                $("#retail_price_normal_fk").val(atm_money(old_value));
                // $("#retail_price_normal_fk_text").text(atm_money(old_value));
            } else {
                $("#retail_price_normal_fk").val('');
                // $("# _text").text("0.00");

            }
            $("#retail_price_normal").val(old_value);
            $("#element_price").attr("value", "default_rate");
        }


        function setDefaultKwh() {
            $("#normalRateModal").modal("show");
            $('#normalRateModal').on('shown.bs.modal', function (e) {
                $("#retail_price_normal_fk").focus();
            });
            let old_value = $("#kwh_default_value").val();
            if (parseInt(old_value) > 0) {
                $("#retail_price_normal_fk").val(atm_money(old_value));
                // $("#retail_price_normal_fk_text").text(atm_money(old_value));
            } else {
                $("#retail_price_normal_fk").val('');
                // $("#retail_price_normal_fk_text").text("0.00");

            }
            $("#retail_price_normal").val(old_value);
            $("#element_price").attr("value", "default_kwh");
        }




        function update(data, old_value) {
            $("#updateModal").modal("show");
            $("#value").val(old_value);
            $("#key").val("lot_no");
            $("#element").attr("value", data);
        }

        $('#updateModal').on('hidden.bs.modal', function (e) {
            let key = $("#key").val();
            let value = $("#value").val();
            let element = $("#element").val();
            if (value != null) {
                $.ajax({
                    method: "post",
                    url: "{{route('car_park.updateValue')}}",
                    data: {key: key, value: value, element: element}
                })
                    .done((data) => {
                        console.log("data", data);
                        table.ajax.reload();

                    })
                    .fail((data) => {
                        console.log("data", data)
                    });
            }
        });


        function atm_money(num) {
            if (num.toString().length == 1) {
                return '0.0' + num.toString()
            } else if (num.toString().length == 2) {
                return '0.' + num.toString()
            } else if (num.toString().length == 3) {
                return num.toString()[0] + '.' + num.toString()[1] +
                    num.toString()[2];
            } else if (num.toString().length >= 4) {
                return num.toString().slice(0, (num.toString().length - 2)) +
                    '.' + num.toString()[(num.toString().length - 2)] +
                    num.toString()[(num.toString().length - 1)];
            }
        }
        let key = "rate";

        function updateRate(data, old_value) {
            $("#normalRateModal").modal("show");
            $('#normalRateModal').on('shown.bs.modal', function (e) {
                $("#retail_price_normal_fk").focus();
            });
            key = "rate";
            if (parseInt(old_value) > 0) {
                $("#retail_price_normal_fk").val(atm_money(old_value));
                // $("#retail_price_normal_fk_text").text(atm_money(old_value));
            } else {
                $("#retail_price_normal_fk").val('');
                // $("#retail_price_normal_fk_text").text("0.00");

            }
            $("#retail_price_normal").val(old_value);
            $("#element_price").attr("value", data);
        }


        function updateKwh(data, old_value) {
            console.log(data);
            $("#normalRateModal").modal("show");
            $('#normalRateModal').on('shown.bs.modal', function (e) {
                $("#retail_price_normal_fk").focus();
            });

            key = "kwh";
            if (parseInt(old_value) > 0) {
                $("#retail_price_normal_fk").val(atm_money(old_value));
                // $("#retail_price_normal_fk_text").text(atm_money(old_value));
            } else {
                $("#retail_price_normal_fk").val('').focus();
                // $("#retail_price_normal_fk_text").text("0.00");

            }
            $("#retail_price_normal").val(old_value);
            $("#element_price").attr("value", data);
        }

        filter_price('#retail_price_normal_fk', '#retail_price_normal')

        function filter_price(target_field, buffer_in) {
            $(target_field).off();
            $(target_field).on("keydown", function (event) {
                event.preventDefault()
                if (event.keyCode == 8) {
                    $(buffer_in).val('')
                    $(target_field).val('')
                    return null
                }
                if (isNaN(event.key) ||
                    $.inArray(event.keyCode, [13, 38, 40, 37, 39]) !== -1 ||
                    event.keyCode == 13) {
                    if ($(buffer_in).val() != '') {
                        $(target_field).val(atm_money(parseInt($(buffer_in).val())))
                    } else {
                        $(target_field).val('')
                    }
                    return null;
                }

                const input = event.key;
                old_val = $(buffer_in).val()


                if (old_val === '0.00') {
                    $(buffer_in).val('')
                    $(target_field).val('')
                    old_val = ''
                }


                $(buffer_in).val('' + old_val + input)
                $(target_field).val(atm_money(parseInt($(buffer_in).val())))

            });

            $(target_field).focusout(function (event) {
                valdate = $(buffer_in).val();
                console.log("Validate_result", valdate);
                if( valdate == '' ){
                    messageModal("Price out of range");
                }
            });
        }

        // $("#retail_price_normal_fk").on("keyup keypress", function (evt) {
        //     let old_value = "";
        //     let type_evt_not_use = ["0", "1", "2", "3", "4", "5", "6", "7", "8", "9"];

        //     if (evt.type === "keypress") {
        //         let value = $("#retail_price_normal_fk").val();
        //         console.log("value", value);
        //         old_value = parseInt(value.replace('.', ''));
        //         $("#retail_price_normal").val(old_value == '' ? 0 : old_value);
        //     } else {
        //         if (evt.key === "Backspace") {
        //             let value = $("#retail_price_normal_fk").val();
        //             console.log("value", value);
        //             old_value = parseInt(value.replace('.', ''));
        //             $("#retail_price_normal").val(old_value);
        //         }

        //         let use_key = "";
        //         if (type_evt_not_use.includes(evt.key)) {
        //             use_key = evt.key;
        //             console.log(evt.key);
        //         }

        //         old_value = parseInt((isNaN($("#retail_price_normal").val()) == false ? $("#retail_price_normal").val() : 0) + "" + use_key);
        //         let nan = isNaN(old_value);
        //         console.log("up", old_value);

        //         if (old_value !== "" && nan == false) {
        //             $("#retail_price_normal_fk").val(atm_money(parseInt(old_value)));
        //             // $("#retail_price_normal_fk_text").text(atm_money(parseInt(old_value)));
        //             $("#retail_price_normal").val(parseInt(old_value));
        //         } else {
        //             $("#retail_price_normal_fk").val("0.00");
        //             // $("#retail_price_normal_fk_text").text("0.00");
        //             $("#retail_price_normal").val(0);
        //         }
        //     }
        // });


        $('#normalRateModal').on('hidden.bs.modal', function (e) {

            let value = $("#retail_price_normal").val();
            let element = $("#element_price").val();

            if( value == '' ){
                return;
            }

            $.ajax({
                method: "post",
                url: "{{route('car_park.updateValue')}}",
                data: {key: key, value: value, element: element}
            })
                .done((data) => {
                    console.log(data);
                    if (data.other == "default_rate" || data.other == "default_kwh" ) {
                        loadDefaultRate();
                    } else {
                        table.ajax.reload();
                    }

                })
                .fail((data) => {
                    console.log("data", data)
                });
        });

    </script>
@endsection
@include('common.footer')
