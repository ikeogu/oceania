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

#receipt-table_length,
#receipt-table_filter,
#receipt-table_info,
.paginate_button {
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

table.dataTable th.dt-right,
table.dataTable td.dt-right {
    text-align: right !important;
}

td {
    vertical-align: middle !important;
    text-align: center;
}

.bg-fuel-refund {
    color: white !important;
    border-color: #ff7e30 !important;
    background-color: #ff7e30 !important;
}
.boxhead a:hover {
    text-decoration: none;
}

.bg-total{
    background-color: rgb(255,126,48) !important;

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

*:focus{
    outline:  none !important;
    margin-bottom: 0 !important;
}

button:hover{
	background: #19b83e !important;
	table.dataTable.order-column tbody tr>.sorting_1,
	table.dataTable.order-column tbody tr>.sorting_2,
	table.dataTable.order-column tbody tr>.sorting_3,
	table.dataTable.display tbody tr>.sorting_1,
	table.dataTable.display tbody tr>.sorting_2,
	table.dataTable.display tbody tr>.sorting_3 {
		background-color: #fff !important;
	}
}
}

</style>
<?php $__env->stopSection(); ?>


<?php $__env->startSection('content'); ?>
<?php echo $__env->make('common.header', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->make('common.menubuttons', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<div id="landing-view">
    <div class="container-fluid">
		<div class="clearfix"></div>
		<div class="d-flex mt-0 p-0"
			style="width:100%;margin-top:5px !important;margin-bottom:5px !important" >
			<div class="col pl-0 align-self-center" style="width:70%">
				<h2 style="margin-bottom: 0;">C-Store Audited Report List</h2>
			</div>
			<!--
				"btn sellerbutton btn-success bg-confirm-button mr-0"
			-->

			<div class="col-md-2 text-right pr-0 mb-0"
				style="">
				<button style="margin-bottom:0 !important"
					class="btn btn-success sellerbutton-wide btn-sq-lg
					screend-button" onclick="window.open('<?php echo e(route('audited_notes')); ?>','_blank')">

				<i
				style="position:relative;color:#fff; font-size:25px;"
				class="fa fa-plus fa-5x"  aria-hidden="true">
			    </i>
				</button>

			</div>
		</div>
        <div style="margin-top: 0;">
		<table border="0" cellpadding="0" cellspacing="0" class="table"
		 	id="auditedReportList" style="margin-top:0px; width:100%">
        <thead class="thead-dark"  >
        <tr id="table-th" style="border-style: none">
            <th valign="middle" class="text-center" style="width:30px;">No</th>
            <th valign="middle" class="text-left" style="">Document No</th>
            <th valign="middle" class="text-center" style="width:200px;"> Date</th>

        </tr>
        </thead>
        <tbody id="shows">

        </tbody>
    </table>
    </div>
</div>

<script>

    var tableData = {};
    let auditedReportList =  $('#auditedReportList').DataTable({
           "processing": true,
		   "serverSide": true,
            "ajax": {
			/* This is just a sample route */
                "url": "<?php echo e(route('cstore.listPrdAuditedRpt')); ?>",
                "type": "GET",
                cache: false,
                data: function (d) {
                    d.search = $('input[type=search]').val();
                    return $.extend(d, tableData);
                },
                'headers': {
                    'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
                },
            },
            columns: [{
                    "data": 'DT_RowIndex',
                    orderable: false,
                    searchable: false
            },{
                data: 'auditedreport_systemid',
                name: 'auditedreport_systemid',
                render: function (data) {
                    return "<a href='/audited_note_report/" + data +
                    "' target='_blank' style='text-decoration: none;'>" +
                    data + "</a>"
                }
            },{
                data: 'created_at',
                name: 'created_at',

            }],
            "columnDefs": [{
                "className": "dt-left vt_middle", "targets": [1]
            }]
        });

    $.ajaxSetup({
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
    });

    $(document).ready(function() {
		var hidden, visibilityState, visibilityChange;

		if (typeof document.hidden !== "undefined") {
			hidden = "hidden";
			visibilityChange = "visibilitychange";
			ovisibilityState = "visibilityState";

		} else if (typeof document.msHidden !== "undefined") {
			hidden = "msHidden";
			visibilityChange = "msvisibilitychange";
			visibilityState = "msVisibilityState";
		}

		var document_hidden = document[hidden];

		document.addEventListener(visibilityChange, function() {
			if(document_hidden != document[hidden]) {
				if(document[hidden]) {

				} else {
					auditedReportList.ajax.reload();
				}

				document_hidden = document[hidden];
			}
		});
	});
</script>
<?php $__env->stopSection(); ?>



<?php echo $__env->make('common.footer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->make('common.web', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/emmanuel/oceania/trunk/oceania/resources/views/cstore_audited_rpt/cstore_audited_report_list.blade.php ENDPATH**/ ?>