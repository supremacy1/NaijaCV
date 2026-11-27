<?php

namespace Tests\Feature;

use App\Models\Cv;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class GuestCvTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cv_saves_the_selected_template(): void
    {
        $response = $this->post('/create-cv', [
            'title' => 'My CV',
            'template' => 'executive',
            'full_name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'phone' => '+2348000000000',
        ]);

        $cv = Cv::query()->sole();

        $response->assertRedirect(route('guest-cvs.preview', [
            'token' => $cv->guest_token,
        ]));
        $this->assertSame('executive', $cv->template);
        $this->assertSame('pending', $cv->payment_status);
        $this->assertFalse($cv->is_paid);
    }

    public function test_simple_guest_cv_is_free(): void
    {
        $this->post('/create-cv', [
            'title' => 'My CV',
            'template' => 'simple',
            'full_name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'phone' => '+2348000000000',
        ]);

        $cv = Cv::query()->sole();

        $this->assertSame('free', $cv->payment_status);
        $this->assertFalse($cv->is_paid);
    }

    public function test_guest_can_preview_a_different_premium_template(): void
    {
        $this->post('/create-cv', [
            'title' => 'My CV',
            'template' => 'simple',
            'full_name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'phone' => '+2348000000000',
        ]);

        $cv = Cv::query()->sole();

        $this->get(route('guest-cvs.preview', [
            'token' => $cv->guest_token,
            'template' => 'corporate_blue',
        ]))
            ->assertOk()
            ->assertSee('template-corporate_blue')
            ->assertSee('Corporate Blue')
            ->assertSee('name="template" value="corporate_blue"', false);
    }

    public function test_payment_uses_the_template_selected_in_preview(): void
    {
        Http::fake([
            'api.paystack.co/transaction/initialize' => Http::response([
                'status' => true,
                'data' => ['authorization_url' => 'https://checkout.paystack.com/test'],
            ]),
        ]);
        config(['services.paystack.secret_key' => 'test-secret']);

        $this->post('/create-cv', [
            'title' => 'My CV',
            'template' => 'simple',
            'full_name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'phone' => '+2348000000000',
        ]);

        $cv = Cv::query()->sole();

        $this->post(route('guest-cvs.pay', ['token' => $cv->guest_token]), [
            'template' => 'corporate_blue',
        ])->assertRedirect('https://checkout.paystack.com/test');

        $this->assertSame('corporate_blue', $cv->refresh()->template);
    }

    public function test_guest_cv_preview_renders_successfully(): void
    {
        $this->followingRedirects();

        $response = $this->post('/create-cv', [
            'title' => 'My CV',
            'template' => 'simple',
            'full_name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'phone' => '+2348000000000',
        ]);

        $response->assertOk()
            ->assertSee('Your CV has been created!');
    }

    public function test_guest_can_save_a_free_cv_to_their_account_after_signing_in(): void
    {
        $this->post('/create-cv', [
            'title' => 'My CV',
            'template' => 'simple',
            'full_name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'phone' => '+2348000000000',
        ]);
        $cv = Cv::query()->sole();
        $previewRoute = route('guest-cvs.preview', ['token' => $cv->guest_token]);

        $this->get($previewRoute)
            ->assertOk()
            ->assertSee('Download PDF')
            ->assertSee('Save to account')
            ->assertSee('data-cv-preview-container', false)
            ->assertSee('cv-save-download-actions', false)
            ->assertSee('aria-label="Preview previous template"', false)
            ->assertSee('aria-label="Preview next template"', false);

        $this->get(route('guest-cvs.save-start', ['token' => $cv->guest_token]))
            ->assertRedirect(route('login'))
            ->assertSessionHas('url.intended', $previewRoute);

        Notification::fake();
        $this->post(route('register'), [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'password' => 'password123!',
            'password_confirmation' => 'password123!',
        ])->assertRedirect($previewRoute);

        $user = User::where('email', 'jane@example.com')->sole();
        $user->forceFill(['email_verified_at' => now()])->save();
        $this->actingAs($user)
            ->post(route('guest-cvs.save', ['token' => $cv->guest_token]), [
                'template' => 'simple',
            ])
            ->assertRedirect(route('cvs.show', $cv));

        $this->assertDatabaseHas('cvs', [
            'id' => $cv->id,
            'user_id' => $user->id,
            'guest_token' => null,
            'expires_at' => null,
        ]);
        $this->assertDatabaseMissing('cvs', [
            'id' => $cv->id,
            'guest_token' => $cv->guest_token,
        ]);
    }

    public function test_unpaid_guest_premium_cv_cannot_be_saved_to_an_account(): void
    {
        $this->post('/create-cv', [
            'title' => 'My CV',
            'template' => 'corporate_blue',
            'full_name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'phone' => '+2348000000000',
        ]);
        $cv = Cv::query()->sole();
        $user = User::factory()->create(['email_verified_at' => now()]);

        $this->actingAs($user)
            ->post(route('guest-cvs.save', ['token' => $cv->guest_token]), [
                'template' => 'corporate_blue',
            ])
            ->assertForbidden();

        $this->assertNull($cv->refresh()->user_id);
    }
}
