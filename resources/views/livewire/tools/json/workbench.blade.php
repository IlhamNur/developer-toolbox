<main class="tool-page" x-data="{ copied: false, copy() { navigator.clipboard?.writeText($refs.output.value || $refs.output.textContent || ''); this.copied = true; setTimeout(() => this.copied = false, 1500); } }">
    <a href="{{ route('dashboard') }}" class="back-link">← Back to dashboard</a>
    <div class="tool-page-header"><span class="section-kicker">JSON workspace</span><h1>{{ $title }}</h1><p>Inspect and transform JSON with clear, predictable output.</p></div>
    @if ($slug === 'json-diff')
        <div class="json-columns"><x-json-panel label="Original JSON" wire:model="input" /><x-json-panel label="Modified JSON" wire:model="modifiedInput" /></div>
    @else
        <x-json-panel label="Input" wire:model="input" />
    @endif
    <div class="tool-actions"><button type="button" class="primary-action" wire:click="process">{{ $slug === 'json-diff' ? 'Compare JSON' : 'Process' }} <span>⌘ ↵</span></button><button type="button" class="secondary-action" wire:click="clear">Clear</button>@if ($slug === 'json-formatter')<label class="indent-label">Indent <select wire:model="indentation"><option value="2">2 spaces</option><option value="4">4 spaces</option><option value="tabs">Tabs</option></select></label>@elseif ($slug === 'json-to-object')<label class="indent-label">Target <select wire:model="indentation"><option value="java">Java Map</option><option value="mvel">MVEL Map</option></select></label>@endif</div>
    @if ($error)<div class="tool-error" role="alert"><strong>Unable to process input.</strong><span>{{ $error }}</span></div>@endif
    @if ($output !== '')<section class="output-panel"><div class="output-heading"><span class="section-kicker">Output</span><button type="button" class="copy-action" @click="copy()" x-text="copied ? '✓ Copied' : 'Copy'"></button></div><textarea x-ref="output" readonly>{{ $output }}</textarea><div class="output-meta"><span>{{ strlen($output) }} characters</span><span>{{ substr_count($output, "\n") + 1 }} lines</span></div></section>@endif
</main>