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
            <th>Receiving Note</th>
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
            <th style="background-color: #000000 ;color:white ; font-weight:bold ;">Price</th>
            <th style="background-color: #000000 ;color:white ; font-weight:bold ;">Qty</th>
            <th  style="background-color: #000000 ;color:white ; font-weight:bold ;">Cost</th>
             <th style="background-color: #000000 ;color:white ; font-weight:bold ;">CostValue</th>

        </tr>
        </thead>
        <tbody>

            @foreach($receivingNote as $in =>$i)

                <tr>
                    <td style='text-align:center;'>{{ $in + 1}}</td>
                    <td style='text-align:center;'>{{ $i->psystemid }}</td>
                    <td style='text-align:center;'>{{ $i->barcode }}</td>
                    <td>{{ $i->name }}</td>
                    <td  style='text-align:right;' data-format="0.00">{{ $i->price/100 }}</td>
                    <td>{{ $i->qty }}</td>
                    <td  style='text-align:right;' data-format="0.00">{{ $i->cost/100 }}</td>
                    <td  style='text-align:right;' data-format="0.00">{{ $i->costvalue/100 }}</td>
                </tr>
            @endforeach

        </tbody>
    </table>

</body>
</html>
