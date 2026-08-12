<?php

namespace Vendor\MetaLeadIngester\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Vendor\MetaLeadIngester\Models\MetaAccount;
use Vendor\MetaLeadIngester\Models\MetaLead;
use Exception;
use Carbon\Carbon;

class ProcessMetaLeadJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * Calculate the number of seconds to wait before retrying the job.
     *
     * @return array<int, int>
     */
    public function backoff(): array
    {
        return [30, 60, 120];
    }

    /**
     * Create a new job instance.
     */
    public function __construct(
        protected ?string $pageId,
        protected string $leadgenId,
        protected ?string $formId,
        protected ?int $createdAt
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // 1. Check if lead already exists to prevent duplicate processing
        if (MetaLead::where('leadgen_id', $this->leadgenId)->exists()) {
            Log::info("MetaLeadIngester: Lead {$this->leadgenId} already exists. Skipping.");
            return;
        }

        // 2. Retrieve MetaAccount for the given page ID
        $account = MetaAccount::where('page_id', $this->pageId)->first();

        if (!$account) {
            Log::warning("MetaLeadIngester: Meta account not found for page ID {$this->pageId}.");
            return;
        }

        if (!$account->is_active) {
            Log::info("MetaLeadIngester: Meta account for page ID {$this->pageId} is inactive. Skipping lead.");
            return;
        }

        // 3. Fetch lead details via Meta Graph API
        $apiVersion = config('meta-lead-ingester.graph_api_version', 'v20.0');
        $endpoint = "https://graph.facebook.com/{$apiVersion}/{$this->leadgenId}";

        $response = Http::get($endpoint, [
            'access_token' => $account->page_access_token,
        ]);

        if ($response->failed()) {
            throw new Exception("MetaLeadIngester: Failed to fetch lead data. HTTP Status: {$response->status()} Response: {$response->body()}");
        }

        $leadData = $response->json();

        // 4. Parse field data dynamically
        $parsedData = $this->parseFieldData($leadData['field_data'] ?? []);

        // 5. Store lead
        MetaLead::create([
            'meta_account_id' => $account->id,
            'leadgen_id' => $this->leadgenId,
            'form_id' => $this->formId,
            'full_name' => $parsedData['full_name'],
            'email' => $parsedData['email'],
            'phone_number' => $parsedData['phone_number'],
            'raw_field_data' => $parsedData['raw_field_data'],
            'lead_created_at' => $this->createdAt ? Carbon::createFromTimestamp($this->createdAt) : now(),
        ]);
        
        Log::info("MetaLeadIngester: Successfully processed and stored lead {$this->leadgenId}.");
    }

    /**
     * Parse field data array to extract common fields and keep the rest as JSON.
     *
     * @param array $fieldData
     * @return array
     */
    protected function parseFieldData(array $fieldData): array
    {
        $fullName = null;
        $email = null;
        $phoneNumber = null;
        $rawFieldData = [];

        foreach ($fieldData as $field) {
            $name = $field['name'] ?? '';
            // value is usually an array with a single element
            $value = isset($field['values']) && is_array($field['values']) && count($field['values']) > 0 
                ? $field['values'][0] 
                : null;

            if ($value === null) {
                continue;
            }

            switch ($name) {
                case 'full_name':
                    $fullName = $value;
                    break;
                case 'email':
                    $email = $value;
                    break;
                case 'phone_number':
                    $phoneNumber = $value;
                    break;
                case 'first_name':
                    if (!$fullName) {
                        $fullName = $value;
                    }
                    $rawFieldData[$name] = $value;
                    break;
                case 'last_name':
                    if ($fullName && $name === 'last_name') {
                        $fullName .= ' ' . $value;
                    } elseif (!$fullName) {
                        $fullName = $value;
                    }
                    $rawFieldData[$name] = $value;
                    break;
                default:
                    $rawFieldData[$name] = $value;
                    break;
            }
        }

        return [
            'full_name' => $fullName,
            'email' => $email,
            'phone_number' => $phoneNumber,
            'raw_field_data' => $rawFieldData,
        ];
    }
}
