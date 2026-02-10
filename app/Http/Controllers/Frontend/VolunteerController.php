<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Frontend\StoreVolunteerRequest;
use App\Models\Slider;
use App\Models\Volunteer;
use Illuminate\Http\Request;

class VolunteerController extends Controller
{
    public function join()
    {
        $sliders = Slider::where('publish', 1)->limit(3)->get();

        return view('frontend.volunteers.join', compact('sliders'));
    }

    public function store(StoreVolunteerRequest $request)
    {
        $volunteer = Volunteer::create($request->only([
            'name', 'identity_num', 'email', 'phone_number',
            'interest', 'initiative_name', 'prev_experience',
        ]));

        if ($request->hasFile('photo')) {
            $volunteer->addMediaFromRequest('photo')->toMediaCollection('photo');
        }

        if ($request->hasFile('cv')) {
            $volunteer->addMediaFromRequest('cv')->toMediaCollection('cv');
        }

        return redirect()
            ->route('frontend.volunteer.join')
            ->with('successMessage', trans('frontend.volunteer.submitted'));
    }
}
