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

.paging_full_numbers a.paginate_button {
    color: #fff !important;
}

.paging_full_numbers a.paginate_active {
    color: #fff !important;
}

table.dataTable th.dt-right,
table.dataTable td.dt-right {
    text-align: right !important;
}

td {
    vertical-align: middle !important;
    text-align: center;
}


table.dataTable.display tbody tr.odd>.sorting_1 {
    background-color: unset !important;
}

tr:hover,
tr:hover>.sorting_1 {
    background: none !important;
}

table.dataTable.display tbody tr.odd>.sorting_1,
table.dataTable.order-column.stripe tbody tr.odd>.sorting_1 {
    background: none !important;
}
table, th, td {
  border: 0.3px solid rgb(243, 237, 237) !important;
  border-collapse: collapse !important;
}
button:focus{
    outline: 0;
}
button:hover{
     background: #1c9939 !important;
     color: #fff;
}
table.dataTable.order-column tbody tr>.sorting_1,
table.dataTable.order-column tbody tr>.sorting_2,
table.dataTable.order-column tbody tr>.sorting_3,
table.dataTable.display tbody tr>.sorting_1,
table.dataTable.display tbody tr>.sorting_2,
table.dataTable.display tbody tr>.sorting_3 {
    background-color: #fff !important;
}
label {
    float: left;
}
input {
    width: 70%;
}

input.number {
	text-align: center;
	border: none;
	border: 1px solid #e2dddd;
	margin: 0px;
	width: 90px;
	border-radius: 5px;
	height: 38px;
	border-radius: 5px;
	background-color: #d4d3d36b !important;
	vertical-align: text-bottom;
}

.dt-center.vt_middle.small-pad-y {
    padding-top: 0%;
    padding-bottom: 0%;
}
table ,tr,td{
    padding:0.29% !important;
}
</style>
<?php $__env->stopSection(); ?>



<?php $__env->startSection('content'); ?>
<?php echo $__env->make('common.header', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->make('common.menubuttons', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<div id="landing-view">
    <div class="container-fluid">
		<div class="clearfix"></div>
		<div class="row"
			style="margin-bottom: 5px; margin-top:5px; height:70px; margin-left:1px; margin-right:5px;">
			<div class="col-md-4 pl-0 align-self-center" style="">
				<h2 style="margin-bottom: 0;"> Receiving Note</h2>
			</div>
             <div class="col-md-3 pl-0 align-self-center" style="">
				<p style="margin-bottom: 0; font-size:18px; font-weight:bold;"> <?php echo e($location->name); ?></p>
                <p style="margin-bottom: 0;font-size:18px;font-weight:bold;"> <?php echo e($location->systemid); ?></p>
                <p style="margin-bottom: 0;font-size:18px;font-weight:bold;"> Doc No. <?php echo e($docId); ?></p>
			</div>
            <div class="col-md-3 pl-0 align-self-center" style="">
				<p style="margin-bottom: 0;font-size:18px;font-weight:bold;"> <?php echo e($user->fullname); ?></p>
                <p style="margin-bottom: 0;font-size:18px;font-weight:bold;"> <?php echo e($user->systemid); ?></p>
                <p style="margin-bottom: 0;font-size:18px;font-weight:bold;"> <?php echo e($time); ?></p>
			</div>

			<div class="row col-md-2 text-left m-0 pr-0">
                <div style="display:inline;
                padding-left:208px;margin-bottom:20px" id="btnFetch2">
                        <button class="btn btn-success "
                        style="height:70px;width: 70px; border-radius:10px !important;"
                        id="fulltank-rl" style="color:white;" onclick="downLoadExcel()">
                        <span class="d-none spinner-border spinner-border-sm" role="status" aria-hidden="true" style="z-index:2; position: fixed; margin-top: 3px;
                        margin-left:14px"></span>
                        Excel
                    </button>
                </div>

				<?php if(!empty($invoice_no)): ?>
                    <div class="pr-0" style="justify-content:flex-end">
                        <strong style="font-size:18px;">
                            <label class="mb-0">Invoice No:&nbsp;</label>
                            <span>
                                <?php echo e($invoice_no); ?>

                            </span>
                        </strong>
                    </div>
				<?php endif; ?>
                </div>

			</div>
		</div>
         <div style="margin-top: 0;margin-right:15px; margin-left:15px; margin-top:55px;">
            <table border="0" cellpadding="0" cellspacing="0" class="table " id="rec_note_table" style="margin-top: 0px; width:100%">
                <thead class="thead-dark"  >
		            <tr id="table-th" style="border-style: none">
		                <th valign="middle">No</th>
		                <th valign="middle">Product ID</th>
		                <th valign="middle">Barcode</th>
                        <th valign="middle">Product Name</th>
		                <th valign="middle">Price</th>
		                <th valign="middle">Qty</th>
		                <th valign="middle">Cost</th>
		                <th valign="middle">Cost Value</th>
		            </tr>
                </thead>
                <tbody id="shows">
            	</tbody>
             </table>
         </div>
	</div>
<script src="<?php echo e(asset('js/number_format.js')); ?>"></script>
<script>


let loyalty = "loyalty";
let qty = "qty";

var received_products = {}

var tbl_url = "<?php echo e(route('receiving_notes.get_datatable_products')); ?>";
var tableData = {
	systemid: "<?php echo e(Request::route('id')); ?>"
};

var rec_note_table = $('#rec_note_table').DataTable({
    "processing": false,
    "serverSide": true,
    "autoWidth": false,
     "language": {
		"zeroRecords": "No data available in table",
		"info": "Showing page _PAGE_ of _PAGES_",
		"infoFiltered": ""
	},
    "ajax": {
        /* This is just a sample route */
        "url": "<?php echo e(route('receiving_notes.confirmed_datatable')); ?>",
        "type": "POST",
        data: function (d) {
            return $.extend(d, tableData);
        },
        'headers': {
            'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
        },
        error: function (xhr, error, code)
        {
            console.log(xhr);
            console.log(code);
        },
    },
    columns: [
        { data: 'DT_RowIndex', name: 'DT_RowIndex'},
        { data: 'systemid', name: 'systemid' },
        { data: 'barcode', name: 'barcode'},
        { data: 'product_name', name: 'product_name' },
        { data: 'product_price', name: 'product_price' },
        { data: 'qty', name: 'qty' },
        { data: 'cost', name: 'cost' },
        { data: 'costvalue', name: 'costvalue' },
    ],
    // "order": [0, 'desc'],
    "columnDefs": [
        {"width": "30px", "targets": [0]},
        {"width": "150px", "targets": [1, 2]},
        {"width": "auto", "targets": [3]},
        {"width": "120px", "targets": [4, 5, 6, 7]},
        {"className": "dt-left vt_middle", "targets": [3]},
        {"className": "dt-center vt_middle", "targets": [1, 2, 5, 6]},
        {"className": "dt-center vt_middle px-2", "targets": [4]},
        {"className": "dt-head-center dt-body-right", "targets": [7]},
        // {"className": "dt-center vt_middle", "targets": [5]},
    ]
});



$(document).ready(function() {
    rec_note_table.ajax.reload();
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
});

function downLoadExcel(){

    $.ajax({
		url: "<?php echo e(route('receiving.excel_download')); ?>",
		// headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
		type: 'post',
        data: {
            doc_no: '<?php echo e($docId); ?>'
        },
		xhrFields: {
            responseType: 'blob'
        },
        success: function(response){
            var blob = new Blob([response]);
            var link = document.createElement('a');
            link.href = window.URL.createObjectURL(blob);
            link.download = "ReceivingNote.xlsx";
            link.click();
        },
        error: function(blob){
            console.log(blob);
        }
    });
}

</script>

<?php $__env->stopSection(); ?>


<?php echo $__env->make('common.footer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->make('common.web', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/emmanuel/oceania/trunk/oceania/resources/views/receiving_note/cstore_receiving_note_confirmed.blade.php ENDPATH**/ ?>