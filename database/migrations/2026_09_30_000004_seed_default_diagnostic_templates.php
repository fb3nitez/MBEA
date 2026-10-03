<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $templates = [
            [
                'name' => 'Pre-SSRI/SNRI Baseline Workup',
                'tag' => 'Baseline',
                'tag_class' => 'tag-psychiatric',
                'description' => 'CBC, liver, thyroid, and metabolic baseline tests',
                'tests' => ['CBC with differential', 'AST', 'ALT', 'Total Bilirubin', 'Alkaline Phosphatase', 'GGT', 'TSH', 'Free T3', 'Free T4', 'Fasting Blood Sugar', 'HbA1c', 'Lipid Profile'],
                'sort_order' => 5,
            ],
            [
                'name' => 'Antipsychotic Metabolic Monitoring',
                'tag' => 'Metabolic',
                'tag_class' => 'tag-metabolic',
                'description' => 'Glucose, HbA1c, lipids, and liver monitoring',
                'tests' => ['Fasting Blood Sugar', 'HbA1c', 'Lipid Profile', 'AST', 'ALT', 'Total Bilirubin', 'Alkaline Phosphatase', 'GGT'],
                'sort_order' => 6,
            ],
            [
                'name' => 'Lithium Monitoring Workup',
                'tag' => 'Mood Stabilizer',
                'tag_class' => 'tag-bipolar',
                'description' => 'Renal function, electrolytes, and thyroid monitoring',
                'tests' => ['BUN', 'Creatinine', 'eGFR', 'Sodium', 'Potassium', 'Chloride', 'Calcium', 'Magnesium', 'TSH', 'Free T4'],
                'sort_order' => 7,
            ],
            [
                'name' => 'General Annual Wellness Panel',
                'tag' => 'Wellness',
                'tag_class' => 'tag-lifestyle',
                'description' => 'CBC, metabolic, liver, and urinalysis screening',
                'tests' => ['CBC with differential', 'Fasting Blood Sugar', 'HbA1c', 'Lipid Profile', 'AST', 'ALT', 'Total Bilirubin', 'Alkaline Phosphatase', 'GGT', 'Complete urinalysis'],
                'sort_order' => 8,
            ],
            [
                'name' => 'Cardiac Risk Workup',
                'tag' => 'Cardiac',
                'tag_class' => 'tag-anxiety',
                'description' => 'ECG, lipids, and electrolyte screening',
                'tests' => ['ECG', 'Lipid Profile', 'Sodium', 'Potassium', 'Chloride', 'Calcium', 'Magnesium'],
                'sort_order' => 9,
            ],
        ];

        foreach ($templates as $template) {
            if (DB::table('clinical_templates')->where('type', 'dx')->where('name', $template['name'])->exists()) {
                continue;
            }

            DB::table('clinical_templates')->insert([
                'type' => 'dx',
                'name' => $template['name'],
                'tag' => $template['tag'],
                'tag_class' => $template['tag_class'],
                'description' => $template['description'],
                'payload' => json_encode(['tests' => $template['tests']], JSON_THROW_ON_ERROR),
                'sort_order' => $template['sort_order'],
                'created_by' => null,
                'last_used_at' => null,
                'usage_count' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        DB::table('clinical_templates')
            ->where('type', 'dx')
            ->whereIn('name', [
                'Pre-SSRI/SNRI Baseline Workup',
                'Antipsychotic Metabolic Monitoring',
                'Lithium Monitoring Workup',
                'General Annual Wellness Panel',
                'Cardiac Risk Workup',
            ])
            ->delete();
    }
};
