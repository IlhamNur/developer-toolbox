<main class="tool-page" x-data="{ copied: false, copy() { const text = $refs.output?.value || $refs.output?.textContent || ''; navigator.clipboard?.writeText(text); this.copied = true; setTimeout(() => this.copied = false, 1500); } }">
    <a href="{{ route('dashboard') }}" class="back-link">← Back to dashboard</a>
    <div class="tool-page-header">
        <span class="section-kicker">Solr workspace</span>
        <h1>{{ $title }}</h1>
        <p>Build encoded Solr query parameters with filters, fields, sorting, and pagination.</p>
    </div>

    <label class="json-panel">
        <span class="panel-label">Query</span>
        <input class="mini-input" style="width: 100%;" wire:model="query" placeholder="*:*">
    </label>

    <label class="json-panel">
        <span class="panel-label">Filter queries</span>
        <textarea class="code-input" wire:model="filters" spellcheck="false" placeholder="status:active&#10;category:network"></textarea>
        <span class="panel-meta">One field:value filter per line</span>
    </label>

    <div class="json-panel">
        <span class="panel-label">Result options</span>
        <div class="options-grid">
            <label>Fields <input type="text" wire:model="fields" class="mini-input"></label>
            <label>Sort <input type="text" wire:model="sort" class="mini-input" placeholder="score desc"></label>
            <label>Start <input type="number" wire:model="start" min="0" class="mini-input"></label>
            <label>Rows <input type="number" wire:model="rows" min="1" class="mini-input"></label>
        </div>
    </div>

    <div class="tool-actions">
        <button type="button" class="primary-action" wire:click="build">Build Query</button>
        <button type="button" class="secondary-action" wire:click="clear">Clear</button>
    </div>

    @if ($error)
        <div class="tool-error" role="alert">
            <strong>Unable to build the query.</strong>
            <span>{{ $error }}</span>
        </div>
    @endif

    @if ($output !== '')
        <section class="output-panel">
            <div class="output-heading">
                <span class="section-kicker">Query parameters</span>
                <button type="button" class="copy-action" @click="copy()" x-text="copied ? '✓ Copied' : 'Copy'"></button>
            </div>
            <textarea x-ref="output" readonly>{{ $output }}</textarea>
        </section>
    @endif
</main>
