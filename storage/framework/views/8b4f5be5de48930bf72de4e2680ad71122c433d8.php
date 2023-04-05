<style>
button:focus {
	box-shadow: none !important;
}
</style>
<div class="container-fluid">
<div class="row">
	<div class="col-md-12 mt-1 mb-1 pt-1 bg-light"
		style="padding-left:30px">

		<button class="btn btn-sq-lg p-0"
			id="top-pinkcrab-btn" style=""
            onclick="reloadPage()">
			<img style="height:70px;width:70px;
				focus:ring-0;focus:border-transparent;
				object-fit:contain"
			src="<?php echo e(asset('images/pinkcrab_50x50.png')); ?>"/>
		</button>

		<button class="btn btn-sq-lg poa-closetab-button"
			id="top-closetab-btn"
			style="color:white;margin-right:15px;float:right;"
			onclick="window.close()">
			<i style="margin-top:-8px;padding-left:0;padding-right:0;
				font-size:80px" class="fa fa-times-thin"></i>
		</button>

	</div>

</div>
<script>
function reloadPage() {
	window.location.reload();
}
</script>
<?php /**PATH /home/emmanuel/oceania/trunk/oceania/resources/views/common/menubuttons.blade.php ENDPATH**/ ?>