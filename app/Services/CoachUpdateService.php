<?php

namespace App\Services;

use App\Models\CoachingNote;
use App\Models\CoachingTask;
use App\Models\LifeCoachUpdate;
use App\Models\PatientRecord;
use App\Models\User;
use Carbon\Carbon;

class CoachUpdateService
{
    public function sync(User $coach): void
    {
        $preferences = $this->preferences($coach);
        if ($preferences['task_reminders']) {
            CoachingTask::where('life_coach_id', $coach->id)->where('is_done', false)->whereNotNull('due_date')->get()->each(function (CoachingTask $task) use ($coach) {
                $due = Carbon::parse($task->due_date);
                $this->upsert($coach, 'task_due', 'Task due: '.$task->description, $due->toDateString() === now()->toDateString() ? 'Due today' : 'Due '.$due->format('M j, Y'), 'task', $task->id, 'task:'.$task->id.':'.$due->toDateString());
            });
        }
        if ($preferences['patient_assignments']) {
            PatientRecord::where('life_coach_id', $coach->id)->latest('updated_at')->limit(25)->get()->each(function (PatientRecord $patient) use ($coach) {
                $this->upsert($coach, 'patient_assigned', 'New patient assigned', $patient->fullname, 'patient', $patient->id, 'patient:'.$patient->id.':'.$patient->updated_at?->toDateString(), $patient->updated_at);
            });
        }
        if ($preferences['note_followups']) {
            CoachingNote::where('life_coach_id', $coach->id)->latest()->limit(25)->get()->each(function (CoachingNote $note) use ($coach) {
                $this->upsert($coach, 'note_followup', 'Follow up on coaching note', $note->session_type, 'note', $note->id, 'note:'.$note->id, $note->created_at);
            });
        }
    }

    public function preferences(User $coach): array
    {
        return array_merge([
            'task_reminders' => true,
            'patient_assignments' => true,
            'note_followups' => true,
        ], $coach->notification_preferences ?: []);
    }

    public function feed(User $coach, ?string $type = null, int $limit = 5, int $offset = 0): array
    {
        $this->sync($coach);
        $preferences = $this->preferences($coach);
        $allowed = collect([
            'task_reminders' => 'task_due',
            'patient_assignments' => 'patient_assigned',
            'note_followups' => 'note_followup',
        ])->filter(fn (string $type, string $preference) => ! empty($preferences[$preference]))->values()->all();
        $query = LifeCoachUpdate::where('user_id', $coach->id)->whereNull('dismissed_at')->whereIn('type', $allowed);
        if ($type && $type !== 'all') {
            if ($type === 'tasks') {
                $query->where('type', 'task_due');
            } else {
                $query->whereIn('type', ['patient_assigned', 'note_followup']);
            }
        }
        $updates = $query->orderByRaw('read_at is null desc')->latest()->get()->map(fn (LifeCoachUpdate $item) => $this->toArray($item));
        $tasks = CoachingTask::where('life_coach_id', $coach->id)->where('is_done', false)->with('patientRecord')->get()->map(function (CoachingTask $task) {
            $due = $task->due_date ? Carbon::parse($task->due_date) : null;

            return ['id' => 'task-'.$task->id, 'type' => 'task', 'icon' => 'check-square', 'title' => $task->description, 'body' => ($task->patientRecord?->fullname ?: 'Task').' · Due '.($due?->format('M j, Y') ?: 'TBD'), 'read' => false, 'related_type' => 'task', 'related_id' => $task->id, 'created_at' => $task->created_at?->toIso8601String(), 'relative_time' => $due?->diffForHumans()];
        });
        $items = collect($updates->all())->merge($tasks->all())->sortByDesc(fn (array $item) => [$item['read'] ? 0 : 1, $item['created_at']])->slice($offset, $limit)->values();
        $pending = CoachingTask::where('life_coach_id', $coach->id)->where('is_done', false)->count();
        $unread = LifeCoachUpdate::where('user_id', $coach->id)->whereNull('dismissed_at')->whereNull('read_at')->whereIn('type', $allowed)->count();

        return ['items' => $items, 'has_more' => $items->count() === $limit, 'unread_count' => $unread, 'pending_tasks' => $pending];
    }

    public function markRead(User $coach, int $id): void
    {
        LifeCoachUpdate::where('user_id', $coach->id)->whereKey($id)->update(['read_at' => now()]);
    }

    public function markAllRead(User $coach): void
    {
        LifeCoachUpdate::where('user_id', $coach->id)->whereNull('read_at')->update(['read_at' => now()]);
    }

    public function dismiss(User $coach, int $id): void
    {
        LifeCoachUpdate::where('user_id', $coach->id)->whereKey($id)->update(['dismissed_at' => now()]);
    }

    private function upsert(User $coach, string $type, string $title, ?string $body, string $relatedType, int $relatedId, string $key, $date = null): void
    {
        LifeCoachUpdate::updateOrCreate(['event_key' => $coach->id.':'.$key], ['user_id' => $coach->id, 'type' => $type, 'title' => $title, 'body' => $body, 'related_type' => $relatedType, 'related_id' => $relatedId, 'data' => ['date' => $date?->toIso8601String()]]);
    }

    private function toArray(LifeCoachUpdate $item): array
    {
        return ['id' => $item->id, 'type' => $item->type, 'icon' => $item->type === 'task_due' ? 'check-square' : ($item->type === 'patient_assigned' ? 'user-plus' : 'edit-3'), 'title' => $item->title, 'body' => $item->body, 'read' => (bool) $item->read_at, 'related_type' => $item->related_type, 'related_id' => $item->related_id, 'created_at' => $item->created_at?->toIso8601String(), 'relative_time' => $item->created_at?->diffForHumans()];
    }
}
