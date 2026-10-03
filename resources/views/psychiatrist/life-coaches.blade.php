@extends('layouts.psychiatrist')

@section('title', 'Life Coach Management')
@section('page', 'life-coaches')
@section('page_title', 'Life Coach Management')

@section('content')
    <section class="psych-section active" id="section-life-coaches">
        <section class="card lc-coach-list" aria-labelledby="coach-list-title">
            <div class="card-header">
                <span class="card-title" id="coach-list-title">Current Life Coaches</span>
                <div class="lc-management-actions">
                    <span class="lc-management-count">{{ $coaches->count() }}
                        {{ Str::plural('coach', $coaches->count()) }}</span>
                    <button class="btn-blue" type="button" id="open-coach-modal"><i data-feather="plus"></i> Add Life Coach</button>
                </div>
            </div>
            <div class="lc-coach-table-wrap">
                <table class="lc-coach-table">
                    <thead>
                        <tr>
                            <th>Life Coach</th>
                              <th>Email</th>
                            <th>Phone</th>
                            <th>Bio</th>
                            <th><span class="sr-only">Actions</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($coaches as $coach)
                            <tr>
                                <td>
                                    <div class="lc-table-person"><span
                                            class="lc-coach-avatar">{{ collect(explode(' ', trim($coach->name)))->filter()->map(fn($part) => strtoupper(substr($part, 0, 1)))->take(2)->implode('') }}</span><span><strong>{{ $coach->name }}</strong><small>Active
                                                Life Coach</small></span></div>
                                </td>
                                <td><a href="mailto:{{ $coach->email }}">{{ $coach->email }}</a></td>
                                <td>{{ $coach->phone ?: '—' }}</td>
                                <td class="lc-table-bio">{{ $coach->bio ?: 'No bio added yet.' }}</td>
                                <td class="lc-table-action"><a class="btn-outline-sm"
                                        href="{{ route('psychiatrist.life-coaches', ['edit' => $coach->id]) }}">Edit</a></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5">
                                    <div class="lc-management-empty"><i data-feather="users"></i><strong>No Life Coaches
                                            yet</strong><span>Add the first coach to start building the care team.</span></div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <div class="modal-overlay lc-coach-modal {{ ($editingCoach || $errors->any()) ? '' : 'hidden' }}" id="coach-form"
            role="dialog" aria-modal="true" aria-labelledby="coach-form-title">
            <section class="modal-box lc-coach-form-card">
                <div class="card-header">
                    <span class="card-title"
                        id="coach-form-title">{{ $editingCoach ? 'Edit Life Coach' : 'Add Life Coach' }}</span>
                    <button class="modal-close" type="button" id="close-coach-modal" aria-label="Close"><i
                            data-feather="x"></i></button>
                </div>
                <form method="POST"
                    action="{{ $editingCoach ? route('psychiatrist.life-coaches.update', $editingCoach->id) : route('psychiatrist.life-coaches.store') }}"
                    class="lc-management-form">
                    @csrf
                    @if ($editingCoach) @method('PUT') @endif
                    <div class="field-group">
                        <label class="field-label" for="coach-name">Full name</label>
                        <input class="field-input" id="coach-name" name="name"
                            value="{{ old('name', $editingCoach?->name) }}" required />
                        @error('name')<span class="lc-management-error">{{ $message }}</span>@enderror
                    </div>
                    <div class="field-group">
                        <label class="field-label" for="coach-email">Email</label>
                        <input class="field-input" id="coach-email" name="email" type="email"
                            value="{{ old('email', $editingCoach?->email) }}" required />
                        @error('email')<span class="lc-management-error">{{ $message }}</span>@enderror
                    </div>
                    <div class="field-group">
                        <label class="field-label" for="coach-phone">Phone</label>
                        <input class="field-input" id="coach-phone" name="phone"
                            value="{{ old('phone', $editingCoach?->phone) }}" placeholder="09XX XXX XXXX" />
                        @error('phone')<span class="lc-management-error">{{ $message }}</span>@enderror
                    </div>
                    <div class="field-group">
                        <label class="field-label" for="coach-bio">Short bio</label>
                        <textarea class="field-textarea" id="coach-bio" name="bio" rows="4"
                            maxlength="250">{{ old('bio', $editingCoach?->bio) }}</textarea>
                        @error('bio')<span class="lc-management-error">{{ $message }}</span>@enderror
                    </div>
                    <div class="lc-management-password-heading">
                        {{ $editingCoach ? 'Reset password (optional)' : 'Initial password' }}
                    </div>
                    <div class="field-group">
                        <label class="field-label" for="coach-password">Password</label>
                        <input class="field-input" id="coach-password" name="password" type="password"
                            autocomplete="new-password" {{ $editingCoach ? '' : 'required' }} />
                        @error('password')<span class="lc-management-error">{{ $message }}</span>@enderror
                    </div>
                    <div class="field-group">
                        <label class="field-label" for="coach-password-confirmation">Confirm password</label>
                        <input class="field-input" id="coach-password-confirmation" name="password_confirmation"
                            type="password" autocomplete="new-password" {{ $editingCoach ? '' : 'required' }} />
                    </div>
                    <button class="btn-blue lc-management-submit" type="submit"><i data-feather="save"></i>
                        {{ $editingCoach ? 'Save details' : 'Create Life Coach' }}</button>
                </form>
            </section>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var modal = document.getElementById('coach-form');
            var open = document.getElementById('open-coach-modal');
            var close = document.getElementById('close-coach-modal');
            function closeModal() { if (modal) modal.classList.add('hidden'); }
            if (open) open.addEventListener('click', function () { modal.classList.remove('hidden'); document.getElementById('coach-name').focus(); });
            if (close) close.addEventListener('click', closeModal);
            if (modal) modal.addEventListener('click', function (event) { if (event.target === modal) closeModal(); });
            document.addEventListener('keydown', function (event) { if (event.key === 'Escape' && modal && !modal.classList.contains('hidden')) closeModal(); });
        });
    </script>
@endpush