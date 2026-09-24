<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Seed the Rx lifestyle templates into existing databases. A template is
     * only inserted when no row with the same name and type exists, so
     * user-edited templates are never overwritten and later deletions are
     * not resurrected on a re-run.
     */
    public function up(): void
    {
        $defaults = [
            [
                'type' => 'rx',
                'name' => 'Insomnia — Sleep Hygiene Plan',
                'tag' => 'Sleep',
                'tag_class' => 'tag-lifestyle',
                'description' => 'Fixed wake time, no screens 1h before bed',
                'payload' => [
                    'diag' => 'Insomnia / poor sleep',
                    'meds' => [],
                    'lifestyle' => [
                        ['category' => 'sleep', 'title' => 'Fixed wake-up time', 'target' => '7-8 h sleep', 'frequency' => 'Daily', 'duration' => '4 weeks', 'instructions' => 'Wake at the same time every day, including weekends.'],
                        ['category' => 'sleep', 'title' => 'No screens before bed', 'target' => '60 min buffer', 'frequency' => 'Nightly', 'duration' => '4 weeks', 'instructions' => 'Stop phone, TV and computer use one hour before bedtime.'],
                        ['category' => 'sleep', 'title' => 'Bed is for sleep only', 'target' => 'Leave bed if awake > 20 min', 'frequency' => 'Nightly', 'duration' => '4 weeks', 'instructions' => 'If unable to sleep, get up, do a quiet activity in dim light, return when sleepy.'],
                        ['category' => 'stress', 'title' => 'Wind-down breathing', 'target' => '10 minutes', 'frequency' => 'Nightly', 'duration' => '4 weeks', 'instructions' => 'Slow diaphragmatic breathing or guided relaxation before lights out.'],
                    ],
                ],
                'sort_order' => 5,
            ],
            [
                'type' => 'rx',
                'name' => 'Depression — Graded Exercise',
                'tag' => 'Exercise',
                'tag_class' => 'tag-lifestyle',
                'description' => 'Build up to 150 min/week moderate activity',
                'payload' => [
                    'diag' => 'Major Depressive Disorder — behavioural activation',
                    'meds' => [],
                    'lifestyle' => [
                        ['category' => 'exercise', 'title' => 'Walking', 'target' => '150 min/week', 'frequency' => '5x/week, 30 min', 'duration' => '8 weeks', 'instructions' => 'Start at an easy pace; increase duration before intensity.'],
                        ['category' => 'exercise', 'title' => 'Resistance / bodyweight routine', 'target' => '2 sessions/week', 'frequency' => '2x/week', 'duration' => '8 weeks', 'instructions' => 'Major muscle groups; stop short of exhaustion.'],
                        ['category' => 'social', 'title' => 'Planned social contact', 'target' => '1 activity/week', 'frequency' => 'Weekly', 'duration' => '8 weeks', 'instructions' => 'Schedule one social or group activity each week and record mood after.'],
                    ],
                ],
                'sort_order' => 6,
            ],
            [
                'type' => 'rx',
                'name' => 'Mediterranean-Style Nutrition Plan',
                'tag' => 'Nutrition',
                'tag_class' => 'tag-lifestyle',
                'description' => 'Whole foods, fish twice weekly, limit ultra-processed',
                'payload' => [
                    'diag' => 'Mood-supportive nutrition',
                    'meds' => [],
                    'lifestyle' => [
                        ['category' => 'nutrition', 'title' => 'Fruits & vegetables', 'target' => '5 servings/day', 'frequency' => 'Daily', 'duration' => 'Ongoing', 'instructions' => 'Aim for a variety of colours across meals.'],
                        ['category' => 'nutrition', 'title' => 'Fish / seafood meals', 'target' => '2 servings/week', 'frequency' => '2x/week', 'duration' => 'Ongoing', 'instructions' => 'Prefer oily fish such as salmon, sardines or mackerel.'],
                        ['category' => 'nutrition', 'title' => 'Limit sugary drinks & fast food', 'target' => '≤ 1x/week', 'frequency' => 'Ongoing', 'duration' => 'Ongoing', 'instructions' => 'Replace with water and home-prepared meals.'],
                    ],
                ],
                'sort_order' => 7,
            ],
            [
                'type' => 'rx',
                'name' => 'Depression — SSRI + Graded Exercise',
                'tag' => 'Depression',
                'tag_class' => 'tag-depression',
                'description' => 'Sertraline 50mg + graded exercise programme',
                'payload' => [
                    'diag' => 'F32.1 Major Depressive Disorder',
                    'meds' => [['name' => 'Sertraline', 'dose' => '50mg', 'freq' => ['Morning'], 'qty' => 30]],
                    'lifestyle' => [
                        ['category' => 'exercise', 'title' => 'Walking', 'target' => '150 min/week', 'frequency' => '5x/week, 30 min', 'duration' => '8 weeks', 'instructions' => 'Start at an easy pace; increase duration before intensity.'],
                        ['category' => 'exercise', 'title' => 'Resistance / bodyweight routine', 'target' => '2 sessions/week', 'frequency' => '2x/week', 'duration' => '8 weeks', 'instructions' => 'Major muscle groups; stop short of exhaustion.'],
                        ['category' => 'social', 'title' => 'Planned social contact', 'target' => '1 activity/week', 'frequency' => 'Weekly', 'duration' => '8 weeks', 'instructions' => 'Schedule one social or group activity each week and record mood after.'],
                    ],
                ],
                'sort_order' => 8,
            ],
        ];

        foreach ($defaults as $default) {
            $exists = DB::table('clinical_templates')
                ->where('type', $default['type'])
                ->where('name', $default['name'])
                ->exists();

            if ($exists) {
                continue;
            }

            DB::table('clinical_templates')->insert([
                'type' => $default['type'],
                'name' => $default['name'],
                'tag' => $default['tag'] ?? null,
                'tag_class' => $default['tag_class'] ?? null,
                'description' => $default['description'] ?? null,
                'payload' => json_encode($default['payload'] ?? []),
                'sort_order' => $default['sort_order'] ?? 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        DB::table('clinical_templates')
            ->whereIn('name', [
                'Insomnia — Sleep Hygiene Plan',
                'Depression — Graded Exercise',
                'Mediterranean-Style Nutrition Plan',
                'Depression — SSRI + Graded Exercise',
            ])
            ->where('type', 'rx')
            ->delete();
    }
};
