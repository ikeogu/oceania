<style type="text/css">
.s.bd-highlight {
	width: 13%;
}
.btn-slim-prawn:focus,
.btn-slim-prawn:hover {
	color: #34dabb;
	background-color: transparent;
	border-radius: 5px;
	outline: none;
	border:1px solid #34dabb;
	padding-top:2px !important;
	padding-bottom:2px !important;
}
.btn-slim-prawn {
	color: white;
	background-color: transparent;
	border-radius: 5px;
	outline: none;
	border:1px solid white;
	padding-top:2px !important;
	padding-bottom:2px !important;
}
</style>

<nav class="navbar navbar-light bg-light p-0 align-items-center" 
	style="display:flex;
		background-image:linear-gradient(rgb(38, 8, 94),rgb(86, 49, 210));">

    <div class="navbar-text  ml-0 pl-3 w-100 pr-4 align-items-center"
		style="color: white;display:flex;padding-bottom:6px">
		<div class="row col-md-11 align-items-center d-flex">
		<div class="col-md-3 pl-0">
        <img src="{{ asset('images/small_logo.png') }}" alt=""
			style="object-fit:contain;width: 20px; height: 20px;
			cursor: pointer;" srcset="" class="mr-1"

		@if (request()->session()->has('ONLY_ONE_HOST'))
			onclick="location.href='{{route('main.view.onehost')}}';">
		<span onclick="location.href='{{route('main.view.onehost')}}';"
		@else
			onclick="location.href='{{route('main.view')}}';">
		<span onclick="location.href='{{route('main.view')}}';"
		@endif
			style="position:relative;top:2px;left:-4.5px;cursor: pointer;">
			<b>OPOSsum</b>
		</span>

        <span style="position:relative;top:2px;margin-left:50px">
			<b></b>
		</span>

        @if (isset($pagename))
            @if ($pagename=="screen_d")
            <button style="position:relative;right:10px;top:0"
				class="navbar-nav ml-auto mb-0 mr-0
				poa-closetab-button-sm">
   			<i onclick="window.close()"
				style="position:relative;top:-2px;
				font-size:25px" class="closetab fa fa-times-thin">
			</i>
			</button>
            @endif
        @endif
    </div>
    
	
    <!-- <div class="col-md-9 " style=" padding-left: 100px" >
		<div class="d-flex flex-row bd-highlight align-items-center">
			<div class="pl-0 pr-2 s bd-highlight" style="width: 7%;">
				Lot&nbsp;No
			</div>
			<div class="bd-highlight text-center"
				style="padding-left:0; width: 17%;">
				Lot&nbsp;ID
			</div>
			<div class="pl-4 pr-0 bd-highlight text-center"
				style="width: 21%;">
				In
			</div>
			<div class="pl-4 pr-0 bd-highlight text-center"
				style="width: 20%;">
				Out
			</div>
			<div class="pl-4 pr-0 bd-highlight text-center"
				style="width: 16%;">
				Charger
			</div>
			<div class="pl-4 pr-0 bd-highlight text-center"
				style="width: 12%;">
				@if( $current_setting_mode == 'hour' )
					Hour
				@else
					kWh
				@endif
			</div>
			<div class="pl-1 bd-highlight text-center"
				 style="width:10%;">
				Rate
			</div>
			<div class="pl-0 pr-0 bd-highlight text-center"
				style="width:11%;">
				{{empty($terminal->currency) ? 'MYR': $terminal->currency }}
			</div>
			<div class="pl-1 bd-highlight text-center"
				 style="width:18%;">
				Status
			</div>
			<div id="hour-mode" class="text-center pl-2 pr-0 s bd-highlight"
				style="padding-right: 15px !important;width:10%;">
				@if( $current_setting_mode == 'hour' )
					/Hour Mode
				@else 
					kWh Mode
				@endif
			</div>
		</div>
	</div> -->
	</div>
	<div class="bd-highlight pl-5" style="white-space: nowrap; margin-left:20px">
				@if( $current_setting_mode == 'hour' )
					/Hour Mode
				@else 
					kWh Mode
				@endif
	</div>
    </div>
</nav>
