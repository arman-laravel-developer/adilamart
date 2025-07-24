@extends('admin.master')

@push('style')
<style>
    .product-item-wrap {
        margin-bottom: 30px;
        border: 1px solid #f04d6e;
        padding: 10px;
        border-radius: 10px;
    }

    .product-item-image-outer {
        position: relative;
        overflow: hidden;
    }

    .product-item-image-outer .product-item-image {
        display: block;
    }

    .product-item-image-outer .product-badges {
        position: absolute;
        top: 10px;
        right: 0;
    }

    .product-item-image-outer .product-badges span {
        display: inline-block;
        color: #fff;
        padding: 3px 10px 3px 15px;
        background: #f24f70;
        text-transform: capitalize;
        font-size: 16px;
        font-weight: 500;
    }

    .product-item-image-outer .product-badges:after {
        content: '';
        position: absolute;
        z-index: -1;
        left: -27px;
        top: 0;
        width: 10px;
        border: 15px solid #f24f70;
        border-left-color: transparent;
        z-index: 9;
    }

    .product-content-outer .product-item-ratting {
        padding-left: 0;
        display: inline-flex;
        margin-top: 8px;
        margin-bottom: 5px;
    }

    .product-item-ratting li i {
        font-size: 13px;
        color: #ff9800;
    }

    .product-content-outer .product-name {
        display: inline-block;
        font-size: 18px;
        color: #000;
        font-weight: 500;
    }

    .product-item-bottom .product-price span {
        font-size: 18px;
        font-weight: 700;
        color: #f24f70;
    }

    .product-item-bottom .product-price .old-price {
        font-size: 14px;
        color: #adadad;
        margin-left: 5px;
    }

    .product-content-outer .product-item-bottom .add-cart-btn {
        padding: 6px 20px 6px 20px;
        border-radius: 4px;
        background-color: #ee1d48c7;
        border: 1px solid #ee1d48c7;
        font-size: 14px;
        font-weight: 500;
        color: #fff;
        transition: all .3s ease;
        display: inline-block;
        margin-top: 5px;
    }

    .quick-order-btn-inner {
        margin-top: 10px;
        display: block;
        text-align: center;
        background: #ee1d48c7;
        border-radius: 5px;
        padding: 8px 0;
        font-size: 16px;
        font-weight: 500;
        color: #fff;
        border: 1px solid #ee1d48c7;
    }

    .quick-order-btn-inner:hover {
        background: transparent;
        color: #ee1d48c7;
    }

    .product-item-wrap:hover .product-hover-list {
        right: 5%;
        visibility: visible;
        opacity: 1;
        z-index: 9;
    }

    .product-item-wrap:hover .product-name {
        color: #f24f70;
    }

    .product-item-wrap:hover .add-cart-btn {
        background: transparent;
        color: #f24f70;
        border: 1px solid #ee1d48c7;
    }

    .product-hover-list-item-link:hover i {
        color: #ee1d48c7;
    }
    .product-item-wrap:hover .product-item-image img {
        -webkit-transform: scale3d(1.05, 1.05, 1.05) translateZ(0);
        transform: scale3d(1.05, 1.05, 1.05) translateZ(0);
    }
    .product-item-image-outer .product-item-image img {
        transition: 0.25s opacity, 0.25s visibility, transform 1.5s cubic-bezier(0, 0, 0.2, 1), -webkit-transform 1.5s cubic-bezier(0, 0, 0.2, 1);
        width: 100%;
        height: 150px;
        object-fit: contain;
    }
    </style>
@endpush

@section('content')


    <div class="page-wrapper">
        <div class="page-content">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <form action="{{url('/order/add-manually')}}" method="GET">
                                @csrf
                                <div class="input-group mb-3">
                                    <select class="form-control" name="search" id="search">
                                        <option selected disabled>Select a Product</option>
                                        @foreach ($products as $product)
                                            <option value="{{ $product->id }}">{{ $product->name }}</option>
                                        @endforeach
                                    </select>
                                    <button type="submit"class="input-group-text bg-primary text-white">Search</button>
                                    <a href="{{url('/order/add-manually')}}" class="input-group-text bg-danger text-white">Clear</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <form action="{{ url('/order/checkout-manually/multiple') }}" method="post">
            @csrf
                <div class="card">
                    <div class="card-header">
                        <h5 style="display: inline-block;margin-bottom: 0">Menual Order</h5>
                        <button type="submit" id="submit-btn" value="Print" class="btn btn-sm btn-success float-end mr-2" style="cursor: pointer">Multiple Order</button>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach ($products as $product )
                            <div class="col-xl-2 col-lg-3 col-sm-4">
                                <div class="product-item-wrap">
                                    <div class="product-item-image-outer">
                                        <a href="{{url('order/checkout-manually/'.$product->id)}}" class="product-item-image">
                                            <input type="checkbox" name="id[]" id="id{{ $product->id }}" value="{{ $product->id }}" />
                                            <img src="{{asset('product/images/'.$product->image)}}" alt="product_image">
                                        </a>
                                        <div class="product-badges">
                                            <span>{{ $product->product_type }}</span>
                                        </div>
                                    </div>
                                    <div class="product-content-outer">
                                        <a href="{{url('order/checkout-manually/'.$product->id)}}" class="product-name">
                                            {{ substr($product->name, 0,20) }}
                                        </a>
                                        <div class="product-item-bottom">
                                            <div class="product-price">
                                                <span>{{$product->regular_price}} Tk.</span>
                                            </div>
                                            <a href="{{url('order/checkout-manually/'.$product->id)}}" class="add-cart-btn">Order Now</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('script')
    <script>
        function selects(){
            var selec=document.getElementsByName('id[]');
            console.log(selec);
            for(var i=0; i<selec.length; i++){
                if(selec[i].type == 'checkbox')
                    selec[i].checked=true;
            }
        }

        function deSelect() {
            var selec = document.getElementsByName('id[]');
            for (var i = 0; i < selec.length; i++) {
                if (selec[i].type == 'checkbox')
                    selec[i].checked = false;

            }
        }
    </script>
@endpush
