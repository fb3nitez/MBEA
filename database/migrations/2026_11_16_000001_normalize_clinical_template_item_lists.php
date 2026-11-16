<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('clinical_templates')
            ->select(['id', 'type', 'payload'])
            ->orderBy('id')
            ->chunkById(100, function ($templates): void {
                foreach ($templates as $template) {
                    $payload = json_decode($template->payload ?? '{}', true, 512, JSON_THROW_ON_ERROR);
                    if (! is_array($payload)) {
                        continue;
                    }

                    if ($template->type === 'dx' && isset($payload['tests'])) {
                        $payload['tests'] = $this->normalizeTests($payload['tests']);
                    } elseif ($template->type === 'rx' && isset($payload['meds'])) {
                        $payload['meds'] = $this->normalizeMedications($payload['meds']);
                    } else {
                        continue;
                    }

                    DB::table('clinical_templates')
                        ->where('id', $template->id)
                        ->update(['payload' => json_encode($payload, JSON_THROW_ON_ERROR)]);
                }
            });
    }

    public function down(): void
    {
        // The normalized JSON arrays cannot be losslessly restored to their original text form.
    }

    private function normalizeTests(mixed $tests): array
    {
        if (is_string($tests)) {
            $tests = preg_split('/[\r\n,|]+/', $tests) ?: [];
        }

        if (! is_array($tests)) {
            return [];
        }

        $normalized = [];
        foreach ($tests as $test) {
            if (! is_string($test)) {
                continue;
            }

            foreach (preg_split('/[\r\n,|]+/', $test) ?: [] as $part) {
                $part = trim($part);
                if ($part !== '') {
                    $normalized[] = $part;
                }
            }
        }

        return array_values(array_unique($normalized));
    }

    private function normalizeMedications(mixed $medications): array
    {
        if (is_string($medications)) {
            $medications = preg_split('/\r\n|\r|\n|,\s*(?=[^|,\r\n]+(?:\|[^|\r\n]*){3})/', $medications) ?: [];
        }

        if (! is_array($medications)) {
            return [];
        }

        $normalized = [];
        foreach ($medications as $medication) {
            if (is_string($medication)) {
                $records = preg_split('/,\s*(?=[^|,\r\n]+(?:\|[^|\r\n]*){3})/', $medication) ?: [];
                foreach ($records as $record) {
                    $parts = array_map('trim', explode('|', $record));
                    $record = [
                        'name' => $parts[0] ?? '',
                        'dose' => $parts[1] ?? '',
                        'freq' => isset($parts[2]) && $parts[2] !== ''
                            ? array_values(array_filter(array_map('trim', explode(',', $parts[2]))))
                            : [],
                        'qty' => $parts[3] ?? '',
                    ];
                    if (trim((string) $record['name']) !== '') {
                        $normalized[] = $record;
                    }
                }
                continue;
            }

            if (! is_array($medication) || trim((string) ($medication['name'] ?? '')) === '') {
                continue;
            }

            $frequency = $medication['freq'] ?? $medication['frequency'] ?? [];
            if (is_string($frequency)) {
                $frequency = preg_split('/[,|]+/', $frequency) ?: [];
            }

            $normalized[] = [
                'name' => trim((string) $medication['name']),
                'dose' => $medication['dose'] ?? '',
                'freq' => is_array($frequency)
                    ? array_values(array_filter(array_map(fn ($value) => trim((string) $value), $frequency)))
                    : [],
                'qty' => $medication['qty'] ?? '',
            ];
        }

        return $normalized;
    }
};
