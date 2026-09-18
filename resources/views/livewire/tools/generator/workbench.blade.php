<main class="tool-page" x-data="{ copied: false, copy() { const text = $refs.output?.value || $refs.output?.textContent || ''; navigator.clipboard?.writeText(text); this.copied = true; setTimeout(() => this.copied = false, 1500); } }">
    <a href="{{ route('dashboard') }}" class="back-link">← Back to dashboard</a>
    <div class="tool-page-header">
        <span class="section-kicker">Generator workspace</span>
        <h1>{{ $title }}</h1>
        <p>Create safe, reusable values for testing, identifiers, and passwords.</p>
    </div>

    @if ($slug === 'uuid-generator')
        <div class="json-panel">
            <span class="panel-label">Generate count</span>
            <div class="tool-actions compact">
                <label class="indent-label">
                    <input type="number" wire:model="count" min="1" max="50" value="1" class="mini-input">
                </label>
                <button type="button" class="primary-action" wire:click="generate">Generate</button>
            </div>
        </div>
    @endif

    @if ($slug === 'random-string-generator')
        <div class="json-panel">
            <span class="panel-label">Options</span>
            <div class="options-grid">
                <label>Length <input type="number" wire:model="length" min="1" max="128" class="mini-input"></label>
                <label><input type="checkbox" wire:model="uppercase"> Uppercase</label>
                <label><input type="checkbox" wire:model="lowercase"> Lowercase</label>
                <label><input type="checkbox" wire:model="numbers"> Numbers</label>
                <label><input type="checkbox" wire:model="symbols"> Symbols</label>
            </div>
            <div class="tool-actions compact"><button type="button" class="primary-action" wire:click="generate">Generate</button></div>
        </div>
    @endif

    @if ($slug === 'password-generator')
        <div class="json-panel">
            <span class="panel-label">Password options</span>
            <div class="options-grid">
                <label>Length <input type="number" wire:model="length" min="8" max="128" class="mini-input"></label>
                <label><input type="checkbox" wire:model="uppercase"> Uppercase</label>
                <label><input type="checkbox" wire:model="lowercase"> Lowercase</label>
                <label><input type="checkbox" wire:model="numbers"> Numbers</label>
                <label><input type="checkbox" wire:model="symbols"> Symbols</label>
                <label><input type="checkbox" wire:model="excludeAmbiguous"> Exclude ambiguous characters</label>
            </div>
            <div class="tool-actions compact"><button type="button" class="primary-action" wire:click="generate">Generate</button></div>
        </div>
    @endif

    @if ($error)
        <div class="tool-error" role="alert">
            <strong>Unable to generate.</strong>
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
