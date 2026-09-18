<main class="tool-page" x-data="{ copied: false, copy() { const text = $refs.output?.value || $refs.output?.textContent || ''; navigator.clipboard?.writeText(text); this.copied = true; setTimeout(() => this.copied = false, 1500); } }">
    <a href="{{ route('dashboard') }}" class="back-link">← Back to dashboard</a>
    <div class="tool-page-header">
        <span class="section-kicker">API workspace</span>
        <h1>{{ $title }}</h1>
        <p>Build a request payload or generate a cURL command from a few fields.</p>
    </div>

    <div class="json-panel">
        <span class="panel-label">Request details</span>
        <div class="options-grid">
            <label>Method
                <select wire:model="method" class="mini-input">
                    <option value="GET">GET</option>
                    <option value="POST">POST</option>
                    <option value="PUT">PUT</option>
                    <option value="PATCH">PATCH</option>
                    <option value="DELETE">DELETE</option>
                </select>
            </label>
            <label>URL
                <input type="text" wire:model="url" class="mini-input" style="width: 220px;">
            </label>
        </div>
    </div>

    <label class="json-panel">
        <span class="panel-label">Headers</span>
        <textarea class="code-input" wire:model="headers" spellcheck="false" placeholder="Accept: application/json"></textarea>
    </label>

    @if ($slug === 'http-request')
        <label class="json-panel">
            <span class="panel-label">Query string</span>
            <textarea class="code-input" wire:model="query" spellcheck="false" placeholder="page=2&limit=10"></textarea>
        </label>
    @endif

    <label class="json-panel">
        <span class="panel-label">Body</span>
        <textarea class="code-input" wire:model="body" spellcheck="false" placeholder='{"name":"Ada"}'></textarea>
    </label>

    <div class="tool-actions">
        <button type="button" class="primary-action" wire:click="generate">Generate</button>
        <button type="button" class="secondary-action" wire:click="clear">Clear</button>
    </div>

    @if ($error)
        <div class="tool-error" role="alert">
            <strong>Unable to generate the request.</strong>
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
