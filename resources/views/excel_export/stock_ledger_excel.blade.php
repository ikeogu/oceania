<tr>
    <td>Stock Ledger</td>
     <td>{{ date('dMy',strtotime($start_date)) .'-'.  date('dMy',strtotime($stop_date)) }}</td>
</tr>

<table>
  @foreach ($products as $product)
    <tr>
        <td style="font-weight: bold;">{{ $product->name }}</td>
    </tr>
    <thead>
        <tr >
            <th style="background-color: #000000; color:white; font-weight:bold;">No</th>
            <th style="background-color: #000000; color:white; font-weight:bold;">Document No</th>
            <th style="background-color: #000000; color:white; font-weight:bold;">Type</th>
            <th style="background-color: #000000; color:white; font-weight:bold;">Last Update</th>
            <th style="background-color: #000000; color:white; font-weight:bold;">Cost</th>
            <th style="background-color: #000000; color:white; font-weight:bold;">Qty</th>

        </tr>
    </thead>

    <tbody>
	@php
		$cnt =0;
		$stock_ledgers = $stock_ledgers->sortBy('last_update');
		$stock_ledgers = $stock_ledgers->reverse();
	@endphp

	@foreach ($stock_ledgers as  $stock_ledger)

		@if($stock_ledger->product_systemid == $product->systemid)
			@php
			$cnt++;
			@endphp
			<tr>
				<td style="align:left; color:#000000;">{{ $cnt }}</td>
				<td>{{ $stock_ledger->systemid}}</td>
				<td>
					@if($stock_ledger->type == 'stockin')
						Stock In
					@elseif($stock_ledger->type == 'stockout')
						Stock Out
					@elseif($stock_ledger->type == 'received')
						Received
                    @elseif($stock_ledger->type == 'returned')
						Returned
					@elseif($stock_ledger->type == 'cash_sales')
						Cash sales
					@endif
				</td>
				<td>{{ date('dMy H:i:s',strtotime($stock_ledger->last_update))}}</td>
				<td data-format="0.00"> {{ $stock_ledger->cost}}</td>
				<td>{{ $stock_ledger->qty }}</td>
			</tr>
		@endif
	@endforeach

    </tbody>
    <tr></tr>
    <tr></tr>
    <tr></tr>
    @endforeach
{{-- remove somerows --}}
</table>
