<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\DropshippingProductRequest;
use App\Http\Requests\ProductRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductColor;
use App\Models\ProductImage;
use App\Models\ProductSize;
use App\Models\RelatedProduct;
use App\Models\Subcategory;
use App\Models\AddPage;
use App\Models\OfferProduct;
use App\Models\PageProduct;
use App\Models\TopProducts;
use App\Repository\Interface\BrandInterface;
use App\Repository\Interface\CategoryInterface;
use App\Repository\Interface\ProductInterface;
use App\Repository\Interface\PageProductInterface;
use App\Repository\Interface\SubcategoryInterface;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Http;
use DB;
use PDF;

class ProductController extends Controller
{
    protected $product;
    protected $page_product;
    protected $category;
    protected $subcategory;
    protected $brand;
    public function __construct(ProductInterface $product,PageProductInterface $page_product, CategoryInterface $category, SubcategoryInterface $subcategory, BrandInterface $brand)
    {
        $this->product = $product;
        $this->page_product = $page_product;
        $this->category = $category;
        $this->subcategory = $subcategory;
        $this->brand = $brand;
    }

    public function index()
    {
        $type = 'Own';
        $products = Product::orderBy('created_at', 'desc')->where('is_page_product', 0)->where('b_product_id', null)->paginate(30);
        return view('admin.products.index', compact('products','type'));
    }

    public function dropShippingProducts ()
    {
        $type = 'Dropshipping';
        $products = Product::orderBy('created_at', 'desc')->where('is_page_product', 0)->where('b_product_id','!=', null)->paginate(30);
        return view('admin.products.index', compact('products', 'type'));
    }

    public function pageProductIndex ()
    {
        $page_products = Product::orderBy('created_at', 'desc')->where('is_page_product', 1)->paginate(30);
        return view('admin.page_products.index', compact('page_products'));
    }

    public function create()
    {
        return view('admin.products.create', [
            'categories' => Category::orderBy('created_at', 'desc')->get(),
            'subcategories' => Subcategory::orderBy('created_at', 'desc')->get(),
            'brands' => Brand::orderBy('created_at', 'desc')->get(),
            'type' => 'Own'
        ]);
    }

    public function createVariableProduct()
    {
        return view('admin.products.create-variable-product', [
            'categories' => Category::orderBy('created_at', 'desc')->get(),
            'subcategories' => Subcategory::orderBy('created_at', 'desc')->get(),
            'brands' => Brand::orderBy('created_at', 'desc')->get(),
            'type' => 'Own'
        ]);
    }

    public function createDropshippingProduct ()
    {
        return view('admin.products.create', [
            'categories' => Category::orderBy('created_at', 'desc')->get(),
            'subcategories' => Subcategory::orderBy('created_at', 'desc')->get(),
            'brands' => Brand::orderBy('created_at', 'desc')->get(),
            'type' => 'Dropshipping'
        ]);
    }

    public function store(ProductRequest $request)
    {
        $image = $request->file('image');
        $input['image'] = rand().'pro_main'.$request->name.'.'.$image->getClientOriginalExtension();
        $destinationPath = 'product/images';
        $imgFile = Image::make($image->getRealPath());
        $imgFile->resize(240, 240, function ($constraint) {
            $constraint->aspectRatio();
        })->save($destinationPath.'/'.$input['image']);
        $image->move($destinationPath, $input['image']);
        $imageUrl = url($destinationPath.'/'.$input['image']);

        if($request->type){
            //dd($request->type);
            // $product = new PageProduct();
            $product = new Product();
            $page= AddPage::find($request->type);
            // $product->type = Str::slug($page->name);
            $product->page_name = Str::slug($page->name);
            $product->is_page_product = 1;
        }
        else{
            $product = new Product();
            $product->seo_title = $request->seo_title;
            $product->seo_description = $request->seo_description;
            $product->seo_keyword = $request->seo_keyword;
        }

        $product->name = $request->name;
        $product->slug = str_replace(' ', '-', strtolower($request->name));
        $product->cat_id = $request->cat_id;
        $product->sub_cat_id = $request->sub_cat_id;
        $product->qty = $request->qty;
        $product->buy_price = $request->buy_price;
        $product->regular_price = $request->regular_price;
        if ($request->discount_price){
            $product->discount_price = $request->discount_price;
        }
        $product->product_code = $request->product_code;
        $product->short_description = $request->short_description;
        $product->long_description = $request->long_description;
        $product->policy = $request->policy;
        $product->product_type = $request->product_type;
        $product->image = $input['image'];
        $product->imageUrl = $imageUrl;
        $product->video_link = $request->video_link;
        $product->save();

        if(!empty($product)){

            if($request->gallery_image){
                $imageGallery = $request->gallery_image;
                foreach($imageGallery as $image){
                    $galleryImageName = rand().$request->name.'.'.$image->extension();
                    $imgGallery = Image::make($image->path());
                    $imgGallery->resize(440, 440, function ($const) {
                        $const->aspectRatio();
                    })->save('galleryImage'. '/'. $galleryImageName);
                    $imageUrl = url('galleryImage'.'/'.$galleryImageName);

                    $productGalleryImage = new ProductImage();
                    if($request->type){
                        $productGalleryImage->product_id = $product->id;
                    }
                    else{
                        $productGalleryImage->product_id = $product->id;
                    }
                    $productGalleryImage->gallery_image = $galleryImageName;
                    $productGalleryImage->imageUrl = $imageUrl;
                    $productGalleryImage->save();
                }
            }
        }

        // Product color
        if($request->filled('color')){
            $colors = $request->color;
            if (is_array($colors) || is_object($colors)){
                foreach ($colors as $key => $color){
                    $colorName = new ProductColor();
                    if($request->type){
                        $colorName->product_id = $product->id;
                    }
                    else{
                        $colorName->product_id = $product->id;
                    }
                    $colorName->color = $request->color[$key];
                    $colorName->save();
                }
            }

        }
        // Product size
        if ($request->filled('size')){
            $sizes = $request->filled('size');
            if (is_array($sizes) || is_object($sizes)){
                foreach ($sizes as $key => $size){
                    $sizeName = new ProductSize();
                    $sizeName->product_id = $product->id;
                    $sizeName->size = $request->size[$key];
                    $sizeName->save();
                }
            }
        }
        // Related product
        if($request->filled('related_product_id')){
            $relatedProducts = $request->related_product_id;
            if (is_array($relatedProducts || is_object($relatedProducts))){
                foreach ($relatedProducts as $key => $related){
                    $relatedProduct = new RelatedProduct();
                    $relatedProduct->product_id = $product->id;
                    $relatedProduct->related_product_id = $request->related_product_id[$key];
                    $relatedProduct->save();
                }
            }
        }
        if($request->type){
            return redirect()->route('page.products.index')->with('success', 'Product has been successfully created.');
        }
        return redirect()->route('products.index')->with('success', 'Product has been successfully created.');
    }

    public function storeVariableProduct (Request $request)
    {
        $image = $request->file('image');
        $input['image'] = rand().'pro_main'.$request->name.'.'.$image->getClientOriginalExtension();
        $destinationPath = 'product/images';
        $imgFile = Image::make($image->getRealPath());
        $imgFile->resize(240, 240, function ($constraint) {
            $constraint->aspectRatio();
        })->save($destinationPath.'/'.$input['image']);
        $image->move($destinationPath, $input['image']);
        $imageUrl = url($destinationPath.'/'.$input['image']);

        if($request->type){
            //dd($request->type);
            // $product = new PageProduct();
            $product = new Product();
            $page= AddPage::find($request->type);
            // $product->type = Str::slug($page->name);
            $product->page_name = Str::slug($page->name);
            $product->is_page_product = 1;
        }
        else{
            $product = new Product();
            $product->seo_title = $request->seo_title;
            $product->seo_description = $request->seo_description;
            $product->seo_keyword = $request->seo_keyword;
        }

        if(isset($request->priority)){
            $checkPriority = Product::where('priority', $request->priority)->where('id','!=', $product->id)->first();
            if($checkPriority == null){
                $product->priority = $request->priority;
            }
            elseif($checkPriority != null){
                $checkPriority->priority = 1000;
                $product->priority = $request->priority;
            }
        }
        $product->is_variable = true;
        $product->name = $request->name;
        $product->slug = str_replace(' ', '-', strtolower($request->name));
        $product->cat_id = $request->cat_id;
        $product->sub_cat_id = $request->sub_cat_id;
        $product->qty = $request->qty;
        $product->buy_price = $request->buy_price;
        $product->regular_price = $request->regular_price;
        if ($request->discount_price){
            $product->discount_price = $request->discount_price;
        }
        $product->product_code = $request->product_code;
        $product->short_description = $request->short_description;
        $product->long_description = $request->long_description;
        $product->policy = $request->policy;
        $product->product_type = $request->product_type;
        $product->image = $input['image'];
        $product->imageUrl = $imageUrl;
        $product->video_link = $request->video_link;
        $product->save();

        if(!empty($product)){
            if($request->gallery_image){

                $galleryImages = $request->file('gallery_image');
                $prices = $request->input('price');
                $colors = $request->input('color');
                $sizes = $request->input('size');

                foreach ($galleryImages as $index => $image) {
                    $galleryImageName = rand().$request->name.'.'.$image->extension();
                    $imgGallery = Image::make($image->path());
                    $imgGallery->resize(440, 440, function ($const) {
                        $const->aspectRatio();
                    })->save('galleryImage'. '/'. $galleryImageName);
                    $imageUrl = url('galleryImage'.'/'.$galleryImageName);

                    $productGalleryImage = new ProductImage();
                    $productGalleryImage->product_id = $product->id;  // assuming $product is available
                    $productGalleryImage->gallery_image = $galleryImageName;
                    $productGalleryImage->price = $prices[$index];
                    $productGalleryImage->color = $colors[$index];
                    $productGalleryImage->size = $sizes[$index];
                    $productGalleryImage->imageUrl = $imageUrl;
                    $productGalleryImage->save();
                }
            }
        }

        // Product color
        if($request->filled('color')){
            $colors = $request->color;
            if (is_array($colors) || is_object($colors)){
                foreach ($colors as $key => $color){
                    $colorName = new ProductColor();
                    $colorName->product_id = $product->id;
                    $colorName->color = $color;
                    $colorName->save();
                }
            }
        }
        // Product size
        if ($request->filled('size')) {
            $sizes = $request->input('size');
            if (is_array($sizes) || is_object($sizes)) {
                foreach ($sizes as $size) {
                    $sizeName = new ProductSize();
                    $sizeName->product_id = $product->id;
                    $sizeName->size = $size;
                    $sizeName->save();
                }
            }
        }
        // Related product
        if($request->filled('related_product_id')){
            $relatedProducts = $request->related_product_id;
            if (is_array($relatedProducts || is_object($relatedProducts))){
                foreach ($relatedProducts as $key => $related){
                    $relatedProduct = new RelatedProduct();
                    $relatedProduct->product_id = $product->id;
                    $relatedProduct->related_product_id = $request->related_product_id[$key];
                    $relatedProduct->save();
                }
            }
        }
        if($request->type){
            return redirect()->route('page.products.index')->with('success', 'Product has been successfully created.');
        }
        return redirect()->route('products.index')->with('success', 'Product has been successfully created.');
    }

    public function storeDropshippingProduct (DropshippingProductRequest $request)
    {
        //Check Product ID Validaty Using Banggomart API...

        //Check Product ID Validaty Using Banggomart API...

        $image = $request->file('image');
        $input['image'] = rand().'pro_main'.$request->name.'.'.$image->getClientOriginalExtension();
        $destinationPath = 'product/images';
        $imgFile = Image::make($image->getRealPath());
        $imgFile->resize(240, 240, function ($constraint) {
            $constraint->aspectRatio();
        })->save($destinationPath.'/'.$input['image']);
        $image->move($destinationPath, $input['image']);
        $imageUrl = url($destinationPath.'/'.$input['image']);

        if($request->type){
            //dd($request->type);
            // $product = new PageProduct();
            $product = new Product();
            $page= AddPage::find($request->type);
            // $product->type = Str::slug($page->name);
            $product->page_name = Str::slug($page->name);
            $product->is_page_product = 1;
        }
        else{
            $product = new Product();
            $product->seo_title = $request->seo_title;
            $product->seo_description = $request->seo_description;
            $product->seo_keyword = $request->seo_keyword;
        }

        $product->b_product_id = $request->b_product_id;
        $product->name = $request->name;
        $product->slug = str_replace(' ', '-', strtolower($request->name));
        $product->cat_id = $request->cat_id;
        $product->sub_cat_id = $request->sub_cat_id;
        $product->qty = $request->qty;
        $product->buy_price = $request->buy_price;
        $product->regular_price = $request->regular_price;
        if ($request->discount_price){
            $product->discount_price = $request->discount_price;
        }
        $product->product_code = $request->product_code;
        $product->short_description = $request->short_description;
        $product->long_description = $request->long_description;
        $product->policy = $request->policy;
        $product->product_type = $request->product_type;
        $product->image = $input['image'];
        $product->imageUrl = $imageUrl;
        $product->video_link = $request->video_link;
        $product->save();

        if(!empty($product)){

            if($request->gallery_image){
                $imageGallery = $request->gallery_image;
                foreach($imageGallery as $image){
                    $galleryImageName = rand().$request->name.'.'.$image->extension();
                    $imgGallery = Image::make($image->path());
                    $imgGallery->resize(440, 440, function ($const) {
                        $const->aspectRatio();
                    })->save('galleryImage'. '/'. $galleryImageName);
                    $imageUrl = url('galleryImage'.'/'.$galleryImageName);

                    $productGalleryImage = new ProductImage();
                    if($request->type){
                        $productGalleryImage->product_id = $product->id;
                    }
                    else{
                        $productGalleryImage->product_id = $product->id;
                    }
                    $productGalleryImage->gallery_image = $galleryImageName;
                    $productGalleryImage->imageUrl = $imageUrl;
                    $productGalleryImage->save();
                }
            }
        }

        // Product color
        if($request->filled('color')){
            $colors = $request->color;
            if (is_array($colors) || is_object($colors)){
                foreach ($colors as $key => $color){
                    $colorName = new ProductColor();
                    if($request->type){
                        $colorName->product_id = $product->id;
                    }
                    else{
                        $colorName->product_id = $product->id;
                    }
                    $colorName->color = $request->color[$key];
                    $colorName->save();
                }
            }

        }
        // Product size
        if ($request->filled('size')){
            $sizes = $request->filled('size');
            if (is_array($sizes) || is_object($sizes)){
                foreach ($sizes as $key => $size){
                    $sizeName = new ProductSize();
                    $sizeName->product_id = $product->id;
                    $sizeName->size = $request->size[$key];
                    $sizeName->save();
                }
            }
        }
        // Related product
        if($request->filled('related_product_id')){
            $relatedProducts = $request->related_product_id;
            if (is_array($relatedProducts || is_object($relatedProducts))){
                foreach ($relatedProducts as $key => $related){
                    $relatedProduct = new RelatedProduct();
                    $relatedProduct->product_id = $product->id;
                    $relatedProduct->related_product_id = $request->related_product_id[$key];
                    $relatedProduct->save();
                }
            }
        }
        if($request->type){
            return redirect()->route('page.products.index')->with('success', 'Product has been successfully created.');
        }
        return redirect()->route('products.dropshipping')->with('success', 'Product has been successfully created.');
    }

    public function storeDropshippingVariableProduct (Request $request)
    {
        $image = $request->file('image');
        $input['image'] = rand().'pro_main'.$request->name.'.'.$image->getClientOriginalExtension();
        $destinationPath = 'product/images';
        $imgFile = Image::make($image->getRealPath());
        $imgFile->resize(240, 240, function ($constraint) {
            $constraint->aspectRatio();
        })->save($destinationPath.'/'.$input['image']);
        $image->move($destinationPath, $input['image']);
        $imageUrl = url($destinationPath.'/'.$input['image']);

        if($request->type){
            //dd($request->type);
            // $product = new PageProduct();
            $product = new Product();
            $page= AddPage::find($request->type);
            // $product->type = Str::slug($page->name);
            $product->page_name = Str::slug($page->name);
            $product->is_page_product = 1;
        }
        else{
            $product = new Product();
            $product->seo_title = $request->seo_title;
            $product->seo_description = $request->seo_description;
            $product->seo_keyword = $request->seo_keyword;
        }

        if(isset($request->priority)){
            $checkPriority = Product::where('priority', $request->priority)->where('id','!=', $product->id)->first();
            if($checkPriority == null){
                $product->priority = $request->priority;
            }
            elseif($checkPriority != null){
                $checkPriority->priority = 1000;
                $product->priority = $request->priority;
            }
        }
        $product->b_product_id = $request->b_product_id;
        $product->is_variable = true;
        $product->name = $request->name;
        $product->vendor_id = $request->vendor_id;
        $product->slug = str_replace(' ', '-', strtolower($request->name));
        $product->cat_id = $request->cat_id;
        $product->sub_cat_id = $request->sub_cat_id;
        $product->qty = $request->qty;
        $product->buy_price = $request->buy_price;
        $product->regular_price = $request->regular_price;
        if ($request->discount_price){
            $product->discount_price = $request->discount_price;
        }
        $product->product_code = $request->product_code;
        $product->short_description = $request->short_description;
        $product->long_description = $request->long_description;
        $product->policy = $request->policy;
        $product->product_type = $request->product_type;
        $product->image = $input['image'];
        $product->imageUrl = $imageUrl;
        $product->video_link = $request->video_link;
        $product->save();

        if(!empty($product)){
            if($request->gallery_image){

                $galleryImages = $request->file('gallery_image');
                $prices = $request->input('price');
                $colors = $request->input('color');
                $sizes = $request->input('size');

                foreach ($galleryImages as $index => $image) {
                    $galleryImageName = rand().$request->name.'.'.$image->extension();
                    $imgGallery = Image::make($image->path());
                    $imgGallery->resize(440, 440, function ($const) {
                        $const->aspectRatio();
                    })->save('galleryImage'. '/'. $galleryImageName);
                    $imageUrl = url('galleryImage'.'/'.$galleryImageName);

                    $productGalleryImage = new ProductImage();
                    $productGalleryImage->product_id = $product->id;  // assuming $product is available
                    $productGalleryImage->gallery_image = $galleryImageName;
                    $productGalleryImage->price = $prices[$index];
                    $productGalleryImage->color = $colors[$index];
                    $productGalleryImage->size = $sizes[$index];
                    $productGalleryImage->imageUrl = $imageUrl;
                    $productGalleryImage->save();
                }
            }
        }

        // Product color
        if($request->filled('color')){
            $colors = $request->color;
            if (is_array($colors) || is_object($colors)){
                foreach ($colors as $key => $color){
                    $colorName = new ProductColor();
                    $colorName->product_id = $product->id;
                    $colorName->color = $color;
                    $colorName->save();
                }
            }
        }
        // Product size
        if ($request->filled('size')) {
            $sizes = $request->input('size');
            if (is_array($sizes) || is_object($sizes)) {
                foreach ($sizes as $size) {
                    $sizeName = new ProductSize();
                    $sizeName->product_id = $product->id;
                    $sizeName->size = $size;
                    $sizeName->save();
                }
            }
        }
        // Related product
        if($request->filled('related_product_id')){
            $relatedProducts = $request->related_product_id;
            if (is_array($relatedProducts || is_object($relatedProducts))){
                foreach ($relatedProducts as $key => $related){
                    $relatedProduct = new RelatedProduct();
                    $relatedProduct->product_id = $product->id;
                    $relatedProduct->related_product_id = $request->related_product_id[$key];
                    $relatedProduct->save();
                }
            }
        }
        if($request->type){
            return redirect()->route('page.products.index')->with('success', 'Product has been successfully created.');
        }
        return redirect()->route('products.index')->with('success', 'Product has been successfully created.');
    }

    public function edit($id, $slug)
    {
        return view('admin.products.edit', [
            'categories' => Category::orderBy('created_at', 'desc')->get(),
            'subcategories' => Subcategory::orderBy('created_at', 'desc')->get(),
            'brands' => Brand::orderBy('created_at', 'desc')->get(),
            'product' => $this->product->edit($id)
        ]);
    }

    public function editVariableProduct ($id, $slug)
    {
        $categories = Category::orderBy('created_at', 'desc')->get();
        $subcategories = Subcategory::orderBy('created_at', 'desc')->get();
        $brands = Brand::orderBy('created_at', 'desc')->get();
        $product = $this->product->edit($id);

        return view ('admin.products.edit-variable-product', compact('categories', 'subcategories', 'brands', 'product'));
    }

    public function update(ProductUpdateRequest $request, $id)
    {
        $productUpdate = Product::find($id);
        $imageUpdate = $request->file('image');
        if (isset($imageUpdate)){
            if ($imageUpdate && file_exists(('product/images/').$productUpdate['image'])){
                unlink('product/images/'.$productUpdate->image);
            }

            $updateImageName['image'] = rand().'pro_main'.$request->name.'.'.$imageUpdate->getClientOriginalExtension();
            $updateDestinationPath = 'product/images';

            $imgFile = Image::make($imageUpdate->getRealPath());

            $imgFile->resize(240, 240, function ($constraint) {
                $constraint->aspectRatio();
            })->save($updateDestinationPath.'/'.$updateImageName['image']);
            $imageUpdate->move($updateDestinationPath, $updateImageName['image']);
            $productUpdate->image = $updateImageName['image'];
            $imageUrl = url($updateDestinationPath.'/'.$updateImageName['image']);
            $productUpdate->imageUrl = $imageUrl;
        }

        if(isset($request->priority)){
            $productUpdate->priority = $request->priority;
        }
        $productUpdate->name = $request->name;
        $productUpdate->slug = str_replace(' ', '-', strtolower($request->name));
        $productUpdate->cat_id = $request->cat_id;
        $productUpdate->sub_cat_id = $request->sub_cat_id;
        $productUpdate->qty = $request->qty;
        $productUpdate->buy_price = $request->buy_price;
        $productUpdate->regular_price = $request->regular_price;
        if ($request->discount_price){
            $productUpdate->discount_price = $request->discount_price;
        }
        $productUpdate->product_code = $request->product_code;
        $productUpdate->short_description = $request->short_description;
        $productUpdate->long_description = $request->long_description;
        $productUpdate->policy = $request->policy;
        $productUpdate->product_type = $request->product_type;
        $productUpdate->seo_title = $request->seo_title;
        $productUpdate->seo_description = $request->seo_description;
        $productUpdate->seo_keyword = $request->seo_keyword;
        $productUpdate->video_link = $request->video_link;
        $productUpdate->save();

        if($request->gallery_image){
            $imageGallery = $request->gallery_image;
            ProductImage::where('product_id', $productUpdate->id)->delete();
            foreach($imageGallery as $image){
                $galleryImageName = rand().$request->name.'.'.$image->extension();
                $imgGallery = Image::make($image->path());
                $imgGallery->resize(440, 440, function ($const) {
                    $const->aspectRatio();
                })->save('galleryImage'. '/'. $galleryImageName);
                $imageUrl = url('galleryImage'.'/'.$galleryImageName);

                $productGalleryImage = new ProductImage();
                $productGalleryImage->product_id = $productUpdate->id;
                $productGalleryImage->gallery_image = $galleryImageName;
                $productGalleryImage->imageUrl = $imageUrl;
                $productGalleryImage->save();
            }
        }

        // Product color
            if(!empty($productUpdate)){
                if ($request->filled('color')){
                    $colors = $request->color;
                    ProductColor::where('product_id', $productUpdate->id)->delete();
                    foreach ($colors as $key => $color){
                        $colorName = new ProductColor();
                        $colorName->product_id = $productUpdate->id;
                        $colorName->color = $request->color[$key];
                        $colorName->save();
                    }
                }
            }
        // Product size
        if ($request->has('size')){
            if(!empty($productUpdate)){
                $sizes = $request->size;
                ProductSize::where('product_id', $productUpdate->id)->delete();
                foreach ($sizes as $key => $size){
                    $sizeName = new ProductSize();
                    $sizeName->product_id = $productUpdate->id;
                    $sizeName->size = $request->size[$key];
                    $sizeName->save();
                }
            }
        }

        // Related product
        if(!empty($product)){
            $relatedProducts = $request->related_product_id;
            RelatedProduct::where('product_id', $productUpdate->id)->delete();
            foreach ($relatedProducts as $key => $related){
                $relatedProduct = new RelatedProduct();
                $relatedProduct->product_id = $product->id;
                $relatedProduct->related_product_id = $request->related_product_id[$key];
                $relatedProduct->save();
            }
        }

        return redirect()->route('products.index')->with('success', 'Product has been successfully updated.');
    }

    public function updateVariableProduct (Request $request, $id)
    {
        if($request->type){
            //dd($request->type);
            // $product = new PageProduct();
            $product = new Product();
            $page= AddPage::find($request->type);
            // $product->type = Str::slug($page->name);
            $product->page_name = Str::slug($page->name);
            $product->is_page_product = 1;
        }
        else{
            $product = Product::where('id', $id)->with('productImages')->first();
            $product->seo_title = $request->seo_title;
            $product->seo_description = $request->seo_description;
            $product->seo_keyword = $request->seo_keyword;
        }

        if(isset($request->image)){
            $image = $request->file('image');
            $input['image'] = rand().'pro_main'.$request->name.'.'.$image->getClientOriginalExtension();
            $destinationPath = 'product/images';
            $imgFile = Image::make($image->getRealPath());
            $imgFile->resize(240, 240, function ($constraint) {
                $constraint->aspectRatio();
            })->save($destinationPath.'/'.$input['image']);
            $image->move($destinationPath, $input['image']);
            $imageUrl = url($destinationPath.'/'.$input['image']);
            $product->image = $input['image'];
            $product->imageUrl = $imageUrl;
        }

        if(isset($request->priority)){
            $checkPriority = Product::where('priority', $request->priority)->where('id','!=', $product->id)->first();
            if($checkPriority == null){
                $product->priority = $request->priority;
            }
            elseif($checkPriority != null){
                $checkPriority->priority = 1000;
                $product->priority = $request->priority;
            }
        }
        $product->name = $request->name;
        $product->slug = str_replace(' ', '-', strtolower($request->name));
        $product->cat_id = $request->cat_id;
        $product->sub_cat_id = $request->sub_cat_id;
        $product->qty = $request->qty;
        $product->buy_price = $request->buy_price;
        $product->regular_price = $request->regular_price;
        if ($request->discount_price){
            $product->discount_price = $request->discount_price;
        }
        $product->product_code = $request->product_code;
        $product->short_description = $request->short_description;
        $product->long_description = $request->long_description;
        $product->policy = $request->policy;
        $product->product_type = $request->product_type;
        $product->video_link = $request->video_link;
        $product->save();

        if(!empty($product)){
            if($request->gallery_image){
                //Delete Previous Image...
                $prevousImage = ProductImage::where('product_id', $id)->get();
                if(!empty($prevousImage)){
                    foreach($prevousImage as $image){
                        $image->delete();
                    }
                }
                //Delete Previous Image...
                $galleryImages = $request->file('gallery_image');
                $prices = $request->input('price');
                $colors = $request->input('color');
                $sizes = $request->input('size');

                foreach ($galleryImages as $index => $image) {
                    $galleryImageName = rand().$request->name.'.'.$image->extension();
                    $imgGallery = Image::make($image->path());
                    $imgGallery->resize(440, 440, function ($const) {
                        $const->aspectRatio();
                    })->save('galleryImage'. '/'. $galleryImageName);
                    $imageUrl = url('galleryImage'.'/'.$galleryImageName);

                    $productGalleryImage = new ProductImage();
                    $productGalleryImage->product_id = $product->id;  // assuming $product is available
                    $productGalleryImage->gallery_image = $galleryImageName;
                    $productGalleryImage->price = $prices[$index];
                    $productGalleryImage->color = $colors[$index];
                    $productGalleryImage->size = $sizes[$index];
                    $productGalleryImage->imageUrl = $imageUrl;
                    $productGalleryImage->save();
                }
            }
        }

        // Product color
        if($request->filled('color')){
            $colors = $request->color;
            if (is_array($colors) || is_object($colors)){
                foreach ($colors as $key => $color){
                    $colorName = new ProductColor();
                    $colorName->product_id = $product->id;
                    $colorName->color = $color;
                    $colorName->save();
                }
            }
        }
        // Product size
        if ($request->filled('size')) {
            $sizes = $request->input('size');
            if (is_array($sizes) || is_object($sizes)) {
                foreach ($sizes as $size) {
                    $sizeName = new ProductSize();
                    $sizeName->product_id = $product->id;
                    $sizeName->size = $size;
                    $sizeName->save();
                }
            }
        }
        // Related product
        if($request->filled('related_product_id')){
            $relatedProducts = $request->related_product_id;
            if (is_array($relatedProducts || is_object($relatedProducts))){
                foreach ($relatedProducts as $key => $related){
                    $relatedProduct = new RelatedProduct();
                    $relatedProduct->product_id = $product->id;
                    $relatedProduct->related_product_id = $request->related_product_id[$key];
                    $relatedProduct->save();
                }
            }
        }
        if($request->type){
            return redirect()->route('page.products.index')->with('success', 'Product has been successfully created.');
        }
        return redirect()->route('products.index')->with('success', 'Product has been successfully created.');
    }

    public function active($id)
    {
        $this->product->active($id);
        return redirect()->back()->with('success', 'Product has been successfully Inactivated.');
    }

    public function inactive($id)
    {
        $this->product->inactive($id);
        return redirect()->back()->with('success', 'Product has been successfully Actived.');
    }

    public function delete($id)
    {
        $this->product->delete($id);
        return redirect()->back()->with('success', 'Product has been successfully deleted.');
    }

    public function pageProductcreate ()
    {
        return view('admin.page_products.create', [
            'categories' => Category::orderBy('created_at', 'desc')->get(),
            'subcategories' => Subcategory::orderBy('created_at', 'desc')->get(),
            'brands' => Brand::orderBy('created_at', 'desc')->get(),
            'pages' => AddPage::orderBy('created_at', 'desc')->get()
        ]);
    }

    public function pageProductEdit ($id, $slug)
    {
        // return view('admin.page_products.edit', [
        //     'categories' => Category::orderBy('created_at', 'desc')->get(),
        //     'subcategories' => Subcategory::orderBy('created_at', 'desc')->get(),
        //     'brands' => Brand::orderBy('created_at', 'desc')->get(),
        //     'product' => $this->page_product->edit($id)
        // ]);
        $categories = Category::orderBy('created_at', 'desc')->get();
        $subcategories = Subcategory::orderBy('created_at', 'desc')->get();
        $brands = Brand::orderBy('created_at', 'desc')->get();
        $product = Product::find($id);

        //dd($product);

        return view('admin.page_products.edit', compact('categories', 'subcategories', 'brands', 'product'));

    }

    public function pageProductUpdate (Request $request, $id)
    {
        // $productUpdate = PageProduct::find($id);
        $productUpdate = Product::find($id);

        $imageUpdate = $request->file('image');
        if (isset($imageUpdate)){
            if ($imageUpdate && file_exists(('product/images/').$productUpdate['image'])){
                unlink('product/images/'.$productUpdate->image);
            }

            $updateImageName['image'] = rand().'pro_main'.$request->name.'.'.$imageUpdate->getClientOriginalExtension();
            $updateDestinationPath = 'product/images';

            $imgFile = Image::make($imageUpdate->getRealPath());

            $imgFile->resize(240, 240, function ($constraint) {
                $constraint->aspectRatio();
            })->save($updateDestinationPath.'/'.$updateImageName['image']);
            $imageUpdate->move($updateDestinationPath, $updateImageName['image']);
            $productUpdate->image = $updateImageName['image'];
        }

        $productUpdate->name = $request->name;
        $productUpdate->slug = str_replace(' ', '-', strtolower($request->name));
        $productUpdate->cat_id = $request->cat_id;
        $productUpdate->sub_cat_id = $request->sub_cat_id;
        $productUpdate->qty = $request->qty;
        $productUpdate->buy_price = $request->buy_price;
        $productUpdate->regular_price = $request->regular_price;
        if ($request->discount_price){
            $productUpdate->discount_price = $request->discount_price;
        }
        $productUpdate->product_code = $request->product_code;
        $productUpdate->short_description = $request->short_description;
        $productUpdate->long_description = $request->long_description;
        $productUpdate->policy = $request->policy;
        $productUpdate->product_type = $request->product_type;
        $productUpdate->save();

        if($request->gallery_image){
            $imageGallery = $request->gallery_image;
            ProductImage::where('product_id', $productUpdate->id)->delete();
            foreach($imageGallery as $image){
                $galleryImageName = rand().$request->name.'.'.$image->extension();
                $imgGallery = Image::make($image->path());
                $imgGallery->resize(440, 440, function ($const) {
                    $const->aspectRatio();
                })->save('galleryImage'. '/'. $galleryImageName);

                $productGalleryImage = new ProductImage();
                $productGalleryImage->product_id = $productUpdate->id;
                $productGalleryImage->gallery_image = $galleryImageName;
                $productGalleryImage->save();
            }
        }

        // Product color
            if(!empty($productUpdate)){
                if ($request->filled('color')){
                    $colors = $request->color;
                    ProductColor::where('product_id', $productUpdate->id)->delete();
                    foreach ($colors as $key => $color){
                        $colorName = new ProductColor();
                        $colorName->product_id = $productUpdate->id;
                        $colorName->color = $request->color[$key];
                        $colorName->save();
                    }
                }
            }
        // Product size
        if ($request->has('size')){
            if(!empty($productUpdate)){
                $sizes = $request->size;
                ProductSize::where('product_id', $productUpdate->id)->delete();
                foreach ($sizes as $key => $size){
                    $sizeName = new ProductSize();
                    $sizeName->product_id = $productUpdate->id;
                    $sizeName->size = $request->size[$key];
                    $sizeName->save();
                }
            }
        }

        // Related product
        if(!empty($product)){
            $relatedProducts = $request->related_product_id;
            RelatedProduct::where('product_id', $productUpdate->id)->delete();
            foreach ($relatedProducts as $key => $related){
                $relatedProduct = new RelatedProduct();
                $relatedProduct->product_id = $product->id;
                $relatedProduct->related_product_id = $request->related_product_id[$key];
                $relatedProduct->save();
            }
        }

        return redirect('/page/products')->with('success', 'Product has been successfully updated.');
    }

    public function productQtyUpdate(Request $request, $id)
    {
        $productQty = Product::find($id);
        $productQty->qty = $request->qty;
        $productQty->save();
        return $productQty;
    }


    //Droploo Products with API...
    public function droplooProductList()
    {
        $appKey = 'ZZZWFYHPHXNPEZKO';
        $appSecret = 'i6qwm6Md9E3Af0N0Vg2kieWjFbloJqT5';
        $userName = 'ishak_adilamartcom';
        $apiUrl = 'https://dropshipper.droploo.com/api/products';


        $response = Http::withHeaders([
            'App-Secret' => $appSecret,
            'App-Key' => $appKey,
            'Username' => $userName,
        ])->get($apiUrl);

        if ($response->successful()) {
            $responseData = $response->json();
            $products = $responseData['products'] ?? [];
            $imagePath = $responseData['imagePath'] ?? '';

            return view('admin.products.droploo-product', compact('products', 'imagePath'));
        } else {
            return response()->json(['error' => 'Failed to fetch data from API'], $response->status());
        }
    }


    public function droplooProductAdd ($id)
    {
        //Is the Product Already Added....
        $product = Product::where('b_product_id', $id)->first();
        if($product != null){
            return redirect()->back()->with('error', 'Product is already added!');
        }
        //Is the Product Already Added....

        $appKey = 'ZZZWFYHPHXNPEZKO';
        $appSecret = 'i6qwm6Md9E3Af0N0Vg2kieWjFbloJqT5';
        $userName = 'ishak_adilamartcom';
        $apiUrl = 'https://dropshipper.droploo.com/api/product/'.$id;

        $response = Http::withHeaders([
            'App-Secret' => $appSecret,
            'App-Key' => $appKey,
            'Username' => $userName,
        ])->get($apiUrl);

        if ($response->successful()) {
            $responseData = $response->json();
            $product = $responseData['product'];
            $subcategories = Subcategory::orderBy('name', 'asc')->get();
            if($product['is_variable'] == 1){
                return view('admin.products.droploo-variable-product', compact('product', 'subcategories'));
            }
            else{
                return view('admin.products.droploo-product-add', compact('product', 'subcategories'));
            }
        }

        else {
            return response()->json(['error' => 'Failed to fetch data from API'], $response->status());
        }
    }

    public function topProductList ()
    {
        $products = TopProducts::orderBy('created_at', 'desc')->with('product')->get();
        return view('admin.products.top-list', compact('products'));
    }

    public function topProductCreate ()
    {
        return view('admin.products.create-top');
    }

    public function topProductStore (Request $request)
    {
        if($request->filled('related_product_id')){
            $relatedProducts = $request->related_product_id;
                foreach ($relatedProducts as $key => $related){
                    $relatedProduct = new TopProducts();
                    $relatedProduct->product_id = $request->related_product_id[$key];
                    $relatedProduct->save();
                }
                return redirect('/top/products/list')->with('success', 'Created Successfully!!');
        }
        return redirect('/top/products/list')->with('error', 'Select a product!');
    }

    public function topProductDelete ($id)
    {
        $product = TopProducts::find($id);
        $product->delete();
        return redirect()->back();
    }

    public function offerProductList ()
    {
        $products = OfferProduct::orderBy('created_at', 'desc')->with('product', 'offer')->get();
        return view('admin.products.offer-list', compact('products'));
    }

    public function offerProductCreate ()
    {
        return view('admin.products.create-offer-product');
    }

    public function offerProductStore (Request $request)
    {
        if(isset($request->offer_id)){
            if($request->filled('related_product_id')){
                $relatedProducts = $request->related_product_id;
                    foreach ($relatedProducts as $key => $related){
                        $relatedProduct = new OfferProduct();
                        $relatedProduct->product_id = $request->related_product_id[$key];
                        $relatedProduct->offer_id = $request->offer_id;
                        $relatedProduct->save();
                    }
                    return redirect('/offer/products/list')->with('success', 'Created Successfully!!');
            }
            else{
                return redirect()->back()->with('error', 'Select a product!');
            }
        }

        else{
            return redirect()->back()->with('error', 'Select an offer!');
        }
    }

    public function offerProductDelete ($id)
    {
        $product = OfferProduct::find($id);
        $product->delete();
        return redirect()->back();
    }
}
