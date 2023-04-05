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

.active_button:hover, .active_button:active,
.active_button_activated {
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

.calendar-days th {
	width: 14.28%;
}

.modal-inside .row {
	padding: 0px !important;
}

tr td {
	color: white;
	cursor: pointer;
}

.selected_date {
	color: #008000;
}

.date_table > tbody > tr > th {
	font-size: 22px;
	color: white;
	background-color: rgba(255, 255, 255, 0.5);
}

.date_table > tbody > tr > td {
	color: #fff;
	font-weight: 600;
	border: unset;
	font-size: 20px;
	cursor: pointer;
}

/* table.dataTable tbody td{
	border-left: 1px solid #dee2e6;
	border-right: 1px solid #dee2e6;
	border-top: none;
	border-bottom: none;
}
*/
.btn-green {
	background-color: green !important;
	color: #fff !important;
	box-shadow: none !important;
	border: 0px !important;
}

.btn-green:focus {
	background-color: green !important;
	color: #fff !important;
	box-shadow: none !important;
	border: 0px !important;
}

.bg-blue {
	background-color: #007bff;
	color: #fff;
}

.date_table1 > tbody > tr > th {
	font-size: 22px;
	color: white;
	background-color: rgba(255, 255, 255, 0.5);
}

.date_table1 > tbody > tr > td {
	color: #fff;
	font-weight: 600;
	border: unset;
	font-size: 20px;
	cursor: pointer;
}

.selected_date {
	color: #008000 !important;
	font-weight: bold !important;
}

.selected_date1 {
	color: #008000 !important;
	font-weight: bold !important;
}

#Datepick .d-table {
	display: -webkit-flex !important;
	display: -ms-flexbox !important;
	display: flex !important;
}

/*
.dataTables_filter input {
	width: 300px;
}
*/

.greenshade {
	height: 30px;
	/* For browsers that do not support gradients */
	background-color: green;
	/* Standard syntax (must be last) */
	background-image: linear-gradient(-90deg, green, white);}

.dt-button{
	display: none;
}

.bg-purplelobster{
	color:white;
	border-color:rgba(0,0,255,0.5);
	background-color:rgba(0,0,255,0.5)
}

/* for calender short day */
.shortDay ul{
	llist-style: none;
	background-color: rgba(255, 255, 255, 0.5);
	position: relative;
	left: -75px;
	width: 124%;
	height: 55px;
	line-height: 42px;
}

.shortDay ul > li{
	font-size: 22px;
	color: white;
	font-weight: 700 !important;
	/* background-color: #2b1f1f; */
	padding: 5px 24px;
	text-align: left !important;
}
.list-inline-item:not(:last-child){
	margin-right: 0 !important;
}
.modal-content{
	overflow: hidden;
}
.modal-inside .row {
	margin: 0px;
	color: #fff;
	margin-top: 15px;
	padding: 0px !important;
}
.selected-button {
	background-color: green;;
	color: #fff;
}

.selected-button:hover {
	color: #fff !important;
}

.un-selected-button {
	background-color: #007bff;
	color: #fff;
}

.un-selected-button:hover {
	background: green;;
	color: white;
}

.disabled {
	color: gray!important;
	cursor: not-allowed !important;
}
.active {
	color:darkgreen;
	font-weight:700;
}
</style>
@endsection

@include('common.menubuttons')
@section('content')
<div class="container-fluid">
	<div class="d-flex mt-0 p-0"
		 style="height: 75px;">
		<div style="padding:0; " class="align-self-center col-md-8">
			<h2 class="mb-0">Commercial Cabinet</h2>
		</div>

		<div class="col-md-4 pl-0 align-self-center">
			<div class="row mb-0" style="float:right;">
				<a href="javascript:void(0)" onclick="save()"
					class=""
					style="padding:4px; width:110px; float:right;
						margin-left:5px; text-align: center;color: black;
						text-decoration: none;
						border: 1px solid darkgray; border-radius: 5px"
					id="addDate">{{date("dMy")}}
				</a>
			</div>
		</div>
	</div>

	<div class="col-md-12 pl-0 pr-0" style="">
		<table id="comm_cabinet" class="table table-bordered">
			<thead class="thead-dark">
			<tr style="">
				<th class="text-center" style="width: 30px">No</th>
				<th class="text-center" style="width: auto">Receipt No</th>
				<th class="text-center" style="width: 180px">Receipt</th>
			</tr>
			</thead>
			<tbody id="shows">
			<tr>
				<td>1</td>
				<td><a style="text-decoration: none" href="javascript:void(0)"
					   onclick="popupCommCabinet(1)">8888888</a></td>
				<td>1</td>
			</tr>
			</tbody>
		</table>
	</div>
</div>

<div class="modal fade" id="datePickerModal" tabindex="-1"
	 role="dialog" aria-labelledby="staffNameLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered  mw-75 w-50" role="document">
		<div class="modal-content modal-inside bg-purplelobster">
			<div class="modal-body text-center" style="min-height: 485px;max-height:485px">

				<div class="row">
					<div class="col-md-2">
						<i class="prev-month fa fa-chevron-left fa-3x"
						   style="cursor:pointer;display: inline-flex;"></i>
					</div>
					<div class=" col-md-8">
						<div class="month-year text-center text-white"></div>
					</div>
					<div class="col-md-2">
						<i style="cursor:pointer"
						   class="next-month fa fa-chevron-right fa-3x"></i>
					</div>
				</div>
				<div class="row">
					<div class="shortDay">
						<ul>
							<li class="list-inline-item">S</li>
							<li class="list-inline-item">M</li>
							<li class="list-inline-item">T</li>
							<li class="list-inline-item">W</li>
							<li class="list-inline-item">T</li>
							<li class="list-inline-item">F</li>
							<li class="list-inline-item">S</li>
						</ul>
					</div>

				</div>
				<table class="table date_table">
					<tr style="display: none;">
						<th>S</th>
						<th>M</th>
						<th>T</th>
						<th>W</th>
						<th>T</th>
						<th>F</th>
						<th>S</th>
					</tr>
				</table>
			</div>

		</div>
	</div>
</div>


<div class="modal fade" id="commCabinetDetailModal" tabindex="-1" role="dialog">
	<div class="modal-dialog  modal-dialog-centered"
		 style="width: 366px; margin-top: 0!important;margin-bottom: 0!important;">

		<!-- Modal content-->
		<div class="modal-content  modal-inside detail_view">

		</div>

	</div>
</div>

@endsection
@section('script')
    <script src="{{asset('js/osmanli_calendar.js')}}?version={{date("hmis")}}"></script>

    <script>
        $.ajaxSetup({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
        });

        var tableData = {};
        var table = $('#comm_cabinet').DataTable(
		/*
		{
            "processing": false,
            "serverSide": true,
            "autoWidth": false,
            "ajax": {
                "url": "{{route('comm_cabinet.list')}}",
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
                {data: 'systemid', name: 'systemid'},
            ],
            "order": [0, 'desc'],
            "columnDefs": [
                {"width": "30px", "targets": [0]},
                {"width": "180px", "targets": [2]},
                {"width": "auto", "targets": [1]},
                {"className": "dt-center vt_middle", "targets": [1]},
                {orderable: true, targets: [-1]},
            ],
        }
		*/
		);

        let store_date = dateToYMDEmpty(new Date());
        $("#end_date").val(store_date);
        $("#start_date").val(store_date);
        var terminal_date;

        $(document).ready(function () {
            $.ajax({
                url: "{{route('sales.terminal.date')}}",
                type: "get",
                success(response) {
                    console.log(response);
                    terminal_date = new Date(response);
                    console.log("Terminal: " + terminal_date);

                }

            })
        });


        function popupCommCabinetReceipt(data) {
            $.ajax({
                method: "post",
                url: "{{route('local_cabinet.evReceiptDetail')}}",
                data: {id: data}
            }).done((data) => {
                $(".detail_view").html(data);
                $("#evReceiptDetailModal").modal('show');
            })
			.fail((data) => {
				console.log("data", data)
			});
        }
        let select_date = new Date();
        function save() {

            $("#datePickerModal").modal("show");
            //Date picker
            var start_date_dialog = osmanli_calendar;
            let date = new Date();
            //start_date_dialog.CURRENT_DATE = new Date();
            start_date_dialog.SELECT_DATE = select_date;
            start_date_dialog.MAX_DATE = date;
            start_date_dialog.MIN_DATE = terminal_date;
            start_date_dialog.DAYS_DISABLE_MIN = "ON";
            start_date_dialog.DAYS_DISABLE_MAX = "ON";

            console.log(terminal_date);

            $('.prev-month').click(function () {
                start_date_dialog.pre_month()
            });
            $('.next-month').click(function () {
                start_date_dialog.next_month()
            });

            start_date_dialog.ON_SELECT_FUNC = function () {
                var date = osmanli_calendar.SELECT_DATE;
                var start_date = dateToYMDEmpty(date);
                console.log("select",date);
                select_date  =date;
                $('#addDate').text(dateToYMDEmpty(date));
            };

            start_date_dialog.init()
        }

        $('.date_table > tbody > tr > td').click(function(e) {
            var start_date_dialog = osmanli_calendar;
            var date = start_date_dialog.SELECT_DATE;
            var target = e.target;
            $('.date_table > tbody > tr > td').removeClass('selected_date');
            $(target).addClass('selected_date');

            let day = $(target).html();

            $("#datePickerModal").modal("hide");
        });


        function dateToYMDEmpty(date) {
            var strArray = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            var d = date.getDate();
            var m = strArray[date.getMonth()];
            var y = date.getFullYear().toString().substr(-2);
            var currentHours = date.getHours();
            return '' + (d <= 9 ? '0' + d : d) + '' + m + '' + y + ' ';
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


        function updateRate(data, old_value) {
            $("#normalRateModal").modal("show");
            if (parseInt(old_value) > 0) {
                $("#retail_price_normal_fk").val(atm_money(old_value));
            } else {
                $("#retail_price_normal_fk").val('');
            }
            $("#retail_price_normal").val(old_value);
            $("#element_price").attr("value", data);
        }


        $("#retail_price_normal_fk").on("keyup keypress", function (evt) {
            let old_value = "";
            let type_evt_not_use = ["0", "1", "2", "3", "4", "5", "6", "7", "8", "9"];

            if (evt.type === "keypress") {
                let value = $("#retail_price_normal_fk").val();
                console.log("value", value);
                old_value = parseInt(value.replace('.', ''));
                $("#retail_price_normal").val(old_value);
                console.log("old_value", old_value);
            } else {
                if (evt.key === "Backspace") {
                    let value = $("#retail_price_normal_fk").val();
                    console.log("value", value);
                    old_value = parseInt(value.replace('.', ''));
                    $("#retail_price_normal").val(old_value);
                    console.log("old_value", old_value);
                }

                let use_key = "";
                if (type_evt_not_use.includes(evt.key)) {
                    use_key = evt.key;
                }

                old_value = parseInt($("#retail_price_normal").val() + "" + use_key);
                let nan = isNaN(old_value);
                console.log("up", nan);

                if (old_value !== "" && nan == false) {
                    $("#retail_price_normal_fk").val(atm_money(parseInt(old_value)));
                    $("#retail_price_normal").val(parseInt(old_value));
                } else {
                    $("#retail_price_normal_fk").val("0.00");
                    $("#retail_price_normal").val(0);
                }
            }
        });


        $('#normalRateModal').on('hidden.bs.modal', function (e) {
            let key = "rate";
            let value = $("#retail_price_normal").val();
            let element = $("#element_price").val();

            $.ajax({
                method: "post",
                url: "{{route('car_park.updateValue')}}",
                data: {key: key, value: value, element: element}
            })
                .done((data) => {
                    table.ajax.reload();
                })
                .fail((data) => {
                    console.log("data", data)
                });
        });

        function popupCommCabinet(data) {
            $.ajax({
                method: "post",
                url: "{{route('comm_cabinet.commCabinetDetail')}}",
                data: {id: data}
            }).done((data) => {
                $(".detail_view").html(data);
                $("#commCabinetDetailModal").modal('show');
            })
			.fail((data) => {
				console.log("data", data)
			});
        }

    </script>
@endsection
@include('common.footer')
