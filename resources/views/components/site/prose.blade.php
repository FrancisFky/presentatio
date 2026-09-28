{{-- Texte mis en forme saisi dans l'admin (déjà nettoyé à l'enregistrement) --}}
<div {{ $attributes->class([
    'prose prose-slate max-w-none sm:prose-lg',
    'prose-headings:font-display prose-headings:font-semibold prose-headings:text-brand-700',
    'prose-a:text-brand-500 prose-a:underline-offset-4 hover:prose-a:text-gold-700',
    'prose-strong:text-brand-700 prose-blockquote:border-gold-400 prose-blockquote:font-display prose-blockquote:text-xl prose-blockquote:text-brand-600',
    'prose-li:marker:text-gold-500 prose-img:rounded-xl',
]) }}>
    {{ $slot }}
</div>
