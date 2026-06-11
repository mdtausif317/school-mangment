<?php

namespace App\Services;

use App\Models\SubscriptionPayment;
use Razorpay\Api\Api;

class RazorpayService
{
    protected Api $api;

    public function __construct()
    {
        $this->api = new Api(
            config('services.razorpay.key'),
            config('services.razorpay.secret')
        );
    }

    public function isConfigured(): bool
    {
        return filled(config('services.razorpay.key'))
            && filled(config('services.razorpay.secret'));
    }

    public function createOrder(SubscriptionPayment $payment): array
    {
        $order = $this->api->order->create([
            'receipt' => 'sub_'.$payment->id,
            'amount' => (int) round($payment->amount * 100),
            'currency' => 'INR',
            'notes' => [
                'school_id' => (string) $payment->school_id,
                'payment_id' => (string) $payment->id,
                'plan_id' => (string) $payment->subscription_plan_id,
            ],
        ]);

        return $order->toArray();
    }

    public function verifyPaymentSignature(string $orderId, string $paymentId, string $signature): void
    {
        $this->api->utility->verifyPaymentSignature([
            'razorpay_order_id' => $orderId,
            'razorpay_payment_id' => $paymentId,
            'razorpay_signature' => $signature,
        ]);
    }
}
