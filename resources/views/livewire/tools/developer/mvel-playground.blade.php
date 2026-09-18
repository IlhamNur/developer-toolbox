<main class="tool-page" x-data="{ copied: false, presets: JSON.parse(localStorage.getItem('hmnr-mvel-presets') || '[]'), copy() { const text = $refs.result?.value || ''; navigator.clipboard?.writeText(text); this.copied = true; setTimeout(() => this.copied = false, 1500); }, run() { $wire.run(); }, savePreset() { const preset = { label: this.$wire.expression.split('\n')[0].slice(0, 40) || 'MVEL expression', expression: this.$wire.expression, variables: this.$wire.variables }; this.presets = [preset, ...this.presets.filter((item) => item.expression !== preset.expression)].slice(0, 8); localStorage.setItem('hmnr-mvel-presets', JSON.stringify(this.presets)); }, loadPreset(preset) { this.$wire.set('expression', preset.expression); this.$wire.set('variables', preset.variables); }, clearPresets() { this.presets = []; localStorage.removeItem('hmnr-mvel-presets'); } }" @keydown.meta.enter.window="run()" @keydown.ctrl.enter.window="run()">
    <a href="{{ route('dashboard') }}" class="back-link">← Back to dashboard</a>
    <div class="tool-page-header">
        <span class="section-kicker">Developer workspace</span>
        <h1>{{ $title }}</h1>
        <p>Test and inspect MVEL expressions with controlled variables.</p>
        <span class="panel-meta">MVEL Runtime: {{ $runtimeVersion }}</span>
    </div>

    <div class="json-columns">
        <section class="json-panel">
            <div class="output-heading">
                <span class="panel-label">Variables</span>
                <button type="button" class="copy-action" wire:click="addVariable">+ Add variable</button>
            </div>
            <div class="mvel-variable-list">
                @foreach ($variables as $index => $variable)
                    <div class="mvel-variable-row" wire:key="mvel-variable-{{ $index }}">
                        <input class="mini-input" wire:model="variables.{{ $index }}.name" placeholder="Name">
                        <select class="mini-input" wire:model="variables.{{ $index }}.type">
                            @foreach (['String', 'Integer', 'Long', 'Double', 'Boolean', 'Null', 'Map', 'List', 'JSON'] as $type)
                                <option value="{{ $type }}">{{ $type }}</option>
                            @endforeach
                        </select>
                        <input class="mini-input" wire:model="variables.{{ $index }}.value" placeholder="Value" @if ($variable['type'] === 'Null') disabled @endif>
                        <button type="button" class="copy-action" wire:click="removeVariable({{ $index }})" aria-label="Delete variable">×</button>
                    </div>
                @endforeach
            </div>
        </section>

        <section class="json-panel">
            <div class="output-heading">
                <span class="panel-label">Expression</span>
                <span class="panel-meta">{{ strlen($expression) }} characters</span>
            </div>
            <textarea class="code-input mvel-expression" wire:model="expression" spellcheck="false" placeholder='"Hello " + firstName'></textarea>
            <span class="panel-meta">Run with Ctrl + Enter or Cmd + Enter</span>
        </section>
    </div>

    <section class="json-panel">
        <div class="output-heading"><span class="panel-label">Examples</span></div>
        <div class="tool-actions compact">
            @foreach (['hello' => 'Hello World', 'conditional' => 'Conditional', 'map' => 'Map Access', 'nested' => 'Nested Response', 'boolean' => 'Boolean', 'null' => 'Null Check'] as $key => $label)
                <button type="button" class="secondary-action" wire:click="useExample('{{ $key }}')">{{ $label }}</button>
            @endforeach
        </div>
    </section>

    <section class="json-panel" x-show="presets.length > 0" x-cloak>
        <div class="output-heading">
            <span class="panel-label">Saved presets</span>
            <span class="tool-actions compact">
                <button type="button" class="copy-action" @click="savePreset()">Save current</button>
                <button type="button" class="copy-action" @click="clearPresets()">Clear</button>
            </span>
        </div>
        <div class="recent-row">
            <template x-for="(preset, index) in presets" :key="index">
                <button type="button" class="recent-item" @click="loadPreset(preset)">
                    <span class="recent-dot"></span>
                    <span x-text="preset.label"></span>
                    <span class="recent-arrow">↗</span>
                </button>
            </template>
        </div>
        <span class="panel-meta">Presets are stored in this browser and may contain sensitive values.</span>
    </section>

    <div class="tool-actions">
        <button type="button" class="primary-action" wire:click="run">▶ Run</button>
        <button type="button" class="secondary-action" @click="savePreset()">Save preset</button>
        <button type="button" class="secondary-action" wire:click="clear">Clear</button>
    </div>

    @if ($error)
        <div class="tool-error" role="alert">
            <strong>MVEL Execution Error</strong>
            <span>{{ $error }}</span>
        </div>
    @endif

    @if ($result !== '')
        <section class="output-panel">
            <div class="output-heading">
                <span class="section-kicker">Result</span>
                <button type="button" class="copy-action" @click="copy()" x-text="copied ? '✓ Copied' : 'Copy'"></button>
            </div>
            <textarea x-ref="result" readonly>{{ $result }}</textarea>
            <div class="output-meta">
                <span>Type: {{ $resultType }}</span>
                <span>Execution time: {{ $executionTimeMs }} ms</span>
            </div>
        </section>
    @endif
</main>
