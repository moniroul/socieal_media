<?php

namespace App\Http\Controllers;

use App\Models\UserBasicInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserBasicInfoController extends Controller
{
    public function storeBasicInfo(Request $request)
    {
        $validated = $request->validate([
            'address' => 'required|string',
            'postcode' => 'required|string',
            'number' => 'required|string',
        ]);


        $userID =  Auth::guard('api')->user()->id;

        $basicInfo =   UserBasicInfo::create([
            'user_id' => $userID,
            'address' => $validated['address'],
            'postcode' => $validated['postcode'],
            'number' => $validated['number'],
        ]);

        return response()->json(['message' => 'Basic info saved successfully.', 'data' => $basicInfo]);
    }
}
