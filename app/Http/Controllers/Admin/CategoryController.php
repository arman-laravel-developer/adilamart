<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use App\Repository\Interface\CategoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{

    protected $category;

    public function __construct(CategoryInterface $category)
    {
        $this->category = $category;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.category.index', [
            'categories' => $this->category->getAllData()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.category.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(CategoryRequest $request)
    {
        // $data = $request->only(['name', 'image', 'slug', 'banner']);
        // $this->category->storeOrUpdate($id = null, $data);
        $imageName = time().'.'.$request->image->extension();
        $request->image->move('category/', $imageName);
        $imageUrl = url('category/'.$imageName);

        $bannerImage = rand().'.'.$request->banner->extension();
        $request->banner->move('category/', $bannerImage);
        $bannerUrl = url('category/'.$bannerImage);

        $category = new Category();
        $category->name = $request->name;
        $category->image = $imageName;
        $category->imageUrl = $imageUrl;
        $category->banner = $bannerImage;
        $category->bannerUrl = $bannerUrl;
        $category->slug = Str::slug($request->name);
        $category->save();

        return redirect()->route('categories.index')->with('success', 'Category has been successfully created.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function edit($id)
    {
        return view('admin.category.edit', [
            'category' => $this->category->edit($id)
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        // $data = $request->only(['name', 'image', 'slug', 'banner']);
        // if(!is_null($id)){
        //     $this->category->storeOrUpdate($id, $data);
        //     return redirect()->route('categories.index')->with('success', 'Category has been successfully updated.');
        // }

        $category = Category::find($id);
        if(isset($request->image)){
            $imageName = time().'.'.$request->image->extension();
            $request->image->move('category/', $imageName);
            $category->image = $imageName;
            $imageUrl = url('category/'.$imageName);
            $category->imageUrl = $imageUrl;
        }

        if(isset($request->banner)){
            $bannerImage = rand().'.'.$request->banner->extension();
            $request->banner->move('category/', $bannerImage);
            $category->banner = $bannerImage;
            $bannerUrl = url('category/'.$bannerImage);
            $category->bannerUrl = $bannerUrl;
        }

        $category->name = $request->name;
        $category->slug = Str::slug($request->name);
        $category->save();

        return redirect()->route('categories.index')->with('success', 'Category has been successfully created.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->category->delete($id);
        return redirect()->route('categories.index')->with('error', 'Category has been successfully deleted.');
    }
}
