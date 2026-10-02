<?php

namespace Tests\Feature;

use App\Mail\WebsiteFormMail;
use App\Models\Page;
use App\Models\User;
use App\Support\ContentSchema;
use Database\Seeders\SiteContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class SiteContentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(SiteContentSeeder::class);
    }

    public function test_public_pages_render_built_in_content(): void
    {
        $pages = [
            '/' => 'Every child deserves to stay in school.',
            '/about-us' => 'Every children and youth enjoy full development potential',
            '/history' => 'Localization and Formal Launch',
            '/programs' => 'Skills For Professional Program (S4P)',
            '/page/jobs-announcement' => 'There are no open positions at the moment.',
            '/page/annual-report' => 'Annual Report 2025',
            '/page/strategic-plan' => 'Strategic Plan 2025 – 2029',
            '/page/partner' => 'Plan International',
            '/page/volunteer' => 'Apply to volunteer',
            '/gallery' => 'Our Image in Bandos Komar Organization',
            '/page/video' => 'youtube.com/embed/UizLZDLvr9c',
            '/contact' => 'Bandos%20Komar%20Phnom%20Penh',
            '/donate' => 'Contribute Today',
        ];

        foreach ($pages as $url => $text) {
            $this->get($url)->assertOk()->assertSee($text, false);
        }

        $this->get('/page/' . ContentSchema::SITE)->assertNotFound();
    }

    public function test_admin_edits_and_translations_show_on_the_website(): void
    {
        $page = Page::where('slug', 'home')->firstOrFail();

        $this->actingAs($this->admin())
            ->put(route('system-management.pages.update', $page), $this->baseFields($page) + [
                'blocks' => [
                    'en' => ['hero_title' => 'New hero title', 'stats' => [['number' => 7, 'suffix' => '+', 'plain' => '0', 'label' => 'Schools']]],
                    'km' => ['hero_title' => 'ចំណងជើងថ្មី', 'stats' => [['label' => 'សាលារៀន']]],
                ],
                'block_files' => ['en' => ['problem_image' => UploadedFile::fake()->image('photo.jpg')]],
            ])
            ->assertRedirect(route('system-management.pages.index'));

        $saved = $page->fresh()->page_content;
        $imagePath = $saved['en']['problem_image'];
        $this->assertStringStartsWith('images/content/home/', $imagePath);
        $this->assertFileExists(public_path($imagePath));
        @unlink(public_path($imagePath));

        $this->get('/')->assertSee('New hero title')->assertSee('Schools')->assertSee($imagePath, false);
        $this->withSession(['locale' => 'km'])->get('/')->assertSee('ចំណងជើងថ្មី')->assertSee('សាលារៀន')
            ->assertSee('ទំព័រដើម'); // Khmer menu title from the seeder
    }

    public function test_admin_uploads_annual_report_pdf(): void
    {
        $page = Page::where('slug', 'annual-report')->firstOrFail();

        $this->get('/page/annual-report')->assertSee('Coming soon')->assertDontSee('Download PDF');

        $this->actingAs($this->admin())
            ->put(route('system-management.pages.update', $page), $this->baseFields($page) + [
                'blocks' => ['en' => [
                    'intro_title' => 'Our yearly results',
                    'cover_label' => 'Annual Report',
                    'download_label' => 'Download PDF',
                    'soon_label' => 'Coming soon',
                    'reports' => [['year' => '2025', 'title' => 'Annual Report 2025', 'text' => 'Results for 2025.', 'pdf' => '']],
                ]],
                'block_files' => ['en' => ['reports' => [['pdf' => UploadedFile::fake()->create('report 2025.pdf', 50, 'application/pdf')]]]],
            ])
            ->assertRedirect(route('system-management.pages.index'));

        $pdfPath = $page->fresh()->page_content['en']['reports'][0]['pdf'];
        $this->assertStringStartsWith('images/content/annual-report/report-2025-', $pdfPath);
        $this->assertFileExists(public_path($pdfPath));
        @unlink(public_path($pdfPath));

        $this->get('/page/annual-report')->assertSee('Download PDF')->assertSee($pdfPath, false)->assertDontSee('Coming soon');
    }

    public function test_admin_rejects_unsafe_uploads(): void
    {
        $page = Page::where('slug', 'home')->firstOrFail();

        $this->actingAs($this->admin())
            ->put(route('system-management.pages.update', $page), $this->baseFields($page) + [
                'blocks' => ['en' => ['hero_title' => 'Should not save']],
                'block_files' => ['en' => ['problem_image' => UploadedFile::fake()->create('shell.php', 1, 'image/png')]],
            ])
            ->assertSessionHasErrors('blocks');

        $this->assertNotSame('Should not save', $page->fresh()->page_content['en']['hero_title'] ?? null);
    }

    public function test_website_forms_are_emailed(): void
    {
        Mail::fake();

        $this->postJson('/contact', ['name' => 'Dara', 'email' => 'dara@example.com', 'message' => 'Hello'])
            ->assertOk()->assertJson(['ok' => true]);

        $this->postJson('/forms/donate', ['name' => 'Dara', 'email' => 'dara@example.com', 'amount' => 30])
            ->assertOk()->assertJsonFragment(['message' => 'Thank you! Your donation of $30 was sent. Our team will contact you by email soon.']);

        $this->postJson('/forms/job', [
            'name' => 'Dara', 'email' => 'dara@example.com', 'position' => 'Community Facilitator',
            'cv' => UploadedFile::fake()->create('cv.pdf', 20, 'application/pdf'),
        ])->assertOk();

        $this->postJson('/forms/donate', ['name' => 'Dara', 'email' => 'dara@example.com', 'amount' => 0])
            ->assertStatus(422)->assertJson(['ok' => false]);

        Mail::assertSent(WebsiteFormMail::class, 3);
        Mail::assertSent(WebsiteFormMail::class, fn ($mail) => $mail->hasTo('admin@bandoskomar.org')
            && ($mail->fields['Amount (USD)'] ?? null) === '30');
    }

    private function admin(): User
    {
        return User::factory()->create(['role' => User::ROLE_SYSTEM, 'status' => true]);
    }

    private function baseFields(Page $page): array
    {
        return [
            // The edit form always sends every language's title.
            'translations' => $page->translations,
            'slug' => $page->slug,
            'sort_order' => $page->sort_order,
            'is_active' => '1',
        ];
    }
}
