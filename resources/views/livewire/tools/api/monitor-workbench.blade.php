<main class="tool-page" x-data="{ copied: false, copy() { const text = $refs.output?.value || ''; navigator.clipboard?.writeText(text); this.copied = true; setTimeout(() => this.copied = false, 1500); } }">
    <a href="{{ route('dashboard') }}" class="back-link">← Back to dashboard</a>
    <div class="tool-page-header">
        <span class="section-kicker">API workspace</span>
        <h1>{{ $title }}</h1>
        <p>Check external endpoints manually and compare availability, status, and response time.</p>
    </div>

    <label class="json-panel">
        <span class="panel-label">Endpoints</span>
        <textarea class="code-input" wire:model="endpoints" spellcheck="false" placeholder="Service name|https://example.com"></textarea>
        <span class="panel-meta">One endpoint per line using Name|URL. Private and local hosts are blocked.</span>
    </label>

    <div class="tool-actions">
        <button type="button" class="primary-action" wire:click="check">Check endpoints</button>
        <button type="button" class="secondary-action" wire:click="clear">Clear</button>
    </div>

    @if ($results !== [])
        <section class="json-panel">
            <div class="output-heading"><span class="panel-label">Current status</span><span class="panel-meta">{{ count($results) }} endpoints</span></div>
            <div class="recent-row">
                @foreach ($results as $result)
                    <div class="recent-item">
                        <span class="recent-dot {{ $result['status'] === 'UP' ? 'is-up' : 'is-down' }}"></span>
                        <span><strong>{{ $result['name'] }}</strong><small>{{ $result['url'] }}{{ $result['error'] ? ' · ' . $result['error'] : '' }}</small></span>
                        <span>{{ $result['status'] }} · {{ $result['code'] ?? '-' }} · {{ $result['time_ms'] }} ms</span>
                    </div>
                @endforeach
            </div>
        </section>
    @endif
</main>
