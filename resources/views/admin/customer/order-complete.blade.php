@extends('admin.master')

@section('content')
<div class="page-wrapper">
    <div class="page-content">
        <div class="row">
            <div class="col">
                <div class="card radius-10 mb-0">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div>
                                    <h5 class="mb-1">Delivered Orders</h5>
                                </div>
                            </div>
                            <div class="col-md-4"></div>
                            <div class="col-md-4">
                                <form action="{{ url('/delivered/order') }}" method="GET">
                                    @csrf
                                    <div class="input-group mb-3">
                                        <input type="text" name="search" class="form-control"
                                            placeholder="Search orderId and Customer phone...">
                                        <button type="submit"
                                            class="input-group-text bg-primary text-white">Search</button>
                                        <a href="{{ url('/delivered/order') }}"
                                            class="input-group-text bg-danger text-white">Clear</a>
                                    </div>
                                </form>
                            </div>
                        </div>
                        @if (session('name') == 'admin')
                                <div class="row">
                                    <div class="col-md-4">
                                        {{-- <div class="user-dropdown-menu" id="user">
                                            <a href="javascript:;" class="user-dropdown-link" onclick="addclass()">
                                                User
                                            </a>
                                            <ul class="user-item-submenu">
                                                <li class="submenu-item">
                                                    <a href="#" class="submenu-item-link">
                                                        Saidul Isalm
                                                    </a>
                                                </li>
                                                <li class="submenu-item">
                                                    <a href="#" class="submenu-item-link">
                                                        Muntasir Pranto
                                                    </a>
                                                </li>
                                                <li class="submenu-item">
                                                    <a href="#" class="submenu-item-link">
                                                        Al Amin Saki
                                                    </a>
                                                </li>
                                            </ul>
                                        </div> --}}
                                    </div>
                                    <div class="col-md-8">
                                        <form method="GET" action="{{ url('/delivered/order') }}"
                                            class="form-inline mb-3">
                                            @csrf
                                            <div class="input-group mb-3">
                                                <span class="input-group-text bg-gradient-blues">From</span>
                                                <input type="date" class="form-control" name="from"
                                                    placeholder="From date" aria-label="Username">
                                                <span class="input-group-text bg-gradient-burning">To</span>
                                                <input type="date" class="form-control" name="to"
                                                    placeholder="To date" aria-label="Server">
                                                <button type="submit" class="btn btn-sm btn-info"><i
                                                        class="fa fa-search"></i> Search</button>
                                                <a href="{{ url('/delivered/order') }}" class="btn btn-sm btn-danger"><i
                                                        class="fa fa-search"></i> Clear</a>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            @endif
                        <form action="{{ url('/order/update') }}" method="post">
                            @csrf
                            {{-- @include('admin.includes.action-button') --}}
                            <div class="mt-3">
                                <table class="table table-striped table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="5%">Select</th>
                                            <th width="5%">SL</th>
                                            <th width="5%">Ds Order?</th>
                                            <th width="15%">Order ID</th>
                                            <th width="15%">Customer</th>
                                            <th width="15%">Product</th>
                                            <th width="20%">Total</th>
                                            <th width="5%">Status</th>
                                            <th width="10%">Date</th>
                                            <th width="20%">Users</th>
                                            <th width="10%">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($delivered_orders as $key => $order)
                                            <tr>
                                                <td>
                                                    @if($order->order_status != null)
                                                        <input type="checkbox" name="id[]" id="id{{ $order->id }}" value="{{ $order->id }}" />
                                                    @endif
                                                </td>
                                                <td>{{ $loop->index + 1 }}</td>
                                                <td>
                                                    @if ($order->is_dropshipping==true)
                                                        <span class="badge rounded-pill bg-success">Yes</span>
                                                    @else
                                                        <span class="badge rounded-pill bg-warning">No</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="badge bg-info"
                                                        style="font-size: 12px; color: black">{{env('APP_NAME')}}</span><br />
                                                    <span
                                                        style="font-size: 16px; font-weight:600;">{{ $order->orderId ?? 'No order id found' }}</span><br />
                                                    <span
                                                        class="badge rounded-pill bg-primary">{{ $order->order_type }}</span>
                                                    <br />
                                                    {{ $order->created_at->diffForHumans() }}
                                                </td>
                                                <td>
                                                    {{ $order->name ?? 'No name found' }}<br />
                                                    <span
                                                        style="color: green">{{ $order->phone ?? 'No phone found' }}</span><br />
                                                    {{ substr($order->address, 0, 70) ?? 'No address found' }} <br />
                                                    <span
                                                        class="badge rounded-pill {{ $order->customer_type == 'Old Customer' ? 'bg-danger' : 'bg-success' }}">{{ $order->customer_type }}</span>
                                                    <br />
                                                </td>
                                                <td>
                                                    @foreach ($order->orderDetails as $details)
                                                        {{ $order->qty ?? 'No name found' }}X
                                                        {{ $details->product?->name }}<br />
                                                        {{ 'Size: ' . $details->size?? '' }} | {{ 'Color: ' . $details->color?? '' }}
                                                    @endforeach
                                                </td>
                                                <td>
                                                    <b>Amount :</b> {{ $order->price }} Tk. <br />
                                                    <b>Delivery :</b> {{ $order->area }} Tk.
                                                </td>
                                                <td>{{ Str::ucfirst($order->order_status) }}</td>
                                                <td>{{ date('d-m-Y', strtotime($order->created_at)) }}</td>
                                                <td>{{ Str::ucfirst($order->admin?->name) }}</td>
                                                <td>
                                                    <!-- <a href="{{ url('/order/return/status/' . $order->id) }}" class="btn btn-sm btn-info">Return</a>
                                                <a href="{{ url('/order/damage/status/' . $order->id) }}" class="btn btn-sm btn-danger">Damage</a>
                                                <a href="{{ url('/order/missing/status/' . $order->id) }}" class="btn btn-sm btn-warning">Missing</a>
                                                <a href="{{ url('/order/delivered/status/' . $order->id) }}" class="btn btn-sm btn-warning">Delivered</a> -->
                                                    <div class="action-dropdown-menu">
                                                        <a href="javascript:;" class="action-dropdown-link">
                                                            Action
                                                        </a>
                                                        <ul class="action-btn-list">
                                                            <li class="action-btn-list-item">
                                                                <a href="{{ url('/order/return/status/' . $order->id) }}"
                                                                    class="action-btn-link">
                                                                    Return
                                                                </a>
                                                            </li>
                                                            <li class="action-btn-list-item">
                                                                <a href="{{ url('/order/damage/status/' . $order->id) }}"
                                                                    class="action-btn-link">
                                                                    Damage
                                                                </a>
                                                            </li>
                                                            <li class="action-btn-list-item">
                                                                <a href="{{ url('/order/missing/status/' . $order->id) }}"
                                                                    class="action-btn-link">
                                                                    Missing
                                                                </a>
                                                            </li>
                                                            <li class="action-btn-list-item">
                                                                <a href="{{ url('/order/delivered/status/' . $order->id) }}"
                                                                    class="action-btn-link">
                                                                    Delivered
                                                                </a>
                                                            </li>
                                                            <li class="action-btn-list-item">
                                                                <a href="#" class="action-btn-link">
                                                                    Edit
                                                                </a>
                                                            </li>
                                                            <li class="action-btn-list-item">
                                                                <a href="#" class="action-btn-link">
                                                                    Hold
                                                                </a>
                                                            </li>
                                                            <li class="action-btn-list-item">
                                                                <a href="#" class="action-btn-link">
                                                                    Cancel
                                                                </a>
                                                            </li>
                                                            <li class="action-btn-list-item">
                                                                <a href="#" class="action-btn-link">
                                                                    Delete
                                                                </a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
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
        function deSelect(){
            var selec=document.getElementsByName('id[]');
            for(var i=0; i<selec.length; i++){
                if(selec[i].type == 'checkbox')
                    selec[i].checked=false;

            }
        }
    </script>
@endpush
