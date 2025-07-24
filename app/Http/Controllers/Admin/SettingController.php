<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\About;
use App\Models\Banner;
use App\Models\GeneralSetting;
use App\Models\GoogleFacebookCode;
use App\Models\PaymentPolicy;
use App\Models\PrivacyPolicy;
use App\Models\RefundPolicy;
use App\Models\TermsCondition;
use Carbon\Carbon;
use Codeboxr\PathaoCourier\Facade\PathaoCourier;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;
use File;

class SettingController extends Controller
{
    public function bannerAdd()
    {
        return view('admin.settings.banner-create');
    }

    public function bannerList()
    {
        $banners = Banner::orderBy('created_at', 'desc')->get();
        return view('admin.settings.banner-list', compact('banners'));
    }

    public function bannerStore(Request $request)
    {
        $this->validate($request, [
            'type' => 'required',
            'image' => 'required',
        ]);
        $bannerImage = $request->file('image');
        $imageName = time().'.'.$bannerImage->getClientOriginalExtension();
        $destinationPath = 'setting';
        $imgFile = Image::make($bannerImage->getRealPath());
        $imgFile->save($destinationPath.'/'.$imageName);
        $bannerImage->move($destinationPath, $imageName);

        $addNewBanner = new Banner();
        $addNewBanner->type = $request->type;
        $addNewBanner->image = $imageName;
        $addNewBanner->save();
        return redirect('/banner/list')->with('success', 'Banner has been created.');
    }

    public function bannerEdit($id)
    {
        $banner = Banner::find($id);
        return view('admin.settings.banner-edit', compact('banner'));
    }

    public function bannerUpdate(Request $request, $id)
    {
        $this->validate($request, [
            'type' => 'required',
            'image' => 'required',
        ]);
        $banner = Banner::find($id);
        if ($request->hasFile('image')){
            if ($banner->image && file_exists(public_path('/setting/'.$banner->image))){
                unlink(public_path('/setting/'.$banner->image));
            }

            $update_bannerImage = $request->file('image');
            $update_imageName = time().'.'.$update_bannerImage->getClientOriginalExtension();
            $update_destinationPath = 'setting';
            $update_imgFile = Image::make($update_bannerImage->getRealPath());
            $update_imgFile->save($update_destinationPath.'/'.$update_imageName);
            $update_bannerImage->move($update_destinationPath, $update_imageName);
            $banner->image = $update_imageName;
        }
        $banner->type = $request->type;
        $banner->save();
        return redirect('/banner/list')->with('success', 'Banner has been created.');
    }

    public function bannerDelete($id)
    {
        $banner = Banner::find($id);
        $banner->delete();
        File::delete(public_path('setting/'.$banner->image));
        return redirect()->back()->with('success', 'Banner has been deleted');
    }


    public function privacyPolicy()
    {
        $privacyPolicy = PrivacyPolicy::first();
        return view('admin.settings.privacy-policy', compact('privacyPolicy'));
    }

    public function privacyPolicyStore(Request $request)
    {
        PrivacyPolicy::updateOrCreate([
            'id' => 1
        ], [
            'privacy_policy' => $request->privacy_policy
        ]);
        $this->setSuccessMessage('Privacy Policy has been updated');
        return redirect()->back();
    }

    public function termsCondition()
    {
        $termsCondition = TermsCondition::first();
        return view('admin.settings.terms-condition', compact('termsCondition'));
    }

    public function termsConditionStore(Request $request)
    {
        TermsCondition::updateOrCreate([
            'id' => 1
        ], [
            'terms_condition' => $request->terms_condition
        ]);
        $this->setSuccessMessage('Terms Condition has been updated');
        return redirect()->back();
    }

    public function refundPolicy()
    {
        $refundPolicy = RefundPolicy::first();
        return view('admin.settings.refund-policy', compact('refundPolicy'));
    }

    public function refundPolicyStore(Request $request)
    {
        RefundPolicy::updateOrCreate([
            'id' => 1
        ], [
            'refund_policy' => $request->refund_policy
        ]);
        $this->setSuccessMessage('Terms Condition has been updated');
        return redirect()->back();
    }

    public function paymentPolicy()
    {
        $paymentPolicy = PaymentPolicy::first();
        return view('admin.settings.payment-policy', compact('paymentPolicy'));
    }

    public function paymentPolicyStore(Request $request)
    {
        PaymentPolicy::updateOrCreate([
            'id' => 1
        ], [
            'payment_policy' => $request->payment_policy
        ]);
        $this->setSuccessMessage('Payment policy has been updated');
        return redirect()->back();
    }

    public function adminAbout()
    {
        $about = About::first();
        return view('admin.settings.about', compact('about'));
    }

    public function adminAboutStore(Request $request)
    {
        About::updateOrCreate([
            'id' => 1
        ], [
            'about' => $request->about
        ]);
        $this->setSuccessMessage('About has been updated');
        return redirect()->back();
    }

    public function showPathaoCourier(){
        return view('admin.settings.pathao-courier');
    }

    public function pathaoCourierStore(Request $request)
    {
       PathaoCourier::order()->create([
            "store_id"            => $request->store_id, // Find in store list,
            "merchant_order_id"   => $request->merchant_order_id, // Unique order id
            "recipient_name"      => $request->recipient_name, // Customer name
            "recipient_phone"     => $request->recipient_phone, // Customer phone
            "recipient_address"   => $request->recipient_address, // Customer address
            "recipient_city"      => $request->recipient_city, // Find in city method
            "recipient_zone"      => $request->recipient_zone, // Find in zone method
            "recipient_area"      => $request->recipient_area, // Find in Area method
            "delivery_type"       => $request->delivery_type, // 48 for normal delivery or 12 for on demand delivery
            "item_type"           => $request->item_type, // 1 for document,
            "special_instruction" => $request->speciali_instruction,
            "item_quantity"       => $request->item_quantity, // item quantity
            "item_weight"         => $request->item_weight, // parcel weight
            "amount_to_collect"   => $request->amount_to_collect, // amount to collect
            "item_description"    => $request->item_description // product details
        ]);

        return redirect()->back()->with('success', 'Parcel has been created');
    }


    public function gtmForm()
    {
        $code = GoogleFacebookCode::find(1);
        return view('admin.settings.gtm', compact('code'));
    }

    public function gtmStore(Request $request)
    {
        $this->validate($request, [
            'gtm_id' => 'required',
        ]);

        GoogleFacebookCode::updateOrCreate([
            'id' => 1
        ],[
            'gtm_id' => $request->gtm_id,
        ]);

        return redirect()->back()->with('success', 'Code has been updtaed');
    }

    public function generalSetting ()
    {
        $general_setting = GeneralSetting::first();
        return view('admin.general_setting.index', compact('general_setting'));
    }

    public function updateGeneralSetting (Request $request)
    {
        $general_setting = GeneralSetting::first();
        if($request->hasFile('logo')){
            if(file_exists(public_path('setting/'.$general_setting->logo))){
                File::delete(public_path('setting/'.$general_setting->logo));
                $name = time() . '.' . $request->logo->getClientOriginalExtension();
                $request->logo->move('setting/', $name);
                $general_setting->logo = $name;
                $imageUrl = url('setting/'.$name);
                $general_setting->logo_url = $imageUrl;
            }
            else{
                $name = time() . '.' . $request->logo->getClientOriginalExtension();
                $request->logo->move('setting/', $name);
                $general_setting->logo = $name;
                $imageUrl = url('setting/'.$name);
                $general_setting->logo_url = $imageUrl;
            }

        }

        $general_setting->phone = $request->phone;
        $general_setting->email = $request->email;
        $general_setting->facebook = $request->facebook;
        $general_setting->instagram = $request->instagram;
        $general_setting->twitter = $request->twitter;
        $general_setting->youtube = $request->youtube;
        $general_setting->address = $request->address;
        $offerTime = date('Y-m-d', strtotime($request->input('offer_time'))) . 'T' . date('H:i:s', strtotime($request->input('offer_time')));
        $general_setting->offer_time = $offerTime;

        $general_setting->save();

        return redirect()->back()->withSuccess('Updated Successfully!!');
    }
}
