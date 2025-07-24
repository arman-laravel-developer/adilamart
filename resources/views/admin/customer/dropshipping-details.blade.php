@extends('admin.master')

@push('style')
    <style type="text/css">
        .input-group-wrap {
            margin-bottom: 15px;
        }
        .input-group-wrap label {
            font-size: 15px;
            font-weight: 500;
            color: #000;
            margin-bottom: 3px;
        }
        .table td, .table th{
            vertical-align: middle;
            padding: 5px;
        }
    </style>
@endpush

@section('content')
<div class="page-wrapper">
    <div class="page-content">
        <form action="{{ url('/user/dropshipping-order/transfer/'.$order->id) }}" method="post" class="order-details-form form-group">
            @csrf
            <div class="row">
                <div class="col-md-4">
                    <div class="customer-details-wrap card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <strong>Customer Details </strong>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="input-group-wrap">
                                        <label for="invoice">
                                            Invoice <span style="color: red;">*</span>
                                        </label>
                                        <input type="text" name="invoice" class="form-control" value="{{ $order->orderId ?? 'Orderid' }}" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="input-group-wrap">
                                        <label for="c_name">
                                            Customer Name <span style="color: red;">*</span>
                                        </label>
                                        <input type="text" name="name" class="form-control" value="{{ $order->name ?? 'Customer name' }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="input-group-wrap">
                                        <label for="c_phone">
                                            Customer Phone <span style="color: red;">*</span>
                                        </label>
                                        <input type="text" name="phone" class="form-control" value="{{ $order->phone ?? 'No phone' }}">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="input-group-wrap">
                                        <label for="c_address">
                                            Customer Full Address <span style="color: red;">*</span>
                                        </label>
                                        <textarea class="form-control" rows="4" name="address">{{ $order->address ?? 'No address' }}</textarea>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="input-group-wrap">
                                        <label for="pathao_special_note">
                                            Special Notes
                                        </label>
                                        <textarea class="form-control" rows="4" name="pathao_special_note">{{ $order->pathao_special_note ?? 'No Notes' }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="order-details-wrap card">
                        <div class="card-header">
                            <strong>Order Details</strong>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered table-striped">
                                <tr>
                                    <th style="width: 20%;">Name</th>
                                    <th style="width: 20%;">Color</th>
                                    <th style="width: 20%;">Size</th>
                                    <th style="width: 15%;">Qty</th>
                                    <th style="width: 20%;">Price</th>
                                    <th style="width: 5%;">Action</th>
                                </tr>
                                @php
                                    $sum = 0;
                                @endphp
                                @foreach ($order->orderDetails as $orderDetail)
                                    <tr>
                                        <td>
                                            <img src="{{ asset('/product/images/' .$orderDetail->product?->image) }}" height="40" width="40"/><br>
                                            {{ $orderDetail->product?->name ?? 'Product name' }}
                                        </td>
                                        <td>
                                            <select class="form-control" name="color" id="color-{{ $orderDetail?->id }}" onchange="productColor({{ $orderDetail }})">
                                                @if ($orderDetail->color == null || $orderDetail->color == 'No color')
                                                    <option selected disabled>No Color</option>
                                                    @foreach ($orderDetail->product?->colors as $color)
                                                    <option value="{{ $color->color }}">{{ $color->color }}</option>
                                                    @endforeach
                                                @else
                                                    @foreach ($orderDetail->product?->colors as $color)
                                                    <option value="{{ $color->color }}" {{ $color->color == $orderDetail->color ? 'selected' : '' }}>{{ $color->color }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </td>
                                        <td>
                                            <select class="form-control" name="size" id="size-{{ $orderDetail?->id }}" onchange="productSize({{ $orderDetail }})">
                                                @foreach ($orderDetail->product?->sizes as $size)
                                                    @if($size->size != null)
                                                        <option value="{{ $size->size }}" {{ $size->size == $orderDetail->size ? 'selected' : '' }}>{{ $size->size }}</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                           <span class="badge rounded-pill bg-primary">{{ $totalProductQty = $orderDetail?->qty }}</span>
                                           <input type="number" name="qty" id="qty-{{ $orderDetail?->id }}" onblur="productQty({{ $orderDetail }})"  value="" placeholder="Qty" style="width:80px;"/>
                                        </td>
                                        <td>
                                            <input type="number" name="regular_price" id="regular_price-{{ $orderDetail?->id }}" onblur="productPrice({{ $orderDetail }})" value="{{ $total = $orderDetail?->qty * $orderDetail->price }}" class="form-control"/>
                                        </td>
                                        <td>
                                            <a href="{{ url('/order/delete/'.$orderDetail->id) }}" class="btn btn-sm btn-danger delete-btn">
                                                <i class="bx bx-trash-alt"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @php
                                        $sum += $total
                                    @endphp
                                    <input class="form-control" type="hidden" name="per_price" id="per_price" value="{{ $orderDetail->price ?? 'price' }}">
                                @endforeach
                            </table>
                            <label style="padding-bottom: 5px;font-weight: 600;font-size: 15px;letter-spacing: 1px;">Related Product</label>
                            <select class="form-control mb-3" name="related_product_id">
                                <option value="AL">Select A Related Product</option>
                                  @foreach(\App\Models\Product::orderBy('created_at', 'desc')->where('b_product_id', '!=', null)->get() as $product)
                                      <option value="{{ $product->id }}">
                                        {{ $product->name }}
                                      </option>
                                  @endforeach
                            </select>
                            <div class="mt-3 mb-3">
                                <div class="row">
                                    <div class="col-md-6">
                                        <strong>Sub Total</strong>
                                    </div>
                                    <div class="col-md-6">
                                        <input class="form-control" type="number" readonly name="price" id="sub_total" value="{{ $sum }}">
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3 mb-3">
                                <div class="row">
                                    <div class="col-md-6">
                                        <strong>Delivery Charge</strong>
                                    </div>
                                    <div class="col-md-6">
                                        <input class="form-control" type="number" name="area" id="area" onkeyup="" value="{{ $area = $order->area }}">
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3 mb-3">
                                <div class="row">
                                    <div class="col-md-6">
                                        <strong>Charge Type</strong>
                                    </div>
                                    <div class="col-md-6">
                                        <select name="delivery_charge_type" id="delivery_charge_type" class="form-control" required>
                                            <option selected disabled>-- Select delivery charge type --</option>
                                            <option value="COD" @if ($order->delivery_charge_type == "COD") selected @endif>COD</option>
                                            <option value="Advance" @if ($order->delivery_charge_type == "Advance") selected @endif>Advance</option>
                                            <option value="Others" @if ($order->delivery_charge_type == "Others") selected @endif>Others</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3 mb-3">
                                <div class="row">
                                    <div class="col-md-6">
                                        <strong>Discount</strong>
                                    </div>
                                    <div class="col-md-6">
                                        <input class="form-control" type="text" name="discount" onkeyup="orderDiscount(this.value)" id="discount" value="{{ $order->discount ?? '' }}" placeholder="Enter Discount Price">
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3 mb-3">
                                <div class="row">
                                    <div class="col-md-6">
                                        <strong>Advance</strong>
                                    </div>
                                    <div class="col-md-6">
                                        <input class="form-control" type="text" name="advance" onkeyup="orderAdvance(this.value)" id="advance" value="{{ $order->advance ?? '' }}" placeholder="Enter Advance Price">
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3 mb-3">
                                <div class="row">
                                    <div class="col-md-6">
                                        <strong>Total</strong>
                                    </div>
                                    <div class="col-md-6">
                                        @if ($order->discount != null && $order->advance == null)
                                            <input class="form-control total_price" type="text" name="total_price"
                                                id="total_price" value="{{ $sum + $area - $order->discount }}">
                                        @endif

                                        @if ($order->discount != null && $order->advance != null)
                                            @php
                                                $x = $sum + $area;
                                                $y = $x - $order->discount;
                                                $z = $y - $order->advance;

                                            @endphp
                                            <input class="form-control total_price" type="text" name="total_price"
                                                id="total_price" value="{{ $z }}">
                                        @endif

                                        @if ($order->advance == null && $order->discount == null)
                                            <input class="form-control total_price" type="text" name="total_price"
                                                id="total_price" value="{{ $sum + $area }}">
                                        @endif

                                        @if ($order->advance != null && $order->discount == null)
                                            <input class="form-control total_price" type="text" name="total_price"
                                                id="total_price" value="{{ $order->price }}">
                                        @endif

                                    </div>
                                </div>
                            </div>
                            <div class="mt-3 mb-3">
                                <div class="row">
                                    <div class="col-md-6">
                                        <strong>Payment Gateway</strong>
                                    </div>
                                    <div class="col-md-6">
                                        <select name="payment_gateway" id="payment_gateway" class="form-control" required>
                                            <option selected disabled>-- Select Payment Gateway--</option>
                                            <option value="wallet" @if ($order->payment_gateway == "wallet") selected @endif>wallet</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3 mb-3">
                                <div class="row">
                                    <div class="col-md-6">
                                        <strong>Transaction ID</strong>
                                    </div>
                                    <div class="col-md-6">
                                        <input class="form-control" type="text" name="transaction_id" id="transaction_id" value="{{$order->transaction_id}}" placeholder="Enter TransactionID">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer text-center">
                            <button type="submit" id="submit" name="action" value="update" class="btn btn-primary btn-sm">Update Order</button>
                        </div>
                        <div class="card-footer text-center">
                            <button type="submit" id="submit" name="action" value="transfer" class="btn btn-warning btn-sm" onclick="return confirm('Are you syre?')">Transfer Order</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('script')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        let orderId = {{ $order->id }};
        function deliveryCharge(e){
            axios.post('/api/order/delivery/charge/update/' + orderId, {
                area: e
            })
                .then(response => {
                    if(response.status == 200){
                        //alert('Delivery charge has been updated.')
                        location.reload()
                    }
                }).catch(error => {
                return confirm('Something is wrong, Please try again')
            })
        }

        function productPrice(orderDetailPrice){
            let price = document.getElementById('regular_price-' + orderDetailPrice.id).value;
            axios.post('/api/order/price/update/' + orderDetailPrice.id, {
                regular_price: price
            })
                .then(response => {
                    if(response.status == 200){
                        //alert('Order Price has been updated.')
                        location.reload()
                    }
                }).catch(error => {
                return confirm('Something is wrong, Please try again')
            })
        }

        function productQty(orderDetail){
            let qty = document.getElementById('qty-' + orderDetail.id).value;
            axios.post('/api/order/product/qty/update/' + orderDetail.id, {
                qty: qty
            })
                .then(response => {
                    if(response.status == 200){
                        //alert('Qty has been updated.')
                        location.reload()
                    }
                }).catch(error => {
                return confirm('Something is wrong, Please try again')
            })
        }

        function productColor(orderDetail){
            let color = document.getElementById('color-' + orderDetail.id).value;
            axios.post('/api/order/product/color/update/' + orderDetail.id, {
                color: color
            })
                .then(response => {
                    if(response.status == 200){
                        Swal.fire('Product color has been updated')
                    }
                }).catch(error => {
                    Swal.fire('Something is wrong, Please try again')
            })
        }

        function productSize(orderDetail){
            let size = document.getElementById('size-' + orderDetail.id).value;
            axios.post('/api/order/product/size/update/' + orderDetail.id, {
                size: size
            })
                .then(response => {
                    if(response.status == 200){
                        Swal.fire('Product size has been updated')
                    }
                }).catch(error => {
                    Swal.fire('Something is wrong, Please try again')
            })
        }

        let totalCost = document.getElementById('total_price').value;
        let showCost = document.getElementById('total_price');
        function orderAdvance(advance){
            let afterAdvanceCost = parseInt(totalCost) - parseInt(advance);
            showCost.value = afterAdvanceCost;
        }

        let totalCostForDiscount = document.getElementById('total_price').value;
        let showDiscountCost = document.getElementById('total_price');
        function orderDiscount(discount){
            let afterDiscountCost = parseInt(totalCostForDiscount) - parseInt(discount);
            showDiscountCost.value = afterDiscountCost;
        }

        //Code for fetching zone list....
        document.addEventListener("DOMContentLoaded", function() {
            const citySelect = document.getElementById("city");
            const zoneSelect = document.getElementById("zone");

            citySelect.addEventListener("change", function() {
                const selectedCityId = this.value;
                if (selectedCityId) {
                    fetch(`/get-zones/${selectedCityId}`)
                        .then(response => response.json())
                        .then(data => {
                            const zonesData = data.zones.data;

                            // Clear existing options
                            zoneSelect.innerHTML = '<option selected disabled>-- Select Zone --</option>';

                            // Add new zone options
                            zonesData.forEach(zone => {
                                const option = document.createElement("option");
                                option.value = zone.zone_id;
                                option.textContent = zone.zone_name;
                                zoneSelect.appendChild(option);
                            });
                        })
                        .catch(error => console.error(error));
                } else {
                    // Reset zone select if no city is selected
                    zoneSelect.innerHTML = '<option selected disabled>-- Select Zone --</option>';
                }
            });
        });
        //Code for fetching zone list....

        //Code For set city_name in input field...

        const citySelect = document.getElementById('city');
        const cityInput = document.getElementById('city_name');

        citySelect.addEventListener('change', function() {
            const selectedOption = citySelect.options[citySelect.selectedIndex];
            cityInput.value = selectedOption.text;
        });
        //Code For set city_name in input field...

        //Code For set zone_name in input field...

        const zoneSelect = document.getElementById('zone');
        const zoneInput = document.getElementById('zone_name');

        zoneSelect.addEventListener('change', function() {
            const selectedOption = zoneSelect.options[zoneSelect.selectedIndex];
            zoneInput.value = selectedOption.text;
        });
        //Code For set zone_name in input field...

        //If Courier is Othres then City & Zone Will be hidden...
        const courierSelect = document.getElementById('courier');
        const cityZoneWrapper = document.getElementById('cityZoneWrapper');
        const ZoneWrapper = document.getElementById('ZoneWrapper');
        const textareaWrapper = document.getElementById('textareaWrapper');

        courierSelect.addEventListener('change', function () {
            if (courierSelect.value === 'Others') {
                cityZoneWrapper.style.display = 'none';
                ZoneWrapper.style.display = 'none';
                textareaWrapper.style.display = 'block';
            } else {
                cityZoneWrapper.style.display = 'block';
                ZoneWrapper.style.display = 'block';
                textareaWrapper.style.display = 'none';
            }
        });
        //If Courier is Othres then City & Zone Will be hidden...

        if (courierSelect.value === 'Others') {
            cityZoneWrapper.style.display = 'none';
            ZoneWrapper.style.display = 'none';
            textareaWrapper.style.display = 'block';
        }
    </script>
@endpush
