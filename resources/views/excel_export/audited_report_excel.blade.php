<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta http-equiv=\"Content-Type\" content=\"text/html;charset=utf-8\">
</head>
<body>

    <table>

        <thead>
        <tr>
            <th>Audited Report</th>
        </tr>
        <tr>
            <td>Document No</td>
            <td>{{ $docId }}</td>
        <tr>

        </thead>
        <thead>
        <tr>
            <th style="text-align:center;background-color: #000000 ;color:white ; font-weight:bold ;">No</th>
            <th style="background-color: #000000 ;color:white ; font-weight:bold ;">Product ID</th>
            <th style="background-color: #000000 ;color:white ; font-weight:bold ;">Barcode</th>
            <th style="background-color: #000000 ;color:white ; font-weight:bold ;">Product Name</th>
            <th style="background-color: #000000 ;color:white ; font-weight:bold ;">Qty</th>
            <th style="background-color: #000000 ;color:white ; font-weight:bold ;">Audited Qty</th>
            <th style="background-color: #000000 ;color:white ; font-weight:bold ;">Difference</th>
            <th style="background-color: #000000 ;color:white ; font-weight:bold ;">Stock In</th>
            <th style="background-color: #000000 ;color:white ; font-weight:bold ;">Stock Out</th>
        </tr>
        </thead>
        <tbody>

            @foreach($auditedReport as $in =>$i)
            @php
                $cnt = 2 + $in;
            @endphp
            <tr>
                <td style='text-align:center;'>{{ $in + 1}}</td>
                <td style='text-align:center;'>{{ $i->psystemid }}</td>
                <td style='text-align:center;'>{{ $i->barcode }}</td>
                <td>{{ $i->name }}</td>
               <td class="text-center">
                    {{number_format($i->qty)}}
                </td>
                <td class="text-center">
                    {{number_format($i->audited_qty)}}
                </td>
                 <td class="text-center">
                    {{number_format($i->audited_qty - $i->qty)}}
                </td>

                <td class="text-center"
                    style="">
                    @if(($i->audited_qty - $i->qty) >=0)
                            {{number_format($i->audited_qty - $i->qty)}}
                    @else
                        0
                    @endif
                </td>
                <td class="text-center"
                    style="">
                    @if(($i->audited_qty - $i->qty) <=0)
                        {{number_format($i->audited_qty - $i->qty)}}
                    @else
                        0
                    @endif
                </td>
            </tr>
        @endforeach

        </tbody>
    </table>

</body>
</html>
