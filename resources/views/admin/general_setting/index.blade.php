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

                            <form action="{{url('/settings/update')}}" method="post" enctype="multipart/form-data">
                                @csrf
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label style="padding-bottom: 5px;font-weight: 600;font-size: 15px;letter-spacing: 1px;">Phone <small style="color: red; font-size: 18px;">*</small></label>
                                                    <input type="text" name="phone" value="{{$general_setting->phone}}" class="form-control" placeholder="Enter phone"><br>
                                                    <span style="color: red"> {{ $errors->has('phone') ? $errors->first('phone') : ' ' }}</span>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label style="padding-bottom: 5px;font-weight: 600;font-size: 15px;letter-spacing: 1px;">Email <small style="color: red; font-size: 18px;">*</small></label>
                                                    <input type="email" name="email" value="{{$general_setting->email}}" class="form-control" placeholder="Enter email"><br>
                                                    <span style="color: red"> {{ $errors->has('email') ? $errors->first('email') : ' ' }}</span>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label style="padding-bottom: 5px;font-weight: 600;font-size: 15px;letter-spacing: 1px;">Facebook <small style="color: red; font-size: 18px;">*</small></label>
                                                    <input type="text" name="facebook" value="{{$general_setting->facebook}}" class="form-control" placeholder="Enter facebook"><br>
                                                    <span style="color: red"> {{ $errors->has('facebook') ? $errors->first('facebook') : ' ' }}</span>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label style="padding-bottom: 5px;font-weight: 600;font-size: 15px;letter-spacing: 1px;">Instagram <small style="color: red; font-size: 18px;">*</small></label>
                                                    <input type="text" name="instagram" value="{{$general_setting->instagram}}" class="form-control" placeholder="Enter instagram"><br>
                                                    <span style="color: red"> {{ $errors->has('instagram') ? $errors->first('instagram') : ' ' }}</span>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label style="padding-bottom: 5px;font-weight: 600;font-size: 15px;letter-spacing: 1px;">Youtube <small style="color: red; font-size: 18px;">*</small></label>
                                                    <input type="text" name="youtube" value="{{$general_setting->youtube}}" class="form-control" placeholder="Enter youtube"><br>
                                                    <span style="color: red"> {{ $errors->has('youtube') ? $errors->first('youtube') : ' ' }}</span>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label style="padding-bottom: 5px;font-weight: 600;font-size: 15px;letter-spacing: 1px;">Twitter <small style="color: red; font-size: 18px;">*</small></label>
                                                    <input type="text" name="twitter" value="{{$general_setting->twitter}}" class="form-control" placeholder="Enter twitter"><br>
                                                    <span style="color: red"> {{ $errors->has('twitter') ? $errors->first('twitter') : ' ' }}</span>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label style="padding-bottom: 5px;font-weight: 600;font-size: 15px;letter-spacing: 1px;">Logo <small style="color: red; font-size: 18px;">*</small></label>
                                                    <input type="file" name="logo" id="logo" class="form-control"><br>
                                                    <span style="color: red"> {{ $errors->has('logo') ? $errors->first('logo') : ' ' }}</span>
                                                </div>
                                                <img src="{{asset('setting/'.$general_setting->logo)}}" height="100" width="100" alt="">
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label style="padding-bottom: 5px;font-weight: 600;font-size: 15px;letter-spacing: 1px;">Address <small style="color: red; font-size: 18px;">*</small></label>
                                                <textarea class="form-control" rows="5" name="address">{!!$general_setting->address!!}</textarea><br>
                                                <span style="color: red"> {{ $errors->has('address') ? $errors->first('address') : ' ' }}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label style="padding-bottom: 5px;font-weight: 600;font-size: 15px;letter-spacing: 1px;">Offer Remaining Date</label>
                                                <input type="datetime-local" name="offer_time" value="{{ $general_setting->offer_time }}" class="form-control" placeholder="Enter Date"><br>
                                                <span style="color: red">{{ $errors->has('offer_time') ? $errors->first('offer_time') : ' ' }}</span>
                                            </div>
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
