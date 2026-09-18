<main class="tool-page" x-data="{ copied: false, copy() { const text = $refs.output?.value || $refs.output?.textContent || ''; navigator.clipboard?.writeText(text); this.copied = true; setTimeout(() => this.copied = false, 1500); } }">
    <a href="{{ route('dashboard') }}" class="back-link">← Back to dashboard</a>
    <div class="tool-page-header">
        <span class="section-kicker">Developer workspace</span>
        <h1>{{ $title }}</h1>
        <p>Keep reusable MVEL patterns close at hand for strings, JSON, dates, lists, and API responses.</p>
    </div>

    <div class="json-panel">
        <span class="panel-label">Snippet category</span>
        <div class="options-grid">
            <select wire:model="category" class="mini-input">
                @foreach ($categories as $snippetCategory)
                    <option value="{{ $snippetCategory }}">{{ str($snippetCategory)->title() }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="tool-actions">
        <button type="button" class="primary-action" wire:click="loadSnippet">Load Snippet</button>
        <button type="button" class="secondary-action" wire:click="clear">Clear</button>
    </div>

    @if ($error)
        <div class="tool-error" role="alert">
            <strong>Unable to load the snippet.</strong>
            <span>{{ $error }}</span>
        </div>
    @endif

    @if ($output !== '')
        <section class="output-panel">
            <div class="output-heading">
                <span class="section-kicker">MVEL expression examples</span>
                <button type="button" class="copy-action" @click="copy()" x-text="copied ? '✓ Copied' : 'Copy'"></button>
            </div>
            <textarea x-ref="output" readonly>{{ $output }}</textarea>
        </section>
    @endif
</main>
