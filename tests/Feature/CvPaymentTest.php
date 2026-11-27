<?php

namespace Tests\Feature;

use App\Models\Cv;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class CvPaymentTest extends TestCase
{
    use RefreshDatabase;

    public function test_premium_draft_checkout_saves_the_cv_and_starts_paystack_payment(): void
    {
        Http::fake([
            'api.paystack.co/transaction/initialize' => Http::response([
                'status' => true,
                'data' => ['authorization_url' => 'https://checkout.paystack.com/test'],
            ]),
        ]);
        config(['services.paystack.secret_key' => 'test-secret']);

        $user = User::factory()->create(['email_verified_at' => now()]);
        $this->actingAs($user);

        $response = $this->post(route('cvs.preview.create'), [
            'title' => 'My CV',
            'template' => 'simple',
            'full_name' => 'Jane Doe',
            'email' => $user->email,
            'phone' => '+2348000000000',
        ]);
        $draftId = basename(parse_url($response->headers->get('Location'), PHP_URL_PATH));

        $this->post(route('cvs.preview.pay', ['draft' => $draftId]), [
            'template' => 'corporate_blue',
        ])->assertRedirect('https://checkout.paystack.com/test');

        $cv = $user->cvs()->sole();
        $this->assertSame('corporate_blue', $cv->template);
        $this->assertSame('unpaid', $cv->payment_status);
        $this->assertFalse($cv->is_paid);
        $this->assertStringStartsWith('NAIJACV', $cv->payment_reference);

        Http::assertSent(fn ($request) =>
            $request->url() === 'https://api.paystack.co/transaction/initialize'
            && $request['amount'] === 300000
            && $request['currency'] === 'NGN'
            && $request['reference'] === $cv->payment_reference
        );
    }

    public function test_payment_callback_unlocks_cv_only_after_paystack_verification(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $reference = 'NAIJACVTESTREFERENCE';
        $cv = $user->cvs()->create([
            'title' => 'My CV',
            'full_name' => 'Jane Doe',
            'email' => $user->email,
            'phone' => '+2348000000000',
            'template' => 'corporate_blue',
            'is_paid' => false,
            'payment_status' => 'unpaid',
            'payment_reference' => $reference,
        ]);
        config(['services.paystack.secret_key' => 'test-secret']);
        Http::fake([
            'api.paystack.co/transaction/verify/' . $reference => Http::response([
                'status' => true,
                'data' => [
                    'status' => 'success',
                    'amount' => 300000,
                    'currency' => 'NGN',
                    'reference' => $reference,
                ],
            ]),
        ]);

        $this->actingAs($user)
            ->get(route('cvs.payment.callback', [
                'cv' => $cv,
                'reference' => $reference,
            ]))
            ->assertRedirect(route('cvs.download', $cv));

        $this->assertDatabaseHas('cvs', [
            'id' => $cv->id,
            'is_paid' => true,
            'payment_status' => 'paid',
            'payment_reference' => $reference,
        ]);
        $this->assertNotNull($cv->fresh()->payment_verified_at);
    }

    public function test_payment_callback_does_not_unlock_cv_for_an_incorrect_amount(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $reference = 'NAIJACVWRONGAMOUNT';
        $cv = $user->cvs()->create([
            'title' => 'My CV',
            'full_name' => 'Jane Doe',
            'email' => $user->email,
            'phone' => '+2348000000000',
            'template' => 'corporate_blue',
            'is_paid' => false,
            'payment_status' => 'unpaid',
            'payment_reference' => $reference,
        ]);
        config(['services.paystack.secret_key' => 'test-secret']);
        Http::fake([
            'api.paystack.co/transaction/verify/' . $reference => Http::response([
                'status' => true,
                'data' => [
                    'status' => 'success',
                    'amount' => 1000,
                    'currency' => 'NGN',
                    'reference' => $reference,
                ],
            ]),
        ]);

        $this->actingAs($user)
            ->get(route('cvs.payment.callback', [
                'cv' => $cv,
                'reference' => $reference,
            ]))
            ->assertRedirect(route('cvs.show', $cv))
            ->assertSessionHas('error');

        $this->assertFalse($cv->fresh()->is_paid);
        $this->assertSame('unpaid', $cv->fresh()->payment_status);
    }

    public function test_paystack_webhook_unlocks_an_authenticated_premium_cv(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $reference = 'NAIJACVWEBHOOKREFERENCE';
        $cv = $user->cvs()->create([
            'title' => 'My CV',
            'full_name' => 'Jane Doe',
            'email' => $user->email,
            'phone' => '+2348000000000',
            'template' => 'corporate_blue',
            'is_paid' => false,
            'payment_status' => 'unpaid',
            'payment_reference' => $reference,
        ]);
        $secretKey = 'test-secret';
        config(['services.paystack.secret_key' => $secretKey]);
        $body = json_encode([
            'event' => 'charge.success',
            'data' => [
                'reference' => $reference,
                'status' => 'success',
                'amount' => 300000,
                'currency' => 'NGN',
            ],
        ], JSON_UNESCAPED_SLASHES);
        $signature = hash_hmac('sha512', $body, $secretKey);

        $this->call(
            'POST',
            route('paystack.webhook'),
            [],
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_X_PAYSTACK_SIGNATURE' => $signature,
            ],
            $body
        )->assertOk();

        $this->assertTrue($cv->fresh()->is_paid);
        $this->assertSame('paid', $cv->fresh()->payment_status);
    }
}
