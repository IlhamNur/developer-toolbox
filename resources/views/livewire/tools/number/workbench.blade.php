<main class="tool-page" x-data="{ copied: false, copy() { const text = $refs.output?.value || $refs.output?.textContent || ''; navigator.clipboard?.writeText(text); this.copied = true; setTimeout(() => this.copied = false, 1500); } }">
    <a href="{{ route('dashboard') }}" class="back-link">← Back to dashboard</a>
    <div class="tool-page-header">
        <span class="section-kicker">Number workspace</span>
        <h1>{{ $title }}</h1>
        <p>Format numbers for display or convert between numeric bases quickly.</p>
    </div>

    <label class="json-panel">
        <span class="panel-label">Input</span>
        <textarea class="code-input" wire:model="input" spellcheck="false" placeholder="Enter a number or value..."></textarea>
    </label>

    @if ($slug === 'number-format')
        <div class="json-panel">
            <span class="panel-label">Formatting</span>
            <div class="options-grid">
                <label>Decimals <input type="number" wire:model="decimals" min="0" max="10" class="mini-input"></label>
                <label>Thousands <input type="text" wire:model="thousandsSeparator" maxlength="1" class="mini-input"></label>
                <label>Decimal <input type="text" wire:model="decimalSeparator" maxlength="1" class="mini-input"></label>
            </div>
        </div>
    @else
        <div class="json-panel">
            <span class="panel-label">Base conversion</span>
            <div class="options-grid">
                <label>From <input type="number" wire:model="fromBase" min="2" max="36" class="mini-input"></label>
                <label>To <input type="number" wire:model="toBase" min="2" max="36" class="mini-input"></label>
            </div>
        </div>
    @endif

    <div class="tool-actions">
        <button type="button" class="primary-action" wire:click="convert">Convert</button>
        <button type="button" class="secondary-action" wire:click="clear">Clear</button>
    </div>

    @if ($error)
        <div class="tool-error" role="alert">
            <strong>Unable to process the number.</strong>
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
