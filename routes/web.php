<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\Admin\OrdersController;
use App\Http\Controllers\Admin\MembershipController;
use App\Http\Controllers\Admin\EmployeeStatController;
use App\Http\Controllers\Admin\UpdateDatabaseController;
use App\Http\Controllers\Admin\PayrollController;
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
    Route::get('/admin-login',[AuthController::class,'login']);
    Route::post('/loginProcess',[AuthController::class,'adminLogin']);  
});

Route::get('/webhooks',[WebhookController::class,'handle']);



Route ::group(['middleware' =>['admin']],function(){
    Route::get('/admin-dashboard',[OrdersController::class,'Dashboard']);

    Route::get('/sales-data', [OrdersController::class, 'salesData']);

    Route::get('/admin-dashboard/locations',[AdminDashboardController::class,'locations']);
    Route::get('get/locations',[AdminDashboardController::class,'getLocation']);
    Route::get('/admin-dashboard/memberships',[AdminDashboardController::class,'memberships']);
    Route::post('/admin-dashboard/memberships/locations',[AdminDashboardController::class,'getMembershipByLocation']);
    Route::get('/admin-dashboard/memberships/status',[AdminDashboardController::class,'getUserByMemberships']);
    Route::post('/admin-dashboard/memberships/date',[AdminDashboardController::class,'getMembershipByDate']);
    Route::post('/admin-dashboard/memberships/users',[AdminDashboardController::class,'userFilter']);

    Route::get('get/memberships',[AdminDashboardController::class,'getMemberships']);
    Route::get('dump/memberships',[MembershipController::class,'dumpToDatabase']);

    Route::get('/admin-dashboard/memberships-transactions',[MembershipController::class,'MembershipsTransaction']);
    Route::get('/admin-dashboard/get/memberships-transactions',[MembershipController::class,'getMembershipsTransaction']);
    
    Route::get('/admin-dashboard/billing-stats',[MembershipController::class,'BillingStats']);
    Route::get('/admin-dashboard/get/billing-stats',[MembershipController::class,'getBillingStats']);

    Route::get('/csvData',[AdminDashboardController::class,'csvData']);

    Route::get('/admin-dashboard/orders',[OrdersController::class,'Orders'])->name('admin.dashboard.orders');
    Route::get('/admin-dashboard/get-orders',[OrdersController::class,'GetOrders']);
    Route::get('/admin-dashboard/get-sales',[OrdersController::class,'TotalSales']);
    Route::get('/admin-dashboard/total-sales',[OrdersController::class,'SalesStats'])->name('admin.dashboard.sales');

    Route::get('/admin-dashboard/users',[OrdersController::class,'Users'])->name('admin.dashboard.users');
    Route::get('/admin-dashboard/get-users',[OrdersController::class,'GetUsers']);

    Route::get('/admin-dashboard/employees',[EmployeeStatController::class,'Employees'])->name('admin.dashboard.employees');
    Route::get('/admin-dashboard/get-employees',[EmployeeStatController::class,'GetEmployees']);

    // Route::get('/admin-dashboard/memberships-instances',[OrdersController::class,'Instances'])->name('admin.dashboard.Instances');
    // Route::get('/admin-dashboard/get-instances',[OrdersController::class,'GetInstances']);

    Route::get('/admin-dashboard/memberships-instances',[MembershipController::class,'Instances'])->name('admin.dashboard.Instances');
    Route::get('/admin-dashboard/get-instances',[MembershipController::class,'GetInstances']);

    Route::get('/admin-dashboard/update-records-automatically',[UpdateDatabaseController::class,'saveUsersdata']);

    Route::get('admin-dashboard/payroll',[PayrollController::class,'Payroll']);
    Route::get('admin-dashboard/payroll-stats',[PayrollController::class,'PayrollStates']);
    Route::get('admin-dashboard/get-payroll',[PayrollController::class,'GetPayroll']);


});

Route::get('/test-api/{api}',[TestController::class,'testapi'])->name('test.api');
Route::get('/test-api/{api}/{id}',[TestController::class,'testapibyid']);



// Routes for the wordpress 
// Route::get('wordpress/issue',[MarianaController::class,'checkPaymentProcess']);
// Route::get('wordpress/setintent',[MarianaController::class,'createStripeSetupIntent']);
// Route::get('wordpress/storepayment',[MarianaController::class,'storePaymentMethod']);
// Route::get('wordpress/paymentintent',[MarianaController::class,'createPaymentIntent']);
// Route::get('wordpress/confirmpayment',[MarianaController::class,'confirmPayment']);




