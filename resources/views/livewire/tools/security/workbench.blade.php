<main class="tool-page" x-data="{ copied: false, copy() { const text = $refs.output?.value || $refs.output?.textContent || ''; navigator.clipboard?.writeText(text); this.copied = true; setTimeout(() => this.copied = false, 1500); } }">
    <a href="{{ route('dashboard') }}" class="back-link">← Back to dashboard</a>
    <div class="tool-page-header">
        <span class="section-kicker">Security workspace</span>
        <h1>{{ $title }}</h1>
        <p>Generate and compare hashes for quick confirmation and validation checks.</p>
    </div>

    <label class="json-panel">
        <span class="panel-label">Input</span>
        <textarea class="code-input" wire:model="input" spellcheck="false" placeholder="Paste plain text or a value to validate..."></textarea>
    </label>

    @if ($slug === 'hash-generator')
        <div class="json-panel">
            <span class="panel-label">Algorithm</span>
            <div class="tool-actions compact">
                <label><input type="radio" wire:model="algorithm" value="sha256"> SHA-256</label>
                <label><input type="radio" wire:model="algorithm" value="sha1"> SHA-1</label>
                <label><input type="radio" wire:model="algorithm" value="md5"> MD5</label>
            </div>
        </div>
    @else
        <label class="json-panel">
            <span class="panel-label">Hash to compare</span>
            <textarea class="code-input" wire:model="hash" spellcheck="false" placeholder="Paste the known hash value..."></textarea>
        </label>
    @endif

    <div class="tool-actions">
        <button type="button" class="primary-action" wire:click="process">Process</button>
        <button type="button" class="secondary-action" wire:click="clear">Clear</button>
    </div>

    @if ($error)
        <div class="tool-error" role="alert">
            <strong>Unable to process the request.</strong>
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
