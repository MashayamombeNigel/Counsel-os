<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use App\Models\Client;
use App\Models\Document;
use App\Models\DocumentInsight;
use App\Models\Matter;
use App\Models\ResearchSession;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $user = $this->createDemoUser();

        $this->createFlagshipMatter($user);
        $this->createFillerData($user);
    }

    /**
     * Fixed, predictable credentials so the README's demo credentials
     * placeholder finally has something real to point at. Uses
     * updateOrCreate so re-running the seeder doesn't create duplicates.
     */
    protected function createDemoUser(): User
    {
        return User::updateOrCreate(
            ['email' => 'demo@counselos.test'],
            [
                'name' => 'Demo Attorney',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
    }

    /**
     * The exact demo dataset from spec Section 20: Acme Property
     * Holdings, the Riverside lease-review matter, a pre-analyzed
     * sample lease with the specific risks/obligations/deadlines the
     * spec's demo script calls out by name, a research session
     * matching the spec's suggested question, and a task converted
     * from one of the deadlines - so the demo script can be walked
     * end to end without waiting on a live Gemini call.
     */
    protected function createFlagshipMatter(User $user): void
    {
        $client = Client::updateOrCreate(
            ['user_id' => $user->id, 'name' => 'Acme Property Holdings'],
            [
                'organization' => 'Acme Property Holdings LLC',
                'email' => 'contact@acmeproperty.example',
                'phone' => '555-0142',
                'address' => '400 Riverside Ave, Suite 210',
                'notes' => 'Commercial property holding company, recurring lease review client.',
            ]
        );

        $matter = Matter::updateOrCreate(
            ['client_id' => $client->id, 'title' => 'Lease Review - Riverside Office Unit'],
            [
                'description' => 'Review of a proposed commercial office lease ahead of tenant sign-off.',
                'practice_area' => 'Commercial Real Estate',
                'status' => 'in_review',
                'opened_at' => now()->subDays(6),
            ]
        );

        // Synthetic sample text only - not a real client document, per
        // spec Section 17's AI safety baseline ("demo documents must
        // be synthetic or public-domain style examples").
        $extractedText = <<<TEXT
        COMMERCIAL LEASE AGREEMENT (SAMPLE)

        This lease is entered into between Riverside Holdings ("Landlord") and
        Acme Property Holdings LLC ("Tenant") for the office unit located at
        400 Riverside Ave, Suite 210.

        Section 4 - Rent. Tenant shall pay monthly rent of $4,200, due on the
        1st of each month. A late payment penalty of 5% applies after a 5-day
        grace period.

        Section 7 - Maintenance. Tenant is responsible for interior
        maintenance and minor repairs. Landlord is responsible for structural
        and exterior maintenance.

        Section 9 - Insurance. Tenant shall maintain commercial general
        liability insurance of no less than $1,000,000 for the lease term.

        Section 12 - Termination. Either party may terminate with 60 days
        written notice. The notice delivery method is not explicitly defined,
        creating ambiguity as to what constitutes valid notice.

        Section 15 - Renewal. Tenant must provide written renewal notice no
        later than 90 days before the lease expiration date of March 1, 2027.

        Section 18 - Inspection. Landlord reserves the right to inspect the
        premises with 48 hours notice, no more than twice annually.
        TEXT;

        $document = Document::updateOrCreate(
            ['matter_id' => $matter->id, 'original_name' => 'Sample_Commercial_Lease_Agreement.pdf'],
            [
                'uploaded_by' => $user->id,
                'filename' => 'demo-sample-lease.pdf',
                // NOTE: no real file is written to disk for this seeded
                // record - the document viewer only reads extracted_text
                // and the DocumentInsight columns, so a real file isn't
                // needed for the demo script to work. If you later add a
                // "download original file" feature, this record won't
                // have a real file behind it and will need a genuine
                // upload to demo that specific feature.
                'storage_path' => 'demo/sample-lease.pdf',
                'mime_type' => 'application/pdf',
                'file_size' => 84213,
                'document_type' => 'lease',
                'extracted_text' => $extractedText,
                'processing_status' => 'analyzed',
            ]
        );

        $insight = DocumentInsight::updateOrCreate(
            ['document_id' => $document->id],
            [
                'summary' => 'A commercial office lease between Riverside Holdings (Landlord) and Acme Property Holdings LLC (Tenant) for a Riverside Ave office unit, with standard rent, maintenance, and insurance terms, and a notably ambiguous termination notice clause.',
                'key_parties_json' => ['Riverside Holdings (Landlord)', 'Acme Property Holdings LLC (Tenant)'],
                'key_clauses_json' => [
                    ['title' => 'Rent', 'description' => 'Monthly rent of $4,200 due on the 1st, with a 5% late penalty after a 5-day grace period.'],
                    ['title' => 'Maintenance split', 'description' => 'Tenant handles interior/minor repairs; Landlord handles structural and exterior maintenance.'],
                ],
                'risks_json' => [
                    ['title' => 'Late payment penalty', 'severity' => 'medium', 'reason' => 'A 5% penalty applies quickly after only a 5-day grace period, which is tighter than typical commercial terms.'],
                    ['title' => 'Maintenance obligations', 'severity' => 'low', 'reason' => 'Interior/exterior split is standard but should be confirmed against the unit\'s actual condition before signing.'],
                    ['title' => 'Termination notice ambiguity', 'severity' => 'high', 'reason' => 'The lease does not define a valid notice delivery method, which could create a dispute over whether termination notice was properly given.'],
                ],
                'obligations_json' => [
                    ['party' => 'Tenant', 'obligation' => 'Maintain commercial general liability insurance of at least $1,000,000.', 'source_hint' => 'Section 9'],
                    ['party' => 'Tenant', 'obligation' => 'Pay monthly rent by the 1st of each month.', 'source_hint' => 'Section 4'],
                    ['party' => 'Tenant', 'obligation' => 'Handle interior maintenance and minor repairs.', 'source_hint' => 'Section 7'],
                ],
                'deadlines_json' => [
                    ['title' => 'Monthly rent due date', 'date' => 'unknown', 'reason' => 'Recurring - due on the 1st of every month per Section 4.'],
                    ['title' => 'Lease renewal notice deadline', 'date' => '2026-12-01', 'reason' => 'Written renewal notice must be given at least 90 days before the March 1, 2027 expiration.'],
                    ['title' => 'Landlord inspection notice window', 'date' => 'unknown', 'reason' => 'Landlord may inspect with 48 hours notice, up to twice a year - not a fixed date but worth tracking.'],
                ],
                'questions_json' => [
                    'Should the termination notice clause be amended to specify a delivery method (e.g. certified mail)?',
                    'Is the 5-day grace period before the late penalty consistent with the tenant\'s typical payment cycle?',
                ],
                'model_name' => 'gemini-2.5-flash',
                'raw_ai_response' => '(seeded demo record - no live Gemini call was made for this fixture)',
            ]
        );

        // Task converted from the renewal deadline - demonstrates the
        // "convert deadline to task" workflow from Epic 5 without
        // requiring a live click-through during a demo.
        Task::updateOrCreate(
            ['matter_id' => $matter->id, 'title' => 'Lease renewal notice deadline'],
            [
                'source_document_id' => $document->id,
                'description' => 'Written renewal notice must be given at least 90 days before the March 1, 2027 expiration.',
                'due_date' => '2026-12-01',
                'status' => 'open',
                'priority' => 'high',
                'created_by' => $user->id,
            ]
        );

        // Matches the spec Section 20 demo script's suggested research
        // question exactly, with a plausible answer grounded in the
        // seeded insight data above.
        ResearchSession::updateOrCreate(
            ['matter_id' => $matter->id, 'user_id' => $user->id, 'query' => 'What liabilities does the tenant assume?'],
            [
                'response' => "Short answer: The tenant assumes liability primarily around insurance, timely rent payment, and interior maintenance.\n\nSupporting points:\n- Tenant must maintain at least \$1,000,000 in commercial general liability insurance for the full lease term.\n- A 5% late payment penalty applies after only a 5-day grace period - a relatively narrow window.\n- Tenant is responsible for interior maintenance and minor repairs, though structural/exterior liability remains with the Landlord.\n- The termination notice clause does not specify a valid delivery method, which could expose either party to a dispute if a notice is contested.\n\nRelevant source document: Sample_Commercial_Lease_Agreement.pdf\n\nThis is AI-generated review assistance and does not constitute legal advice.",
                'sources_json' => ['Sample_Commercial_Lease_Agreement.pdf'],
                'model_name' => 'gemini-2.5-flash',
            ]
        );

        $this->logTimeline($matter->id, $user->id, 'matter_created', "Matter \"{$matter->title}\" created for {$client->name}.", now()->subDays(6));
        $this->logTimeline($matter->id, $user->id, 'document_uploaded', "Document \"{$document->original_name}\" uploaded.", now()->subDays(5));
        $this->logTimeline($matter->id, $user->id, 'text_extracted', "Text extracted from \"{$document->original_name}\". Ready for AI analysis.", now()->subDays(5)->addHour());
        $this->logTimeline($matter->id, $user->id, 'ai_analysis_completed', "AI analysis completed for \"{$document->original_name}\".", now()->subDays(4));
        $this->logTimeline($matter->id, $user->id, 'task_created', 'Task "Lease renewal notice deadline" created from AI-extracted deadline.', now()->subDays(3));
    }

    /**
     * Lighter-weight filler so the dashboard's stat cards and lists
     * look like a real, active workspace rather than a single lonely
     * matter - a handful of clients/matters across different statuses,
     * a couple of open (non-analyzed) tasks, and no AI insight data
     * attached (keeps the seeder fast and avoids implying every
     * document in the system has been through analysis).
     */
    protected function createFillerData(User $user): void
    {
        $fillerClients = [
            ['name' => 'Chen & Associates', 'organization' => 'Chen & Associates Ltd'],
            ['name' => 'Marcus Whitfield', 'organization' => null],
            ['name' => 'Northgate Retail Group', 'organization' => 'Northgate Retail Group Inc'],
        ];

        $fillerMatters = [
            ['title' => 'Employment Contract Review - Senior Hire', 'practice_area' => 'Employment', 'status' => 'open'],
            ['title' => 'NDA Drafting - Vendor Partnership', 'practice_area' => 'Contracts', 'status' => 'waiting_client'],
            ['title' => 'Retail Space Sublease Dispute', 'practice_area' => 'Commercial Real Estate', 'status' => 'closed'],
        ];

        foreach ($fillerClients as $index => $clientData) {
            $client = Client::updateOrCreate(
                ['user_id' => $user->id, 'name' => $clientData['name']],
                [
                    'organization' => $clientData['organization'],
                    'email' => strtolower(str_replace(' ', '.', $clientData['name'])) . '@example.com',
                ]
            );

            $matterData = $fillerMatters[$index];

            $matter = Matter::updateOrCreate(
                ['client_id' => $client->id, 'title' => $matterData['title']],
                [
                    'practice_area' => $matterData['practice_area'],
                    'status' => $matterData['status'],
                    'opened_at' => now()->subDays(rand(2, 20)),
                    'closed_at' => $matterData['status'] === 'closed' ? now()->subDays(1) : null,
                ]
            );

            if ($matterData['status'] !== 'closed') {
                Task::updateOrCreate(
                    ['matter_id' => $matter->id, 'title' => 'Follow up with client on outstanding items'],
                    [
                        'due_date' => now()->addDays(rand(1, 10)),
                        'status' => 'open',
                        'priority' => ['low', 'medium', 'high'][array_rand(['low', 'medium', 'high'])],
                        'created_by' => $user->id,
                    ]
                );
            }

            $this->logTimeline($matter->id, $user->id, 'matter_created', "Matter \"{$matter->title}\" created for {$client->name}.", now()->subDays(rand(2, 20)));
        }
    }

    protected function logTimeline(int $matterId, int $userId, string $action, string $description, $createdAt): void
    {
        ActivityLog::create([
            'user_id' => $userId,
            'matter_id' => $matterId,
            'subject_type' => Matter::class,
            'subject_id' => $matterId,
            'action' => $action,
            'description' => $description,
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
        ]);
    }
}
