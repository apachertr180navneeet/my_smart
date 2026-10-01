<?php

namespace App\Services;

use App\Models\PaymentGateway;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Stripe\Customer;
use Stripe\EphemeralKey;
use Stripe\PaymentIntent;
use Stripe\SetupIntent;
use Stripe\Stripe;
use Exception;

class StripeService
{
    protected ?string $secretKey;
    protected ?string $publicKey;

    public function __construct()
    {
        $this->secretKey = config('services.stripe.secret') ?: env('STRIPE_SECRET_KEY');
        $this->publicKey = config('services.stripe.key') ?: env('STRIPE_PUBLIC_KEY');

        // Fallback to PaymentGateway database entry if .env is not yet populated
        if (empty($this->secretKey)) {
            $gateway = PaymentGateway::where('type', 'stripe')->first();
            if ($gateway) {
                $values = $gateway->is_test ? json_decode($gateway->value, true) : json_decode($gateway->live_value, true);
                $this->secretKey = $values['stripe_key'] ?? null;
                if (empty($this->publicKey)) {
                    $this->publicKey = $values['stripe_publickey'] ?? null;
                }
            }
        }

        if (!empty($this->secretKey)) {
            Stripe::setApiKey($this->secretKey);
        }
    }

    /**
     * Get or create Stripe Customer for the given user.
     * Prevents duplicate Stripe customers.
     */
    public function getOrCreateCustomer(User $user): string
    {
        if (empty($this->secretKey)) {
            throw new Exception('Stripe is not configured. Secret key is missing.');
        }

        if (!empty($user->stripe_customer_id)) {
            try {
                $customer = Customer::retrieve($user->stripe_customer_id);
                if ($customer && !($customer->deleted ?? false)) {
                    return $customer->id;
                }
            } catch (Exception $e) {
                Log::warning('Existing Stripe customer retrieve failed: ' . $e->getMessage() . '. Creating new one.');
            }
        }

        $fullName = trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? ''));
        if (empty($fullName)) {
            $fullName = $user->username ?? ('User #' . $user->id);
        }

        $customer = Customer::create([
            'email' => $user->email,
            'name' => $fullName,
            'metadata' => [
                'user_id' => $user->id,
            ],
        ]);

        $user->stripe_customer_id = $customer->id;
        $user->save();

        return $customer->id;
    }

    /**
     * Create PaymentIntent and EphemeralKey.
     *
     * @param User $user
     * @param int|float $amount Smallest currency unit (e.g. cents)
     * @param string $currency
     * @return array
     */
    public function createPaymentIntent(User $user, $amount, string $currency = 'usd'): array
    {
        if (empty($this->secretKey)) {
            throw new Exception('Stripe is not configured. Secret key is missing.');
        }

        $customerId = $this->getOrCreateCustomer($user);

        $paymentIntent = PaymentIntent::create([
            'amount' => (int) $amount,
            'currency' => strtolower($currency),
            'customer' => $customerId,
            'setup_future_usage' => 'off_session',
            'automatic_payment_methods' => [
                'enabled' => true,
            ],
            'metadata' => [
                'user_id' => $user->id,
            ],
        ]);

        $ephemeralKey = EphemeralKey::create(
            [
                'customer' => $customerId,
            ],
            [
                'stripe_version' => '2024-06-20',
            ]
        );

        return [
            'id' => $paymentIntent->id,
            'client_secret' => $paymentIntent->client_secret,
            'customer' => $customerId,
            'ephemeral_key' => $ephemeralKey->secret,
        ];
    }

    /**
     * Create SetupIntent and EphemeralKey for saved cards management.
     */
    public function createCustomerSession(User $user): array
    {
        if (empty($this->secretKey)) {
            throw new Exception('Stripe is not configured. Secret key is missing.');
        }

        $customerId = $this->getOrCreateCustomer($user);

        $setupIntent = SetupIntent::create([
            'customer' => $customerId,
            'payment_method_types' => ['card'],
        ]);

        $ephemeralKey = EphemeralKey::create(
            [
                'customer' => $customerId,
            ],
            [
                'stripe_version' => '2024-06-20',
            ]
        );

        return [
            'customer' => $customerId,
            'ephemeral_key' => $ephemeralKey->secret,
            'setup_intent_client_secret' => $setupIntent->client_secret,
        ];
    }

    /**
     * Server-side Stripe verification of a PaymentIntent.
     *
     * @param string $txnId PaymentIntent ID
     * @param float|int|null $expectedAmount Expected amount in standard currency (or cents)
     * @param User|null $user
     * @param string|null $expectedCurrency
     * @return array
     */
    public function verifyPaymentIntent(string $txnId, $expectedAmount = null, ?User $user = null, ?string $expectedCurrency = null): array
    {
        if (empty($this->secretKey)) {
            return [
                'success' => false,
                'message' => 'Stripe secret key is not configured on the server.',
            ];
        }

        try {
            $paymentIntent = PaymentIntent::retrieve($txnId);

            if ($paymentIntent->status !== 'succeeded') {
                return [
                    'success' => false,
                    'message' => 'Payment has not succeeded. Current status: ' . $paymentIntent->status,
                ];
            }

            // Verify user association if provided
            if ($user) {
                if (!empty($paymentIntent->customer) && !empty($user->stripe_customer_id) && $paymentIntent->customer !== $user->stripe_customer_id) {
                    return [
                        'success' => false,
                        'message' => 'Payment transaction does not belong to the authenticated user.',
                    ];
                }

                if (isset($paymentIntent->metadata['user_id']) && (int) $paymentIntent->metadata['user_id'] !== (int) $user->id) {
                    return [
                        'success' => false,
                        'message' => 'Payment user metadata mismatch.',
                    ];
                }
            }

            // Verify amount if provided
            if ($expectedAmount !== null && $expectedAmount > 0) {
                $expectedCents = (int) round($expectedAmount * 100);
                if ($paymentIntent->amount !== $expectedCents && $paymentIntent->amount !== (int) $expectedAmount) {
                    return [
                        'success' => false,
                        'message' => 'Payment amount mismatch. Expected: ' . $expectedAmount . ', Received: ' . ($paymentIntent->amount / 100),
                    ];
                }
            }

            // Verify currency if provided
            if ($expectedCurrency !== null && !empty($expectedCurrency)) {
                if (strtolower($paymentIntent->currency) !== strtolower($expectedCurrency)) {
                    return [
                        'success' => false,
                        'message' => 'Payment currency mismatch.',
                    ];
                }
            }

            return [
                'success' => true,
                'message' => 'Payment verified successfully.',
                'amount' => $paymentIntent->amount / 100,
                'currency' => $paymentIntent->currency,
                'payment_intent' => $paymentIntent,
            ];
        } catch (Exception $e) {
            Log::error('Stripe verification failed for txn_id ' . $txnId . ': ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Payment verification failed with Stripe.',
            ];
        }
    }
}
