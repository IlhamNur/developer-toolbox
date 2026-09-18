<main class="tool-page" x-data="{ copied: false, history: JSON.parse(localStorage.getItem('hmnr-api-history') || '[]'), copy() { const text = $refs.output?.value || $refs.output?.textContent || ''; navigator.clipboard?.writeText(text); this.copied = true; setTimeout(() => this.copied = false, 1500); }, saveRequest() { const request = { method: $refs.method.value, url: $refs.url.value, headers: $refs.headers.value, environment: $refs.environment.value, query: $refs.query?.value || '', body: $refs.body.value }; this.history = [request, ...this.history.filter((item) => JSON.stringify(item) !== JSON.stringify(request))].slice(0, 8); localStorage.setItem('hmnr-api-history', JSON.stringify(this.history)); }, loadRequest(request) { ['method', 'url', 'headers', 'environment', 'query', 'body'].forEach((field) => { if (!$refs[field]) return; $refs[field].value = request[field] || ''; $refs[field].dispatchEvent(new Event('input', { bubbles: true })); }); }, exportHistory() { const blob = new Blob([JSON.stringify(this.history, null, 2)], { type: 'application/json' }); const link = document.createElement('a'); link.href = URL.createObjectURL(blob); link.download = 'hmnr-api-collection.json'; link.click(); URL.revokeObjectURL(link.href); }, importHistory(event) { const file = event.target.files?.[0]; if (!file) return; const reader = new FileReader(); reader.onload = () => { try { const imported = JSON.parse(reader.result); if (!Array.isArray(imported)) throw new Error(); this.history = imported.filter((item) => item && item.method && item.url).slice(0, 8); localStorage.setItem('hmnr-api-history', JSON.stringify(this.history)); } catch (_) { this.history = this.history; } }; reader.readAsText(file); event.target.value = ''; }, clearHistory() { this.history = []; localStorage.removeItem('hmnr-api-history'); } }">
    <a href="{{ route('dashboard') }}" class="back-link">← Back to dashboard</a>
    <div class="tool-page-header">
        <span class="section-kicker">API workspace</span>
        <h1>{{ $title }}</h1>
        <p>{{ $slug === 'api-request-tester' ? 'Send a controlled HTTP request and inspect its response.' : 'Build a request payload or generate a cURL command from a few fields.' }}</p>
    </div>

    <div class="json-panel">
        <span class="panel-label">Request details</span>
        <div class="options-grid">
            <label>Method
                <select x-ref="method" wire:model="method" class="mini-input">
                    <option value="GET">GET</option>
                    <option value="POST">POST</option>
                    <option value="PUT">PUT</option>
                    <option value="PATCH">PATCH</option>
                    <option value="DELETE">DELETE</option>
                </select>
            </label>
            <label>URL
                <input x-ref="url" type="text" wire:model="url" class="mini-input" style="width: 220px;">
            </label>
        </div>
    </div>

    <label class="json-panel">
        <span class="panel-label">Headers</span>
        <textarea x-ref="headers" class="code-input" wire:model="headers" spellcheck="false" placeholder="Accept: application/json"></textarea>
    </label>

    <label class="json-panel">
        <span class="panel-label">Environment variables</span>
        <textarea x-ref="environment" class="code-input" wire:model="environment" spellcheck="false" placeholder="BASE_URL=https://api.example.com&#10;TOKEN=demo-token"></textarea>
        <span class="panel-meta">Use placeholders like @{{BASE_URL}} or @{{TOKEN}}</span>
    </label>

    @if ($slug === 'http-request')
        <label class="json-panel">
            <span class="panel-label">Query string</span>
            <textarea x-ref="query" class="code-input" wire:model="query" spellcheck="false" placeholder="page=2&limit=10"></textarea>
        </label>
    @endif

    <label class="json-panel">
        <span class="panel-label">Body</span>
        <textarea x-ref="body" class="code-input" wire:model="body" spellcheck="false" placeholder='{"name":"Ada"}'></textarea>
    </label>

    <div class="tool-actions">
        <button type="button" class="primary-action" wire:click="generate">{{ $slug === 'api-request-tester' ? 'Send request' : 'Generate' }}</button>
        <button type="button" class="secondary-action" @click="saveRequest()">Save request</button>
        <button type="button" class="secondary-action" wire:click="clear">Clear</button>
    </div>

    <section class="json-panel" x-show="history.length > 0" x-cloak>
        <div class="output-heading">
            <span class="panel-label">Request history</span>
            <span class="tool-actions compact">
                <button type="button" class="copy-action" @click="exportHistory()">Export</button>
                <label class="copy-action">Import<input type="file" accept="application/json" hidden @change="importHistory($event)"></label>
                <button type="button" class="copy-action" @click="clearHistory()">Clear history</button>
            </span>
        </div>
        <div class="recent-row">
            <template x-for="(request, index) in history" :key="index">
                <button type="button" class="recent-item" @click="loadRequest(request)">
                    <span class="recent-dot"></span>
                    <span x-text="request.method + ' ' + request.url"></span>
                    <span class="recent-arrow">↗</span>
                </button>
            </template>
        </div>
    </section>

    @if ($error)
        <div class="tool-error" role="alert">
            <strong>Unable to generate the request.</strong>
            <span>{{ $error }}</span>
        </div>
    @endif

    @if ($output !== '')
        <section class="output-panel">
            <div class="output-heading">
                <span class="section-kicker">{{ $slug === 'api-request-tester' ? 'Response' : 'Output' }}</span>
                <button type="button" class="copy-action" @click="copy()" x-text="copied ? '✓ Copied' : 'Copy'"></button>
            </div>
            <textarea x-ref="output" readonly>{{ $output }}</textarea>
            @if ($slug === 'api-request-tester')
                <div class="output-meta"><span>Status: {{ $responseStatus }}</span><span>Response time: {{ $responseTime }} ms</span></div>
            @endif
        </section>
    @endif
</main>
