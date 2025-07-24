@extends('admin.master')

@section('content')
    <div class="page-wrapper">
        <div class="page-content">
            <div class="row">
                <div class="col">
                    <div class="card radius-10 mb-0">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div>
                                    <h5 class="mb-1">Edit Offer</h5>
                                </div>
                                <div class="ms-auto">
                                    <a href="{{ url('/offers') }}" class="btn btn-primary btn-sm radius-30">Offers</a>
                                </div>
                            </div>
                            <form action="{{ url('update-offer/'.$offer->id) }}" method="post" enctype="multipart/form-data">
                                @csrf
                                <div class="form-group">
                                    <label>Name</label>
                                    <input type="text" name="name" class="form-control" placeholder="Offer name" value="{{$offer->name}}" required>
                                    <span style="color: red"> {{ $errors->has('name') ? $errors->first('name') : ' ' }}</span>
                                </div>
                                <div class="form-group">
                                    <label>Image</label>
                                    <input type="file" name="image" class="form-control"/>
                                    <img src="{{asset('offer/'.$offer->image)}}" height="200" width="400">
                                </div>
                                <button type="submit" class="btn btn-success mt-2 float-right">Update</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
