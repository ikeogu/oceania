<?php $__env->startSection('styles'); ?>

<script type="text/javascript" src="<?php echo e(asset('js/console_logging.js')); ?>"></script>
<script type="text/javascript" src="<?php echo e(asset('js/qz-tray.js')); ?>"></script>
<script type="text/javascript" src="<?php echo e(asset('js/opossum_qz.js')); ?>"></script>
<script type="text/javascript" src="<?php echo e(asset('js/opossum_scanner.js')); ?>"></script>
<script type="text/javascript" src="<?php echo e(asset('js/number_format.js')); ?>"></script>

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

    td{
        border:1px solid rgb(226, 223, 223) !important;
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
    @keyframes  blink-caret {
        from, to {
            border-color: transparent
        }
        50% {
            border-color: black;
        }
    }

</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<?php echo $__env->make('common.header', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->make('common.menubuttons', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<div id="landing-view">
    <div class="container-fluid mb-5">
		<div class="clearfix"></div>
		<div class="d-flex"
			style="width:100%;margin-bottom: 5px; margin-top:5px">
			<div class="col-md-11 pl-0 align-self-center" style="">
				<h2 style="margin-bottom: 0;"> Returning Note</h2>
			</div>

			<div class="col-md-1 pr-0 pl-0 m-0"
				style="display:flex;justify-content:flex-end">
				<button id="confirm_update"
					class="btn btn-success sellerbutton bg-confirm-button"
					onclick="update_quantity()"
					style="margin-bottom:0 !important;border-radius:10px;
					cursor:not-allowed;font-size:14px;" disabled>Confirm
				</button>
			</div>
		</div>

         <div style="margin-top: 0">
            <table border="0" cellpadding="0" cellspacing="0" class="table"
				id="return_table" style="margin-top: 0px; width:100%">
                <thead class="thead-dark"  >
                <tr id="table-th" style="border-style: none">
                    <th valign="middle" class="text-center"style="width: 30px;text-align: center !important;">No</th>
                   	<th valign="middle" class="text-center" style="width: 160px;text-align: center !important;">Barcode</th>
                    <th valign="middle" class="text-left" style="text-align: left !important;">Product Name</th>
                    <th valign="middle" class="text-center no-pad" style="width: 140px;text-align: center !important;">Qty</th>
                     <th valign="middle" class="text-center no-pad" style="width: 140px;text-align: center !important;">Returning Qty</th>
                    <th valign="middle" class="text-center" style="width: 100px;text-align: center !important;">Cost</th>
                </tr>
                </thead>
                <tbody  id="show">
            	</tbody>
             </table>
         </div>
	</div>


<?php $__env->startSection('script'); ?>

<script>
	$.ajaxSetup({
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
    });

    var container = [];
    var tableData = {};
    let returningtable= $('#return_table').DataTable({
        "processing": true,
        "serverSide": true,
        ajax: {
            url: "<?php echo e(route('returning_list.datatable')); ?>",
            type: 'POST',
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
            { data: 'barcode' },
            { data: 'name' },
            { data: 'qty' },
            {data:'returning_qty'},
            { data: 'cost' }
        ],

        "columnDefs": [
            {"width": "30px", "targets": [0]},
            {"width": "160px", "targets": [1]},
            {"width": "60px", "targets": [3]},
            {"width": "100px", "targets": [4]},
            {
                "targets": 2, // your case first column
                "className": "text-left",
            },
            {"className": "dt-left vt_middle", "targets": [2]},
            {"className": "dt-right vt_middle slim-cell", "targets": [4,]},
            {"className": "dt-center vt_middle slim-cell", "targets": [0, 1, 3]},
            {"className": "vt_middle slim-cell", "targets": [2]},
            // {"className": "vt_middle slim-cell", "targets": [6]},
            {orderable: false, targets: [-1]},
            {
                "searchable": true,
                "caseInsensitive": false,
                "targets": [1,2,3, 4 ]
            }
        ],
        "drawCallback": function( settings ) {

        }

    });

    function increaseValue(id) {

        var num_element = document.getElementById('number_'+id);
        var existing_qty = parseFloat($(`#qty_${id}`).text().replace(',',''))
        var value = parseFloat(num_element.value);
        value = isNaN(value) ? 0 : value;
        value++;
        if (existing_qty >= value)	{
            num_element.value = value.toFixed(0);
            isConfirmEnabled++;
        }
        qty = $('#number_'+ id).val()
        cost = $('#cost_'+ id).text();

        if (cost.indexOf(',') > -1) {
           cost = number_format(cost)
        }else if (cost.indexOf('.') > -1){
            cost =  parseFloat(cost);
        }else{
            cost = cost
        }

        container = container.filter(function(me) {

            if(me.product_id != id) {
                return me.qty !==0;
            }
        });
        container.push({
            'product_id': id,
            'cost':  cost,
            'qty': qty
        })

        tableData = {
            'container':container
        };
    }

    function decreaseValue(id) {
        var num_element = document.getElementById('number_'+id);
        var existing_qty = parseFloat($(`#qty_${id}`).text().replace(',',''))
        var value = parseFloat(num_element.value);
            value = isNaN(value) ? 0 : value;
            value < 1 ? value = 1 : '';
            value--;

          if (existing_qty >= value)	{
            num_element.value = value.toFixed(0);
            isConfirmEnabled--;
          }

        qty = $('#number_'+ id).val()
        cost = $('#cost_' + id).text();

       if (cost.indexOf(',') > -1) {
           cost = number_format(cost)
        }else if (cost.indexOf('.') > -1){
            cost =  parseFloat(cost);
        }else{
            cost = cost
        }

        container = container.filter(function(me) {

            if(me.product_id != id) {
                return me.qty !== 0;
            }

        });

        container.push({
            'product_id': id,
            'cost': cost,
            'qty': qty
        })

        tableData = {
            'container':container
        };
    }

    function changeValueOnBlur(id) {
        var num_element = document.getElementById('number_'+id);
        var existing_qty = parseFloat($(`#qty_${id}`).text().replace(',',''));

        //alert('existing_qty='+existing_qty);

        var value = parseFloat(num_element.value);

        //alert('value='+value);

        if (existing_qty < value) {
            $(`#number_${id}`).val( '0.00');
        }

        qty = $('#number_'+ id).val()
        cost = $('#cost_'+ id).text();

       if (cost.indexOf(',') > -1) {
           cost = number_format(cost)
        }else if (cost.indexOf('.') > -1){
            cost =  parseFloat(cost);
        }else{
            cost = cost
        }
        container = container.filter(function(me) {
            if(me.product_id != id) {
                return me.qty !==0;
            }

        });

        container.push({
            'product_id': id,
            'cost':  cost,
            'qty': qty
        })

        tableData = {
            'container':container
        };
    }


    function update_quantity() {

        if (container.length < 1)
            return;
        container.filter(function(params) {
            params.qty !== 0;
        })

        $.ajax({
            url: "<?php echo e(route('returning.save_returning_qty')); ?>",
            type: "POST",
            'headers': {
                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
            },
            data: {table_data : container},
            cache: false,
            success: function(dataResult){
                messageModal(`Returning quantity stored successful`);
                returningtable.ajax.reload();
            }

        });

        container = [];
        isConfirmEnabled = 0;
    }

    function messageModal(msg) {
        $('#modalMessage').modal('show');
        $('#statusModalLabelMsg').html(msg);
        setTimeout(function(){
            $('#modalMessage').modal('hide');
        }, 3500);
    }


    $(document).ready(function() {
        isConfirmEnabled = 0;
        setInterval(() => {
            if (isConfirmEnabled > 0) {
                $("#confirm_update").removeAttr('disabled');
                $("#confirm_update").css('background','');
                $("#confirm_update").css('cursor','');
            } else {
                $("#confirm_update").attr('disabled', true);
                $("#confirm_update").css('background','gray');
                $("#confirm_update").css('cursor','not-allowed');
            }
        }, 100);

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
					returningtable.ajax.reload();
				}

				document_hidden = document[hidden];
			}
		});
	});

</script>
<?php $__env->stopSection(); ?>

<?php $__env->stopSection(); ?>


<?php echo $__env->make('common.footer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->make('common.web', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/emmanuel/oceania/trunk/oceania/resources/views/returning/cstore_returning.blade.php ENDPATH**/ ?>