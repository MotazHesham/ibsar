<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\FrontAchievement;
use App\Models\FrontPartner;
use App\Models\FrontReview; 
use App\Models\FrontProject;
use App\Models\Slider;
use App\Models\Subscription;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {  
        $achievements = FrontAchievement::all();
        $partners = FrontPartner::all();
        $reviews = FrontReview::all(); 
        $projects = FrontProject::take(8)->get();   
        $sliders = Slider::where('publish',1)->limit(3)->get();
        return view('frontend.home', compact('achievements', 'partners', 'reviews', 'projects', 'sliders'));
    }

    public function subscription(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email|unique:subscriptions|max:255',
            ], [
                'email.required' => 'Please enter your email address',
                'email.email' => 'Please enter a valid email address',
                'email.unique' => 'This email is already subscribed to our newsletter'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            session()->flash('errorMessage', $e->errors()['email'][0]);
            return redirect()->back();
        }

        $subscription = Subscription::create([
            'email' => $request->email,  
        ]);

        session()->flash('successMessage', trans('frontend.footer.newsletter_success'));
        return redirect()->back();
    }

    public function getDistrictsByCity(Request $request)
    {
        $city = City::find($request->city_id);
        if(!$city){
            return response()->json([]);
        }
        $districts = $city->districts()->get()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');
        return response()->json($districts);
    }
}
