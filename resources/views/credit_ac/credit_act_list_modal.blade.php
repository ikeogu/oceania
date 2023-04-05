@foreach($getoneway as $company)
	@php
	$company_id = $company->id;
	@endphp

	<div class="col-md-12 ml-0 pl-0">
		<div class="row align-items-center d-flex">
		<div onclick="selectac({{$company_id}})"
			class="col-md-12 pl-3 productselect"
			style="cursor:pointer;line-height:1.2;margin:5px;font-size:20px;padding-top:0;text-align: left;">

			{{ $company->company_name }}
		</div>
		</div>
	</div><br>
@endforeach 

@foreach($getCompany as $company)
	@php
	$company_id = $company->id;
	@endphp

	<div class="col-md-12 ml-0 pl-0">
		<div class="row align-items-center d-flex">
		<div onclick="selectac({{$company_id}})"
			class="col-md-12 pl-3 productselect"
			style="cursor:pointer;line-height:1.2;margin:5px;font-size:20px;padding-top:0;text-align: left;">
			{{ $company->name }}
		</div>
		</div>
	</div><br>
@endforeach 
