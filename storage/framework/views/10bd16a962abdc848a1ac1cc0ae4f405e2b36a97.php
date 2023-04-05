<?php $__env->startSection('styles'); ?>
<script type="text/javascript" src="<?php echo e(asset('js/qz-tray.js')); ?>"></script>
<script type="text/javascript" src="<?php echo e(asset('js/opossum_qz.js')); ?>"></script>
<?php
// die(date("H:i:s"));
?>
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

#eodSummaryListModal-table_paginate, #eodSummaryListModal-table_previous,
#eodSummaryListModal-table_next, #eodSummaryListModal-table_length,
#eodSummaryListModal-table_filter, #eodSummaryListModal-table_info {
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
/* Calaneder Css styles */
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
	text-align:center;
}

table.dataTable tbody td{
	border-left: 1px solid #dee2e6;
	border-right: 1px solid #dee2e6;
	border-top: none;
	border-bottom: none;
}

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

.dataTables_filter input {
	width: 300px;
}

.greenshade {
	height: 30px;
	background-color: green; /* For browsers that do not support gradients */
	background-image: linear-gradient(-90deg, green, white); /* Standard syntax (must be last) */
}
.dt-button{
	display: none;
}

.bg-purplelobster{
	color:white;
	border-color:rgba(0,0,255,0.5);
	background-color:rgba(0,0,255,0.5)
}

/*//for calender short day*/
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
.isDisabled {
  pointer-events: none;
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
<div id="landing-view">
	<!--div id="landing-content" style="width: 100%"-->
	<div class="container-fluid">
		<div class="clearfix"></div>
		<div class="row py-2 align-items-center" style="display:flex;height:75px">
			<div class="col" style="width:70%">
				<h2 style="margin-bottom: 0;">Sales Report</h2>
			</div>

			<div class="col-md-2 text-right">
				<h5 style="margin-bottom:0;"></h5>
			</div>
		</div>


		<div>
            <form id="form1">
                <h5 class="mb-0">Convenience Store Sales</h5>
                <hr class="mt-0 mb-2" style="border-color:#c0c0c0">
				<div style="right:200px;display:inline;padding-left:0;margin-bottom:20px">
					<input class="to_date form-control btnremove"
					style="display:inline;margin-top:10px;padding-top:0px !important;
					position:relative;top:2px;
					padding-bottom: 0px; width:110px;padding-right:0;padding-left:0px;
					text-align: center;"
					value="End Date"
					onclick="show_dialog15()"
					id="cs_start_date" name="start_date" placeholder="Select" />
				</div>
				To
				<div style="right:200px;display:inline;padding-left:0;
					margin-bottom:20px">
					<input class="to_date form-control btnremove"
					style="display:inline;margin-top:10px;padding-top:0px !important;
					position:relative;top:2px;
					padding-bottom: 0px; width:110px;padding-right:0;
					padding-left:0px; text-align: center;"
					value="End Date"
					onclick="show_dialog20()"
					id="cs_end_date" name="end_date" placeholder="Select" />
				</div>

				<div style="right:200px;display:inline;
					padding-left:40px;margin-bottom:20px" id="btnFetch2">
					<button class="btn btn-success bg-download"
						style="height:70px;width:70px;border-radius:10px;
						outline:none;font-size: 14px" onclick="downloadPDF()">
						<span class="d-none spinner-border spinner-border-sm" role="status" aria-hidden="true" style="z-index:2; position: fixed; margin-top: 3px;
							margin-left:7px"></span>
						PDF
					</button>
				</div>
            </form>
		</div>



        <div>
			<form id="form2">
				<h5 class="mb-0">Fuel Sales Summary</h5>

				<hr class="mt-0 mb-2" style="border-color:#c0c0c0">

				<div style="right:200px;display:inline;padding-left:0;margin-bottom:20px">
					<input class="to_date form-control btnremove"
					style="display:inline;margin-top:10px;padding-top:0px !important;
					position:relative;top:2px;
					padding-bottom: 0px; width:110px;padding-right:0;padding-left:0px;
					text-align: center;"
					value="Start Date"
					onclick="fuel_start_dialog()"
					id="fuel_start_date" name="fuel_start_date" placeholder="Select" />
				</div>

				To
				<div style="right:200px;display:inline;padding-left:0;
					margin-bottom:20px">
					<input class="to_date form-control btnremove"
					style="display:inline;margin-top:10px;padding-top:0px !important;
					position:relative;top:2px;
					padding-bottom: 0px; width:110px;padding-right:0;
					padding-left:0px; text-align: center;"
					value="End Date"
					onclick="fuel_end_dialog()"
					id="fuel_end_date" name="fuel_end_date" placeholder="Select" />
				</div>


				<div style="right:200px;display:inline;
					padding-left:40px;margin-bottom:20px" id="btnFetch4">
					<button class="btn btn-success bg-download spinner-button"
						style="height:70px;width:70px;border-radius:10px;
						outline:none;font-size: 14px" onclick="downloadPDF2()" >
						 <span class="d-none spinner-border spinner-border-sm"
						 	role="status" aria-hidden="true"
							style="z-index:2; position: fixed; margin-top: 3px;
							margin-left:7px"></span>
						PDF
					</button>
				</div>

			</form>
		</div>


	




	</div>
</div>


<div class="clearfix"></div>
<br><br>

<div class="modal fade" id="showDateModalFrom" tabindex="-1"
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
    <form id="status-form" action="<?php echo e(route('logout')); ?>"
      method="POST" style="display: none;">
      <?php echo csrf_field(); ?>
    </form>
  </div>
</div>
<div class="modal fade"  id="modalMessage"  tabindex="-1" role="dialog"
    aria-labelledby="staffNameLabel" aria-hidden="true" style="text-align: center; opacity:1.0">
       <div class="modal-dialog modal-dialog-centered  mw-75 w-50" role="document"
        style="display: inline-flex;">
           <div class="modal-content modal-inside bg-purplelobster"
           style="width: 100%;  background-color: <?php echo e(@$color); ?> !important" >
               <div class="modal-header" style="border:0">&nbsp;</div>
               <div class="modal-body text-center">
                   <h5 class="modal-title text-white" id="statusModalLabelMsg"></h5>
               </div>
               <div class="modal-footer" style="border-top:0 none;">&nbsp;</div>
           </div>
       </div>
   </div>


<script src="<?php echo e(asset('/js/osmanli_calendar.js')); ?>?version=<?php echo e(date("hmis")); ?>"></script>

<script>
store_date = dateToYMDEmpty(new Date());
$("#end_date").val(store_date);
$("#ev_end_date").val(store_date);
$("#start_date").val(store_date);
$("#fuel_start_date").val(store_date);
$("#ev_start_date").val(store_date);
$("#h2_start_date").val(store_date);
$("#h2_end_date").val(store_date);
$("#oew_start_date").val(store_date);
$("#oew_end_date").val(store_date);
$("#cs_start_date").val(store_date);
$("#cs_end_date").val(store_date);
$("#opt_start_date").val(store_date);
$("#opt_end_date").val(store_date);
$("#fuel_start_date").val(store_date);
$("#fuel_end_date").val(store_date);


localStorage.removeItem("startH2Date")
localStorage.removeItem("endH2Date")
localStorage.removeItem("showH2StartDate")
localStorage.removeItem("showEVStartDate")
localStorage.removeItem("showEndEVStartDate")
localStorage.removeItem("showFSENDDate")
localStorage.removeItem("showFSStartDate")
localStorage.removeItem("showCSSStartDate")
localStorage.removeItem("showCSSEndDate")
localStorage.removeItem("showOewStartDate")
localStorage.removeItem("showOeweStartDate")
localStorage.removeItem("showOptStartDate")
localStorage.removeItem("endOptDate");
localStorage.removeItem("showFuelStartDate")
localStorage.removeItem("showFuelEndDate")

    var terminal_date;

    $(document).ready(function(){
        $.ajax({
            url: "<?php echo e(route('sales.terminal.date')); ?>",
            type: "get",
            success(response){
                console.log(response);
                terminal_date = new Date(response);
                console.log("Terminal: "+terminal_date);
            }
        })
    });


    function sum(input) {
        var total = 0;
        for (var i = 0; i < input.length; i++) {
            total += Number(input[i]);
        }
        return total;
    }


    function formatNumber(num) {
        return num.toFixed(2).replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,')
    }


    function reinit(destory) {
        if (destory == true) {
            $('#product_sales_pdt_table').DataTable().clear().destroy();
        } else {
            dt = $('#product_sales_pdt_table').DataTable({
                order:[]
            });
        }
    }

    var start_date_dialog = osmanli_calendar;
    var completion_date_dialog = osmanli_calendar;

    function fuel_start_dialog(e) {
        // alert("yessss")
        sessionStorage.removeItem("modalTrue");
        sessionStorage.setItem("modalTrue",'showTransStartDate');

        date = new Date();
        start_date_dialog.MAX_DATE = date;
        start_date_dialog.DAYS_DISABLE_MIN = "ON";
        start_date_dialog.DAYS_DISABLE_MAX = "ON";
        start_date_dialog.MIN_DATE = new Date('<?php echo e($approved_at); ?>');
        $('.next-month').off();
        $('.prev-month').off();

        $('.prev-month').click(function () {start_date_dialog.pre_month()});
        $('.next-month').click(function () {start_date_dialog.next_month()});

        start_date_dialog.CURRENT_DATE = new Date();

        if(localStorage.getItem("showFuelStartDate")===null)
        {
            start_date_dialog.SELECT_DATE = new Date()
        } else{
            var loclaaa=  localStorage.getItem("showFuelStartDate");
            // console.log( loclaaa)
            // start_date_dialog.CURRENT_DATE =new Date(localStorage.getItem("showH2StartDate"));
            start_date_dialog.SELECT_DATE = new Date(localStorage.getItem("showFuelStartDate"))
            start_date_dialog.CURRENT_DATE = new Date(localStorage.getItem("showFuelStartDate"))


            // console.log(start_date_dialog.SELECT_DATE)
        }
            var date =   start_date_dialog.SELECT_DATE.getDate();
            const monthNames = ["January", "February", "March", "April", "May", "June",
            "July", "August", "September", "October", "November", "December"
            ];
            var month  =  monthNames[start_date_dialog.SELECT_DATE .getMonth()];
            var year = start_date_dialog.SELECT_DATE.getFullYear();
            var select_moth_year  =  month+" "+year
            var date =   start_date_dialog.SELECT_DATE.getDate();
            sessionStorage.setItem("date_check",date);
            sessionStorage.setItem("select_moth_year",select_moth_year);

        if(date == 1){
            start_date_dialog.CURRENT_DATE.setDate(4)
            start_date_dialog.ON_SELECT_FUNC = function(){
                var date = osmanli_calendar.SELECT_DATE;
                localStorage.setItem("showFuelStartDate",date)
                var start_date = dateToYMDEmpty(date);
                // console.log(start_date)

                // localStorage.setItem("sTransDate",start_date)

                $("#fuel_start_date").val(start_date);
                jQuery('#showDateModalFrom').modal('hide');
            }

        }else{
            start_date_dialog.ON_SELECT_FUNC = function(){
                var date = osmanli_calendar.SELECT_DATE;
                localStorage.setItem("showFuelStartDate",date)
                var start_date = dateToYMDEmpty(date);
                console.log(start_date)

                // localStorage.setItem("sTransDate",start_date)

                $("#fuel_start_date").val(start_date);
                jQuery('#showDateModalFrom').modal('hide');
            }
        }

        start_date_dialog.init()
        if(date == 1){
            var table_data =  $(".date_table tbody tr").eq(1)
            table_data.children('td').each(function(){
            var data = $(this).html();
                if(data== 1){
                    $(this).addClass("selected_date")

                }
            })
        }
        jQuery('#showDateModalFrom').modal('show');

    //end showTransStartDate
      var EndDate = new Date();
    }

    function fuel_end_dialog(e) {
        // alert("yessss")
        sessionStorage.removeItem("modalTrue");
        sessionStorage.setItem("modalTrue",'showTransStartDate');

        date = new Date();
        start_date_dialog.MAX_DATE = date;
        start_date_dialog.DAYS_DISABLE_MIN = "ON";
        start_date_dialog.DAYS_DISABLE_MAX = "ON";
        //start_date_dialog.MIN_DATE = new Date();

        if(localStorage.getItem("showFuelStartDate") == null){
            start_date_dialog.MIN_DATE = new Date ()
        }else{
            start_date_dialog.MIN_DATE = new Date (localStorage.getItem("showFuelStartDate"))
            start_date_dialog.MIN_DATE.setDate(start_date_dialog.MIN_DATE.getDate())

        }
        $('.next-month').off();
        $('.prev-month').off();

        $('.prev-month').click(function () {start_date_dialog.pre_month()});
        $('.next-month').click(function () {start_date_dialog.next_month()});

        start_date_dialog.CURRENT_DATE = new Date();

        if(localStorage.getItem("showEndFuelStartDate")===null)
        {
            start_date_dialog.SELECT_DATE = new Date()
        } else{
            var loclaaa=  localStorage.getItem("showEndFuelStartDate");
            // console.log( loclaaa)
            // start_date_dialog.CURRENT_DATE =new Date(localStorage.getItem("showH2StartDate"));
            start_date_dialog.SELECT_DATE = new Date(localStorage.getItem("showEndFuelStartDate"))
            start_date_dialog.CURRENT_DATE = new Date(localStorage.getItem("showEndFuelStartDate"))

            // console.log(start_date_dialog.SELECT_DATE)
        }
            var date =   start_date_dialog.SELECT_DATE.getDate();
            const monthNames = ["January", "February", "March", "April", "May", "June",
            "July", "August", "September", "October", "November", "December"
            ];
            var month  =  monthNames[start_date_dialog.SELECT_DATE .getMonth()];
            var year = start_date_dialog.SELECT_DATE.getFullYear();
            var select_moth_year  =  month+" "+year
            var date =   start_date_dialog.SELECT_DATE.getDate();
            sessionStorage.setItem("date_check",date);
            sessionStorage.setItem("select_moth_year",select_moth_year);

        if(date == 1){
            start_date_dialog.CURRENT_DATE.setDate(4)
            start_date_dialog.ON_SELECT_FUNC = function(){
                var date = osmanli_calendar.SELECT_DATE;
                localStorage.setItem("showEndFuelStartDate",date)
                var start_date = dateToYMDEmpty(date);
                // console.log(start_date)

                // localStorage.setItem("sTransDate",start_date)

                $("#fuel_end_date").val(start_date);
                jQuery('#showDateModalFrom').modal('hide');
            }

        }else{
            start_date_dialog.ON_SELECT_FUNC = function(){
                var date = osmanli_calendar.SELECT_DATE;
                localStorage.setItem("showEndFuelStartDate",date)
                var start_date = dateToYMDEmpty(date);
                console.log(start_date)

                // localStorage.setItem("sTransDate",start_date)

                $("#fuel_end_date").val(start_date);
                jQuery('#showDateModalFrom').modal('hide');
            }
        }

        start_date_dialog.init()
        if(date == 1){
            var table_data =  $(".date_table tbody tr").eq(1)
            table_data.children('td').each(function(){
            var data = $(this).html();
                if(data== 1){
                    $(this).addClass("selected_date")

                }
            })
        }
        jQuery('#showDateModalFrom').modal('show');

    //end showTransStartDate
        var EndDate = new Date();
    }

    function show_dialog15(e){

        sessionStorage.removeItem("modalTrue");
        sessionStorage.setItem("modalTrue",'showTransStartDate');

        date = new Date();
        start_date_dialog.MAX_DATE = date;
        start_date_dialog.DAYS_DISABLE_MIN = "ON";
        start_date_dialog.DAYS_DISABLE_MAX = "ON";
        start_date_dialog.MIN_DATE = new Date("<?php echo e($approved_at); ?>");

        $('.next-month').off();
        $('.prev-month').off();

        $('.prev-month').click(function () {start_date_dialog.pre_month()});
        $('.next-month').click(function () {start_date_dialog.next_month()});

        start_date_dialog.CURRENT_DATE = new Date();

        if(localStorage.getItem("showCSSStartDate")===null)
        {
            start_date_dialog.SELECT_DATE = new Date()
        } else{
            var local=  localStorage.getItem("showCSSStartDate");
            // console.log( loclaaa)
            // start_date_dialog.CURRENT_DATE =new Date(localStorage.getItem("showH2StartDate"));
            start_date_dialog.SELECT_DATE = new Date(localStorage.getItem("showCSSStartDate"))
            start_date_dialog.CURRENT_DATE =
                new Date(localStorage.getItem("showCSSStartDate"))
            // console.log(start_date_dialog.SELECT_DATE)
        }

            var date =   start_date_dialog.SELECT_DATE.getDate();
            const monthNames = ["January", "February", "March", "April", "May", "June",
            "July", "August", "September", "October", "November", "December"
            ];
            var month  =  monthNames[start_date_dialog.SELECT_DATE .getMonth()];
            var year = start_date_dialog.SELECT_DATE.getFullYear();
            var select_moth_year  =  month+" "+year
            var date =   start_date_dialog.SELECT_DATE.getDate();
            sessionStorage.setItem("date_check",date);
            sessionStorage.setItem("select_moth_year",select_moth_year);

        if(date == 1){
            start_date_dialog.CURRENT_DATE.setDate(4)
            start_date_dialog.ON_SELECT_FUNC = function(){
                var date = osmanli_calendar.SELECT_DATE;
                localStorage.setItem("showCSSStartDate",date)
                var start_date = dateToYMDEmpty(date);
                // console.log(start_date)

                // localStorage.setItem("sTransDate",start_date)

                $("#cs_start_date").val(start_date);
                jQuery('#showDateModalFrom').modal('hide');
                isDateCorrect('#cs_start_date','#cs_end_date')
            }

        }else{
            start_date_dialog.ON_SELECT_FUNC = function(){
                var date = osmanli_calendar.SELECT_DATE;
                localStorage.setItem("showCSSStartDate",date)
                var start_date = dateToYMDEmpty(date);
                console.log(start_date)

                // localStorage.setItem("sTransDate",start_date)

                $("#cs_start_date").val(start_date);
                jQuery('#showDateModalFrom').modal('hide');
                isDateCorrect('#cs_start_date','#cs_end_date')
            }
        }

        start_date_dialog.init()
        if(date == 1){
            var table_data =  $(".date_table tbody tr").eq(1)
            table_data.children('td').each(function(){
            var data = $(this).html();
                if(data== 1){
                    $(this).addClass("selected_date")

                }
            })
        }
        jQuery('#showDateModalFrom').modal('show');
        isDateCorrect('#cs_start_date','#cs_end_date')
        //end showTransStartDate
        var EndDate = new Date();


    }

    function show_dialog20(e){

            sessionStorage.removeItem("modalTrue");
            sessionStorage.setItem("modalTrue",'showTransStartDate');

            date = new Date();
            start_date_dialog.MAX_DATE = date;
            start_date_dialog.DAYS_DISABLE_MIN = "ON";
            start_date_dialog.DAYS_DISABLE_MAX = "ON";

            if(localStorage.getItem("showCSSStartDate") == null){
                start_date_dialog.MIN_DATE = new Date ()
            }else{
                start_date_dialog.MIN_DATE = new Date (localStorage.getItem("showCSSStartDate"))
                start_date_dialog.MIN_DATE.setDate(start_date_dialog.MIN_DATE.getDate())

            }




            $('.next-month').off();
            $('.prev-month').off();

            $('.prev-month').click(function () {start_date_dialog.pre_month()});
            $('.next-month').click(function () {start_date_dialog.next_month()});

            start_date_dialog.CURRENT_DATE = new Date();

            if(localStorage.getItem("showCSSEndDate")===null)
            {


                start_date_dialog.SELECT_DATE = new Date()
            } else{
                var loclaaa=  localStorage.getItem("showCSSEndDate");
                // console.log( loclaaa)
                // start_date_dialog.CURRENT_DATE =new Date(localStorage.getItem("showH2StartDate"));
                start_date_dialog.SELECT_DATE = new Date(localStorage.getItem("showCSSEndDate"))
                start_date_dialog.CURRENT_DATE = new Date(localStorage.getItem("showCSSEndDate"))


                // console.log(start_date_dialog.SELECT_DATE)
            }

            // console.log()

                var date =   start_date_dialog.SELECT_DATE.getDate();
                const monthNames = ["January", "February", "March", "April", "May", "June",
                "July", "August", "September", "October", "November", "December"
                ];
                var month  =  monthNames[start_date_dialog.SELECT_DATE .getMonth()];
                var year = start_date_dialog.SELECT_DATE.getFullYear();
                var select_moth_year  =  month+" "+year
                var date =   start_date_dialog.SELECT_DATE.getDate();
                sessionStorage.setItem("date_check",date);
                sessionStorage.setItem("select_moth_year",select_moth_year);

            if(date == 1){
                start_date_dialog.CURRENT_DATE.setDate(4)
                start_date_dialog.ON_SELECT_FUNC = function(){
                    var date = osmanli_calendar.SELECT_DATE;
                    localStorage.setItem("showCSSEndDate",date)
                    var start_date = dateToYMDEmpty(date);
                    // console.log(start_date)

                    // localStorage.setItem("sTransDate",start_date)

                    $("#cs_end_date").val(start_date);
                    jQuery('#showDateModalFrom').modal('hide');
                    isDateCorrect('#cs_start_date','#cs_end_date')
                }

            }else{
                start_date_dialog.ON_SELECT_FUNC = function(){
                    var date = osmanli_calendar.SELECT_DATE;
                    localStorage.setItem("showCSSEndDate",date)
                    var start_date = dateToYMDEmpty(date);
                    console.log(start_date)

                    // localStorage.setItem("sTransDate",start_date)

                    $("#cs_end_date").val(start_date);
                    jQuery('#showDateModalFrom').modal('hide');
                    isDateCorrect('#cs_start_date','#cs_end_date')
                }
            }


            start_date_dialog.init()
            if(date == 1){
                var table_data =  $(".date_table tbody tr").eq(1)
                table_data.children('td').each(function(){
                var data = $(this).html();
                    if(data== 1){
                        $(this).addClass("selected_date")

                    }
                })
            }
            jQuery('#showDateModalFrom').modal('show');
            isDateCorrect('#cs_start_date','#cs_end_date')

        //end showTransStartDate
        var EndDate = new Date();


    }


    /*  function show_dialog4(e) {
        // alert("yessss")
        sessionStorage.removeItem("modalTrue");
        sessionStorage.setItem("modalTrue",'showTransStartDate');

        date = new Date();
        start_date_dialog.MAX_DATE = date;
        start_date_dialog.DAYS_DISABLE_MIN = "ON";
        start_date_dialog.DAYS_DISABLE_MAX = "ON";
        start_date_dialog.MIN_DATE = new Date("<?php echo e($approved_at); ?>");

        $('.next-month').off();
        $('.prev-month').off();

        $('.prev-month').click(function () {start_date_dialog.pre_month()});
        $('.next-month').click(function () {start_date_dialog.next_month()});

        start_date_dialog.CURRENT_DATE = new Date();

        if(localStorage.getItem("showH2StartDate")===null)
        {
            start_date_dialog.SELECT_DATE = new Date()
        } else{
            var loclaaa=  localStorage.getItem("showH2StartDate");
            // console.log( loclaaa)
            // start_date_dialog.CURRENT_DATE =new Date(localStorage.getItem("showH2StartDate"));
            start_date_dialog.SELECT_DATE = new Date(localStorage.getItem("showH2StartDate"))
            start_date_dialog.CURRENT_DATE = new Date(localStorage.getItem("showH2StartDate"))
            // console.log(start_date_dialog.SELECT_DATE)
        }
            var date =   start_date_dialog.SELECT_DATE.getDate();
            const monthNames = ["January", "February", "March", "April", "May", "June",
            "July", "August", "September", "October", "November", "December"
            ];
            var month  =  monthNames[start_date_dialog.SELECT_DATE .getMonth()];
            var year = start_date_dialog.SELECT_DATE.getFullYear();
            var select_moth_year  =  month+" "+year
            var date =   start_date_dialog.SELECT_DATE.getDate();
            sessionStorage.setItem("date_check",date);
            sessionStorage.setItem("select_moth_year",select_moth_year);

        if(date == 1){
            start_date_dialog.CURRENT_DATE.setDate(4)
            start_date_dialog.ON_SELECT_FUNC = function(){
                var date = osmanli_calendar.SELECT_DATE;
                localStorage.setItem("showH2StartDate",date)
                var start_date = dateToYMDEmpty(date);
                // console.log(start_date)

                // localStorage.setItem("sTransDate",start_date)

                $("#h2_start_date").val(start_date);
                // isDateCorrect('#h2_start_date','#h2_end_date')
                jQuery('#showDateModalFrom').modal('hide');
                isDateCorrect('#h2_start_date','#h2_end_date')
            }

        }else{
            start_date_dialog.ON_SELECT_FUNC = function(){
                var date = osmanli_calendar.SELECT_DATE;
                localStorage.setItem("showH2StartDate",date)
                var start_date = dateToYMDEmpty(date);
                $("#h2_start_date").val(start_date);

                jQuery('#showDateModalFrom').modal('hide');
                isDateCorrect('#h2_start_date','#h2_end_date')
            }
        }


        start_date_dialog.init()
        if(date == 1){
            var table_data =  $(".date_table tbody tr").eq(0)
            table_data.children('td').each(function(){
            var data = $(this).html();
                if(data== 1){
                    $(this).addClass("selected_date")

                }
            })
        }

        isDateCorrect('#h2_start_date','#h2_end_date')

        //end showTransStartDate
        var EndDate = new Date();

        }

        function show_dialog4_oew(e) {
            // alert("yessss")
            sessionStorage.removeItem("modalTrue");
            sessionStorage.setItem("modalTrue",'showTransStartDate');

            date = new Date();
            start_date_dialog.MAX_DATE = date;
            start_date_dialog.DAYS_DISABLE_MIN = "ON";
            start_date_dialog.DAYS_DISABLE_MAX = "ON";
            start_date_dialog.MIN_DATE = new Date("<?php echo e($approved_at); ?>");


            $('.next-month').off();
            $('.prev-month').off();

            $('.prev-month').click(function () {start_date_dialog.pre_month()});
            $('.next-month').click(function () {start_date_dialog.next_month()});

            start_date_dialog.CURRENT_DATE = new Date();

            if(localStorage.getItem("showOewStartDate")===null)
            {
                start_date_dialog.SELECT_DATE = new Date()
            } else{
                var loclaaa=  localStorage.getItem("showOewStartDate");
                // console.log( loclaaa)
                // start_date_dialog.CURRENT_DATE =new Date(localStorage.getItem("showH2StartDate"));
                start_date_dialog.SELECT_DATE = new Date(localStorage.getItem("showOewStartDate"))
                start_date_dialog.CURRENT_DATE = new Date(localStorage.getItem("showOewStartDate"))

            // console.log(start_date_dialog.SELECT_DATE)
            }
                var date =   start_date_dialog.SELECT_DATE.getDate();
                const monthNames = ["January", "February", "March", "April", "May", "June",
                "July", "August", "September", "October", "November", "December"
                ];
                var month  =  monthNames[start_date_dialog.SELECT_DATE .getMonth()];
                var year = start_date_dialog.SELECT_DATE.getFullYear();
                var select_moth_year  =  month+" "+year
                var date =   start_date_dialog.SELECT_DATE.getDate();
                sessionStorage.setItem("date_check",date);
                sessionStorage.setItem("select_moth_year",select_moth_year);

            if(date == 1){
                start_date_dialog.CURRENT_DATE.setDate(4)
                start_date_dialog.ON_SELECT_FUNC = function(){
                    var date = osmanli_calendar.SELECT_DATE;
                    localStorage.setItem("showOewStartDate",date)
                    var start_date = dateToYMDEmpty(date);
                    $("#oew_start_date").val(start_date);
                    jQuery('#showDateModalFrom').modal('hide');
                    isDateCorrect('#oew_start_date','#oew_end_date')
                }

            }else{
                start_date_dialog.ON_SELECT_FUNC = function(){
                    var date = osmanli_calendar.SELECT_DATE;
                    localStorage.setItem("showOewStartDate",date)
                    var start_date = dateToYMDEmpty(date);
                    // localStorage.setItem("sTransDate",start_date)

                    $("#oew_start_date").val(start_date);
                    jQuery('#showDateModalFrom').modal('hide');
                    isDateCorrect('#oew_start_date','#oew_end_date')
                }
            }
            start_date_dialog.init()
            if(date == 1){
                var table_data =  $(".date_table tbody tr").eq(1)
                table_data.children('td').each(function(){
                var data = $(this).html();
                    if(data== 1){
                        $(this).addClass("selected_date")

                    }
                })
            }
            jQuery('#showDateModalFrom').modal('show');
            isDateCorrect('#oew_start_date','#oew_end_date')
            var EndDate = new Date();
        }

        function show_dialog4_oewe(e) {

            sessionStorage.removeItem("modalTrue");
            sessionStorage.setItem("modalTrue",'showTransStartDate');

            date = new Date();
            start_date_dialog.MAX_DATE = date;
            start_date_dialog.DAYS_DISABLE_MIN = "ON";
            start_date_dialog.DAYS_DISABLE_MAX = "ON";
            let stD =  $("#oew_start_date").val();
            start_date_dialog.MIN_DATE = new Date(stD);


            $('.next-month').off();
            $('.prev-month').off();

            $('.prev-month').click(function () {start_date_dialog.pre_month()});
            $('.next-month').click(function () {start_date_dialog.next_month()});

            start_date_dialog.CURRENT_DATE = new Date();

            if(localStorage.getItem("showOeweStartDate")===null)
            {


                start_date_dialog.SELECT_DATE = new Date()
            } else{
                var loclaaa=  localStorage.getItem("showOeweStartDate");
                // console.log( loclaaa)
                // start_date_dialog.CURRENT_DATE =new Date(localStorage.getItem("showH2StartDate"));
                start_date_dialog.SELECT_DATE = new Date(localStorage.getItem("showOeweStartDate"))
                start_date_dialog.CURRENT_DATE = new Date(localStorage.getItem("showOeweStartDate"))


                // console.log(start_date_dialog.SELECT_DATE)
            }
                var date =   start_date_dialog.SELECT_DATE.getDate();
                const monthNames = ["January", "February", "March", "April", "May", "June",
                "July", "August", "September", "October", "November", "December"
                ];
                var month  =  monthNames[start_date_dialog.SELECT_DATE .getMonth()];
                var year = start_date_dialog.SELECT_DATE.getFullYear();
                var select_moth_year  =  month+" "+year
                var date =   start_date_dialog.SELECT_DATE.getDate();
                sessionStorage.setItem("date_check",date);
                sessionStorage.setItem("select_moth_year",select_moth_year);

            if(date == 1){
                start_date_dialog.CURRENT_DATE.setDate(4)
                start_date_dialog.ON_SELECT_FUNC = function(){
                    var date = osmanli_calendar.SELECT_DATE;
                    localStorage.setItem("showOeweStartDate",date)
                    var start_date = dateToYMDEmpty(date);
                    // console.log(start_date)

                    // localStorage.setItem("sTransDate",start_date)

                    $("#oew_end_date").val(start_date);
                    jQuery('#showDateModalFrom').modal('hide');
                    isDateCorrect('#oew_start_date','#oew_end_date')
                }

            }else{
                start_date_dialog.ON_SELECT_FUNC = function(){
                    var date = osmanli_calendar.SELECT_DATE;
                    localStorage.setItem("showOeweStartDate",date)
                    var start_date = dateToYMDEmpty(date);
                    console.log(start_date)

                    // localStorage.setItem("sTransDate",start_date)

                    $("#oew_end_date").val(start_date);
                    jQuery('#showDateModalFrom').modal('hide');
                    isDateCorrect('#oew_start_date','#oew_end_date')
                }
            }


            start_date_dialog.init()
            if(date == 1){
                var table_data =  $(".date_table tbody tr").eq(1)
                table_data.children('td').each(function(){
                var data = $(this).html();
                    if(data== 1){
                        $(this).addClass("selected_date")

                    }
                })
            }
            jQuery('#showDateModalFrom').modal('show');
            isDateCorrect('#oew_start_date','#oew_end_date')


        //end showTransStartDate
        var EndDate = new Date();
        }
        function show_dialog8(e) {
            // alert("yessss")
            sessionStorage.removeItem("modalTrue");
            sessionStorage.setItem("modalTrue",'showTransStartDate');

            date = new Date();
            start_date_dialog.MAX_DATE = date;
            start_date_dialog.DAYS_DISABLE_MIN = "ON";
            start_date_dialog.DAYS_DISABLE_MAX = "ON";
            start_date_dialog.MIN_DATE = new Date("<?php echo e($approved_at); ?>");
            $('.next-month').off();
            $('.prev-month').off();

            if(localStorage.getItem("showFSStartDate") == null){
                start_date_dialog.MIN_DATE = new Date ()
            }else{
                start_date_dialog.MIN_DATE = new Date (localStorage.getItem("showFSStartDate"))
                start_date_dialog.MIN_DATE.setDate(start_date_dialog.MIN_DATE.getDate())

            }

            $('.prev-month').click(function () {start_date_dialog.pre_month()});
            $('.next-month').click(function () {start_date_dialog.next_month()});

            start_date_dialog.CURRENT_DATE = new Date();

            if(localStorage.getItem("showFSENDDate")===null)
            {


                start_date_dialog.SELECT_DATE = new Date()
            } else{
                var loclaaa=  localStorage.getItem("showFSENDDate");
                // console.log( loclaaa)
                // start_date_dialog.CURRENT_DATE =new Date(localStorage.getItem("showH2StartDate"));
                start_date_dialog.SELECT_DATE = new Date(localStorage.getItem("showFSENDDate"))
                start_date_dialog.CURRENT_DATE = new Date(localStorage.getItem("showFSENDDate"))


                // console.log(start_date_dialog.SELECT_DATE)
            }
                var date =   start_date_dialog.SELECT_DATE.getDate();
                const monthNames = ["January", "February", "March", "April", "May", "June",
                "July", "August", "September", "October", "November", "December"
                ];
                var month  =  monthNames[start_date_dialog.SELECT_DATE .getMonth()];
                var year = start_date_dialog.SELECT_DATE.getFullYear();
                var select_moth_year  =  month+" "+year
                var date =   start_date_dialog.SELECT_DATE.getDate();
                sessionStorage.setItem("date_check",date);
                sessionStorage.setItem("select_moth_year",select_moth_year);

            if(date == 1){
                start_date_dialog.CURRENT_DATE.setDate(4)
                start_date_dialog.ON_SELECT_FUNC = function(){
                    var date = osmanli_calendar.SELECT_DATE;
                    localStorage.setItem("showFSENDDate",date)
                    var start_date = dateToYMDEmpty(date);
                    // console.log(start_date)

                    // localStorage.setItem("sTransDate",start_date)

                    $("#fuel_end_date").val(start_date);
                    jQuery('#showDateModalFrom').modal('hide');
                    isDateCorrect('#fuel_start_date','#fuel_end_date')
                }

            }else{
                start_date_dialog.ON_SELECT_FUNC = function(){
                    var date = osmanli_calendar.SELECT_DATE;
                    localStorage.setItem("showFSENDDate",date)
                    var start_date = dateToYMDEmpty(date);
                    console.log(start_date)

                    // localStorage.setItem("sTransDate",start_date)

                    $("#fuel_end_date").val(start_date);
                    jQuery('#showDateModalFrom').modal('hide');
                    isDateCorrect('#fuel_start_date','#fuel_end_date')
                }
            }


            start_date_dialog.init()
            if(date == 1){
                var table_data =  $(".date_table tbody tr").eq(1)
                table_data.children('td').each(function(){
                var data = $(this).html();
                    if(data== 1){
                        $(this).addClass("selected_date")

                    }
                })
            }
            jQuery('#showDateModalFrom').modal('show');
            isDateCorrect('#fuel_start_date','#fuel_end_date')


        //end showTransStartDate
        var EndDate = new Date();

        }

        function show_dialog7(e) {
            // alert("yessss")
            sessionStorage.removeItem("modalTrue");
            sessionStorage.setItem("modalTrue",'showTransStartDate');

            date = new Date();
            start_date_dialog.MAX_DATE = date;
            start_date_dialog.DAYS_DISABLE_MIN = "ON";
            start_date_dialog.DAYS_DISABLE_MAX = "ON";
            start_date_dialog.MIN_DATE = new Date("<?php echo e($approved_at); ?>");
            $('.next-month').off();
            $('.prev-month').off();

            $('.prev-month').click(function () {start_date_dialog.pre_month()});
            $('.next-month').click(function () {start_date_dialog.next_month()});

            start_date_dialog.CURRENT_DATE = new Date();

            if(localStorage.getItem("showFSStartDate")===null)
            {


                start_date_dialog.SELECT_DATE = new Date()
            } else{
                var loclaaa=  localStorage.getItem("showFSStartDate");
                // console.log( loclaaa)
                // start_date_dialog.CURRENT_DATE =new Date(localStorage.getItem("showH2StartDate"));
                start_date_dialog.SELECT_DATE = new Date(localStorage.getItem("showFSStartDate"))
                start_date_dialog.CURRENT_DATE = new Date(localStorage.getItem("showFSStartDate"))


                // console.log(start_date_dialog.SELECT_DATE)
            }
                var date =   start_date_dialog.SELECT_DATE.getDate();
                const monthNames = ["January", "February", "March", "April", "May", "June",
                "July", "August", "September", "October", "November", "December"
                ];
                var month  =  monthNames[start_date_dialog.SELECT_DATE .getMonth()];
                var year = start_date_dialog.SELECT_DATE.getFullYear();
                var select_moth_year  =  month+" "+year
                var date =   start_date_dialog.SELECT_DATE.getDate();
                sessionStorage.setItem("date_check",date);
                sessionStorage.setItem("select_moth_year",select_moth_year);

            if(date == 1){
                start_date_dialog.CURRENT_DATE.setDate(4)
                start_date_dialog.ON_SELECT_FUNC = function(){
                    var date = osmanli_calendar.SELECT_DATE;
                    localStorage.setItem("showFSStartDate",date)
                    var start_date = dateToYMDEmpty(date);
                    // console.log(start_date)

                    // localStorage.setItem("sTransDate",start_date)

                    $("#fuel_start_date").val(start_date);
                    jQuery('#showDateModalFrom').modal('hide');
                    isDateCorrect('#fuel_start_date','#fuel_end_date')
                }

            }else{
                start_date_dialog.ON_SELECT_FUNC = function(){
                    var date = osmanli_calendar.SELECT_DATE;
                    localStorage.setItem("showFSStartDate",date)
                    var start_date = dateToYMDEmpty(date);
                    console.log(start_date)

                    // localStorage.setItem("sTransDate",start_date)

                    $("#fuel_start_date").val(start_date);
                    jQuery('#showDateModalFrom').modal('hide');
                    isDateCorrect('#fuel_start_date','#fuel_end_date')
                }
            }


            start_date_dialog.init()
            if(date == 1){
                var table_data =  $(".date_table tbody tr").eq(1)
                table_data.children('td').each(function(){
                var data = $(this).html();
                    if(data== 1){
                        $(this).addClass("selected_date")

                    }
                })
            }
            jQuery('#showDateModalFrom').modal('show');
            isDateCorrect('#fuel_start_date','#fuel_end_date')

        //end showTransStartDate
        var EndDate = new Date();

        }

        // Ev Dialog
        function ev_start_dialog(e) {
            // alert("yessss")
            sessionStorage.removeItem("modalTrue");
            sessionStorage.setItem("modalTrue",'showTransStartDate');

            date = new Date();
            start_date_dialog.MAX_DATE = date;
            start_date_dialog.DAYS_DISABLE_MIN = "ON";
            start_date_dialog.DAYS_DISABLE_MAX = "ON";
            start_date_dialog.MIN_DATE = new Date("<?php echo e($approved_at); ?>");
            $('.next-month').off();
            $('.prev-month').off();

            $('.prev-month').click(function () {start_date_dialog.pre_month()});
            $('.next-month').click(function () {start_date_dialog.next_month()});

            start_date_dialog.CURRENT_DATE = new Date();

            if(localStorage.getItem("showEVStartDate")===null)
            {


                start_date_dialog.SELECT_DATE = new Date()
            } else{
                var loclaaa=  localStorage.getItem("showEVStartDate");
                // console.log( loclaaa)
                // start_date_dialog.CURRENT_DATE =new Date(localStorage.getItem("showH2StartDate"));
                start_date_dialog.SELECT_DATE = new Date(localStorage.getItem("showEVStartDate"))
                start_date_dialog.CURRENT_DATE = new Date(localStorage.getItem("showEVStartDate"))


                // console.log(start_date_dialog.SELECT_DATE)
            }
                var date =   start_date_dialog.SELECT_DATE.getDate();
                const monthNames = ["January", "February", "March", "April", "May", "June",
                "July", "August", "September", "October", "November", "December"
                ];
                var month  =  monthNames[start_date_dialog.SELECT_DATE .getMonth()];
                var year = start_date_dialog.SELECT_DATE.getFullYear();
                var select_moth_year  =  month+" "+year
                var date =   start_date_dialog.SELECT_DATE.getDate();
                sessionStorage.setItem("date_check",date);
                sessionStorage.setItem("select_moth_year",select_moth_year);

            if(date == 1){
                start_date_dialog.CURRENT_DATE.setDate(4)
                start_date_dialog.ON_SELECT_FUNC = function(){
                    var date = osmanli_calendar.SELECT_DATE;
                    localStorage.setItem("showEVStartDate",date)
                    var start_date = dateToYMDEmpty(date);
                    // console.log(start_date)

                    // localStorage.setItem("sTransDate",start_date)

                    $("#ev_start_date").val(start_date);
                    jQuery('#showDateModalFrom').modal('hide');
                    isDateCorrect('#ev_start_date','#ev_end_date')
                }

            }else{
                start_date_dialog.ON_SELECT_FUNC = function(){
                    var date = osmanli_calendar.SELECT_DATE;
                    localStorage.setItem("showEVStartDate",date)
                    var start_date = dateToYMDEmpty(date);
                    console.log(start_date)

                    // localStorage.setItem("sTransDate",start_date)

                    $("#ev_start_date").val(start_date);
                    jQuery('#showDateModalFrom').modal('hide');
                    isDateCorrect('#ev_start_date','#ev_end_date')
                }
            }


            start_date_dialog.init()
            if(date == 1){
                var table_data =  $(".date_table tbody tr").eq(1)
                table_data.children('td').each(function(){
                var data = $(this).html();
                    if(data== 1){
                        $(this).addClass("selected_date")

                    }
                })
            }
            jQuery('#showDateModalFrom').modal('show');
            isDateCorrect('#ev_start_date','#ev_end_date')

        //end showTransStartDate
        var EndDate = new Date();
        }
        function ev_end_dialog(e) {
            // alert("yessss")
            sessionStorage.removeItem("modalTrue");
            sessionStorage.setItem("modalTrue",'showTransStartDate');

            date = new Date();
            start_date_dialog.MAX_DATE = date;
            start_date_dialog.DAYS_DISABLE_MIN = "ON";
            start_date_dialog.DAYS_DISABLE_MAX = "ON";
            start_date_dialog.MIN_DATE = new Date("<?php echo e($approved_at); ?>");

            if(localStorage.getItem("showEVStartDate") == null){
                start_date_dialog.MIN_DATE = new Date ()
            }else{
                start_date_dialog.MIN_DATE = new Date (localStorage.getItem("showEVStartDate"))
                start_date_dialog.MIN_DATE.setDate(start_date_dialog.MIN_DATE.getDate())

            }
            $('.next-month').off();
            $('.prev-month').off();

            $('.prev-month').click(function () {start_date_dialog.pre_month()});
            $('.next-month').click(function () {start_date_dialog.next_month()});

            start_date_dialog.CURRENT_DATE = new Date();

            if(localStorage.getItem("showEndEVStartDate")===null)
            {


                start_date_dialog.SELECT_DATE = new Date()
            } else{
                var loclaaa=  localStorage.getItem("showEndEVStartDate");
                // console.log( loclaaa)
                // start_date_dialog.CURRENT_DATE =new Date(localStorage.getItem("showH2StartDate"));
                start_date_dialog.SELECT_DATE = new Date(localStorage.getItem("showEndEVStartDate"))
                start_date_dialog.CURRENT_DATE = new Date(localStorage.getItem("showEndEVStartDate"))


                // console.log(start_date_dialog.SELECT_DATE)
            }
                var date =   start_date_dialog.SELECT_DATE.getDate();
                const monthNames = ["January", "February", "March", "April", "May", "June",
                "July", "August", "September", "October", "November", "December"
                ];
                var month  =  monthNames[start_date_dialog.SELECT_DATE .getMonth()];
                var year = start_date_dialog.SELECT_DATE.getFullYear();
                var select_moth_year  =  month+" "+year
                var date =   start_date_dialog.SELECT_DATE.getDate();
                sessionStorage.setItem("date_check",date);
                sessionStorage.setItem("select_moth_year",select_moth_year);

            if(date == 1){
                start_date_dialog.CURRENT_DATE.setDate(4)
                start_date_dialog.ON_SELECT_FUNC = function(){
                    var date = osmanli_calendar.SELECT_DATE;
                    localStorage.setItem("showEndEVStartDate",date)
                    var start_date = dateToYMDEmpty(date);
                    // console.log(start_date)

                    // localStorage.setItem("sTransDate",start_date)

                    $("#ev_end_date").val(start_date);
                    jQuery('#showDateModalFrom').modal('hide');
                    isDateCorrect('#ev_start_date','#ev_end_date')
                }

            }else{
                start_date_dialog.ON_SELECT_FUNC = function(){
                    var date = osmanli_calendar.SELECT_DATE;
                    localStorage.setItem("showEndEVStartDate",date)
                    var start_date = dateToYMDEmpty(date);
                    console.log(start_date)

                    // localStorage.setItem("sTransDate",start_date)

                    $("#ev_end_date").val(start_date);
                    jQuery('#showDateModalFrom').modal('hide');
                    isDateCorrect('#ev_start_date','#ev_end_date')
                }
            }


            start_date_dialog.init()
            if(date == 1){
                var table_data =  $(".date_table tbody tr").eq(1)
                table_data.children('td').each(function(){
                var data = $(this).html();
                    if(data== 1){
                        $(this).addClass("selected_date")

                    }
                })
            }
            jQuery('#showDateModalFrom').modal('show');
            isDateCorrect('#ev_start_date','#ev_end_date')

        //end showTransStartDate
        var EndDate = new Date();
        }

        // outdoor_payment_terminal_start
        function outdoor_payment_terminal_start(e) {
            // alert("yessss")
            sessionStorage.removeItem("modalTrue");
            sessionStorage.setItem("modalTrue",'showTransStartDate');

            date = new Date();
            start_date_dialog.MAX_DATE = date;
            start_date_dialog.DAYS_DISABLE_MIN = "ON";
            start_date_dialog.DAYS_DISABLE_MAX = "ON";
            start_date_dialog.MIN_DATE = new Date("<?php echo e($approved_at); ?>");
            $('.next-month').off();
            $('.prev-month').off();

            $('.prev-month').click(function () {start_date_dialog.pre_month()});
            $('.next-month').click(function () {start_date_dialog.next_month()});

            start_date_dialog.CURRENT_DATE = new Date();

            if(localStorage.getItem("showOptStartDate")===null)
            {


                start_date_dialog.SELECT_DATE = new Date()
            } else{
                var loclaaa=  localStorage.getItem("showOptStartDate");
                // console.log( loclaaa)
                // start_date_dialog.CURRENT_DATE =new Date(localStorage.getItem("showH2StartDate"));
                start_date_dialog.SELECT_DATE = new Date(localStorage.getItem("showOptStartDate"))
                start_date_dialog.CURRENT_DATE = new Date(localStorage.getItem("showOptStartDate"))


                // console.log(start_date_dialog.SELECT_DATE)
            }
                var date =   start_date_dialog.SELECT_DATE.getDate();
                const monthNames = ["January", "February", "March", "April", "May", "June",
                "July", "August", "September", "October", "November", "December"
                ];
                var month  =  monthNames[start_date_dialog.SELECT_DATE .getMonth()];
                var year = start_date_dialog.SELECT_DATE.getFullYear();
                var select_moth_year  =  month+" "+year
                var date =   start_date_dialog.SELECT_DATE.getDate();
                sessionStorage.setItem("date_check",date);
                sessionStorage.setItem("select_moth_year",select_moth_year);

            if(date == 1){
                start_date_dialog.CURRENT_DATE.setDate(4)
                start_date_dialog.ON_SELECT_FUNC = function(){
                    var date = osmanli_calendar.SELECT_DATE;
                    localStorage.setItem("showOptStartDate",date)
                    var start_date = dateToYMDEmpty(date);
                    // console.log(start_date)

                    // localStorage.setItem("sTransDate",start_date)

                    $("#opt_start_date").val(start_date);
                    jQuery('#showDateModalFrom').modal('hide');
                    isDateCorrect('#opt_start_date','#opt_end_date')
                }

            }else{
                start_date_dialog.ON_SELECT_FUNC = function(){
                    var date = osmanli_calendar.SELECT_DATE;
                    localStorage.setItem("showOptStartDate",date)
                    var start_date = dateToYMDEmpty(date);
                    console.log(start_date)

                    // localStorage.setItem("sTransDate",start_date)

                    $("#opt_start_date").val(start_date);
                    jQuery('#showDateModalFrom').modal('hide');
                    isDateCorrect('#opt_start_date','#opt_end_date')
                }
            }


            start_date_dialog.init()
            if(date == 1){
                var table_data =  $(".date_table tbody tr").eq(1)
                table_data.children('td').each(function(){
                var data = $(this).html();
                    if(data== 1){
                        $(this).addClass("selected_date")

                    }
                })
            }
            jQuery('#showDateModalFrom').modal('show');
            isDateCorrect('#opt_start_date','#opt_end_date')

        //end showTransStartDate
        var EndDate = new Date();
        }

        function outdoor_payment_terminal_end(e) {
            // alert("yessss")
            sessionStorage.removeItem("modalTrue");
            sessionStorage.setItem("modalTrue",'showTransStartDate');

            date = new Date();
            start_date_dialog.MAX_DATE = date;
            start_date_dialog.DAYS_DISABLE_MIN = "ON";
            start_date_dialog.DAYS_DISABLE_MAX = "ON";
            start_date_dialog.MIN_DATE = new Date("<?php echo e($approved_at); ?>");

            if(localStorage.getItem("showOptStartDate") == null){
                start_date_dialog.MIN_DATE = new Date ()
            }else{
                start_date_dialog.MIN_DATE = new Date (localStorage.getItem("showOptStartDate"))
                start_date_dialog.MIN_DATE.setDate(start_date_dialog.MIN_DATE.getDate())

            }
            $('.next-month').off();
            $('.prev-month').off();

            $('.prev-month').click(function () {start_date_dialog.pre_month()});
            $('.next-month').click(function () {start_date_dialog.next_month()});

            start_date_dialog.CURRENT_DATE = new Date();

            if(localStorage.getItem("endOptDate")===null)
            {


                start_date_dialog.SELECT_DATE = new Date()
            } else{
                var loclaaa=  localStorage.getItem("endOptDate");
                // console.log( loclaaa)
                // start_date_dialog.CURRENT_DATE =new Date(localStorage.getItem("showH2StartDate"));
                start_date_dialog.SELECT_DATE = new Date(localStorage.getItem("showOptStartDate"))
                start_date_dialog.CURRENT_DATE = new Date(localStorage.getItem("showOptStartDate"))


                // console.log(start_date_dialog.SELECT_DATE)
            }
                var date =   start_date_dialog.SELECT_DATE.getDate();
                const monthNames = ["January", "February", "March", "April", "May", "June",
                "July", "August", "September", "October", "November", "December"
                ];
                var month  =  monthNames[start_date_dialog.SELECT_DATE .getMonth()];
                var year = start_date_dialog.SELECT_DATE.getFullYear();
                var select_moth_year  =  month+" "+year
                var date =   start_date_dialog.SELECT_DATE.getDate();
                sessionStorage.setItem("date_check",date);
                sessionStorage.setItem("select_moth_year",select_moth_year);

            if(date == 1){
                start_date_dialog.CURRENT_DATE.setDate(4)
                start_date_dialog.ON_SELECT_FUNC = function(){
                    var date = osmanli_calendar.SELECT_DATE;
                    localStorage.setItem("showOptStartDate",date)
                    var start_date = dateToYMDEmpty(date);
                    // console.log(start_date)

                    // localStorage.setItem("sTransDate",start_date)

                    $("#opt_end_date").val(start_date);
                    jQuery('#showDateModalFrom').modal('hide');
                    isDateCorrect('#opt_start_date','#opt_end_date')
                }

            }else{
                start_date_dialog.ON_SELECT_FUNC = function(){
                    var date = osmanli_calendar.SELECT_DATE;
                    localStorage.setItem("endOptDate",date)
                    var start_date = dateToYMDEmpty(date);
                    console.log(start_date)

                    // localStorage.setItem("sTransDate",start_date)

                    $("#opt_end_date").val(start_date);
                    jQuery('#showDateModalFrom').modal('hide');
                    isDateCorrect('#opt_start_date','#opt_end_date')
                }
            }


            start_date_dialog.init()
            if(date == 1){
                var table_data =  $(".date_table tbody tr").eq(1)
                table_data.children('td').each(function(){
                var data = $(this).html();
                    if(data== 1){
                        $(this).addClass("selected_date")

                    }
                })
            }
            jQuery('#showDateModalFrom').modal('show');
            isDateCorrect('#opt_start_date','#opt_end_date')

        //end showTransStartDate
        var EndDate = new Date();
        }
    */

    function dateToYMDEmpty(date) {
        var strArray=['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
             'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        var d = date.getDate();
        var m = strArray[date.getMonth()];
        var y = date.getFullYear().toString().substr(-2);
        var currentHours = date.getHours();
        return '' + (d <= 9 ? '0' + d : d) + '' + m + '' + y ;
    }

    var end_date_dialog = osmanli_calendar;


    function show_dialog5() {

        // alert("yessss")
        sessionStorage.removeItem("modalTrue");
        sessionStorage.setItem("modalTrue",'showTransStartDate');

        date = new Date();
        start_date_dialog.MAX_DATE = date;
        start_date_dialog.DAYS_DISABLE_MIN = "ON";
        start_date_dialog.DAYS_DISABLE_MAX = "ON";
        start_date_dialog.MIN_DATE = new Date("<?php echo e($approved_at); ?>");


        if(localStorage.getItem("showH2StartDate") == null){
            start_date_dialog.MIN_DATE = new Date ()
        }else{
            start_date_dialog.MIN_DATE = new Date (localStorage.getItem("showH2StartDate"))
            start_date_dialog.MIN_DATE.setDate(start_date_dialog.MIN_DATE.getDate())

        }
        $('.next-month').off();
        $('.prev-month').off();

        $('.prev-month').click(function () {start_date_dialog.pre_month()});
        $('.next-month').click(function () {start_date_dialog.next_month()});

        start_date_dialog.CURRENT_DATE = new Date();

        if(localStorage.getItem("endH2Date")===null)
        {


            start_date_dialog.SELECT_DATE = new Date()
        } else{
            var loclaaa=  localStorage.getItem("endH2Date");
            // console.log( loclaaa)
            // start_date_dialog.CURRENT_DATE =new Date(localStorage.getItem("showH2StartDate"));
            start_date_dialog.SELECT_DATE = new Date(localStorage.getItem("endH2Date"))
            start_date_dialog.CURRENT_DATE = new Date(localStorage.getItem("endH2Date"))


            // console.log(start_date_dialog.SELECT_DATE)
        }
            var date =   start_date_dialog.SELECT_DATE.getDate();
            const monthNames = ["January", "February", "March", "April", "May", "June",
            "July", "August", "September", "October", "November", "December"
            ];
            var month  =  monthNames[start_date_dialog.SELECT_DATE .getMonth()];
            var year = start_date_dialog.SELECT_DATE.getFullYear();
            var select_moth_year  =  month+" "+year
            var date =   start_date_dialog.SELECT_DATE.getDate();
            sessionStorage.setItem("date_check",date);
            sessionStorage.setItem("select_moth_year",select_moth_year);

        if(date == 1){
            start_date_dialog.CURRENT_DATE.setDate(4)
            start_date_dialog.ON_SELECT_FUNC = function(){
                var date = osmanli_calendar.SELECT_DATE;
                localStorage.setItem("endH2Date",date)
                var start_date = dateToYMDEmpty(date);
                // console.log(start_date)

                // localStorage.setItem("sTransDate",start_date)

                $("#h2_end_date").val(start_date);
                jQuery('#showDateModalFrom').modal('hide');
                isDateCorrect('#h2_start_date','#h2_end_date')
            }

        }else{
            start_date_dialog.ON_SELECT_FUNC = function(){
                var date = osmanli_calendar.SELECT_DATE;
                localStorage.setItem("endH2Date",date)
                var start_date = dateToYMDEmpty(date);
                console.log(start_date)

                // localStorage.setItem("sTransDate",start_date)

                $("#h2_end_date").val(start_date);
                jQuery('#showDateModalFrom').modal('hide');
                isDateCorrect('#h2_start_date','#h2_end_date')
            }
        }
        start_date_dialog.init()

        if(date == 1){
            var table_data =  $(".date_table tbody tr").eq(1)
            table_data.children('td').each(function(){
            var data = $(this).html();
                if(data== 1){
                    $(this).addClass("selected_date")

                }
            })


        }

        jQuery('#showDateModalFrom').modal('show');
        isDateCorrect('#h2_start_date','#h2_end_date')

    //end showTransStartDate
    var EndDate = new Date();

    }



    function onDateSelect_to(selectedDate) {
        if (selectedDate == null) {
            return false;
        }

        const todaysDate = new Date();
        var selectedFinalDate = (selectedDate.getDate() < 10 ? '0' : '') + selectedDate.getDate();
        var selectedFullYear = selectedDate.getFullYear().toString();
        selectedFullYear = selectedFullYear.match(/\d{2}$/);
        $('#date_to').val(selectedFinalDate + selectedDate.toLocaleString('en-us',
        {month: 'short'}) + selectedFullYear);
        jQuery('#showDateModalFrom').modal('hide');
        date_filter();
    }

    function isDateCorrect(start,end){

        var date1 = new Date($(start).val());
        var date2 = new Date($(end).val());
        if(date2.getTime() < date1.getTime()){

            let msg = "The end date should not be earlier than the start date. Please try again.";
            messageModal(msg);
            $(end).val(dateToYMDEmpty(new Date()));
        }else{
            jQuery('#showDateModalFrom').modal('show');
        }
    }

    function messageModal(msg)
    {
            $('#modalMessage').modal('show');
            $('#statusModalLabelMsg').html(msg);
            setTimeout(function(){
                $('#modalMessage').modal('hide');
            }, 4500);
    }

    function downloadPDF(){

        let spinner = $('#btnFetch2').find('span')
            spinner.removeClass('d-none')

        $("#form1").submit(e =>{
            e.preventDefault();
        })
        var start = $("#cs_start_date").val();
        var end = $("#cs_end_date").val();

        data = {
			"start_date":start,
			"end_date":end
        };
		console.log('****-----the print pdf')
        console.log(data);

        $.ajax({
			url: "<?php echo e(route('sales.cstore.report.print.pdf')); ?>",
            type: "POST",
            'headers': {
			  'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
			},
            data:data,
                xhrFields: {
                responseType: 'blob'
            },
            success: function(response){
                var blob = new Blob([response]);
                var link = document.createElement('a');
                link.href = window.URL.createObjectURL(blob);
                link.download = "ConvenienceStoreSales.pdf";
                link.click();
                spinner.addClass('d-none')
            },
            error: function(blob){
                console.log(blob);
            }
		})

    }
     function downloadPDF2(){

        let spinner = $('#btnFetch4').find('span')
            spinner.removeClass('d-none')

        $("#form2").submit(e =>{
            e.preventDefault();
        })
        var start = $("#fuel_start_date").val();
        var end = $("#fuel_end_date").val();

        data = {
			"fuel_start_date":start,
			"fuel_end_date":end
        };
		console.log('****-----the print pdf')
        console.log(data);

        $.ajax({
			url: "<?php echo e(route('fuel_summary')); ?>",
            type: "POST",
            'headers': {
			  'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
			},
            data:data,
                xhrFields: {
                responseType: 'blob'
            },
            success: function(response){
                var blob = new Blob([response]);
                var link = document.createElement('a');
                link.href = window.URL.createObjectURL(blob);
                link.download =  "FuelSummaryPDF.pdf";
                link.click();
                spinner.addClass('d-none')
            },
            error: function(blob){
                console.log(blob);
            }
		})

    }

</script>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('common.footer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->make('common.web', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/emmanuel/oceania/trunk/oceania/resources/views/sales_report/sales_report.blade.php ENDPATH**/ ?>