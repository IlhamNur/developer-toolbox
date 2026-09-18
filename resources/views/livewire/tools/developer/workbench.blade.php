<main class="tool-page" x-data="{ copied: false, copy() { const text = $refs.output?.value || $refs.output?.textContent || ''; navigator.clipboard?.writeText(text); this.copied = true; setTimeout(() => this.copied = false, 1500); } }">
    <a href="{{ route('dashboard') }}" class="back-link">← Back to dashboard</a>
    <div class="tool-page-header">
        <span class="section-kicker">Developer workspace</span>
        <h1>{{ $title }}</h1>
        <p>Quickly inspect patterns, decode local tokens, and generate realistic fixture data.</p>
    </div>

    @if ($slug === 'regex-tester')
        <label class="json-panel">
            <span class="panel-label">Pattern</span>
            <textarea class="code-input" wire:model="pattern" spellcheck="false"></textarea>
        </label>

        <label class="json-panel">
            <span class="panel-label">Subject</span>
            <textarea class="code-input" wire:model="subject" spellcheck="false"></textarea>
        </label>
    @endif

    @if ($slug === 'jwt-decoder')
        <label class="json-panel">
            <span class="panel-label">JWT token</span>
            <textarea class="code-input" wire:model="token" spellcheck="false"></textarea>
        </label>
    @endif

    @if ($slug === 'dummy-json')
        <div class="json-panel">
            <span class="panel-label">Row count</span>
            <div class="tool-actions compact">
                <label class="indent-label">
                    <input type="number" wire:model="count" min="1" max="50" class="mini-input">
                </label>
            </div>
        </div>
    @endif

    <div class="tool-actions">
        <button type="button" class="primary-action" wire:click="run">Run</button>
        <button type="button" class="secondary-action" wire:click="clear">Clear</button>
    </div>

    @if ($error)
        <div class="tool-error" role="alert">
            <strong>Unable to process.</strong>
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
