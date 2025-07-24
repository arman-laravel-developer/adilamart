@extends('admin.master')

@section('content')
    <div class="page-wrapper">
        <div class="page-content">
            <div class="row">
                <div class="col">
                    <div class="card radius-10 mb-0">
                        <div class="card-body">
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            <div class="d-flex align-items-center">
                                <div>
                                    <h5 class="mb-3">Add New Product</h5>
                                </div>
                                <div class="ms-auto">
                                    <a href="{{ url('/offer/products/list') }}" class="btn btn-primary btn-sm">Products</a>
                                </div>
                            </div>

                            <form action="{{ url('/offer/products/store') }}" method="post" enctype="multipart/form-data">
                                @csrf
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div id="newRowForColor"></div>
                                                <label style="padding-bottom: 5px;font-weight: 600;font-size: 15px;letter-spacing: 1px;">Select Offer</label>
                                                <select class="form-control mb-3" name="offer_id">
                                                  <option value="AL">Select A Offer</option>
                                                    @foreach(\App\Models\Offer::orderBy('created_at', 'desc')->get() as $offer)
                                                        <option value="{{ $offer->id }}">{{ $offer->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-12">
                                                <div id="newRowForColor"></div>
                                                <label style="padding-bottom: 5px;font-weight: 600;font-size: 15px;letter-spacing: 1px;">Offer Products</label>
                                                <select class="multiple-related-product form-control mb-3" name="related_product_id[]" multiple="multiple">
                                                  <option value="AL">Select Products</option>
                                                    @foreach(\App\Models\Product::orderBy('created_at', 'desc')->get() as $product)
                                                        <option value="{{ $product->id }}">{{ $product->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                            </div>
                            <button type="submit" class="btn btn-success mt-2 float-right">Submit</button>
                          </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script src="//cdn.ckeditor.com/4.14.1/standard/ckeditor.js"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            $('.ckeditor').ckeditor();
        });
    </script>
    <script>
        $('#addMore').click(function(){
            let html = '';
            html+='<div class="input-group mb-3" id="removeRow">'
                html+='<input type="file" name="gallery_image[]" id="gallery_image" class="form-control">'
                html+='<button class="btn btn-sm btn-danger" type="button" id="remove">'
                    html+='<i class="bx bx-minus" aria-hidden="true" style="margin-left: 7px;"></i>'
                html+='</button>'
            html+='</div>'

            $('#newRow').append(html);
        });

        // remove row
        $(document).on('click', '#remove', function () {
            $(this).closest('#removeRow').remove();
        });

        $('#addMoreSize').click(function(){
            let html = '';
            html+='<div class="input-group mb-3" id="removeSizeRow">'
                html+='<input type="text" name="size[]" id="size" class="form-control" placeholder="Product size">'
                html+='<button class="btn btn-sm btn-danger" type="button" id="removeSize">'
                    html+='<i class="bx bx-minus" aria-hidden="true" style="margin-left: 7px;"></i>'
                html+='</button>'
            html+='</div>'

            $('#newRowForSize').append(html);
        });

        // remove row
        $(document).on('click', '#removeSize', function () {
            $(this).closest('#removeSizeRow').remove();
        });

        $('#addMoreColor').click(function(){
            let html = '';
            html+='<div class="input-group mb-3" id="removeColorRow">'
                html+='<input type="text" name="color[]" id="color" class="form-control" placeholder="Product color">'
                html+='<button class="btn btn-sm btn-danger" type="button" id="removeColor">'
                    html+='<i class="bx bx-minus" aria-hidden="true" style="margin-left: 7px;"></i>'
                html+='</button>'
            html+='</div>'

            $('#newRowForColor').append(html);
        });

        // remove row
        $(document).on('click', '#removeColor', function () {
            $(this).closest('#removeColorRow').remove();
        });
    </script>
@endpush
