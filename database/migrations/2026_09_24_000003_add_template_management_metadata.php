<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clinical_templates', function (Blueprint $table) {
            if (! Schema::hasColumn('clinical_templates', 'created_by')) {
                $table->foreignId('created_by')->nullable()->after('description')->constrained('users')->nullOnDelete();
            }
            if (! Schema::hasColumn('clinical_templates', 'last_used_at')) {
                $table->timestamp('last_used_at')->nullable()->after('sort_order');
            }
            if (! Schema::hasColumn('clinical_templates', 'usage_count')) {
                $table->unsignedInteger('usage_count')->default(0)->after('last_used_at');
            }
        });

        if (! Schema::hasTable('clinical_template_user_states')) {
            Schema::create('clinical_template_user_states', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->foreignId('clinical_template_id')->constrained()->cascadeOnDelete();
                $table->boolean('is_favorite')->default(false);
                $table->timestamp('last_used_at')->nullable();
                $table->unsignedInteger('usage_count')->default(0);
                $table->timestamps();
                $table->unique(['user_id', 'clinical_template_id'], 'ct_user_template_unique');
            });
        } elseif (! collect(DB::select('SHOW INDEX FROM clinical_template_user_states'))->contains(fn ($index) => $index->Key_name === 'ct_user_template_unique')) {
            DB::statement('ALTER TABLE clinical_template_user_states ADD UNIQUE INDEX ct_user_template_unique (user_id, clinical_template_id)');
        }

        foreach (DB::table('clinical_templates')->select('id', 'tag')->get() as $template) {
            $tag = trim((string) $template->tag);
            DB::table('clinical_templates')->where('id', $template->id)->update([
                'tag' => $tag === '' ? null : Str::title(Str::lower($tag)),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('clinical_template_user_states');
        Schema::table('clinical_templates', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropColumn(['created_by', 'last_used_at', 'usage_count']);
        });
    }
};
