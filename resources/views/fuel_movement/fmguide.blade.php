@extends('common.web')

@include('common.header')
@include('common.menubuttons')

@section('content')
<div class="container-fluid">
<div class="row py-2"
	 style="padding-bottom:0px !important;padding-top:0px !important;">

	<div class="col-md-12 align-items-center"
		style="display:flex;height:75px">
		<h2 class="mb-0">
			Fuel Movement: Guide
		</h2>
	</div>
	<div class="col-md-12">
	<ol style="font-size:18px">
		<li>C/Forward
			<ul>
				<li>C/Forward is carry forward = opening stock entered only when system is started to be used.
				</li>
				<li>Second row onwards is yesterday's tank dip figure.
				</li>
			</ul>
		</li>
		<li>Sales (&ell;)
			<ul>
				<li>Sales (&ell;) is sales incurred based on litres.
				</li>
				<li>Click sales figure to access Fuel Movement: Product Ledger: Sales.
				</li>
			</ul>
		</li>
		<li>Receipt
			<ul>
				<li>This involves receiving and adjustment; i.e. Stock In and Stock Out.
				</li>
			</ul>
		</li>
		<li>Book
			<ul>
				<li style="line-height:1.3">
				This is the book value with the formula:<br>
				Book = C/Forward &minus; Sales + Receipt
				</li>
			</ul>
		</li>
		<li>Tank Dip
			<ul>
				<li>Tank Dip is a manually keyed in figure, in the absence of
				Auto Tank Gauge.
				</li>
				<li>Auto Tank Gauge will display a periodic figure.
				</li>
			</ul>
		</li>
		<li>Daily Variance
			<ul>
				<li>Daily Variance = Tank Dip &minus; Book
				</li>
			</ul>
		</li>
		<li>Cumulative
			<ul>
				<li>
				Today's Cumulative = Yesterday's Cumulative + Today's Daily Variance
				</li>
			</ul>
		</li>
		<li>%
			<ul>
				<li style="line-height:1.3">
				% represents the precentage of cumulative based on book value:<br>
				% = (Cumulative / Book) &times; 100
				</li>
			</ul>
		</li>
	<ol>
	<br>
	</div>
</div>
</div>
@endsection

@include('common.footer')

