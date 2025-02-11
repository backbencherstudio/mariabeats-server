<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Franchaisor\Franchaisor;
use App\Models\User;
use App\Traits\CommonTrait;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    use CommonTrait;

    public function index()
    {
        $franchaisors = Franchaisor::all();
        $franchaisees = User::where('role', 'user')->where('approved_at', '!=', null)->get();
        return $this->sendResponse([
            'franchaisors' => $franchaisors,
            'franchaisees' => $franchaisees
        ]);
    }
}
