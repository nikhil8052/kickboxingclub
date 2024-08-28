<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\Admin\OrdersController;
use App\Http\Controllers\Admin\MembershipController;
use App\Http\Controllers\Admin\EmployeeStatController;
use App\Http\Controllers\Admin\UpdateDatabaseController;
use App\Http\Controllers\Admin\PayrollController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Webhooks\WebhookController;
use App\Http\Controllers\MarianaController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\TestController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect('/admin-login');
});

Route::get('/logout',[AuthController::class,'adminLogout']);

Route::middleware('admin.redirect')->group(function () {
    Route::get('/admin-login',[AuthController::class,'login'])->name('login');
    Route::post('/loginProcess',[AuthController::class,'adminLogin']);  
});

Route::get('/webhooks',[WebhookController::class,'handle']);


Route::group(['middleware' =>['auth']],function(){

    Route::get('/admin-dashboard',[DashboardController::class,'Dashboard']);
    Route::get('/sales-data', [DashboardController::class, 'salesData']);

    Route::get('/admin-dashboard/users',[DashboardController::class,'Users'])->name('admin.dashboard.users')->middleware('permission:4');
    Route::get('/admin-dashboard/get-users',[DashboardController::class,'GetUsers'])->middleware('permission:4');

    Route::get('/admin-dashboard/employees',[EmployeeStatController::class,'Employees'])->name('admin.dashboard.employees')->middleware('permission:6');
    Route::get('/admin-dashboard/get-employees',[EmployeeStatController::class,'GetEmployees'])->middleware('permission:6');

    Route::get('/admin-dashboard/locations',[AdminDashboardController::class,'locations']);
    Route::get('/admin-dashboard/get/locations',[AdminDashboardController::class,'getLocation']);


    Route::get('/admin-dashboard/memberships',[AdminDashboardController::class,'memberships'])->middleware('permission:1');
    Route::post('/admin-dashboard/memberships/locations',[AdminDashboardController::class,'getMembershipByLocation'])->middleware('permission:1');
    Route::get('/admin-dashboard/memberships/status',[AdminDashboardController::class,'getUserByMemberships'])->middleware('permission:1');
    Route::post('/admin-dashboard/memberships/date',[AdminDashboardController::class,'getMembershipByDate'])->middleware('permission:1');
    Route::post('/admin-dashboard/memberships/users',[AdminDashboardController::class,'userFilter'])->middleware('permission:1');

    Route::get('get/memberships',[AdminDashboardController::class,'getMemberships'])->middleware('permission:1');
    Route::get('dump/memberships',[MembershipController::class,'dumpToDatabase'])->middleware('permission:1');

    Route::get('/admin-dashboard/memberships-transactions',[MembershipController::class,'MembershipsTransaction'])->middleware('permission:1');
    Route::get('/admin-dashboard/get/memberships-transactions',[MembershipController::class,'getMembershipsTransaction'])->middleware('permission:1');
    
    Route::get('/admin-dashboard/billing-stats',[MembershipController::class,'BillingStats'])->middleware('permission:5');
    Route::get('/admin-dashboard/get/billing-stats',[MembershipController::class,'getBillingStats'])->middleware('permission:5');

    Route::get('/csvData',[AdminDashboardController::class,'csvData']);

    Route::get('/admin-dashboard/orders',[OrdersController::class,'Orders'])->name('admin.dashboard.orders')->middleware('permission:3');
    Route::get('/admin-dashboard/get-orders',[OrdersController::class,'GetOrders'])->middleware('permission:3');
    Route::get('/admin-dashboard/get-sales',[OrdersController::class,'TotalSales'])->middleware('permission:3');
    Route::get('/admin-dashboard/total-sales',[OrdersController::class,'SalesStats'])->name('admin.dashboard.sales')->middleware('permission:3');



    Route::get('/admin-dashboard/add-pay-rates',[EmployeeStatController::class,'addPayRates']);
    Route::post('/admin-dashboard/pay-rates/procc',[EmployeeStatController::class,'payRateProcc']);

    // Route::get('/admin-dashboard/memberships-instances',[OrdersController::class,'Instances'])->name('admin.dashboard.Instances');
    // Route::get('/admin-dashboard/get-instances',[OrdersController::class,'GetInstances']);

    Route::get('/admin-dashboard/memberships-instances',[MembershipController::class,'Instances'])->name('admin.dashboard.Instances')->middleware('permission:1');
    Route::get('/admin-dashboard/get-instances',[MembershipController::class,'GetInstances'])->middleware('permission:1');
    Route::get('/admin-dashboard/export-instances',[MembershipController::class,'exportInstances'])->middleware('permission:1');

    Route::get('/admin-dashboard/update-records-automatically',[UpdateDatabaseController::class,'saveUsersdata']);

    Route::get('admin-dashboard/payroll',[PayrollController::class,'Payroll'])->middleware('permission:2');
    Route::get('admin-dashboard/payroll-stats',[PayrollController::class,'PayrollStates'])->middleware('permission:2');
    Route::get('admin-dashboard/get-payroll',[PayrollController::class,'GetPayroll'])->middleware('permission:2');

    Route::get('admin-dashboard/add-user',[UserController::class,'AddUser'])->name('add.user')->middleware('admin');
    Route::post('admin-dashboard/user-addProcc',[UserController::class,'UserAddProcc'])->name('user.addProcc')->middleware('admin');
    Route::get('admin-dashboard/user-remove/{id}',[UserController::class,'UserRemove'])->name('user.remove')->middleware('admin');
});

Route::get('/test-api/{api}',[TestController::class,'testapi'])->name('test.api');
Route::get('/test-api/{api}/{id}',[TestController::class,'testapibyid']);



// Routes for the wordpress 
// Route::get('wordpress/issue',[MarianaController::class,'checkPaymentProcess']);
// Route::get('wordpress/setintent',[MarianaController::class,'createStripeSetupIntent']);
// Route::get('wordpress/storepayment',[MarianaController::class,'storePaymentMethod']);
// Route::get('wordpress/paymentintent',[MarianaController::class,'createPaymentIntent']);
// Route::get('wordpress/confirmpayment',[MarianaController::class,'confirmPayment']);


