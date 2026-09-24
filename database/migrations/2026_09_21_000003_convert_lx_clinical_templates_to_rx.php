<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Convert ClinicalTemplate rows created for the removed separate
     * "lx" type into rx templates carrying their items as payload.lifestyle.
     * Idempotent: only touches rows still typed 'lx'.
     */
    public function up(): void
    {
        $rows = DB::table('clinical_templates')->where('type', 'lx')->get();

        foreach ($rows as $row) {
            $payload = json_decode((string) $row->payload, true) ?: [];

            DB::table('clinical_templates')
                ->where('id', $row->id)
                ->update([
                    'type' => 'rx',
                    'payload' => json_encode([
                        'diag' => $payload['focus'] ?? null,
                        'meds' => [],
                        'lifestyle' => $payload['items'] ?? [],
                    ]),
                    'updated_at' => now(),
                ]);
        }
    }

    public function down(): void
    {
        $rows = DB::table('clinical_templates')
            ->where('type', 'rx')
            ->get();

        foreach ($rows as $row) {
            $payload = json_decode((string) $row->payload, true) ?: [];

            // Only reverse rows this migration converted (no meds, has lifestyle).
            if (array_key_exists('lifestyle', $payload) && empty($payload['meds'])) {
                DB::table('clinical_templates')
                    ->where('id', $row->id)
                    ->update([
                        'type' => 'lx',
                        'payload' => json_encode([
                            'focus' => $payload['diag'] ?? null,
                            'items' => $payload['lifestyle'] ?? [],
                        ]),
                        'updated_at' => now(),
                    ]);
            }
        }
    }
};
