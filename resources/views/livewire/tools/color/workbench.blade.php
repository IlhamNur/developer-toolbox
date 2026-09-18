<main class="tool-page" x-data="{ copied: false, copy() { const text = $refs.output?.value || $refs.output?.textContent || ''; navigator.clipboard?.writeText(text); this.copied = true; setTimeout(() => this.copied = false, 1500); } }">
    <a href="{{ route('dashboard') }}" class="back-link">← Back to dashboard</a>
    <div class="tool-page-header">
        <span class="section-kicker">Color workspace</span>
        <h1>{{ $title }}</h1>
        <p>Convert between hex and RGB values for styling, UI work, and design handoff.</p>
    </div>

    @if ($slug === 'color-hex-rgb')
        <label class="json-panel">
            <span class="panel-label">HEX color</span>
            <textarea class="code-input" wire:model="input" spellcheck="false" placeholder="#FF0000"></textarea>
        </label>
    @else
        <div class="json-panel">
            <span class="panel-label">RGB values</span>
            <div class="options-grid">
                <label>Red <input type="number" wire:model="red" min="0" max="255" class="mini-input"></label>
                <label>Green <input type="number" wire:model="green" min="0" max="255" class="mini-input"></label>
                <label>Blue <input type="number" wire:model="blue" min="0" max="255" class="mini-input"></label>
            </div>
        </div>
    @endif

    <div class="tool-actions">
        <button type="button" class="primary-action" wire:click="convert">Convert</button>
        <button type="button" class="secondary-action" wire:click="clear">Clear</button>
    </div>

    @if ($error)
        <div class="tool-error" role="alert">
            <strong>Unable to process the color.</strong>
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
