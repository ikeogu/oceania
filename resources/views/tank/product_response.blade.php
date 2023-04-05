<!-- Popup for select product start -->
<div id="modal" class="modal fade" tabindex="-1" role="dialog"
     aria-hidden="true">
    <div class="modal-dialog modal modal-dialog-centered" style="margin: auto;">
        <div style="border-radius:10px"
             class="modal-content bg-purplelobster">
            <div class="modal-header">
                <h3 style="margin-bottom:0">Select Fuel</h3>
            </div>
            <div class="modal-body" style="">
                <div class="row" style="width:100%">
                    <div class="col-md-12" style="">
                        <div id="productList" class="creditmodelDV"
                             style="padding-left: 5%">
                            @forelse($products as $prod)
                                <div class="row mb-2" style="cursor: pointer" onclick="updateProduct({{$prod->product->id}},{{$tank}})" >
                                    @if (!empty($prod->product->photo_1))
                                        <a href='/{{\App\Http\Controllers\OpenitemController::$IMG_PRODUCT_LINK}}{{$prod->product->systemid}}/{{$prod->product->photo_1}}'
                                           target="_blank">

                                            <img style="background-color:white;object-fit:contain; height: 30px; width: 30px"
                                                 src="/{{\App\Http\Controllers\OpenitemController::$IMG_PRODUCT_LINK}}{{$prod->product->systemid}}/thumb/{{$prod->product->thumbnail_1}}"
                                                 width="40px" height="40px">
                                        </a>
                                    @endif

                                    <div style="font-size: 15pt; padding-left: 2%; padding-top: 0.5%">
                                        @if (!empty($prod->product->name))
                                            {{$prod->product->name}}
                                        @else
                                            Product Name
                                        @endif
                                    </div>

                                </div>

                                @empty

                                <div style="font-size: 15pt; padding-left: 2%; padding-top: 0.5%">
                                    No products found
                                </div>

                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Popup for select product end -->




<div class="modal fade" id="fillFields" tabindex="-1" role="dialog">
    <div class="modal-dialog  modal-dialog-centered mw-75 w-50">

        <!-- Modal content-->
        <div class="modal-content  modal-inside bg-purplelobster">
            <div class="modal-header" style="border:none;">&nbsp;</div>
            <div class="modal-body text-center">
                <h5 style="mb-0" id="return_data">
                    Please fill all fields
                </h5>
            </div>
            <div class="modal-footer" style="border:none;">&nbsp;</div>
        </div>

    </div>
</div>

<script type="text/javascript">

    function updateProduct(product,tank) {

        $.ajax({
            url: "{{route('tank.chooseproduct')}}",
            method: "POST",
            data: {tank:tank,product:product},
            success: function (response) {
                console.log("response", response);
                table.ajax.reload();
                $("#modal").modal('hide');
                $("#return_data").html("Product updated successfully");
                $("#fillFields").modal('show');
                setTimeout(function () {
                    $('#fillFields').modal('hide');
                }, 2000);
                //$("#productResponce").html(response);

            }, error: function (e) {
                $("#return_data").html("An error has occured. Try Again");
                $("#fillFields").modal('show');
                setTimeout(function () {
                    $('#fillFields').modal('hide');
                }, 2000);
                //console.log(e.message)
            }
        });
    }
</script>

<style type="text/css">
    .upload-area {
        width: 70%;
        border: 2px solid lightgray;
        border-radius: 3px;
        margin: 0 auto;
        text-align: center;
        overflow: auto;
    }

    .upload-area:hover {
        cursor: pointer;
    }

    .upload-area h1 {
        text-align: center;
        font-weight: normal;
        font-family: sans-serif;
        line-height: 50px;
        color: darkslategray;
    }

    #file {
        display: none;
    }

    /* Thumbnail */
    .thumbnail {
        width: 180px;
        height: 185px;
        padding: 4px;
        border: 2px solid lightgray;
        border-radius: 3px;
        float: left;
    }

    .size {
        font-size: 17px;
        color: #fff;
    }

    #uploadfile > button > i {
        color: #fff
    }

    .green {
        color: #28a745 !important;
    }
</style>
<script type="text/javascript">

</script>
