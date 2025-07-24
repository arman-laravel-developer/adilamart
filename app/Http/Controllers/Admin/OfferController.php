<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Offer;
use App\Models\OfferProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class OfferController extends Controller
{
    public function offerList ()
    {
        $offers = Offer::get();
        return view ('admin.offer.list', compact('offers'));
    }

    public function offerCreate ()
    {
        return view ('admin.offer.create');
    }

    public function offerStore (Request $request)
    {
        $offer = new Offer();

        if(isset($request->image)){
            $imageName = time().'.'.$request->image->extension();
            $request->image->move('offer/', $imageName);
            $imageUrl = url('offer/'.$imageName);
        }

        $offer->name = $request->name;
        $offer->slug = Str::slug($request->name);
        $offer->image = $imageName;
        $offer->imageUrl = $imageUrl;
        $offer->save();

        return redirect()->back()->with('success', 'Offer has been successfully created.');
    }

    public function offerEdit ($id)
    {
        $offer = Offer::find($id);
        return view ('admin.offer.edit', compact('offer'));
    }

    public function offerUpdate (Request $request, $id)
    {
        $offer = Offer::find($id);

        if(isset($request->image)){
            if ($offer->image != null) {
                $imagePath = public_path('offer/' . $offer->image);
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }
            $imageName = time().'.'.$request->image->extension();
            $request->image->move('offer/', $imageName);
            $imageUrl = url('offer/'.$imageName);
            $offer->image = $imageName;
            $offer->imageUrl = $imageUrl;
        }

        $offer->name = $request->name;
        $offer->slug = Str::slug($request->name);
        $offer->save();

        return redirect()->back()->with('success', 'Offer has been successfully updated.');
        
    }

    public function offerInActivate ($id)
    {
        $offer = Offer::find($id);
        $offer->is_active = false;
        $offer->save();

        return redirect()->back();
    }

    public function offerActivate ($id)
    {
        $offer = Offer::find($id);
        $offer->is_active = true;
        $offer->save();

        return redirect()->back();
    }

    public function offerDelete ($id)
    {
        $offer = Offer::find($id);
        $offerProducts = OfferProduct::where('offer_id', $id)->get();

        if($offerProducts->isNotEmpty()){
            foreach($offerProducts as $product){
                $product->delete();
            }
        }

        if ($offer->image != null) {
            $imagePath = public_path('offer/' . $offer->image);
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }

        $offer->delete();
        return redirect()->back();
    }
}
