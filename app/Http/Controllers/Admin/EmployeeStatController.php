<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\Orders;
use App\Models\Locations;
use App\Models\MembershipInstances;
use Carbon\Carbon;
use App\Models\AllUsers;
use App\Models\Employees;

use Illuminate\Support\Facades\DB;

class EmployeeStatController extends Controller
{

    public function Employees()
    {
        return view('admin_dashboard.employees.index');
    }

    public function getEmployees(Request $request){
        $allemployees = Employees::with('user.location')->get();
        return response()->json($allemployees);
    }


}



