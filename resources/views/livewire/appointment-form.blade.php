<div>
    @if ($reference)
        {{-- Demande envoyée : récapitulatif à imprimer ou à garder --}}
        <div class="site-card overflow-hidden" role="status" aria-live="polite" x-data x-init="$el.scrollIntoView({ behavior: 'smooth', block: 'start' })">
            <div class="flag-stripe h-1" aria-hidden="true"></div>
            <div class="p-7 sm:p-10">
                <span class="flex size-14 items-center justify-center rounded-full bg-green-50 text-3xl text-flag-green ring-1 ring-green-200" aria-hidden="true">
                    <i class="ph-fill ph-check-circle"></i>
                </span>
                <h2 class="mt-6 font-display text-3xl font-semibold text-brand-700 sm:text-4xl">{{ __('site.appointment.success_title') }}</h2>
                <p class="mt-3 text-slate-600">{{ __('site.appointment.success_text') }}</p>

                <div class="mt-8 rounded-xl bg-paper p-6 ring-1 ring-gold-200">
                    <p class="text-xs font-semibold tracking-[0.2em] text-slate-500 uppercase">{{ __('site.appointment.reference') }}</p>
                    <p class="mt-1 font-mono text-3xl font-bold tracking-widest text-brand-700 sm:text-4xl">{{ $reference }}</p>
                    @if ($summary)
                        <dl class="mt-6 grid gap-4 border-t border-brand-900/10 pt-5 text-sm sm:grid-cols-2">
                            <div><dt class="text-slate-500">{{ __('site.appointment.service') }}</dt><dd class="font-medium text-brand-700">{{ $summary['service'] }}</dd></div>
                            <div><dt class="text-slate-500">{{ __('site.appointment.date') }}</dt><dd class="font-medium text-brand-700 first-letter:uppercase">{{ $summary['date'] }}, {{ $summary['time'] }}</dd></div>
                            <div class="sm:col-span-2"><dt class="text-slate-500">{{ __('site.appointment.confirmation_sent') }}</dt><dd class="font-medium break-all text-brand-700">{{ $summary['email'] }}</dd></div>
                        </dl>
                    @endif
                </div>

                <p class="mt-6 flex items-start gap-2 text-sm text-slate-600">
                    <i class="ph ph-printer mt-0.5 text-gold-600" aria-hidden="true"></i>{{ __('site.appointment.print_hint') }}
                </p>
                <div class="mt-8 flex flex-col gap-3 sm:flex-row print:hidden">
                    <button type="button" onclick="window.print()" class="site-btn site-btn-navy"><i class="ph-bold ph-printer" aria-hidden="true"></i>{{ __('site.appointment.print') }}</button>
                    <button type="button" wire:click="startOver" class="site-btn site-btn-outline">{{ __('site.appointment.new_request') }}</button>
                </div>
            </div>
        </div>
    @else
        <form wire:submit="submit" class="site-card p-6 sm:p-10" novalidate>
            <h2 class="font-display text-3xl font-semibold text-brand-700">{{ __('site.appointment.form_title') }}</h2>
            <p class="mt-2 text-sm text-slate-600">{!! __('site.forms.required_note', ['star' => '<span class="text-flag-red" aria-hidden="true">*</span>']) !!}</p>

            @error('form')
                <p class="mt-6 flex items-start gap-3 rounded-lg bg-red-50 p-4 text-sm text-red-800 ring-1 ring-red-200" role="alert">
                    <i class="ph-fill ph-warning-octagon mt-0.5 text-lg" aria-hidden="true"></i>{{ $message }}
                </p>
            @enderror

            {{-- Piège à robots : invisible et ignoré des lecteurs d'écran --}}
            <div class="absolute -left-[9999px] h-px w-px overflow-hidden" aria-hidden="true">
                <label for="appointment-website">Website</label>
                <input type="text" id="appointment-website" wire:model="website" tabindex="-1" autocomplete="off">
            </div>

            <fieldset class="mt-8">
                <legend class="flex items-center gap-2 text-xs font-semibold tracking-[0.18em] text-gold-700 uppercase"><i class="ph ph-user" aria-hidden="true"></i>{{ __('site.appointment.you') }}</legend>
                <div class="mt-4 grid gap-5 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <x-site.field name="name" :label="__('site.appointment.fields.name')" required :error="$errors->first('name')" wire:model.blur="name" autocomplete="name" />
                    </div>
                    <x-site.field name="email" type="email" :label="__('site.appointment.fields.email')" required :error="$errors->first('email')" wire:model.blur="email" autocomplete="email" inputmode="email" />
                    <x-site.field name="phone" type="tel" :label="__('site.appointment.fields.phone')" required :error="$errors->first('phone')" wire:model.blur="phone" autocomplete="tel" :hint="__('site.forms.phone_hint')" />
                    <div class="sm:col-span-2">
                        <x-site.field name="nationality" :label="__('site.appointment.fields.nationality')" :error="$errors->first('nationality')" wire:model.blur="nationality" autocomplete="country-name" />
                    </div>
                </div>
            </fieldset>

            <fieldset class="mt-10">
                <legend class="flex items-center gap-2 text-xs font-semibold tracking-[0.18em] text-gold-700 uppercase"><i class="ph ph-calendar-check" aria-hidden="true"></i>{{ __('site.appointment.your_request') }}</legend>
                <div class="mt-4 grid gap-5 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <x-site.field as="select" name="service_id" :label="__('site.appointment.fields.service_id')" required :error="$errors->first('service_id')" wire:model.live="service_id">
                            <option value="">{{ __('site.appointment.choose_service') }}</option>
                            @foreach ($services as $service)
                                <option value="{{ $service->id }}">{{ $service->t('title') }}</option>
                            @endforeach
                            <option value="{{ \App\Livewire\AppointmentForm::OTHER }}">{{ __('site.appointment.other_service') }}</option>
                        </x-site.field>
                    </div>
                    @if ($service_id === \App\Livewire\AppointmentForm::OTHER)
                        <div class="sm:col-span-2">
                            <x-site.field name="service_label" :label="__('site.appointment.fields.service_label')" required :error="$errors->first('service_label')" wire:model.blur="service_label" />
                        </div>
                    @endif
                    <x-site.field name="preferred_date" type="date" :label="__('site.appointment.fields.preferred_date')" required :error="$errors->first('preferred_date')"
                        wire:model.blur="preferred_date" :min="$minDate" :hint="__('site.appointment.date_hint')" />
                    <x-site.field as="select" name="preferred_time" :label="__('site.appointment.fields.preferred_time')" required :error="$errors->first('preferred_time')"
                        wire:model.blur="preferred_time" :hint="__('site.appointment.time_hint')">
                        <option value="">{{ __('site.appointment.choose_time') }}</option>
                        {{-- Pas « $slot » comme variable : le nom est réservé au contenu du composant --}}
                        @foreach ($timeSlots as $time)
                            <option value="{{ $time }}">{{ $time }}</option>
                        @endforeach
                    </x-site.field>
                    <div class="sm:col-span-2">
                        <x-site.field as="textarea" name="notes" :label="__('site.appointment.fields.notes')" :error="$errors->first('notes')" wire:model.blur="notes" maxlength="1000" rows="4" :hint="__('site.appointment.notes_hint')" />
                    </div>
                </div>
            </fieldset>

            <div class="mt-8">
                <div class="flex items-start gap-3">
                    <input id="f-consent" type="checkbox" wire:model.live="consent"
                           class="mt-0.5 size-5 rounded border-brand-300 text-brand-500 focus:ring-brand-500"
                           aria-invalid="{{ $errors->has('consent') ? 'true' : 'false' }}" @error('consent') aria-describedby="f-consent-error" @enderror>
                    <label for="f-consent" class="text-sm leading-relaxed text-slate-700">{{ __('site.appointment.consent') }} <span class="text-flag-red" aria-hidden="true">*</span></label>
                </div>
                @error('consent')
                    <p id="f-consent-error" class="mt-1.5 ms-8 flex items-start gap-1.5 text-sm text-red-700"><i class="ph-fill ph-warning-circle mt-0.5" aria-hidden="true"></i>{{ $message }}</p>
                @enderror
            </div>

            <div class="mt-10 flex flex-col-reverse gap-4 border-t border-brand-900/10 pt-8 sm:flex-row sm:items-center sm:justify-between">
                <p class="flex items-center gap-2 text-xs text-slate-500"><i class="ph ph-lock-simple" aria-hidden="true"></i>{{ __('site.forms.privacy') }}</p>
                <button type="submit" class="site-btn site-btn-gold shrink-0 px-8 whitespace-nowrap" wire:loading.attr="disabled" wire:target="submit">
                    <i class="ph-bold ph-paper-plane-tilt" wire:loading.remove wire:target="submit" aria-hidden="true"></i>
                    <i class="ph-bold ph-circle-notch animate-spin" wire:loading wire:target="submit" aria-hidden="true"></i>
                    <span wire:loading.remove wire:target="submit">{{ __('site.appointment.submit') }}</span>
                    <span wire:loading wire:target="submit">{{ __('site.forms.sending') }}</span>
                </button>
            </div>
        </form>
    @endif
</div>
