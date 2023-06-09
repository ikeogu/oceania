<?php echo $__env->make('common.header', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<?php $__env->startSection('styles'); ?>
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

<?php echo $__env->make('common.menubuttons', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->startSection('content'); ?>
<div class="container-fluid">
	<div class="d-flex mt-0 p-0"
		 style="width:100%; margin-top:5px !important;
		 margin-bottom:5px !important">
		<div style="padding:0" class="align-self-center col-sm-10">
			<h2 class="mb-0">Open Item</h2>
		</div>

		<div class="col-sm-2 pl-0">
			<div class="row mb-0" style="float:right;">

				<button onclick="goToStock('stockin')"
					class="btn btn-success bg-stockin sellerbutton mb-0 mr-0"
					style="padding:0px;float:right;margin-left:5px;
					border-radius:10px" id="stockin_btn">Stock<br>In
				</button>
				<input type="hidden" name="" id="stockin"
					value="<?php echo e(route('openitem.openitem_stockin')); ?>">
				<input type="hidden" name="" id="stockout"
					value="<?php echo e(route('openitem.openitem_stockout')); ?>">
				<button onclick="goToStock('stockout')"
					class="btn btn-success bg-stockout sellerbutton mb-0 mr-0"
					style="padding:0px;float:right;margin-left:5px;
					border-radius:10px" id="stockout_btn">Stock<br>Out
				</button>

				<button onclick="saveProduct()"
					class="btn btn-success sellerbutton mb-0 mr-0"
					style="padding:0px;float:right;margin-left:5px;
					border-radius:10px" id="addproduct_btn">+Product
				</button>
			</div>
		</div>
	</div>

	<div class="col-sm-12 pl-0 pr-0" style="">

	<table id="tableOpenItem"
		class="table table-bordered">
		<thead class="thead-dark">
		<tr>
			<th style="text-align: center;">No</th>
			<th style="text-align: center;">Product&nbsp;ID</th>
			<th style="text-align: center;">Barcode</th>
			<th>Product&nbsp;Name</th>
			<th class="text-center">Cost</th>
			<th class="text-center">Qty</th>
			<th class="text-center">Cost&nbsp;Value</th>
			<th class="text-center">Price</th>
			<th class="text-center" >L</th>
			<th class="text-center" >R</th>
			<th class="text-center"></th>
		</tr>
		</thead>
		<tbody id="shows">
		</tbody>
	</table>
	</div>
</div>

<div class="modal fade" id="normalPriceModal" tabindex="-1"
	 role="dialog" aria-labelledby="staffNameLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered"
		 role="document" style="width: 300px">
		<div class="modal-content ">

			<div class="modal-body bg-purplelobster">
				<div class='text-center ' style="margin:auto">

					<div class="typewriter"
						id="retail_price_normal_fk_text"
						style="padding: 6px 12px 6px 12px;
						background-color: white; color: #0c0c0c; ">0.00
					</div>

					<input type="text" id="retail_price_normal_fk"
						style="text-align:right; margin-top: -14%; opacity: 0"
						class="form-control"
						placeholder='0.00' value="0"/>

					<input type="hidden" id="retail_price_normal_fk_buffer"
						style="text-align:right; " class="form-control"
						placeholder='' value="0"/>

					<input type="hidden" id='retail_price_normal'/>
					<input type="hidden" id="element_price"
						style="text-align:right" value=""
						class="form-control" placeholder='0'/>
                    <input type="hidden" id="pro_id"
                        style="text-align:right" value=""
                        class="form-control" placeholder='0'/>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="deleteOpenModal" tabindex="-1"
	 role="dialog" aria-labelledby="staffNameLabel" aria-hidden="true">
<div class="modal-dialog modal-dialog-centered  mw-75 w-50"
	 role="document">
	<div class="modal-content modal-inside bg-purplelobster">

		<div class="modal-header" style="border-width:0"></div>
		<div class="modal-body text-center">
			<h5 class="modal-title text-white">
				Do you want to permanently
				delete this product?
			</h5>
		</div>
		<div class="modal-footer text-center" style="border-width:0">
			<div class="row" style="width:100%;">
				<input type="hidden" name="" id="prdId" value="">
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
</div>
</div>

<div class="modal fade" id="qteModal" tabindex="-1"
	 role="dialog" aria-labelledby="staffNameLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered"
		 role="document" style="width: 300px">
		<div class="modal-content ">

			<div class="modal-body bg-purplelobster">
				<div class='text-center' style="margin:auto">
					<input type="number" id="value"
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

<div class="modal fade" id="fillFields2" tabindex="-1" role="dialog">
	<div class="modal-dialog  modal-dialog-centered mw-75 w-50">

		<!-- Modal content-->
		<div class="modal-content  modal-inside bg-purplelobster">
			<div class="modal-header" style="border:none;">&nbsp;</div>
			<div class="modal-body text-center">
				<h5 class="mb-0" id="return_data2">
					Please fill all fields.
				</h5>
			</div>
			<div class="modal-footer" style="border: none;">&nbsp;</div>
		</div>

	</div>
</div>

<div class="modal fade" id="add_cost_modal" tabindex="-1"
	 role="dialog" aria-labelledby="staffNameLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered"
		 role="document" style="width: 300px">
		<div class="modal-content ">

            <div class="modal-body bg-purplelobster">
				<div class='text-center ' style="margin:auto">

					<div class="typewriter"
						id="cost_fk_text"
						style="padding: 6px 12px 6px 12px;
						background-color: white; color: #0c0c0c; ">0.00
					</div>

					<input type="text" id="cost_fk"
						style="text-align:right; margin-top: -14%; opacity: 0"
						class="form-control"
						placeholder='0.00' value="0"/>

					<input type="hidden" id="cost_fk_buffer"
						style="text-align:right; " class="form-control"
						placeholder='' value="0"/>

					<input type="hidden" id='retail_cost_normal'/>
					<input type="hidden" id="element_cost"
						style="text-align:right" value=""
						class="form-control" placeholder='0'/>
                    <input type="hidden" id="cost_pro_id"
                        style="text-align:right" value=""
                        class="form-control" placeholder='0'/>
				</div>
			</div>
		</div>
	</div>
</div>


<div class="detail_view"></div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('script'); ?>
    <script src="<?php echo e(asset('js/number_format.js')); ?>"></script>

    <script>
        $.ajaxSetup({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
        });

        let loyalty = "loyalty";
        let qty = "qty";

        var tableData = {};
        var openitemtable = $('#tableOpenItem').DataTable({
            "processing": true,
            "serverSide": true,
            "autoWidth": false,
            "searchable": true,
            "ajax": {
                /* This is just a sample route */
                "url": "<?php echo e(route('openitem.list')); ?>",
                "type": "POST",
                data: function (d) {
                    d.search = $('input[type=search]').val();
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
                {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                // {data: 'product.systemid', name: 'systemid'},
                {
                    data: 'systemid', name: 'systemid'
                },
                {
                    data: 'barcode', name: 'barcode', sorting: false
				},
                {
                    data: 'product_name', name: 'product_name' /* render: function (data) {
                        return data.thumbnail_1 != null ?
                            "<a href='javascript:void(0)' style='text-decoration: none;padding-top: 15px;' onclick='detailProduct(" + data.id + ")'> <img width='25px' height='25px' style='margin: 0px 0px 0px 0px;object-fit:contain' src='/<?php echo e(\App\Http\Controllers\OpenitemController::$IMG_PRODUCT_LINK); ?>" + data.systemid + "/thumb/" + data.thumbnail_1 + "' alt=''> " + (data.name == null ? 'Product Name' : data.name) + "</a>"
                            : "<a href='javascript:void(0)' style='text-decoration: none;padding-top: 15px;' onclick='detailProduct(" + data.id + ")'>  <img src='' alt='' width='0px' height='25px'>" + (data.name == null ? 'Product Name' : data.name) + "</a>";
                    } */
                },

                {
                    data: 'cost', name: 'cost'

                },
                {
                    data: 'qty', name: 'qty'
                },
                {
                    data: 'cost_value', name: 'cost_value'
                },
                {
                    data: 'price', name: 'price'
                },
                {
                    data: 'loyalty', name: 'loyalty', render: function (data) {
                        return number_format(parseFloat(data)) ;
                    }
                },
				{
                    data: 'royalty', name: 'royalty', render: function (data) {
                        return number_format(parseFloat(data)) ;
                    }
				},
                {data: 'action', name: 'action'},
            ],

            "columnDefs": [
                {"width": "30px", "targets": [0,10]},
                {"width": "180px", "targets": [1]},
                {"width": "150px", "targets": [2]},
                {"width": "80px", "targets": [4,5,6,7]},
                {"width": "30px", "targets": [8,9]},
                {"className": "dt-left vt_middle", "targets": [3]},
                {"className": "dt-center vt_middle", "targets": [5]},
                {"className": "dt-center vt_middle", "targets": [0, 1, 4]},
                {"className": "vt_middle slim-cell", "targets": [8,9]},
                {
                    "searchable": true,
                    "caseInsensitive": false,
                    "targets": [1,2,3, 4, 5, 6 ]
                }
            ],
        });

        $('input[type="search"]').keyup(function(){
            openitemtable.search( this.value ).draw();;
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

        function cost_preset_input(num, field){
            isType=1
            if(num==='0.0' || num==='0.00')
                isType=0

            var newnum = parseInt(num.replace(".", ""));
            txt=atm_money(newnum)
            $("#"+field).val(txt)
        }

        function cost_in(num) {
          cost_preset_input(num, 'cost_in')
        }

        function add_cost_modal(data, old_value , pro, product_id) {

          let prd_id = product_id.replace("cost_", "");
          $("#add_cost_modal").modal("show");

          if (parseInt(old_value) > 0) {
              $("#cost_fk").val(atm_money(old_value));
              $("#cost_fk_text").text(atm_money(old_value));
          } else {
              $("#cost_fk").val('');
              $("#cost_fk_text").text("0.00");
          }
          $("#retail_cost_normal").val(old_value);
          $("#element_cost").attr("value", data);
          $("#pro_id").attr("value", pro);


            $('#add_cost_modal').on('show.bs.modal', function (e) {
                $("#cost_record").val(prd_id);
    		})


            $('#add_cost_modal').on('hidden.bs.modal', function (e) {

                let cost_amount = $("#retail_cost_normal").val()
                $.ajax({
                    url: "<?php echo e(route('openitem.openitem_save_prd_cost')); ?>",
                    type: "POST",
                    data: {
                        cost_amount: cost_amount,
                        product_id: prd_id
                    },
                    'headers': {
                        'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
                    },
                    success: function(response) {
                        openitemtable.ajax.reload();
                    },
                    error: function(resp) {
                      console.log(response);
                    }
                })
                    .done((data) => {

                        console.log("data", data);
                         payload = {
                        id: $('#cost_pro_id').val()

                };
                console.log("data", payload);
                    localStorage.removeItem('cost_update_product');
                    localStorage.setItem("cost_update_product",JSON.stringify(payload));
                    openitemtable.ajax.reload();

                    })
                    .fail((data) => {
                        console.log("data", data)
                    });
                    $(this).off('hidden.bs.modal');
            });
        }



        $("#cost_fk").on("keyup keypress", function (evt) {
            let old_value = "";
            let type_evt_not_use = ["0", "1", "2", "3", "4", "5", "6", "7", "8", "9"];

            if (evt.type === "keypress") {
                let value = $("#cost_fk").val();
                console.log("value", value);
                old_value = parseInt(value.replace('.', ''));
                $("#retail_cost_normal").val(old_value == '' ? 0 : old_value);
            } else {
                if (evt.key === "Backspace") {
                    let value = $("#cost_fk").val();
                    console.log("value", value);
                    old_value = parseInt(value.replace('.', ''));
                    $("#retail_cost_normal").val(old_value);
                }

                let use_key = "";
                if (type_evt_not_use.includes(evt.key)) {
                    use_key = evt.key;
                    console.log(evt.key);
                }

                old_value = parseInt((isNaN($("#retail_cost_normal").val()) == false ? $("#retail_cost_normal").val() : 0) + "" + use_key);
                let nan = isNaN(old_value);
                console.log("up", old_value);

                if (old_value !== "" && nan == false) {
                    $("#cost_fk").val(atm_money(parseInt(old_value)));
                    $("#cost_fk_text").text(atm_money(parseInt(old_value)));
                    $("#retail_cost_normal").val(parseInt(old_value));
                } else {
                    $("#cost_fk").val("0.00");
                    $("#cost_fk_text").text("0.00");
                    $("#retail_cost_normal").val(0);
                }
            }
        });

        function saveProduct() {
            $.ajax({
                method: "get",
                url: "<?php echo e(route('openitem.save')); ?>"
            })
			.done((data) => {
				//console.log("data", data);
				openitemtable.ajax.reload();
				$("#return_data2").html("Product added successfully");
				$("#fillFields2").modal('show');
				setTimeout(function () {
					$('#fillFields2').modal('hide');
				}, 2000);

			})
			.fail((data) => {
				console.log("data", data)
			});
        }


        function detailProduct(data) {
            $.ajax({
                method: "post",
                url: "<?php echo e(route('openitem.detail_product')); ?>",
                data: {id: data}
            }).done((data) => {
                $(".detail_view").html(data);
                $("#modal").modal('show');
            })
                .fail((data) => {
                    console.log("data", data)
                });
        }

        $('#qteModal').on('hidden.bs.modal', function (e) {
            let key = $("#key").val();
            let value = $("#value").val();
            let element = $("#element").val();
            if (value != null) {
                $.ajax({
                    method: "post",
                    url: "<?php echo e(route('openitem.update_open')); ?>",
                    data: {key: key, value: value, element: element}
                })
                    .done((data) => {
                        console.log("data", data);
                        openitemtable.ajax.reload();

                    })
                    .fail((data) => {
                        console.log("data", data)
                    });
            }

        });

        function deleteMe(id, prd_id) {

        	$.ajax({
        		url: "<?php echo e(route('openitem.openitem_has_qty')); ?>",
        		type: "POST",
        		data: {
        			product_id: prd_id,
        		},
        		'headers': {
        			'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
        		},
        		success: function(response) {
                    console.log('prod id ', id)
                    console.log('response.has_qty', response.has_qty)
        			if (response.has_qty) {
                        $("#return_data2").html("Product has transactions. Delete is prohibited.");
        				$("#fillFields2").modal('show');
        			} else {
                        $("#deleteOpenModal").modal("show");
                        $("#prdId").attr("value", id);
        			}
        			table.ajax.reload();
        		},
        		error: function(resp) {
        		  console.log(response);
        		}
        	})
        	.done(() => {})
        	.fail(() => {});

        }

        function noDelete() {
            $("#deleteOpenModal").modal("hide");
        }


        function yesDelete() {
            $.ajax({
                method: "post",
                url: "<?php echo e(route('openitem.delete')); ?>",
                data: {id: $("#prdId").val()}
            })
                .done((data) => {
                    console.log("data", data);
                    payload = {
                        id: data.data.product_id,
                    };

                    localStorage.removeItem('delete_product');
                    localStorage.setItem("delete_product",JSON.stringify(payload));

                    $("#deleteOpenModal").modal("hide");
                    openitemtable.ajax.reload();
                    $("#return_data2").html("Product deleted successfully");
                    $("#fillFields2").modal('show');



                    setTimeout(function () {
                        $('#fillFields2').modal('hide');
                    }, 2000);
                })
                .fail((data) => {
                    console.log("data", data)
                });
        }

        function prdOpenItem(data, old_value, send_key) {
            console.log("data", send_key);
            $("#qteModal").modal("show");
            $("#element").attr("value", data);
            $("#value").val(old_value);
            $("#key").attr("value", send_key);
            $("#qteModalLabel").html(send_key.charAt(0).toUpperCase() + send_key.slice(1));
        }

        function prdOpenItemPrice(data, old_value , pro) {
            $("#normalPriceModal").modal("show");
            if (parseInt(old_value) > 0) {
                $("#retail_price_normal_fk").val(atm_money(old_value));
                $("#retail_price_normal_fk_text").text(atm_money(old_value));
            } else {
                $("#retail_price_normal_fk").val('');
                $("#retail_price_normal_fk_text").text("0.00");
            }
            $("#retail_price_normal").val(old_value);
            $("#element_price").attr("value", data);
            $("#pro_id").attr("value", pro);
        }




        $("#retail_price_normal_fk").on("keyup keypress", function (evt) {
            let old_value = "";
            let type_evt_not_use = ["0", "1", "2", "3", "4", "5", "6", "7", "8", "9"];

            if (evt.type === "keypress") {
                let value = $("#retail_price_normal_fk").val();
                console.log("value", value);
                old_value = parseInt(value.replace('.', ''));
                $("#retail_price_normal").val(old_value == '' ? 0 : old_value);
            } else {
                if (evt.key === "Backspace") {
                    let value = $("#retail_price_normal_fk").val();
                    console.log("value", value);
                    old_value = parseInt(value.replace('.', ''));
                    $("#retail_price_normal").val(old_value);
                }

                let use_key = "";
                if (type_evt_not_use.includes(evt.key)) {
                    use_key = evt.key;
                    console.log(evt.key);
                }

                old_value = parseInt((isNaN($("#retail_price_normal").val()) == false ? $("#retail_price_normal").val() : 0) + "" + use_key);
                let nan = isNaN(old_value);
                console.log("up", old_value);

                if (old_value !== "" && nan == false) {
                    $("#retail_price_normal_fk").val(atm_money(parseInt(old_value)));
                    $("#retail_price_normal_fk_text").text(atm_money(parseInt(old_value)));
                    $("#retail_price_normal").val(parseInt(old_value));
                } else {
                    $("#retail_price_normal_fk").val("0.00");
                    $("#retail_price_normal_fk_text").text("0.00");
                    $("#retail_price_normal").val(0);
                }
            }
        });

        function priceChange() {

            let value = $("#retail_price_normal_fk").val();
            if (value != "") {
                $("#retail_price_normal_fk").val(atm_money(parseInt(value.replace('.', ''))));
                $("#retail_price_normal").val(parseInt(value.replace('.', '')));
            } else {
                $("#retail_price_normal_fk").val("0.00");
                $("#retail_price_normal").val(0);
            }
        }


        $('#normalPriceModal').on('hidden.bs.modal', function (e) {
            let key = "price";
            let value = $("#retail_price_normal").val();
            let element = $("#element_price").val();


            $.ajax({
                method: "post",
                url: "<?php echo e(route('openitem.update_open')); ?>",
                data: {key: key, value: value, element: element}
            })
                .done((data) => {

                    console.log("data", data);
                     payload = {
                    id: $('#pro_id').val()

            };
            console.log("data", payload);
                localStorage.removeItem('update_product');
                localStorage.setItem("update_product",JSON.stringify(payload));
                openitemtable.ajax.reload();

                })
                .fail((data) => {
                    console.log("data", data)
                });
        });

        function goToStock(route) {
            window.open($("#" + route).val(), '_blank');
        }
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
					openitemtable.ajax.reload();
				}

				document_hidden = document[hidden];
			}
		});
	});

    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('common.footer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<?php echo $__env->make('common.web', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/emmanuel/oceania/trunk/oceania/resources/views/openitem/openitem_landing.blade.php ENDPATH**/ ?>