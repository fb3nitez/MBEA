<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SpiritualIntake extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'religious_background_childhood_christian' => 'boolean',
        'religious_background_childhood_catholic' => 'boolean',
        'religious_background_childhood_none' => 'boolean',
        'religious_background_childhood_other' => 'boolean',
        'religious_background_adolescent_christian' => 'boolean',
        'religious_background_adolescent_catholic' => 'boolean',
        'religious_background_adolescent_none' => 'boolean',
        'religious_background_adolescent_other' => 'boolean',
        'religious_background_current_christian_science' => 'boolean',
        'religious_background_current_mormonism' => 'boolean',
        'religious_background_current_children_of_god' => 'boolean',
        'religious_background_current_new_age' => 'boolean',
        'religious_background_current_unity_church' => 'boolean',
        'religious_background_current_church_of_the_living_god' => 'boolean',
        'religious_background_current_church_of_new_jerusalem' => 'boolean',
        'religious_background_current_the_forum_est' => 'boolean',
        'religious_background_current_the_way_international' => 'boolean',
        'religious_background_current_word' => 'boolean',
        'religious_background_current_jehovah_witnesses' => 'boolean',
        'religious_background_current_masons' => 'boolean',
        'religious_background_current_other' => 'boolean',
        'church_involvement_attends_weekly' => 'boolean',
        'church_involvement_occasional_attendance' => 'boolean',
        'church_involvement_involved_ministry_service' => 'boolean',
        'church_involvement_no_church_involvement' => 'boolean',
        'church_involvement_needed_in_ministry_service' => 'boolean',
        'church_involvement_no_church_involvement_further' => 'boolean',
        'new_age_horoscopes' => 'boolean',
        'new_age_dungeons_dragons' => 'boolean',
        'new_age_energy_healing' => 'boolean',
        'new_age_speaking_in_trance' => 'boolean',
        'new_age_manifestation_law_of_attraction' => 'boolean',
        'new_age_automatic_writing' => 'boolean',
        'new_age_mediumship' => 'boolean',
        'new_age_magic_eight_ball' => 'boolean',
        'new_age_ancestral_worship' => 'boolean',
        'new_age_astrology' => 'boolean',
        'new_age_psychic_spells_curses' => 'boolean',
        'new_age_reading_palm' => 'boolean',
        'new_age_tarot_cards' => 'boolean',
        'new_age_seance' => 'boolean',
        'new_age_spiritual_guides' => 'boolean',
        'new_age_meditation' => 'boolean',
        'new_age_fortune_telling' => 'boolean',
        'new_age_divination' => 'boolean',
        'new_age_crystal_balls' => 'boolean',
        'new_age_yoga' => 'boolean',
        'new_age_reiki' => 'boolean',
        'new_age_self_hypnosis' => 'boolean',
        'new_age_mind_swapping' => 'boolean',
        'new_age_black_magic' => 'boolean',
        'new_age_healing_prayer' => 'boolean',
        'new_age_new_medicine' => 'boolean',
        'new_age_blood_pacts' => 'boolean',
        'new_age_object_worship' => 'boolean',
        'new_age_narcotics_or_drugs' => 'boolean',
        'new_age_incubus_and_succubus' => 'boolean',
        'new_age_other' => 'boolean',
        'additional_spiritual_issues_unforgiveness' => 'boolean',
        'additional_spiritual_issues_oppression' => 'boolean',
        'additional_spiritual_issues_demonic_family_curses' => 'boolean',
        'additional_spiritual_issues_addiction' => 'boolean',
        'additional_spiritual_issues_mediation' => 'boolean',
        'additional_spiritual_issues_theosophical_society' => 'boolean',
        'additional_spiritual_issues_ritual_family_oppression' => 'boolean',
        'additional_spiritual_issues_mental_disorders' => 'boolean',
        'additional_spiritual_issues_recurrent_family_depression' => 'boolean',
        'additional_spiritual_issues_adultery' => 'boolean',
        'additional_spiritual_issues_poverty' => 'boolean',
        'additional_spiritual_issues_pride' => 'boolean',
        'additional_spiritual_issues_martha' => 'boolean',
        'additional_spiritual_issues_our_house' => 'boolean',
        'additional_spiritual_issues_fear' => 'boolean',
        'additional_spiritual_issues_buddhism' => 'boolean',
        'additional_spiritual_issues_yoga' => 'boolean',
        'additional_spiritual_issues_islam' => 'boolean',
        'additional_spiritual_issues_black_magic' => 'boolean',
        'additional_spiritual_issues_eckankar' => 'boolean',
        'additional_spiritual_issues_religion_of_martial_arts' => 'boolean',
        'additional_spiritual_issues_science_of_the_mind' => 'boolean',
        'additional_spiritual_issues_transcendental_meditation' => 'boolean',
        'additional_spiritual_issues_father_divine' => 'boolean',
        'additional_spiritual_issues_spiritual_mediation' => 'boolean',
        'additional_spiritual_issues_other' => 'boolean',
    ];

    public function patientRecord(): BelongsTo
    {
        return $this->belongsTo(PatientRecord::class);
    }
}
