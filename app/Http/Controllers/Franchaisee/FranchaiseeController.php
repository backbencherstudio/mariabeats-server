<?php

namespace App\Http\Controllers\Franchaisee;

use App\Http\Controllers\Controller;
use App\Models\Address\Country;
use App\Models\Franchaisee\FranchaiseeRequest;
use App\Models\Franchaisor\Franchaisor;
use Illuminate\Http\Request;
use App\Models\User;
use App\Traits\CommonTrait;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Mail;
use App\Mail\FranchaiseeRequestCreated;

class FranchaiseeController extends Controller
{
    use CommonTrait;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = User::where('role', 'user');
        // get the approved franchaisee from franchaisee_requests table
        $approvedFranchaisees = FranchaiseeRequest::where('status', 'approved')->get();
        
        $type = $request->type;
        if ($type == 'request') {
            $query->where('approved_at', null);
        } else {
            $query->where('approved_at', '!=', null);
        }
        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('joined_at', [
                Carbon::parse($request->query('start_date'))->format('Y-m-d 00:00:00'),
                Carbon::parse($request->query('end_date'))->format('Y-m-d 23:59:59')
            ]);
        } elseif ($request->has('start_date')) {
            $query->where('joined_at', '=', Carbon::parse($request->query('start_date'))->format('Y-m-d 00:00:00'));
        } elseif ($request->has('end_date')) {
            $query->where('joined_at', '=', Carbon::parse($request->query('end_date'))->format('Y-m-d 23:59:59'));
        }
        if ($request->has('location_id')) {
            $query->where('address', (int) $request->query('location_id'));
        }
        if ($request->has('investment')) {
            $investmentRange = explode('-', $request->query('investment'));
            if (count($investmentRange) == 2) {
                $query->whereBetween('investment', [
                    (int) $investmentRange[0],
                    (int) $investmentRange[1]
                ]);
            }
        }
        if ($request->has('timeframe')) {
            $query->where('timeframe', (int) $request->query('timeframe'));
        }
        $franchaisees = $query->get();
        foreach ($franchaisees as $franchaisee) {
            $franchaisee->location = Country::where('id', (int) $franchaisee->country)->first();
        }
        // pass the franchaisor name
        foreach ($franchaisees as $franchaisee) {
            $franchaisor = Franchaisor::where('id', (int) $franchaisee->franchaisor_id)->first();
            $franchaisee->brand_name = $franchaisor->brand_name;
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
        try {
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
                // 'franchaisor_id' => 'nullable',
            ]);

            if ($validator->fails()) {
                return $this->sendError('Validation Error.', $validator->errors());
            }

            // check if franchaisee is exists
            $franchaisee = User::where('email', $request->email)->first();
            if ($franchaisee) {
                return $this->sendError('Franchaisee already exists');
            }

            $franchaisee = new User();
            $franchaisee->name = $request->name;
            $franchaisee->email = $request->email;
            $franchaisee->password = Hash::make('password');
            $franchaisee->approved_at = now();
            $franchaisee->phone_number = $request->phone_number;
            $franchaisee->country = $request->country;
            $franchaisee->preferred_location = $request->preferred_location;
            $franchaisee->investment = $request->investment;
            $franchaisee->timeframe = $request->timeframe;
            $franchaisee->joined_at = $request->joined_at;
            $franchaisee->end_at = $request->end_at;
            if ($request->has('franchaisor_id')) {
                // check if franchaisor is exists
                $franchaisor = Franchaisor::find($request->franchaisor_id);
                if (!$franchaisor) {
                    return $this->sendError('Franchaisor not found');
                }
                $franchaisee->franchaisor_id = $request->franchaisor_id;
            }
            $franchaisee->role = 'user';
            $franchaisee->save();

            return $this->sendResponse($franchaisee, 'Franchaisee created successfully');
        } catch (\Throwable $th) {
            return $this->sendError('Error creating franchaisee', $th->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $franchaisee = User::find($id);
        if (!$franchaisee) {
            return $this->sendError('Franchaisee not found');
        }
        $franchaisee->location = Country::where('id', (int) $franchaisee->country)->first();
        return $this->sendResponse($franchaisee);
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
    public function update(Request $request, string $id)
    {
        try {
            if (Auth::user()->role != 'admin') {
                return $this->sendError('Unauthorized', ['error' => 'Unauthorized']);
            }
            $franchaisee = User::find($id);
            if (!$franchaisee) {
                return $this->sendError('Franchaisee not found');
            }
            if ($request->has('name')) {
                $franchaisee->name = $request->name;
            }
            if ($request->has('email')) {
                $franchaisee->email = $request->email;
            }
            if ($request->has('phone_number')) {
                $franchaisee->phone_number = $request->phone_number;
            }
            if ($request->has('country')) {
                $franchaisee->country = $request->country;
            }
            if ($request->has('preferred_location')) {
                $franchaisee->preferred_location = $request->preferred_location;
            }
            if ($request->has('timeframe')) {
                $franchaisee->timeframe = $request->timeframe;
            }
            if ($request->has('approved_at')) {
                $franchaisee->approved_at = now();
            }
            if ($request->has('joined_at')) {
                $franchaisee->joined_at = $request->joined_at;
            }
            if ($request->has('end_at')) {
                $franchaisee->end_at = $request->end_at;
            }
            if ($request->has('status')) {
                $franchaisee->status = $request->status;
            }
            if ($request->has('franchaisor_id')) {
                $franchaisee->franchaisor_id = $request->franchaisor_id;
            }
            $franchaisee->save();

            return $this->sendResponse('Franchaisee updated successfully');
        } catch (\Throwable $th) {
            return $this->sendError('Error updating franchaisee', $th->getMessage());
        }
    }

    public function exportData(Request $request)
    {
        $fileName = 'franchaisees.csv';
        $franchaiseesData = User::where('role', 'user')->get();

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function () use ($franchaiseesData) {
            $handle = fopen('php://output', 'w');

            // CSV headers
            $fields = ['ID', 'Name', 'Email', 'Number', 'Country', 'Based On', 'Investment', 'Investment Brand', 'Date of Request'];
            fputcsv($handle, $fields);

            foreach ($franchaiseesData as $data) {

                fputcsv($handle, [
                    $data->id,
                    $data->name,
                    $data->email,
                    $data->phone_number,
                    $data->country,
                    $data->preferred_location,
                    $data->investment,
                    $data->investment_brand,
                    $data->joined_at,
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, Response::HTTP_OK, $headers);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $franchaisee = User::find($id);
            $franchaisee->delete();
            return $this->sendResponse($franchaisee, 'Franchaisee deleted successfully');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), 500);
        }
    }

    public function doFranchaiseeRequest(Request $request, string $id)
    {
        // dd($request->all());
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'nullable|string|max:255',
                'email' => 'nullable|string|email|max:255',
                'phone_number' => 'nullable|string|max:255',
                'country' => 'nullable|string|max:255',
                'preferred_location' => 'nullable|string|max:255',
                'investment_amount' => 'nullable|numeric',
                'timeframe' => 'nullable|string|max:255',
                'message' => 'nullable|string|max:255',
            ]);

            if ($validator->fails()) {
                return $this->sendError('Validation Error.', $validator->errors());
            }

            $franchaisor = Franchaisor::find($id);
            if (!$franchaisor) {
                return $this->sendError('Franchaisor not found');
            }

            $franchaiseeRequest = new FranchaiseeRequest();
            $franchaiseeRequest->name = $request->name;
            $franchaiseeRequest->email = $request->email;
            $franchaiseeRequest->phone_number = $request->phone_number;
            $franchaiseeRequest->country = $request->country;
            $franchaiseeRequest->preferred_location = $request->preferred_location;
            $franchaiseeRequest->investment_amount = $request->investment_amount;
            $franchaiseeRequest->timeframe = $request->timeframe;
            $franchaiseeRequest->message = $request->message;
            $franchaiseeRequest->franchaisor_id = $franchaisor->id;
            $franchaiseeRequest->save();

            // Send email notification
            Mail::to('gulffranchisehub@gmail.com')->send(new FranchaiseeRequestCreated($franchaiseeRequest, $franchaisor));

            return $this->sendResponse(['franchaiseeRequest' => $franchaiseeRequest, 'message' => 'Franchaisee request sent successfully']);
        } catch (\Throwable $th) {
            return $this->sendError('Error sending franchaisee request', $th->getMessage());
        }
    }

    public function franchaiseeRequests(Request $request)
    {
        $franchaiseeRequests = FranchaiseeRequest::query()
            ->where('status', '!=', 'rejected')
            ->with('franchaisor')
            ->get();

        return $this->sendResponse($franchaiseeRequests);
    }

    public function franchaiseeRequestUpdate(Request $request, string $id)
    {
        try {
            $franchaiseeRequest = FranchaiseeRequest::find($id);
            $franchaiseeRequest->status = $request->status;
            // when status is approved, create a new user
            if ($request->status == 'approved') {
                $user = new User();
                $user->name = $franchaiseeRequest->name;
                $user->email = $franchaiseeRequest->email;
                $user->password = Hash::make('password');
                $user->role = 'user';
                $user->approved_at = now();
                $user->phone_number = $franchaiseeRequest->phone_number;
                if ($franchaiseeRequest->country) {
                    $country = Country::where('name', $franchaiseeRequest->country)->first();
                    if ($country) {
                        $user->country = $country->id;
                    }
                }
                $user->investment = $franchaiseeRequest->investment_amount;
                $user->timeframe = $franchaiseeRequest->timeframe;
                $user->joined_at = now();
                $user->end_at = now()->addYears(1);
                $user->franchaisor_id = $franchaiseeRequest->franchaisor_id;
                $user->save();
            }

            if ($request->status == 'rejected') {
                $franchaiseeRequest->status = 'rejected';
                $user = User::where('email', $franchaiseeRequest->email)->first();
                if ($user) {
                    $user->delete();
                }
            }

            $franchaiseeRequest->save();
            
            return $this->sendResponse(['franchaiseeRequest' => $franchaiseeRequest, 'message' => 'Franchaisee request updated successfully']);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), 500);
        }
    }

    public function franchaiseeRequestDelete(Request $request, string $id)
    {
        try {
            $franchaiseeRequest = FranchaiseeRequest::find($id);
            $franchaiseeRequest->delete();
            return $this->sendResponse(['franchaiseeRequest' => $franchaiseeRequest, 'message' => 'Franchaisee request deleted successfully']);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), 500);
        }
    }
}
