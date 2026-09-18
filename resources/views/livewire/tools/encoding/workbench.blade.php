<main class="tool-page" x-data="{ copied: false, copy() { const text = $refs.output?.value || $refs.output?.textContent || ''; navigator.clipboard?.writeText(text); this.copied = true; setTimeout(() => this.copied = false, 1500); } }">
    <a href="{{ route('dashboard') }}" class="back-link">← Back to dashboard</a>
    <div class="tool-page-header">
        <span class="section-kicker">Encoding workspace</span>
        <h1>{{ $title }}</h1>
        <p>Transform text safely using standard encoding rules without storing sensitive data.</p>
    </div>

    <label class="json-panel">
        <span class="panel-label">Input</span>
        <textarea class="code-input" wire:model="input" spellcheck="false" placeholder="Paste text here..."></textarea>
        <span class="panel-meta">Text input</span>
    </label>

    <div class="tool-actions">
        <button type="button" class="primary-action" wire:click="encode">{{ $slug === 'newline-converter' ? 'Escape' : 'Encode' }}</button>
        <button type="button" class="secondary-action" wire:click="decode">Decode</button>
        <button type="button" class="secondary-action" wire:click="clear">Clear</button>
    </div>

    @if ($error)
        <div class="tool-error" role="alert">
            <strong>Unable to process the input.</strong>
            <span>{{ $error }}</span>
        </div>
    @endif

    @if ($output !== '')
        <section class="output-panel">
            <div class="output-heading">
                <span class="section-kicker">Output</span>
                <button type="button" class="copy-action" @click="copy()" x-text="copied ? '✓ Copied' : 'Copy'"></button>
            </div>
            <textarea x-ref="output" readonly>{{ $output }}</textarea>
        </section>
    @endif
</main>
