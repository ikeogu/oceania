<nav class="navbar navbar-light bg-light p-0"
	style="background-image:linear-gradient(rgb(38, 8, 94),rgb(86, 49, 210)); z-index:1050">

    <div class="navbar-text ml-0 pl-3 align-items-center w-100"
		style="color: white;display:flex">
        <img src="<?php echo e(asset('images/opossum_lgtst-11.png')); ?>" alt=""
			style="object-fit:contain;width: auto; height: 20px;
			cursor: pointer;"
			srcset="" class="mr-1"

<?php if(request()->session()->has('ONLY_ONE_HOST')): ?>
			onclick="location.href='<?php echo e(route('main.view.onehost')); ?>';">
		<span onclick="location.href='<?php echo e(route('main.view.onehost')); ?>';"
<?php else: ?>
			onclick="location.href='<?php echo e(route('main.view')); ?>';">
		<span onclick="location.href='<?php echo e(route('main.view')); ?>';"
<?php endif; ?>
			style="position:relative;top:2px;cursor: pointer;">
			<!--
			<b>OPOSsum</b>
			-->
		</span>

        <span style="position:relative;top:2px;margin-left:50px">
			<b></b>
		</span>

		<div style="width:100%;padding-right:40px" class="float-right">
			<a href="<?php echo e(route('screen.d')); ?>" target="_blank"
				rel="noopener noreferrer">
				<button class="btn poa-bluecrab-button"
					style="float: right !important; margin-bottom:0 !important"
					id="bluecrab_btn" onclick="">
					<img width="25px" src="/images/bluecrab_50x50.png"/>
				</button>
			</a>

			<a href="<?php echo e(route('index.cstore')); ?>" target="_blank"
				rel="noopener noreferrer">
				<button class="btn opos-cstore-button"
					style="float: right !important; margin-bottom:0 !important;
					margin-right:5px !important;
					border-radius: 5px;
					height:25px !important; width:25px !important"
					id="bluecrab_btn" onclick="">
					<img width="18px" src="/images/basket_transparent.png"/>
				</button>
			</a>

			<button class="btn btn-success btn-sq-lg poa-button-drawer"
                style="border-radius:5px; font-size:14px;width:80px!important"
                onclick="open_cashdrawer()">
				<span style="position:relative;top:-5px">Drawer</span>
			</button>

			<button class="btn btn-success btn-sq-lg screend-button
				bg-virtualcabinet float-right align-items-center"
				onclick="window.open('<?php echo e(route('fuel-receipt-list',
					['date'=>date('Y-m-d',strtotime(now()))] )); ?>',
					'_blank')"
				style="margin-left:0 !important;outline:none;
				border-radius:5px; width:120px !important;
				margin-right:10px;
				font-size: 14px; height: 25px !important;">
				<span style="position:relative;top:-5px">Today&nbsp;Cabinet</span>
			</button>


		</div>

        <?php if(isset($pagename)): ?>
            <?php if($pagename=="screen_d"): ?>
            <button style="position:relative;right:10px;top:0"
				class="navbar-nav ml-auto mb-0 mr-0
				poa-closetab-button-sm">
   			<i onclick="window.close()"
				style="position:relative;top:-2px;
				font-size:25px" class="closetab fa fa-times-thin">
			</i>
			</button>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</nav>
<?php /**PATH /home/emmanuel/oceania/trunk/oceania/resources/views/common/header.blade.php ENDPATH**/ ?>