<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Illuminate\Support\Facades\DB;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;

class PaymentController extends Controller
{
    public function createIntent(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'order_id' => 'required|integer|exists:orders,id',
        ]);

        $order = $request->user()
            ->orders()
            ->findOrFail($validated['order_id']);

        if ($order->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Esta orden no está pendiente de pago.',
            ], 422);
        }

        Stripe::setApiKey(config('services.stripe.secret'));

        $intent = PaymentIntent::create([
        'amount' => (int) round($order->total * 100),
        'currency' => 'usd',
        'automatic_payment_methods' => [
            'enabled' => true,
            'allow_redirects' => 'never',
        ],
        'metadata' => [
            'order_id' => $order->id,
            'user_id' => $request->user()->id,
        ],
    ]);

        Payment::create([
            'order_id' => $order->id,
            'payment_method' => 'stripe',
            'transaction_id' => $intent->id,
            'amount' => $order->total,
            'status' => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Intento de pago creado correctamente.',
            'data' => [
                'payment_intent_id' => $intent->id,
                'client_secret' => $intent->client_secret,
                'amount' => $order->total,
                'currency' => 'usd',
            ],
        ], 201);
    }

    public function handleWebhook(Request $request): JsonResponse
    {
        try {
            $event = Webhook::constructEvent(
                $request->getContent(),
                $request->header('Stripe-Signature'),
                config('services.stripe.webhook_secret')
            );
        } catch (\UnexpectedValueException | SignatureVerificationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Firma del webhook inválida.',
            ], 400);
        }

        if (
            $event->type === 'payment_intent.succeeded' ||
            $event->type === 'payment_intent.payment_failed'
        ) {
            $intent = $event->data->object;

            DB::transaction(function () use ($event, $intent) {
                $payment = Payment::where(
                    'transaction_id',
                    $intent->id
                )->lockForUpdate()->first();

                if (!$payment) {
                    return;
                }

                if ($event->type === 'payment_intent.succeeded') {
                    $payment->update(['status' => 'paid']);

                    $payment->order()->update(['status' => 'paid']);
                } else {
                    $payment->update(['status' => 'failed']);
                }
            });
        }

        return response()->json([
            'success' => true,
            'message' => 'Webhook procesado correctamente.',
        ]);
    }
}