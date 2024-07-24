<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


use Moyasar\Moyasar;
use Moyasar\Payment;

class AdminAuthController extends Controller
{
    public function testMoyasar()
    {
        Moyasar::setApiKey(env('MOYASAR_SECRET_KEY'));

        try {
            $payments = Payment::fetch(); // جلب الدفعات للتاكد من ان الاتصال بال API يعمل
            return response()->json($payments);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }
}
