@extends('admin.master')

@section('content')
    <div class="page-wrapper">
        <div class="page-content">
            <div class="row">
                <div class="col">
                    <div class="card radius-10 mb-0">
                        <div class="card-body">

                            @if(Session::has('success'))
                                <x-alert :message="session('success')" title="Success" type="success"></x-alert>
                            @endif
                            @if(Session::has('error'))
                                <x-alert :message="session('error')" title="Error" type="error"></x-alert>
                            @endif

                            <div class="d-flex align-items-center">
                                <div>
                                    <h5 class="mb-1">Offers</h5>
                                </div>
                                <div class="ms-auto">
                                    <a href="{{ url('/create-offer') }}" class="btn btn-primary btn-sm">Add new</a>
                                </div>
                            </div>

                           <div class="table-responsive mt-3">
                               <table class="table align-middle mb-0">
                                   <thead class="table-light">
                                       <tr>
                                           <th width="5%">SL</th>
                                           <th width="10%">Image</th>
                                           <th width="10%">Name</th>
                                           <th width="10%">Status</th>
                                           <th width="10%">Actions</th>
                                       </tr>
                                   </thead>
                                   <tbody>
                                       @if(!empty($offers))
                                            @foreach ($offers as $offer)
                                                <tr>
                                                    <td>{{ $loop->index + 1 }}</td>
                                                    <td>
                                                        <img src="{{ asset('offer/'.$offer->image) }}" style="height: 200px; width: 300px;" alt="offer image" />
                                                    </td>
                                                    <td>{{ $offer->name }}</td>
                                                    <td class="">
                                                        @if($offer->is_active == 1)
                                                            <span class="badge bg-light-success text-success w-100">Active</span>
                                                        @else
                                                            <span class="badge bg-light-danger text-success w-100">Inactive</span>
                                                        @endif

                                                    </td>
                                                    <td>
                                                    <div class="d-flex order-actions">
                                                        <a href="{{ url('edit-offer/'.$offer->id) }}" class="ms-4 text-primary bg-light-primary border-0"><i class='bx bxs-edit' ></i></a>
                                                        @if($offer->is_active == 1)
                                                            <a href="{{ url('inactivate-offer/'.$offer->id) }}" class="badge rounded-pill bg-success">
                                                                <i class="bx bx-up-arrow-alt" style="font-size: 20px; color: rgb(239, 241, 241);"></i>
                                                            </a>
                                                        @else
                                                            <a href="{{ url('activate-offer/'.$offer->id) }}" class="badge rounded-pill bg-warning">
                                                                <i class="bx bx-down-arrow-alt" style="font-size: 20px; color: rgb(230, 59, 59);"></i>
                                                            </a>
                                                        @endif
                                                        <a href="{{ url('delete-offer/'.$offer->id) }}" onclick="return confirm('Are you sure?')" class="ms-4 text-danger bg-light-danger border-0"><i class='bx bxs-trash' ></i></a>
                                                    </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                       @endif
                                   </tbody>
                               </table>
                           </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
