<?php

namespace App\Http\Controllers\Payment;

use App\Actions\Checkout\HandleMidtransNotificationAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class MidtransCallbackController extends Controller
{
    public function __invoke(Request $request, HandleMidtransNotificationAction $handleMidtransNotificationAction): JsonResponse
    {
        try {
            $order = $handleMidtransNotificationAction->handle($request->all());

            return response()->json([
                'ok' => true,
                'order_number' => $order->order_number,
            ]);
        } catch (Throwable $throwable) {
            report($throwable);

            return response()->json([
                'ok' => false,
                'message' => $throwable->getMessage(),
            ], 400);
        }
    }
}
