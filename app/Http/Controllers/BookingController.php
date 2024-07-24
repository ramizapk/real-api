<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Services\PaymentService;
use Illuminate\Http\Request;

use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BookingController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }


    public function book(Request $request)
    {

        $validatedData = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'user_id' => 'required|exists:users,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'amount' => 'required|numeric',
            'currency' => 'required|string',
            'source' => 'required|array',
            'metadata' => 'required|array',
        ]);

        DB::beginTransaction();
        try {
            $property = Property::find($validatedData['property_id']);
            if (!$property) {
                throw new \Exception('Property not found');
            }

            $startDate = new \DateTime($validatedData['start_date']);
            $endDate = new \DateTime($validatedData['end_date']);
            $interval = $startDate->diff($endDate);
            $nights = $interval->days;

            $pricePerNight = $property->price;

            $propertyName = $property->property_name;

            $activeOffer = $property->offers->where('end_date', '>=', now())->first();
            $discount = $activeOffer ? $activeOffer->discount : 0;
            $discountedPricePerNight = $pricePerNight - ($pricePerNight * ($discount / 100));

            $totalAmount = $discountedPricePerNight * $nights;

            if ($validatedData['amount'] != $totalAmount) {
                throw new \Exception('The paid amount does not match the calculated amount' . $nights);
            }

            $booking = Booking::create([
                'property_id' => $validatedData['property_id'],
                'user_id' => $validatedData['user_id'],
                'start_date' => $validatedData['start_date'],
                'end_date' => $validatedData['end_date'],
                'amount' => $validatedData['amount'],
                'currency' => $validatedData['currency'],
                'status' => 'pending'
            ]);

            $paymentResult = $this->paymentService->createPayment(
                $validatedData['amount'],
                $validatedData['currency'],
                'Rental property: ' . $propertyName . ' for ' . $nights . ' days',
                $validatedData['source'],
                "http://localhost:8000/api/v1/payment/callback",
                $validatedData['metadata']
            );

            if (in_array($paymentResult['status'], ['paid', 'initiated', 'pending'])) {
                $booking->status = $paymentResult['status'] === 'paid' ? 'confirmed' : 'pending';
                $booking->save();

                Payment::create([
                    'booking_id' => $booking->id,
                    'amount' => $validatedData['amount'],
                    'currency' => $validatedData['currency'],
                    'status' => $paymentResult['status'],
                    'payment_id' => $paymentResult['payment']['id'],
                ]);

                DB::commit();
                return response()->json([
                    'message' => 'Booking and payment successful',
                    'booking' => $booking,
                    'transaction_url' => $paymentResult["payment"]["source"]["transaction_url"],
                ], 201);
            } else {
                $errorMessage = isset($paymentResult['error']['message']) ? $paymentResult['error']['message'] : 'Payment processing failed.';
                $errorDetails = isset($paymentResult['error']['details']) ? $paymentResult['error']['details'] : 'No additional details available.';
                throw new \Exception($errorMessage);
            }
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'message' => 'Booking and payment failed',
                'error' => $e->getMessage(),
                'details' => $paymentResult['error']['details'] ?? 'No additional details available.',
            ], 400);
        }
    }


    public function handleWebhook(Request $request)
    {
        // تحقق من أن الطلب يأتي من Moyasar باستخدام الرمز السري
        $secretToken = env('MOYASAR_SECRET_KEY');
        if ($request->header('Authorization') !== 'Bearer ' . $secretToken) {

            Log::warning('Unauthorized webhook request');
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // تحقق من البيانات الواردة
        $data = $request->all();
        $paymentId = $data['id'] ?? null;
        $status = $data['status'] ?? null;

        if (!$paymentId || !$status) {
            Log::warning('Missing required parameters in webhook');
            return response()->json(['message' => 'Bad Request'], 400);
        }

        // تحديث حالة الدفع والحجز بناءً على الحالة
        $payment = Payment::where('payment_id', $paymentId)->first();

        if ($payment) {
            $payment->status = $status;
            $payment->save();

            $booking = Booking::find($payment->booking_id);
            if ($booking) {
                switch ($status) {
                    case 'paid':
                        $booking->status = 'confirmed';
                        break;
                    case 'failed':
                        $booking->status = 'failed';
                        break;
                    case 'refunded':
                        $booking->status = 'refunded';
                        break;
                    case 'canceled':
                        $booking->status = 'canceled';
                        break;
                    case 'pending':
                        $booking->status = 'pending';
                        break;
                }
                $booking->save();
            }
            return response()->json(['message' => 'Webhook processed successfully'], 200);
        } else {
            Log::warning('Payment record not found');
            return response()->json(['message' => 'Payment record not found'], 404);
        }
    }
    public function callback(Request $request)
    {
        // عملية الكول باك العادية
        $message = 'Thank you! Your request has been received.';
        return view('payment_status', compact('message'));
    }
}