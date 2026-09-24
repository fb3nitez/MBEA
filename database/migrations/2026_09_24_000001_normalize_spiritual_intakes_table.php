<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $booleanColumns = [
            'religious_background_childhood_christian',
            'religious_background_childhood_catholic',
            'religious_background_childhood_none',
            'religious_background_childhood_other',
            'religious_background_adolescent_christian',
            'religious_background_adolescent_catholic',
            'religious_background_adolescent_none',
            'religious_background_adolescent_other',
            'religious_background_current_christian_science',
            'religious_background_current_mormonism',
            'religious_background_current_children_of_god',
            'religious_background_current_new_age',
            'religious_background_current_unity_church',
            'religious_background_current_church_of_the_living_god',
            'religious_background_current_church_of_new_jerusalem',
            'religious_background_current_the_forum_est',
            'religious_background_current_the_way_international',
            'religious_background_current_word',
            'religious_background_current_jehovah_witnesses',
            'religious_background_current_masons',
            'religious_background_current_other',
            'church_involvement_attends_weekly',
            'church_involvement_occasional_attendance',
            'church_involvement_involved_ministry_service',
            'church_involvement_no_church_involvement',
            'church_involvement_needed_in_ministry_service',
            'church_involvement_no_church_involvement_further',
            'new_age_horoscopes',
            'new_age_dungeons_dragons',
            'new_age_energy_healing',
            'new_age_speaking_in_trance',
            'new_age_manifestation_law_of_attraction',
            'new_age_automatic_writing',
            'new_age_mediumship',
            'new_age_magic_eight_ball',
            'new_age_ancestral_worship',
            'new_age_astrology',
            'new_age_psychic_spells_curses',
            'new_age_reading_palm',
            'new_age_tarot_cards',
            'new_age_seance',
            'new_age_spiritual_guides',
            'new_age_meditation',
            'new_age_fortune_telling',
            'new_age_divination',
            'new_age_crystal_balls',
            'new_age_yoga',
            'new_age_reiki',
            'new_age_self_hypnosis',
            'new_age_mind_swapping',
            'new_age_black_magic',
            'new_age_healing_prayer',
            'new_age_new_medicine',
            'new_age_blood_pacts',
            'new_age_object_worship',
            'new_age_narcotics_or_drugs',
            'new_age_incubus_and_succubus',
            'new_age_other',
            'additional_spiritual_issues_unforgiveness',
            'additional_spiritual_issues_oppression',
            'additional_spiritual_issues_demonic_family_curses',
            'additional_spiritual_issues_addiction',
            'additional_spiritual_issues_mediation',
            'additional_spiritual_issues_theosophical_society',
            'additional_spiritual_issues_ritual_family_oppression',
            'additional_spiritual_issues_mental_disorders',
            'additional_spiritual_issues_recurrent_family_depression',
            'additional_spiritual_issues_adultery',
            'additional_spiritual_issues_poverty',
            'additional_spiritual_issues_pride',
            'additional_spiritual_issues_martha',
            'additional_spiritual_issues_our_house',
            'additional_spiritual_issues_fear',
            'additional_spiritual_issues_buddhism',
            'additional_spiritual_issues_yoga',
            'additional_spiritual_issues_islam',
            'additional_spiritual_issues_black_magic',
            'additional_spiritual_issues_eckankar',
            'additional_spiritual_issues_religion_of_martial_arts',
            'additional_spiritual_issues_science_of_the_mind',
            'additional_spiritual_issues_transcendental_meditation',
            'additional_spiritual_issues_father_divine',
            'additional_spiritual_issues_spiritual_mediation',
            'additional_spiritual_issues_other',
        ];

        $textColumns = [
            'religious_background_childhood_other_text',
            'religious_background_adolescent_other_text',
            'religious_background_current_other_text',
            'new_age_other_text',
            'additional_spiritual_issues_other_text',
            'spiritual_explain_hypnosis',
            'spiritual_guidance_question',
            'spiritual_voices_question',
            'spiritual_unusual_experiences_question',
            'spiritual_prayer_question',
            'spiritual_ritual_worship_question',
        ];

        Schema::table('spiritual_intakes', function (Blueprint $table) use ($booleanColumns, $textColumns) {
            foreach ($booleanColumns as $column) {
                if (! Schema::hasColumn('spiritual_intakes', $column)) {
                    $table->boolean($column)->default(false);
                }
            }

            foreach ($textColumns as $column) {
                if (! Schema::hasColumn('spiritual_intakes', $column)) {
                    $table->text($column)->nullable();
                }
            }

            if (Schema::hasColumn('spiritual_intakes', 'data')) {
                $table->dropColumn('data');
            }
        });
    }

    public function down(): void
    {
        Schema::table('spiritual_intakes', function (Blueprint $table) {
            $table->json('data')->nullable();
        });

        $columns = [
            'religious_background_childhood_christian',
            'religious_background_childhood_catholic',
            'religious_background_childhood_none',
            'religious_background_childhood_other',
            'religious_background_adolescent_christian',
            'religious_background_adolescent_catholic',
            'religious_background_adolescent_none',
            'religious_background_adolescent_other',
            'religious_background_current_christian_science',
            'religious_background_current_mormonism',
            'religious_background_current_children_of_god',
            'religious_background_current_new_age',
            'religious_background_current_unity_church',
            'religious_background_current_church_of_the_living_god',
            'religious_background_current_church_of_new_jerusalem',
            'religious_background_current_the_forum_est',
            'religious_background_current_the_way_international',
            'religious_background_current_word',
            'religious_background_current_jehovah_witnesses',
            'religious_background_current_masons',
            'religious_background_current_other',
            'church_involvement_attends_weekly',
            'church_involvement_occasional_attendance',
            'church_involvement_involved_ministry_service',
            'church_involvement_no_church_involvement',
            'church_involvement_needed_in_ministry_service',
            'church_involvement_no_church_involvement_further',
            'new_age_horoscopes',
            'new_age_dungeons_dragons',
            'new_age_energy_healing',
            'new_age_speaking_in_trance',
            'new_age_manifestation_law_of_attraction',
            'new_age_automatic_writing',
            'new_age_mediumship',
            'new_age_magic_eight_ball',
            'new_age_ancestral_worship',
            'new_age_astrology',
            'new_age_psychic_spells_curses',
            'new_age_reading_palm',
            'new_age_tarot_cards',
            'new_age_seance',
            'new_age_spiritual_guides',
            'new_age_meditation',
            'new_age_fortune_telling',
            'new_age_divination',
            'new_age_crystal_balls',
            'new_age_yoga',
            'new_age_reiki',
            'new_age_self_hypnosis',
            'new_age_mind_swapping',
            'new_age_black_magic',
            'new_age_healing_prayer',
            'new_age_new_medicine',
            'new_age_blood_pacts',
            'new_age_object_worship',
            'new_age_narcotics_or_drugs',
            'new_age_incubus_and_succubus',
            'new_age_other',
            'additional_spiritual_issues_unforgiveness',
            'additional_spiritual_issues_oppression',
            'additional_spiritual_issues_demonic_family_curses',
            'additional_spiritual_issues_addiction',
            'additional_spiritual_issues_mediation',
            'additional_spiritual_issues_theosophical_society',
            'additional_spiritual_issues_ritual_family_oppression',
            'additional_spiritual_issues_mental_disorders',
            'additional_spiritual_issues_recurrent_family_depression',
            'additional_spiritual_issues_adultery',
            'additional_spiritual_issues_poverty',
            'additional_spiritual_issues_pride',
            'additional_spiritual_issues_martha',
            'additional_spiritual_issues_our_house',
            'additional_spiritual_issues_fear',
            'additional_spiritual_issues_buddhism',
            'additional_spiritual_issues_yoga',
            'additional_spiritual_issues_islam',
            'additional_spiritual_issues_black_magic',
            'additional_spiritual_issues_eckankar',
            'additional_spiritual_issues_religion_of_martial_arts',
            'additional_spiritual_issues_science_of_the_mind',
            'additional_spiritual_issues_transcendental_meditation',
            'additional_spiritual_issues_father_divine',
            'additional_spiritual_issues_spiritual_mediation',
            'additional_spiritual_issues_other',
            'religious_background_childhood_other_text',
            'religious_background_adolescent_other_text',
            'religious_background_current_other_text',
            'new_age_other_text',
            'additional_spiritual_issues_other_text',
            'spiritual_explain_hypnosis',
            'spiritual_guidance_question',
            'spiritual_voices_question',
            'spiritual_unusual_experiences_question',
            'spiritual_prayer_question',
            'spiritual_ritual_worship_question',
        ];

        Schema::table('spiritual_intakes', function (Blueprint $table) use ($columns) {
            $table->dropColumn($columns);
        });
    }
};
