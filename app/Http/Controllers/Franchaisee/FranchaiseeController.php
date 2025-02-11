<?php

namespace App\Http\Controllers\Franchaisee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Traits\CommonTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
class FranchaiseeController extends Controller
{
    use CommonTrait;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $type = $request->type;
        if($type == 'request'){
            $franchaisees = User::where('role', 'user')->where('approved_at', null)->get();
        }else{
            $franchaisees = User::where('role', 'user')->where('approved_at', '!=', null)->get();
        }
        return $this->sendResponse($franchaisees);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'phone_number' => 'required',
            'country' => 'required',
            'preferred_location' => 'required',
            'investment' => 'required', 
            'timeframe' => 'required',
            'joined_at' => 'required',
            'end_at' => 'required',
            'franchaisor_id' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $franchaisee = new User();
        $franchaisee->name = $request->name;
        $franchaisee->email = $request->email;
        $franchaisee->password = Hash::make('password');
        $franchaisee->phone_number = $request->phone_number;
        $franchaisee->country = $request->country;
        $franchaisee->preferred_location = $request->preferred_location;
        $franchaisee->investment = $request->investment;
        $franchaisee->timeframe = $request->timeframe;
        $franchaisee->joined_at = $request->joined_at;
        $franchaisee->end_at = $request->end_at;
        $franchaisee->franchaisor_id = $request->franchaisor_id;
        $franchaisee->role = 'user';
        $franchaisee->save();

        return $this->sendResponse($franchaisee, 'Franchaisee created successfully');
        
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(string $id)
    {
        if(Auth::user()->role != 'admin'){
            return $this->sendError('Unauthorized', ['error' => 'Unauthorized']);
        }
        $franchaisee = User::find($id);
        $franchaisee->approved_at = now();
        $franchaisee->save();

        return $this->sendResponse('Franchaisee updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $franchaisee = User::find($id);
        $franchaisee->delete();
        return $this->sendResponse($franchaisee, 'Franchaisee deleted successfully');
    }
}
