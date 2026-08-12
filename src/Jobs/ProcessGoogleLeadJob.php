<?php

namespace Vendor\MetaLeadIngester\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Vendor\MetaLeadIngester\Models\GoogleAccount;
use Vendor\MetaLeadIngester\Models\GoogleLead;
use Illuminate\Support\Facades\Log;
use Exception;

class ProcessGoogleLeadJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected int $googleAccountId;
    protected array $payload;

    /**
     * Create a new job instance.
     *
     * @param int $googleAccountId
     * @param array $payload
     */
    public function __construct(int $googleAccountId, array $payload)
    {
        $this->googleAccountId = $googleAccountId;
        $this->payload = $payload;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $leadId = $this->payload['lead_id'] ?? null;
            
            if (!$leadId) {
                Log::warning('Google Ads lead payload missing lead_id', ['payload' => $this->payload]);
                return;
            }

            // Extract standard fields
            $fullName = null;
            $email = null;
            $phoneNumber = null;
            
            $rawFieldData = [];

            if (isset($this->payload['user_column_data']) && is_array($this->payload['user_column_data'])) {
                foreach ($this->payload['user_column_data'] as $field) {
                    $columnId = $field['column_id'] ?? '';
                    $value = $field['string_value'] ?? null;

                    $rawFieldData[$columnId] = $value;

                    if ($columnId === 'FULL_NAME') {
                        $fullName = $value;
                    } elseif ($columnId === 'EMAIL') {
                        $email = $value;
                    } elseif ($columnId === 'PHONE_NUMBER') {
                        $phoneNumber = $value;
                    }
                }
            }

            // Create or update the lead record
            GoogleLead::updateOrCreate(
                [
                    'google_account_id' => $this->googleAccountId,
                    'lead_id' => $leadId,
                ],
                [
                    'form_id' => $this->payload['form_id'] ?? null,
                    'campaign_id' => $this->payload['campaign_id'] ?? null,
                    'full_name' => $fullName,
                    'email' => $email,
                    'phone_number' => $phoneNumber,
                    'raw_field_data' => $rawFieldData,
                    'lead_created_at' => now(), // Google doesn't send a timestamp in the payload typically, use current time
                ]
            );

        } catch (Exception $e) {
            Log::error('Error processing Google Lead', [
                'error' => $e->getMessage(),
                'payload' => $this->payload
            ]);
            
            // Optionally rethrow if you want the job to be retried
            throw $e;
        }
    }
}
