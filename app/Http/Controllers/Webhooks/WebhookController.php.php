<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WebhookController extends Controller
{
    public function handle(Request $request ){

        savelog("Marientek webhook","WebhookController.php", $request);

        return true ;
    }
}
