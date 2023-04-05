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
td{
    border:1px solid rgb(226, 223, 223) !important;
}

.active_button:hover,
.active_button:active,
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

.typewriter {
    text-align: right;
    border-radius: 3px;
    overflow: hidden;
    white-space: nowrap;
    margin: 0 auto;
    letter-spacing: .0em;
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
ion-icon{
    cursor:pointer;
}
a{
    text-decoration: none !important;
}
</style>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
<?php echo $__env->make('common.header', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->make('common.menubuttons', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<div id="landing-view">
<div class="container-fluid">
	<div class="clearfix"></div>
	<div class="d-flex"
		style="width:100%;margin-bottom: 5px; margin-top:5px">
		<div class="col-md-4 pl-0 align-self-center" style="">
			<h2 style="margin-bottom: 0;"> Audited Report</h2>
		</div>
		<div class="col-md-3 pl-0 align-self-center" style="">
			<h5 style="margin-bottom: 0;"> <?php echo e($location->name); ?></h5>
			<h5 style="margin-bottom: 0;"> <?php echo e($location->systemid); ?></h5>

		</div>
		<div class="col-md-3 pl-0 align-self-center" style="">
			<h5 style="margin-bottom: 0;"> <?php echo e($user->fullname); ?></h5>
			<h5 style="margin-bottom: 0;"> <?php echo e($user->systemid); ?></h5>

		</div>

			<div class="col-md-2 d-flex pr-0"
				style="justify-content:flex-end">
				<button class="btn btn-success sellerbutton mr-0 btn-sq-lg bg-confirm-button"
				onclick="update_quantity()" id="confirm_update"
				style="margin-bottom:0 !important;border-radius:10px; font-size:14px;">Confirm
			</button>


		</div>
	</div>
	<div class="mb-3">
		<table border="0" cellpadding="0" cellspacing="0" class="table "
			id="auditedReportList" style="margin-top: 0px; width:100%">
			<thead class="thead-dark"  >
                <tr id="table-th" style="border-style: none">
                    <th valign="middle">No</th>
                    <th valign="middle">Product&nbsp;ID</th>
                    <th valign="middle">Barcode</th>
                    <th valign="middle">Product&nbsp;Name</th>
                    <th valign="middle">Qty</th>
                    <th valign="middle">Audited Qty</th>
                    <th valign="middle">Difference</th>
                </tr>
            </thead>
             <tbody id="show">
             </tbody>
		</table>
	</div>
</div>
</div>

<?php $__env->startSection('script'); ?>

<script>
    var tablereport ={};
	var isConfirmEnabled = 0;
    var qtyincr = 0;
    detect_button();
    var container = [];
    var stockin_openitems = [];
    var stockout_openitems = [];
    var stockin_inventory = [];
    var stockout_inventory = [];

	var tableData = {};
	var tablereport= $('#auditedReportList').DataTable({
		"processing": true,
		"serverSide": true,
		"autoWidth": false,
        "lengthMenu": [ [10, 25, 50, 100, -1], [10, 25, 50, 100, "All"] ],
		"ajax": {
			/* This is just a sample route */
			"url": "<?php echo e(route('cstore.listAuditedRpt')); ?>",
			"type": "POST",
             cache: false,
			data: function (d) {
                d.search = $('input[type=search]').val();
				return $.extend(d, tableData);
			},
			'headers': {
				'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
			},
		},
		columns: [
			{data: 'DT_RowIndex'},
			{data: 'product_systemid', name: 'product_systemid'},
			{data: 'barcode', name: 'barcode'},
			{data: 'product_name', name: 'product_name'},
			{data: 'product_qty', name: 'product_qty'},
			{data: 'audited_qty', name: 'audited_qty'},
            {data: 'difference', name: 'difference'

            },
		],
		"columnDefs": [
			{"width": "30px", "targets": [0]},
			{"width": "160px", "targets": [1]},
			{"width": "140px", "targets": [2]},
			{"width": "60px", "targets": [4]},
			{"width": "100px", "targets": [5]},
			{"width": "50px", "targets": [6]},
			{"className": "dt-center vt_middle", "targets": [2]},
			{"className": "dt-left vt_middle", "targets": [3]},
			{"className": "dt-right vt_middle slim-cell", "targets": [5]},
			{"className": "dt-center vt_middle slim-cell", "targets": [0, 1, 4]},
			{"className": "vt_middle slim-cell", "targets": [2]},
			//{"className": "vt_middle slim-cell", "targets": [6]},
			{orderable: false, targets: [-1]},
             {
                "searchable": true,
                "caseInsensitive": false,
            }
		],
	});

    function increaseValue(id) {
        var num_element = document.getElementById('number_'+id);
        var value = parseFloat(num_element.value);
        value = isNaN(value) ? 0 : value;
        value++;
        num_element.value = value;
        let qty = $('#qty_'+id).text()
        let div = $('#diff_'+id);

        div.text(num_element.value - qty)

        qtyincr++;
        isConfirmEnabled--;
        detect_button()
        let datainfo = $('#qty_'+id).attr('data-field');

        if(div.text() > 0){
            if(datainfo == 'openitem'){
                stockin_openitems = stockin_openitems.filter(function(me) {
                    if(me.product_id != id) {
                        return me;
                    }
                });
                stockin_openitems.push({
                    'product_id':id,
                    'qty':parseInt(div.text()),

                })
                // console.log("stockin_openitems",stockin_openitems)
            }
            if(datainfo == 'inventory'){
                stockin_inventory = stockin_inventory.filter(function(me) {
                    if(me.product_id != id) {
                        return me;
                    }
                });
                stockin_inventory.push({
                    'product_id':id,
                    'qty':parseInt(div.text()),

                })
                // console.log("stockin_inventory",stockin_inventory)
            }
        }

        if(div.text() <= 0){
            if(datainfo == 'openitem'){
                stockout_openitems = stockout_openitems.filter(function(me) {
                    if(me.product_id != id) {
                        return me;
                    }
                });
                stockout_openitems.push({
                    'product_id':id,
                    'qty':parseInt(div.text())==0 ? 0 :
                        parseInt(div.text())==0 ? 0 : parseInt(div.text()) * -1,

                })
                // console.log("stockout_openitems",stockout_openitems)
            }
            if(datainfo == 'inventory'){
                stockout_inventory = stockout_inventory.filter(function(me) {
                    if(me.product_id != id) {
                        return me;
                    }
                });
                stockout_inventory.push({
                    'product_id':id,
                    'qty':  parseInt(div.text())==0 ? 0 :
                        parseInt(div.text())==0 ? 0 : parseInt(div.text()) * -1,

                })
                // console.log("stockout_inventory",stockout_inventory)

            }
        }
        container = container.filter(function(me) {
            if(me.id != id) {
                return me;
            }
        });
        container.push({
            'id':id,
            'audited_qty':parseInt(num_element.value),
            'diff':parseInt(div.text()),
            'qty':parseInt(qty)
        })
        tableData= {
            'container':container,
        }

        let stockop =  findCommonElement(stockout_openitems,stockin_openitems,1);

        let stockin = findCommonElement(stockout_inventory,stockin_inventory,1);

        stockin_openitems = stockop[1];
        stockout_openitems = stockop[0];
        stockin_inventory = stockin[1];
        stockout_inventory = stockin[0];

        console.log("stockIN-openitems",stockin_openitems);
        console.log("stockOUT-openitems",stockout_openitems);
    }


    function decreaseValue(id) {
        var num_element = document.getElementById('number_'+id);
        var value = parseFloat(num_element.value);
        value = isNaN(value) ? 0 : value;
        value < 1 ? value = 0 : value--;

        num_element.value = value;
        let qty = $('#qty_'+id).text()
        $('#diff_'+id).html(num_element.value - qty)
         qtyincr++;
         isConfirmEnabled--;
        detect_button()
        let datainfo = $('#qty_'+id).attr('data-field');
        let div =  $('#diff_'+id);

        if(div.text() > 0){
            if(datainfo == 'openitem'){
                stockin_openitems = stockin_openitems.filter(function(me) {
                    if(me.product_id != id) {
                        return me;
                    }
                });
                stockin_openitems.push({
                    'product_id':id,
                    'qty':parseInt(div.text()),

                })
                // console.log("stockin_openitems",stockin_openitems)
            }
            if(datainfo == 'inventory'){
                stockin_inventory = stockin_inventory.filter(function(me) {
                    if(me.product_id != id) {
                        return me;
                    }
                });
                stockin_inventory.push({
                    'product_id':id,
                    'qty':parseInt(div.text()),

                })
                // console.log("stockin_inventory",stockin_inventory)
            }
        }

        if(div.text() <= 0){

            if(datainfo == 'openitem'){
                stockout_openitems = stockout_openitems.filter(function(me) {
                    if(me.product_id != id) {
                        return me;
                    }
                });
                stockout_openitems.push({
                    'product_id':id,
                    'qty':parseInt(div.text())==0 ? 0 : parseInt(div.text()) * -1,

                })
                // console.log("stockout_openitems",stockout_openitems)
            }
            if(datainfo == 'inventory'){
                stockout_inventory = stockout_inventory.filter(function(me) {
                    if(me.product_id != id) {
                        return me;
                    }
                });
                stockout_inventory.push({
                    'product_id':id,
                    'qty':parseInt(div.text())==0 ? 0 : parseInt(div.text()) * -1,

                })
                // console.log("stockout_inventory",stockout_inventory)

            }
        }

        let stockop =  findCommonElement(stockin_openitems,stockout_openitems,1);
        let stockin = findCommonElement(stockin_inventory,stockout_inventory,1);

        stockin_openitems = stockop[0];
        stockout_openitems = stockop[1];
        stockin_inventory = stockin[0];
        stockout_inventory = stockin[1];

        container = container.filter(function(me) {
            if(me.id != id) {
                return me;
            }
        });
        container.push({
            'id':id,
            'audited_qty':parseInt(num_element.value),
            'diff':parseInt(div.text()),
            'qty':parseInt(qty)
        })
        tableData= {
            'container':container,
        }

    }

    function changeValueOnBlur(id) {
        x = 0;

        isConfirmEnabled = x;

        num_element = $('#number_'+id).val();
        div = $('#diff_'+id);
        qty = $('#qty_'+id).text();
        div.text(num_element - qty)
        let datainfo = $('#qty_'+id).attr('data-field');
        if(div.text() > 0){
            if(datainfo == 'openitem'){
                stockin_openitems = stockin_openitems.filter(function(me) {
                    if(me.product_id != id) {
                        return me;
                    }
                });
                stockin_openitems.push({
                    'product_id':id,
                    'qty':parseInt(div.text()),

                })
                console.log("stockin_openitems",stockin_openitems)
            }

            if(datainfo == 'inventory'){
                stockin_inventory = stockin_inventory.filter(function(me) {
                    if(me.product_id != id) {
                        return me;
                    }
                });
                stockin_inventory.push({
                    'product_id':id,
                    'qty':parseInt(div.text()),

                })
                console.log("stockin_inventory",stockin_inventory)
            }
        }
        if(div.text() <= 0){
            console.log(div.text())

            if(datainfo == 'openitem'){
                stockout_openitems = stockout_openitems.filter(function(me) {
                    if(me.product_id != id) {
                        return me;
                    }
                });
                stockout_openitems.push({
                    'product_id':id,
                    'qty':parseInt(div.text())==0 ? 0 : parseInt(div.text()) * -1,

                })
                console.log("stockout_openitems",stockout_openitems)
            }
            if(datainfo == 'inventory'){
                stockout_inventory = stockout_inventory.filter(function(me) {
                    if(me.product_id != id) {
                        return me;
                    }
                });
                stockout_inventory.push({
                    'product_id':id,
                    'qty':parseInt(div.text())==0 ? 0 : parseInt(div.text()) * -1,

                })
                console.log("stockout_inventory",stockout_inventory)

            }
        }
        container = container.filter(function(me) {
            if(me.id != id) {
                return me;
            }
        });
        container.push({
            'id':id,
            'audited_qty':parseInt(num_element.value),
            'diff':parseInt(div.text()),
            'qty':parseInt(qty)
        })


    }


    async function update_quantity() {

        if (container.length < 1)
            return;

        await  $.ajax({
            url: "<?php echo e(route('update_audited_notes')); ?>",
            type: "POST",
            'headers': {
                    'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
                },
            data: {container:container},
            cache: false,
                success: function(dataResult){
            }
        });

        if (stockin_inventory.length > 0) {
            await  $.ajax({
                url: "<?php echo e(route('franchise.location_price.stockUpdate')); ?>",
                type: "POST",
                'headers': {
                        'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
                    },
                data: {table_data : stockin_inventory, stock_type:"IN"},
                cache: false,
                success: function(dataResult){
                }
            });
        }

        if (stockout_inventory.length > 0) {
            await $.ajax({
                url: "<?php echo e(route('franchise.location_price.stockUpdate')); ?>",
                type: "POST",
                'headers': {
                        'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
                    },
                data: {table_data : stockout_inventory, stock_type:"OUT"},
                cache: false,
                    success: function(dataResult){
                }
            });
        }

        if(stockin_openitems.length > 0){
            await $.ajax({
                url: "<?php echo e(route('openitem.openitem_stockin_update')); ?>",
                type: "POST",
                'headers': {
                        'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
                    },
                data: {table_data : stockin_openitems, stock_type:"IN"},
                cache: false,
                    success: function(dataResult){
                }
            });
        }

        if(stockout_openitems.length > 0){
            await $.ajax({
                url: "<?php echo e(route('openitem.openitem_stockin_update')); ?>",
                type: "POST",
                'headers': {
                        'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
                    },
                data: {table_data : stockout_openitems, stock_type:"OUT"},
                cache: false,
                    success: function(dataResult){
                }
            });
        }

        messageModal("Audited note created successfully");
        $("#confirm_update").attr('disabled', true);
        $("#confirm_update").css('background','gray');
        $("#confirm_update").css('cursor','not-allowed');
        tablereport.ajax.reload();
        container = [];
        tableData ={}
        // location.reload();
    }

    function findCommonElement(arr1, arr2,poper) {

        for(let i = 0; i < arr1.length; i++) {

            for(let j = 0; j < arr2.length; j++) {

                if(arr1[i].product_id === arr2[j].product_id) {

                    // Return if common element found
                    if(poper ==1){
                        arr1.splice(i, 1);
                    }
                    if(poper ==2){
                        arr2.splice(j, 1);
                    }

                }
            }
        }
        return [arr1,arr2];

    }

    function detect_button(){

        if (qtyincr > 0 && isConfirmEnabled !=1) {
            $("#confirm_update").removeAttr('disabled');
            $("#confirm_update").css('background','linear-gradient(#0447af,#3682f8)');
            $("#confirm_update").css('cursor','pointer');
        } else {
            $("#confirm_update").attr('disabled', true);
            $("#confirm_update").css('background','gray');
            $("#confirm_update").css('cursor','not-allowed');
        }
    }


    function messageModal(msg){
        $('#modalMessage').modal('show');
        $('#statusModalLabelMsg').html(msg);
        setTimeout(function(){
            $('#modalMessage').modal('hide');
        }, 2500);
    }

</script>
<?php $__env->stopSection(); ?>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('common.footer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->make('common.web', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/emmanuel/oceania/trunk/oceania/resources/views/cstore_audited_rpt/cstore_audited_report.blade.php ENDPATH**/ ?>