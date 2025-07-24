<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\About;
use App\Models\Category;
use App\Models\Contact;
use App\Models\GeneralSetting;
use App\Models\GoogleFacebookCode;
use App\Models\Offer;
use App\Models\OfferProduct;
use App\Models\Payment;
use App\Models\PaymentPolicy;
use App\Models\PrivacyPolicy;
use App\Models\RefundPolicy;
use App\Models\Setting;
use App\Models\TermsCondition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GeneralDataController extends Controller
{
    public function getSettings()
    {
        try {
            $settings = GeneralSetting::first();

            if (!$settings) {
                return response()->json([
                    'error' => true,
                    'message' => 'Settings not found',
                    'generalData' => null
                ], 404);
            }

            return response()->json([
                'error' => false,
                'message' => 'Settings retrieved successfully',
                'generalData' => $settings
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => 'An error occurred while retrieving settings',
                'generalData' => null
            ], 500);
        }
    }

    public function getCategories ()
    {
        try {
            $categories = Category::where('status', 1)->orderBy('priority', 'asc')->with('subcategories')->get();

            if ($categories->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Categories not found',
                    'data' => null
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Categories retrieved successfully',
                'data' => $categories
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve categories. ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    public function getSliders ()
    {
        try {
            $sliders = Setting::get();

            if ($sliders->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sliders not found',
                    'data' => null
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'sliders retrieved successfully',
                'data' => $sliders
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve sliders. ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    public function getOffers ()
    {
        try {
            $offers = Offer::where('is_active', 1)->get();

            if ($offers->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'offers not found',
                    'data' => null
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'offers retrieved successfully',
                'data' => $offers
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve offers. ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    public function getOfferProducts ($slug)
    {
        try {

            $offer = Offer::where('slug', $slug)->first();
            if($offer == null){
                return response()->json([
                    'success' => false,
                    'message' => 'offer not found',
                    'data' => null
                ], 404);
            }

            $offerProducts = OfferProduct::where('offer_id', $offer->id)->orderBy('id', 'desc')->with('product')->get();

            if ($offerProducts->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Products not found',
                    'data' => null
                ], 404);
            }

            $products = $offerProducts->pluck('product');

            return response()->json([
                'success' => true,
                'message' => 'Products retrieved successfully',
                'data' => [
                    'offer' => $offer->name,
                    'products' => $products
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve products. ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    public function gtm()
    {
        $code = GoogleFacebookCode::first();

        return response()->json([
            'gtm' => [
                'gtm_id' => $code->gtm_id ?? null,
            ]
        ]);
    }

    public function aboutUs ()
    {
        try {
            $aboutUs = About::first();

            if (!$aboutUs) {
                return response()->json([
                    'error' => true,
                    'message' => 'Data not found',
                    'data' => null
                ], 404);
            }

            return response()->json([
                'error' => false,
                'message' => 'Data retrieved successfully',
                'data' => $aboutUs
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => 'An error occurred while retrieving data',
                'data' => null
            ], 500);
        }
    }

    public function privacyPolicy ()
    {
        try {
            $privacyPolicy = PrivacyPolicy::first();

            if (!$privacyPolicy) {
                return response()->json([
                    'error' => true,
                    'message' => 'Data not found',
                    'data' => null
                ], 404);
            }

            return response()->json([
                'error' => false,
                'message' => 'Data retrieved successfully',
                'data' => $privacyPolicy
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => 'An error occurred while retrieving data',
                'data' => null
            ], 500);
        }
    }

    public function termsConditions ()
    {
        try {
            $termsConditions = TermsCondition::first();

            if (!$termsConditions) {
                return response()->json([
                    'error' => true,
                    'message' => 'Data not found',
                    'data' => null
                ], 404);
            }

            return response()->json([
                'error' => false,
                'message' => 'Data retrieved successfully',
                'data' => $termsConditions
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => 'An error occurred while retrieving data',
                'data' => null
            ], 500);
        }
    }

    public function refundPolicy ()
    {
        try {
            $refundPolicy = RefundPolicy::first();

            if (!$refundPolicy) {
                return response()->json([
                    'error' => true,
                    'message' => 'Data not found',
                    'data' => null
                ], 404);
            }

            return response()->json([
                'error' => false,
                'message' => 'Data retrieved successfully',
                'data' => $refundPolicy
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => 'An error occurred while retrieving data',
                'data' => null
            ], 500);
        }
    }

    public function paymentPolicy ()
    {
        try {
            $paymentPolicy = PaymentPolicy::first();

            if (!$paymentPolicy) {
                return response()->json([
                    'error' => true,
                    'message' => 'Data not found',
                    'data' => null
                ], 404);
            }

            return response()->json([
                'error' => false,
                'message' => 'Data retrieved successfully',
                'data' => $paymentPolicy
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => 'An error occurred while retrieving data',
                'data' => null
            ], 500);
        }
    }

    public function ContactStore (Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'phone' => 'required|string',
            'message' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        try{
            $contact = new Contact();

            $contact->name    = $request->name;
            $contact->phone   = $request->phone;
            $contact->message = $request->message;

            $contact->save();

            return response()->json([
                'success' => true,
                'message' => 'Contact form is submitted successfully',
                'data' => $contact
            ], 201);
        }

        catch(\Exception $e){
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit. ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    public function getOfferTime ()
    {
        try {
            $settings = GeneralSetting::first();
            $offerTime = $settings->pluck('offer_time');

            if (!$offerTime) {
                return response()->json([
                    'error' => true,
                    'message' => 'Data not found',
                    'data' => null
                ], 404);
            }

            return response()->json([
                'error' => false,
                'message' => 'Data retrieved successfully',
                'data' => [
                    'offer_time' => $offerTime
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => 'An error occurred while retrieving data',
                'data' => null
            ], 500);
        }
    }

}
