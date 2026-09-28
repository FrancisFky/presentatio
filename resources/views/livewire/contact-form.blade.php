<div>
    @if ($sent)
        <div class="site-card p-7 sm:p-10" role="status" aria-live="polite">
            <span class="flex size-14 items-center justify-center rounded-full bg-green-50 text-3xl text-flag-green ring-1 ring-green-200" aria-hidden="true"><i class="ph-fill ph-check-circle"></i></span>
            <h3 class="mt-6 font-display text-3xl font-semibold text-brand-700">{{ __('site.contact.success_title') }}</h3>
            <p class="mt-3 text-slate-600">{{ __('site.contact.success_text') }}</p>
            <button type="button" wire:click="startOver" class="site-btn site-btn-outline mt-8">{{ __('site.contact.new_message') }}</button>
        </div>
    @else
        <form wire:submit="submit" class="site-card p-6 sm:p-8" novalidate>
            <p class="text-sm text-slate-600">{!! __('site.forms.required_note', ['star' => '<span class="text-flag-red" aria-hidden="true">*</span>']) !!}</p>

            @error('form')
                <p class="mt-5 flex items-start gap-3 rounded-lg bg-red-50 p-4 text-sm text-red-800 ring-1 ring-red-200" role="alert">
                    <i class="ph-fill ph-warning-octagon mt-0.5 text-lg" aria-hidden="true"></i>{{ $message }}
                </p>
            @enderror

            <div class="absolute -left-[9999px] h-px w-px overflow-hidden" aria-hidden="true">
                <label for="contact-website">Website</label>
                <input type="text" id="contact-website" wire:model="website" tabindex="-1" autocomplete="off">
            </div>

            <div class="mt-6 grid gap-5 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <x-site.field id="c-name" name="name" :label="__('site.contact.fields.name')" required :error="$errors->first('name')" wire:model.blur="name" autocomplete="name" />
                </div>
                <x-site.field id="c-email" name="email" type="email" :label="__('site.contact.fields.email')" required :error="$errors->first('email')" wire:model.blur="email" autocomplete="email" />
                <x-site.field id="c-phone" name="phone" type="tel" :label="__('site.contact.fields.phone')" :error="$errors->first('phone')" wire:model.blur="phone" autocomplete="tel" />
                <div class="sm:col-span-2">
                    <x-site.field id="c-subject" name="subject" :label="__('site.contact.fields.subject')" required :error="$errors->first('subject')" wire:model.blur="subject" maxlength="255" />
                </div>
                <div class="sm:col-span-2">
                    <x-site.field id="c-body" as="textarea" name="body" :label="__('site.contact.fields.body')" required :error="$errors->first('body')" wire:model.blur="body" rows="6" maxlength="5000" />
                </div>
            </div>

            <div class="mt-8 flex flex-col-reverse gap-4 sm:flex-row sm:items-center sm:justify-between">
                <p class="flex items-center gap-2 text-xs text-slate-500"><i class="ph ph-lock-simple" aria-hidden="true"></i>{{ __('site.forms.privacy') }}</p>
                <button type="submit" class="site-btn site-btn-navy shrink-0 px-8 whitespace-nowrap" wire:loading.attr="disabled" wire:target="submit">
                    <i class="ph-bold ph-paper-plane-tilt" wire:loading.remove wire:target="submit" aria-hidden="true"></i>
                    <i class="ph-bold ph-circle-notch animate-spin" wire:loading wire:target="submit" aria-hidden="true"></i>
                    <span wire:loading.remove wire:target="submit">{{ __('site.contact.submit') }}</span>
                    <span wire:loading wire:target="submit">{{ __('site.forms.sending') }}</span>
                </button>
            </div>
        </form>
    @endif
</div>
