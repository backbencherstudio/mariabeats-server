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

class FranchaiseeController extends Controller
{
    use CommonTrait;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = User::where('role', 'user');
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
            // if ($request->has('franchaisor_id')) {
            //     // check if franchaisor is exists
            //     $franchaisor = Franchaisor::find($request->franchaisor_id);
            //     if (!$franchaisor) {
            //         return $this->sendError('Franchaisor not found');
            //     }
            //     $franchaisee->franchaisor_id = $request->franchaisor_id;
            // }
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
        try {
            if (Auth::user()->role != 'admin') {
                return $this->sendError('Unauthorized', ['error' => 'Unauthorized']);
            }
            $franchaisee = User::find($id);
            $franchaisee->approved_at = now();
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
        $franchaisee = User::find($id);
        $franchaisee->delete();
        return $this->sendResponse($franchaisee, 'Franchaisee deleted successfully');
    }

    public function franchaiseeRequest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'phone_number' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'preferred_location' => 'required|string|max:255',
            'investment_amount' => 'required|numeric',
            'timeframe' => 'required|string|max:255',
            'message' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
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
        $franchaiseeRequest->save();
        return $this->sendResponse(['franchaiseeRequest' => $franchaiseeRequest, 'message' => 'Franchaisee request sent successfully']);
    }

    public function franchaiseeRequests(Request $request)
    {
        $franchaiseeRequests = FranchaiseeRequest::query()
            ->where('status', '!=', 'rejected')
            ->get();

        return $this->sendResponse($franchaiseeRequests);
    }

    public function franchaiseeRequestUpdate(Request $request, string $id)
    {
        $franchaiseeRequest = FranchaiseeRequest::find($id);
        $franchaiseeRequest->status = $request->status;
        $franchaiseeRequest->save();
        return $this->sendResponse(['franchaiseeRequest' => $franchaiseeRequest, 'message' => 'Franchaisee request updated successfully']);
    }
}
