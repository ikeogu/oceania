<?php $__env->startSection('styles'); ?>

<script type="text/javascript" src="<?php echo e(asset('js/console_logging.js')); ?>"></script>
<script type="text/javascript" src="<?php echo e(asset('js/qz-tray.js')); ?>"></script>
<script type="text/javascript" src="<?php echo e(asset('js/opossum_qz.js')); ?>"></script>

<style>
.dataTables_wrapper .dataTables_length,
.dataTables_wrapper .dataTables_filter,
.dataTables_wrapper .dataTables_info,
.dataTables_wrapper .dataTables_processing,
.dataTables_wrapper .dataTables_paginate {
	color: black !important;
	font-weight: normal !important;
}

#receipt-table_length, #receipt-table_filter,
#receipt-table_info, .paginate_button {
	color: white !important;
}

#eodSummaryListModal-table_paginate,
#eodSummaryListModal-table_previous,
#eodSummaryListModal-table_next,
#eodSummaryListModal-table_length,
#eodSummaryListModal-table_filter,
#eodSummaryListModal-table_info {
	color: white !important;
}

.paging_full_numbers a.paginate_button {
	color: #fff !important;
}

.paging_full_numbers a.paginate_active {
	color: #fff !important;
}

table.dataTable th.dt-right, table.dataTable td.dt-right {
	text-align: right !important;
}

td {
	vertical-align: middle !important;
	text-align: center !important;
}
tr:hover,  tr:hover > .sorting_1{
	background: none !important;
}

table.dataTable.display tbody tr.odd > .sorting_1, table.dataTable.order-column.stripe tbody tr.odd > .sorting_1 {
    background: none !important;
}

table.dataTable.order-column tbody tr > .sorting_1, table.dataTable.order-column tbody tr > .sorting_2, table.dataTable.order-column tbody tr > .sorting_3, table.dataTable.display tbody tr > .sorting_1, table.dataTable.display tbody tr > .sorting_2, table.dataTable.display tbody tr > .sorting_3 {
    background-color: #fff !important;
}
/** */
.receipt-item-l {
    text-align: left;
    padding-right: 0;
    font-size: 12px;
}

.receipt-item-c {
    padding-right: 0;
    padding-left: 0;
    font-size: 12px;
}

.receipt-item-discount {
    text-align: center;
    padding-right: 0;
    padding-left: 20px;
    font-size: 12px;
}

.receipt-item-r {
    text-align: right;
    padding-left: 0;
    font-size: 12px;
}

.void-stamp {
    font-size: 100px;
    color: red;
    position: absolute;
    z-index: 2;
    font-weight: 500;
    /* margin-top:50%; */
    margin-left: 10%;
    transform: rotate(45deg);

}

.col-pd {
    padding-left: 0 !important;
    padding-right: 0 !important;
}

.keyin_modal_input {
    border: 1px solid white;
    border-radius: 3px;
    width: 80%;
    text-align: right;
    padding-right: 3;
    margin-bottom: 5px;
	outline: none;
}
.spinner-button i {
    color: #fff
}

.spinner-button:hover i {
    color: #fff
}

.fa{
    color:#fff;
}

.fa:hover{
    color:#fff;
}

</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<?php echo $__env->make('common.header', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->make('common.menubuttons', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<div class="modal fade" id="nShiftModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered mw-75" style="width:370px;" role="document">
        <div id="eodSummaryListModal-table-div" class="modal-content bg-purplelobster" style="width:370px; border-radius:10px;">
        <!--Modal EoD Summary-->

            <!--Modal Body Starts-->
            <div class="modal-body" style="font-size: 14px; font-weight: bold;">
                <!--Section 1 starts-->
                <!-- <div class="row">
                    <div class="col-md-7 pr-0">
                        <strong>Shift Summary</strong>
                    </div>
                    <div class="col-md-5 pl-1 text-right">
                        <strong>
                            16Jun22 23:59:59
                        </strong>
                    </div>
                </div> -->

                <!-- <hr style="border: 0.5px solid #a0a0a0;
                    margin-bottom:5px !important;
                    margin-top:5px !important"> -->

                <div class="row">
                    <div class="col-md-12">
                        <h3>Key In</h3>
                    </div>
                </div>

                <hr style="border: 0.5px solid #ffffff;
                    margin-bottom:5px !important;
                    margin-top:5px !important">

                <!--
                <hr style="border: 0.5px solid #a0a0a0;
                    margin-bottom:5px !important;
                    margin-top:5px !important"/>
                -->
				<input type="hidden" id="nshift_id" />
                <div class="row">
                    <div class="col-md-8" style="font-weight: normal">
                        Non-Operational Cash In
                    </div>


                    <div class="col-md-4" style="text-align: right;">
                        <input class="keyin_modal_input" id="cash_in" type="text" value="0.00" />
						<input class="keyin_modal_input" id="cash_in_buffer" type="hidden" value="0.00" />
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-8" style="font-weight: normal;">
                        (-) Non-Operational Cash Out
                    </div>
                    <div class="col-md-4" style="text-align: right;">
                        <input class="keyin_modal_input" id="cash_out" type="text" value="0.00" />
						<input class="keyin_modal_input" id="cash_out_buffer" type="hidden" value="0.00" />
                    </div>
                </div>

                <div class="row" style="font-weight: normal">
                    <div class="col-md-8">
                        (-) Sales Drop
                    </div>
                    <div class="col-md-4" style="text-align: right;">
                        <input class="keyin_modal_input" id="sales_drop" type="text" value="0.00" />
						<input class="keyin_modal_input" id="sales_drop_buffer" type="hidden" value="0.00" />
                    </div>
                </div>

                <div class="row" style="font-weight: normal">
                    <div class="col-md-8">
                        Actual Drawer Amount
                    </div>
                    <div class="col-md-4" style="text-align: right;">
                        <input class="keyin_modal_input" id="drawer_amount" type="text" value="0.00" />
						<input class="keyin_modal_input" id="drawer_amount_buffer" type="hidden" value="0.00" />
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>


<div id="loadOverlay" style="background-color: white; position:absolute; top:0px; left:0px;
	width:100%; height:100%; z-index:2000;">
</div>
<div id="landing-view">
	<!--div id="landing-content" style="width: 100%"-->
	<div class="container-fluid">
		<div class="clearfix"></div>
		<div class="row py-2 align-items-center"
			style="display:flex;height:75px;margin-bottom:0;
			padding-top:5px !important; padding-bottom:5px !important">
			<div class="col" style="width:70%">
				<h2 style="margin-bottom: 0;">
				Shift
				</h2>
			</div>
			<div class="col-md-2">
				<h5 style="margin-bottom:0"><?php echo e($location->name??""); ?></h5>
				<h5 style="margin-bottom:0"><?php echo e($location->systemid??""); ?></h5>
			</div>
			<div class="middle;col-md-3">
				<h5 style="margin-bottom:0;">Terminal ID: <?php echo e($terminal->systemid??""); ?></h5>

			</div>

			<div class="col-md-2 text-right"
				style="padding-bottom: 0;">
				<form action="<?php echo e(route('export_excel_nshift')); ?>" method="post">
					<?php echo csrf_field(); ?>
					<input type="hidden" name="date" value="<?php echo e($date); ?>" />

				</form>
			</div>
			<!-- <div class="col-md-2 text-right">
				<h5 style="margin-bottom:0;"></h5>

			</div> -->
		</div>

		<div id="response"></div>
		<div id="responseeod"></div>
		<table class="table table-bordered display"
			   id="nshiftlist" style="width:100%;">
			<thead class="thead-dark">
			<tr>
				<th class="text-center" style="width:30px;">No</th>
				<th class="text-center" style="width:200px">In</th>
				<th class="text-center" style="width:200px;">Out</th>
				<th class="text-center" style="width:120px;">Staff ID</th>
				<th class="text-left" style="width:auto">Staff Name</th>
				<th class="text-center" style="width:50px">PDF</th>
				<th class="text-center" style="width:50px">Key In</th>
			</tr>
			</thead>
			<tbody>

			</tbody>
		</table>
	</div>
</div>

<div id="res"></div>



<style>
.btn {
	color: #fff !Important;
}

.form-control:disabled, .form-control[readonly] {
	background-color: #e9ecef !important;
	opacity: 1;
}

#void_stamp {
	font-size: 100px;
	color: red;
	position: absolute;
	z-index: 2;
	font-weight: 500;
	margin-top: 130px;
	margin-left: 10%;
	transform: rotate(45deg);
	display: none;
}
/* #loadOverlay{display: none;} */
</style>
<?php $__env->startSection('script'); ?>
<script>
$.ajaxSetup({
	headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
});

    let old_value = ""; // for keyin modal
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

function filter_price(target_field, buffer_in) {
    $(target_field).off();
    $(target_field).on("keydown", function(event) {
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

        if(old_val.length >= 10)
        {
            console.log(old_val);
            $(buffer_in).val(old_val.splice(0, -1));
            console.log('fresh', $(buffer_in).val());
        }else {

            if (old_val === '0.00') {
                $(buffer_in).val('')
                $(target_field).val('')
                old_val = ''
            }
            $(buffer_in).val('' + old_val + input)
            $(target_field).val(atm_money(parseInt($(buffer_in).val())))
        }
    });
}

function format_key_input(id, evt)
{

    let type_evt_not_use = ["0", "1", "2", "3", "4", "5", "6", "7", "8", "9"];

    if (evt.type === "keypress") {
        let value = $(id).val();
        old_value = parseInt(value.replace('.', ''));
        $(id).val(old_value == '' ? 0 : old_value);
    }
	else {
        if (evt.key === "Backspace") {
            let value = $(id).val();
            old_value = parseInt(value.replace('.', ''));
            $(id).val(old_value);
        }

		old_value = parseInt((isNaN($(id).val()) == false ? $(id).val() : 0));
        let nan = isNaN(old_value);

        if (old_value !== "" && nan == false) {
			console.log("up-value", old_value);
			if(num.length <= 11) {
				$(id).val(atm_money(parseInt(old_value)));
			}

        } else {
            $(id).val("0.00");
        }
    }

}

filter_price(`#cash_in`,`#cash_in_buffer`);
$("#cash_in").on("blur", function(evt) {
	let v = $("#cash_in").val();
	if (v == "" || v == '') $("#cash_in").val('0.00')
})

filter_price(`#cash_out`,`#cash_out_buffer`);
$("#cash_out").on("keyup keypress", function (evt) {
	let v = $("#cash_out").val();
	if (v == "" || v == '') $("#cash_out").val('0.00')
});

filter_price(`#sales_drop`,`#sales_drop_buffer`);
$("#sales_drop").on("keyup keypress", function (evt) {
	let v = $("#sales_drop").val();
	if (v == "" || v == '') $("#sales_drop").val('0.00')
});

filter_price(`#drawer_amount`,`#drawer_amount_buffer`);
$("#drawer_amount").on("keyup keypress", function (evt) {
	let v = $("#drawer_amount").val();
	if (v == "" || v == '') $("#drawer_amount").val('0.00')
});


function getOptlist(id) {
	$.ajax({
		method: "post",
		url: "<?php echo e(route('local_cabinet.optList')); ?>",
		data: {id: id}
	}).done((data) => {
		$("#optlistModal-table").html(data);
		$("#optlistModal").modal('show');
	})
	.fail((data) => {
		console.log("data", data)
	});
}

function openShiftModal(id)
{


	var url = "<?php echo e(route('local_cabinet.nshift.details')); ?>";
	$.ajax({
		method: "post",
		url: url,
		data: {nshift_id: id}
	}).done((data) => {

		$("#nshift_id").val(data.id);
		$("#cash_in").val((data.cash_in == null || data.cash_in == undefined) ? "0.00" : atm_money(parseInt(data.cash_in)));
		$("#cash_out").val((data.cash_out == null || data.cash_out == undefined) ? "0.00" : atm_money(parseInt(data.cash_out)));
		$("#sales_drop").val((data.sales_drop == null || data.sales_drop == undefined) ? "0.00" : atm_money(parseInt(data.sales_drop)));
		$("#drawer_amount").val((data.actual == null || data.actual == undefined) ? "0.00" : atm_money(parseInt(data.actual)));
		$("#nShiftModal").modal('show');
	})
	.fail((data) => {
		console.log("data", data)
	});
}

$('#nShiftModal').on('hidden.bs.modal', function (e) {
	var id = parseInt($("#nshift_id").val());
	var cash_in = parseFloat($("#cash_in").val()) * 100;
	var cash_out = parseFloat($("#cash_out").val()) * 100;
	var sales_drop = parseFloat($("#sales_drop").val()) * 100;
	var drawer_amount = parseFloat($("#drawer_amount").val()) * 100;

    $.ajax({
        method: "post",
        url: "<?php echo e(route('local_cabinet.nshift.update')); ?>",
        data: {
        	id: id,
        	cash_in: cash_in,
			cash_out: cash_out,
			sales_drop: sales_drop,
			drawer_amount: drawer_amount
        }
    })
    .done((data) => {
		eodTable.ajax.reload();

    })
    .fail((data) => {
        console.log("data", data)
    });

    location.reload();
    eodTable.ajax.reload();
});


function getEvReceiptlist(id) {
	$.ajax({
		method: "post",
		url: "<?php echo e(route('ev_receipt.evList')); ?>",
		data: {id: id}
	}).done((data) => {
		$("#evlistModal-table").html(data);
		$("#evlistModal").modal('show');
	})
	.fail((data) => {
		console.log("data", data)
	});
}


function print_eod() {
    $.ajax({
        url: "/eod_print",
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        type: 'post',
        data:{
            'eod_date':'16Jun22',
        },
        success: function (response) {
            var error1=false, error2=false;
            console.log('PR ',(response));

            try {
                eval(response);
                console.log('print_eod: eval() working');
            } catch (exc) {
                error1 = true;
                console.error('ERROR eval(): '+
					JSON.stringify(exc));
            }

            if (!error1) {
				try {
                    escpos_print_template();
                    console.log('print_eod: escpos_print_template() working');
                } catch (exc) {
                    error2 = true;
                    console.error('ERROR escpos_print_template(): '+
						JSON.stringify(exc));
                }
            }
        },
        error: function (e) {
            console.log('PR '+JSON.stringify(e));
        }
    });
}

$(document).ready(function() {
	$("#loadOverlay").css("display","none");
    var tbl_data = {
        'date': '<?php echo e($date); ?>',
    }
	var eodtable = $('#nshiftlist').DataTable({
		// "processing": true,
		"serverSide": true,
		async: false,
        "ajax": {
            "url": "<?php echo e(route('local_cabinet.nshift.datatable')); ?>",
            "type": "POST",
            data: function(d) {
                return $.extend(d, tbl_data);
            },
            'headers': {
                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
            }
        },
		columns: [
			{data: 'DT_RowIndex', name: 'DT_RowIndex'},
			{
				data: 'in',
				name: 'in',
				orderable: true,
				searchable: true
			},
			{
				data: 'out',
				name: 'out',
				orderable: true,
				searchable: true
			},
			{
				data: 'staff_id',
				name: 'staff_id',
				orderable: true,
				searchable: true
			},
			{
				data: 'staff_name',
				name: 'staff_name',
				orderable: true,
				searchable: true
			},
			{
				data: 'report',
				name: 'report',
                orderable: false
			},
			{
				data: 'action',
				name: 'action',
                orderable: false
			},

		],
        "columnDefs": [
		    { "className": "text-left dt-left", "targets": [4]}
        ]
	});

	// window.addEventListener('storage', (e) => {
	//     switch (e.key) {
	//         case "reload_for_lc":
	// 			eodtable.ajax.reload();
	// 			localStorage.removeItem('reload_for_lc');
	// 			console.log("lc reload.")
	//             break;
	// 	}
	// });

});
$('.sorting_1').css('background-color', 'white');



// function eod_summarylist(eod_date) {
// 	$.ajax({
// 		url: "<?php echo e(route('local_cabinet.eodsummary.popup')); ?>/" + eod_date,
// 		// headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
// 		type: 'get',
// 		success: function (response) {
// 			// console.log(response);
// 			$('#eodSummaryListModal-table-div').html(response);
// 			$('#eodSummaryListModal').modal('show').html();
// 		},
// 		error: function (e) {
// 			$('#responseeod').html(e);
// 			$("#msgModal").modal('show');
// 		}
// 	});
// }


// function receipt_list(date) {
// 	$.ajax({
// 		url: "<?php echo e(route('local_cabinet.receipt.list')); ?>",
// 		headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
// 		type: 'post',
// 		data: {
// 			date
// 		},
// 		success: function (response) {
// 			//console.log(response);
// 			$('#receiptoposModal-table').html(response);
// 			$('#receiptoposModal').modal('show').html();

// 			$('#receipt-table').DataTable({
// 				"order": [],
// 				"columnDefs": [
// 					{"targets": -1, 'orderable': false}
// 				],
// 				"autoWidth": false,
// 			})
// 		},
// 		error: function (e) {
// 			$('#responseeod').html(e);
// 			$("#msgModal").modal('show');
// 		}
// 	});
// }

 function downLoadPdf(id){
     let spinner = $('#btnFetch2').find('span')
            spinner.removeClass('d-none')


    $.ajax({
		url: "<?php echo e(route('download.nshift.pdf')); ?>",
		// headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
		type: 'post',
        data: {
        	id: id
        },
		xhrFields: {
            responseType: 'blob'
        },
        success: function(response){
            var blob = new Blob([response]);
            var link = document.createElement('a');
            link.href = window.URL.createObjectURL(blob);
            link.download = "shift.pdf";
            link.click();
            spinner.addClass('d-none')
        },
        error: function(blob){
            console.log(blob);
        }
    });
 }

</script>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('common.footer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->make('common.web', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/emmanuel/oceania/trunk/oceania/resources/views/nshift/nshift.blade.php ENDPATH**/ ?>