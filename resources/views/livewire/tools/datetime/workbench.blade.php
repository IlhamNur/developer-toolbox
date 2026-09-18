<main class="tool-page" x-data="{ copied: false, copy() { const text = $refs.output?.value || $refs.output?.textContent || ''; navigator.clipboard?.writeText(text); this.copied = true; setTimeout(() => this.copied = false, 1500); } }">
    <a href="{{ route('dashboard') }}" class="back-link">← Back to dashboard</a>
    <div class="tool-page-header">
        <span class="section-kicker">Date &amp; time workspace</span>
        <h1>{{ $title }}</h1>
        <p>Convert between Unix timestamps and readable date values without leaving the browser.</p>
    </div>

    <div class="json-panel">
        <span class="panel-label">Conversion mode</span>
        <div class="tool-actions compact">
            <label><input type="radio" wire:model="mode" value="timestamp-to-date"> Timestamp → Date</label>
            <label><input type="radio" wire:model="mode" value="date-to-timestamp"> Date → Timestamp</label>
        </div>
    </div>

    <label class="json-panel">
        <span class="panel-label">Input</span>
        <textarea class="code-input" wire:model="input" spellcheck="false" placeholder="Enter a value to convert..."></textarea>
      
        <span class="panel-meta">Use a Unix timestamp or a date string such as 2024-01-01T00:00:00Z.</span>
    </label>

    <div class="json-panel">
        <span class="panel-label">Options</span>
        <div class="options-grid">
            <label>Timezone
                <select wire:model="timezone" class="mini-input">
                    <option value="UTC">UTC</option>
                    <option value="America/New_York">America/New_York</option>
                    <option value="Europe/London">Europe/London</option>
                    <option value="Europe/Berlin">Europe/Berlin</option>
                    <option value="Asia/Tokyo">Asia/Tokyo</option>
                </select>
            </label>
            @if ($mode === 'timestamp-to-date')
                <label>Format
                    <input type="text" wire:model="format" class="mini-input" placeholder="Y-m-d H:i:s">
                </label>
            @endif
        </div>
    </div>

    <div class="tool-actions">
        <button type="button" class="primary-action" wire:click="convert">Convert</button>
        <button type="button" class="secondary-action" wire:click="clear">Clear</button>
    </div>

    @if ($error)
        <div class="tool-error" role="alert">
            <strong>Unable to convert the value.</strong>
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
