<?php

namespace Tests\Feature;

use App\Models\FormSubmission;
use App\Models\User;
use Database\Seeders\SiteContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SubmissionsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(SiteContentSeeder::class);
        Mail::fake();
        Storage::fake('local');
    }

    public function test_website_forms_are_saved_in_the_database(): void
    {
        $this->postJson('/contact', ['name' => 'Dara', 'email' => 'dara@example.com', 'subject' => 'Partnership', 'message' => 'Hello'])->assertOk();
        $this->postJson('/forms/donate', ['name' => 'Sophea', 'email' => 's@example.com', 'amount' => 30, 'message' => 'For books'])->assertOk();
        $this->postJson('/forms/volunteer', ['name' => 'Vanna', 'email' => 'v@example.com', 'availability' => '1 month', 'interest' => 'School gardening'])->assertOk();
        $this->postJson('/forms/job', [
            'name' => 'Rithy', 'email' => 'r@example.com', 'position' => 'Teacher',
            'cv' => UploadedFile::fake()->create('my cv.pdf', 20, 'application/pdf'),
        ])->assertOk();

        $this->assertDatabaseHas('form_submissions', ['type' => 'contact', 'name' => 'Dara', 'subject' => 'Partnership', 'status' => 'new']);
        $this->assertDatabaseHas('form_submissions', ['type' => 'donate', 'name' => 'Sophea', 'amount' => 30]);
        $this->assertDatabaseHas('form_submissions', ['type' => 'volunteer', 'subject' => 'School gardening']);

        $volunteer = FormSubmission::where('type', 'volunteer')->first();
        $this->assertSame('1 month', $volunteer->details['availability']);

        $job = FormSubmission::where('type', 'job')->first();
        $this->assertSame('Teacher', $job->subject);
        $this->assertSame('my cv.pdf', $job->attachment_name);
        Storage::disk('local')->assertExists($job->attachment_path);
    }

    public function test_admin_can_manage_each_kind_of_submission(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_SYSTEM, 'status' => true]);
        $items = [];
        foreach (array_keys(FormSubmission::TYPES) as $type) {
            $items[$type] = FormSubmission::create([
                'type' => $type, 'status' => 'new', 'name' => "Person $type", 'email' => "$type@example.com",
                'amount' => $type === 'donate' ? 25 : null, 'message' => "Message for $type",
            ]);
        }

        foreach ($items as $type => $item) {
            $this->actingAs($admin)->get(route('admin.submissions.index', $type))
                ->assertOk()->assertSee("Person $type")->assertSee(FormSubmission::typeLabel($type));
            $this->actingAs($admin)->get(route('admin.submissions.show', [$type, $item]))
                ->assertOk()->assertSee("Message for $type");
        }

        // Status filter, search and CSV export
        $this->actingAs($admin)->get(route('admin.submissions.index', ['type' => 'donate', 'status' => 'received']))
            ->assertOk()->assertDontSee('Person donate');
        $this->actingAs($admin)->get(route('admin.submissions.index', ['type' => 'contact', 'search' => 'nobody']))
            ->assertOk()->assertDontSee('Person contact');
        $csv = $this->actingAs($admin)->get(route('admin.submissions.index', ['type' => 'donate', 'export' => 'csv']));
        $csv->assertOk();
        $this->assertStringContainsString('Person donate', $csv->streamedContent());

        // A submission is only reachable under its own type
        $this->actingAs($admin)->get(route('admin.submissions.show', ['job', $items['contact']]))->assertNotFound();

        $this->actingAs($admin)
            ->put(route('admin.submissions.update', ['donate', $items['donate']]), ['status' => 'received', 'admin_note' => 'Paid by ABA'])
            ->assertRedirect();
        $this->assertDatabaseHas('form_submissions', ['id' => $items['donate']->id, 'status' => 'received', 'admin_note' => 'Paid by ABA']);

        $this->actingAs($admin)
            ->put(route('admin.submissions.update', ['donate', $items['donate']]), ['status' => 'hired'])
            ->assertSessionHasErrors('status');

        $this->actingAs($admin)->delete(route('admin.submissions.destroy', ['contact', $items['contact']]))->assertRedirect();
        $this->assertDatabaseMissing('form_submissions', ['id' => $items['contact']->id]);
    }

    public function test_admin_can_download_a_cv(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_SYSTEM, 'status' => true]);
        Storage::disk('local')->put('submissions/job/cv.pdf', '%PDF-1.4 test');
        $job = FormSubmission::create([
            'type' => 'job', 'status' => 'new', 'name' => 'Rithy', 'email' => 'r@example.com',
            'attachment_path' => 'submissions/job/cv.pdf', 'attachment_name' => 'Rithy CV.pdf',
        ]);

        $this->actingAs($admin)->get(route('admin.submissions.attachment', ['job', $job]))
            ->assertOk()->assertDownload('Rithy CV.pdf');
    }

    public function test_dashboard_charts_follow_the_period_filter(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_SYSTEM, 'status' => true]);
        $make = fn ($amount, $date, $status = 'new') => FormSubmission::create([
            'type' => 'donate', 'status' => $status, 'name' => 'Donor', 'email' => 'd@example.com', 'amount' => $amount,
        ])->forceFill(['created_at' => $date])->save();

        $make(10, now()->subDays(2));
        $make(100, now()->subDays(2), 'received');
        $make(40, now()->subDays(2), 'cancelled');           // left out of totals
        $make(70, now()->subYear()->startOfYear()->addDays(5));

        $this->actingAs($admin)->get(route('dashboard', ['period' => 'week']))
            ->assertOk()->assertViewHas('stats', fn ($s) => $s['total'] == 110 && $s['count'] === 2 && $s['received'] == 100);

        $this->actingAs($admin)->get(route('dashboard', ['period' => 'year', 'year' => now()->subYear()->year]))
            ->assertOk()->assertViewHas('stats', fn ($s) => $s['total'] == 70)
            ->assertViewHas('chart', fn ($c) => count($c['labels']) === 12 && array_sum($c['amounts']) == 70);

        $this->actingAs($admin)->get(route('dashboard', ['period' => 'all']))
            ->assertOk()->assertViewHas('stats', fn ($s) => $s['total'] == 180)
            ->assertViewHas('chart', fn ($c) => $c['labels'] === [(string) now()->subYear()->year, (string) now()->year]);

        $this->actingAs($admin)->get(route('dashboard', ['period' => 'month', 'month' => now()->subDays(2)->format('Y-m')]))
            ->assertOk()->assertSee('donationBar')->assertSee('donationPie')
            ->assertViewHas('chart', fn ($c) => $c['pieAmounts'][0] >= 10);
    }

    public function test_home_has_no_donation_cards_and_jobs_apply_in_a_popup(): void
    {
        $this->get('/')->assertOk()->assertDontSee('What your donation does')->assertDontSee('class="gift', false);

        $page = \App\Models\Page::where('slug', 'jobs-announcement')->first();
        $content = $page->page_content;
        $content['en']['jobs'] = [['title' => 'Teacher', 'location' => 'Takeo', 'type' => 'Full-time', 'deadline' => '', 'status' => 'open', 'pdf' => '']];
        $page->update(['page_content' => $content]);

        $this->get('/page/jobs-announcement')->assertOk()
            ->assertSee('data-modal-open="apply"', false)
            ->assertSee('class="modal', false)
            ->assertDontSee('<section class="sec alt" id="apply">', false);
    }
}
