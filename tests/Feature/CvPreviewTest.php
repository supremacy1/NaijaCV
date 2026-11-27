<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CvPreviewTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_users_can_preview_new_and_saved_cvs(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $this->actingAs($user);
        $this->get(route('cvs.create'))
            ->assertOk()
            ->assertSee('Preview My CV')
            ->assertSee('The preview updates as you move between templates.')
            ->assertSee('Next template');

        $response = $this->post(route('cvs.preview.create'), [
            'title' => 'My CV',
            'template' => 'simple',
            'full_name' => 'Jane Doe',
            'email' => $user->email,
            'phone' => '+2348000000000',
        ]);

        $draftId = basename(parse_url($response->headers->get('Location'), PHP_URL_PATH));
        $response->assertRedirect(route('cvs.preview', ['draft' => $draftId]));
        $this->assertSame(0, $user->cvs()->count());

        $this->get(route('cvs.preview', [
            'draft' => $draftId,
            'template' => 'corporate_blue',
        ]))
            ->assertOk()
            ->assertSee('corporate_blue', false)
            ->assertSee('your CV has not been saved')
            ->assertSee('data-cv-preview-container', false)
            ->assertSee('aria-label="Preview previous template"', false)
            ->assertSee('aria-label="Preview next template"', false)
            ->assertSee('data-preview-direction="next"', false)
            ->assertSee('cv-save-download-actions', false)
            ->assertSee('Save CV')
            ->assertSee(route('cvs.preview.pay', ['draft' => $draftId]), false)
            ->assertSee('Pay ₦3,000')
            ->assertDontSee('Premium PDF download requires payment')
            ->assertDontSee('Preview another template')
            ->assertDontSee('Choose a template to preview');

        $this->get(route('cvs.preview', ['draft' => $draftId, 'template' => 'simple']))
            ->assertOk()
            ->assertSee('cv-save-download-actions', false)
            ->assertSee('Save CV')
            ->assertSee('Download PDF');

        $response = $this->post(route('cvs.preview.save', ['draft' => $draftId]), [
            'template' => 'corporate_blue',
        ]);
        $cv = $user->cvs()->sole();

        $response->assertRedirect(route('cvs.show', $cv));
        $this->assertSame('corporate_blue', $cv->template);

        $this->get(route('cvs.show', $cv))
            ->assertOk()
            ->assertSee('CV Preview')
            ->assertSee('Jane Doe')
            ->assertSee(route('cvs.pay', $cv), false)
            ->assertSee('Pay ₦3,000');

        $this->get(route('cvs.index'))
            ->assertOk()
            ->assertSee(route('cvs.show', $cv), false)
            ->assertSee('Preview CV')
            ->assertSee('Delete this CV?')
            ->assertSee('confirm-cv-deletion-' . $cv->id, false);
    }

    public function test_free_draft_can_be_downloaded_without_saving_it(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);
        $this->actingAs($user);

        $response = $this->post(route('cvs.preview.create'), [
            'title' => 'My CV',
            'template' => 'simple',
            'full_name' => 'Jane Doe',
            'email' => $user->email,
            'phone' => '+2348000000000',
        ]);
        $draftId = basename(parse_url($response->headers->get('Location'), PHP_URL_PATH));

        $this->post(route('cvs.preview.download', ['draft' => $draftId]), [
            'template' => 'simple',
        ])->assertOk()->assertHeader('content-type', 'application/pdf');

        $this->assertSame(0, $user->cvs()->count());
    }
}
