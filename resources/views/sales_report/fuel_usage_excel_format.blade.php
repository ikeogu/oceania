<table>
    <tr>
        <td>Receipt ID</td>
        <td>Product ID</td>
        <td>Vehicle ID</td>
        <td>Initial Totalizer</td>
        <td>Final Totalizer</td>
        <td>Time Started</td>
        <td>Time Completed</td>
        <td>Filled Amount</td>
        <td>Filled Volume</td>
        <td>Location</td>
        <td>Terminal ID</td>
        <td>Staff Name</td>

    </tr>

    @foreach($data as $dat)

        <tr>

            <td>{{$dat->systemid}}</td>
            <td>{{$dat->product->systemid}}</td>

            <td>{{$dat->vehicle_id}}</td>

            <td>{{$dat->initial_totalizer}}</td>

            <td>{{$dat->final_totalizer}}</td>

            <td>{{$dat->time_started}}</td>

            <td>{{$dat->time_completed}}</td>
            <td>{{$dat->filled_amount}}</td>
            <td>{{$dat->filled_volume}}</td>

            <td>{{$dat->location->name}}</td>

                <td>{{$dat->terminal}}</td>


            <td>{{$dat->user->fullname}}</td>

        </tr>

    @endforeach

</table>