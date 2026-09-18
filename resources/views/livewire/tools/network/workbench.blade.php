<main class="tool-page" x-data="{ copied: false, copy() { const text = $refs.output?.value || $refs.output?.textContent || ''; navigator.clipboard?.writeText(text); this.copied = true; setTimeout(() => this.copied = false, 1500); } }">
    <a href="{{ route('dashboard') }}" class="back-link">← Back to dashboard</a>
    <div class="tool-page-header">
        <span class="section-kicker">Network workspace</span>
        <h1>{{ $title }}</h1>
        <p>Validate addresses and quickly classify whether an IP is public, private, or invalid.</p>
    </div>

    <label class="json-panel">
        <span class="panel-label">IP address</span>
        <textarea class="code-input" wire:model="input" spellcheck="false" placeholder="8.8.8.8"></textarea>
    </label>

    <div class="tool-actions">
        <button type="button" class="primary-action" wire:click="process">Check</button>
        <button type="button" class="secondary-action" wire:click="clear">Clear</button>
    </div>

    @if ($error)
        <div class="tool-error" role="alert">
            <strong>Unable to process the address.</strong>
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
