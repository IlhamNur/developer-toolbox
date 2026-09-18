<div class="min-h-screen" x-data="dashboardTools()" @keydown.window="handleKeydown($event)">
    <div class="mobile-bar lg:hidden">
        <button class="icon-button" type="button" aria-label="Open navigation" @click="sidebarOpen = true"><span aria-hidden="true">=</span></button>
        <a href="{{ route('dashboard') }}" class="brand compact"><span class="brand-mark">H</span><span>HMNR <b>Toolbox</b></span></a>
        <button class="icon-button" type="button" aria-label="Open command palette" @click="paletteOpen = true"><span aria-hidden="true">⌕</span></button>
    </div>

    <div class="app-frame">
        <div class="sidebar-backdrop lg:hidden" x-show="sidebarOpen" x-transition.opacity @click="sidebarOpen = false"></div>
        <aside class="sidebar" :class="sidebarOpen ? 'is-open' : ''">
            <div class="sidebar-top">
                <a href="{{ route('dashboard') }}" class="brand"><span class="brand-mark">H</span><span>HMNR <b>Developer Toolbox</b></span></a>
                <button class="icon-button lg:hidden" type="button" aria-label="Close navigation" @click="sidebarOpen = false">×</button>
            </div>
            <nav class="sidebar-nav" aria-label="Main navigation">
                <a href="{{ route('dashboard') }}" class="nav-link active"><span class="nav-symbol">⌂</span> Dashboard</a>
                @foreach ($categories as $navCategory)
                    <div class="nav-group">
                        <button type="button" class="nav-heading" @click="setCategory('{{ $navCategory }}')"><span>{{ $navCategory }}</span><span>+</span></button>
                        @foreach (collect($tools)->where('category', $navCategory)->take(3) as $tool)
                            <a href="#" class="nav-sublink" @click.prevent="openTool('{{ $tool['slug'] }}')">{{ $tool['name'] }}</a>
                        @endforeach
                    </div>
                @endforeach
            </nav>
            <div class="sidebar-bottom">
                <button type="button" class="nav-link" @click="cycleTheme()"><span class="nav-symbol">◐</span> Theme <span class="theme-label" x-text="theme"></span></button>
                <div class="sidebar-meta"><span class="status-dot"></span> Personal workspace <span class="version">v0.1</span></div>
            </div>
        </aside>

        <main class="main-content">
            <header class="topbar">
                <div class="breadcrumb"><span>Workspace</span><span>/</span><strong>Dashboard</strong></div>
                <button class="search-trigger" type="button" @click="paletteOpen = true"><span class="search-icon">⌕</span><span>Search tools</span><kbd>⌘ K</kbd></button>
            </header>

            <div class="content-wrap">
                <section class="hero-grid">
                    <div>
                        <div class="eyebrow"><span class="eyebrow-line"></span> Developer workspace <span class="eyebrow-line"></span></div>
                        <h1>Small tools.<br><em>Big leverage.</em></h1>
                        <p class="hero-copy">A focused collection of utilities for the moments between writing, testing, and shipping.</p>
                    </div>
                    <div class="hero-aside"><span class="hero-aside-label">Today’s focus</span><strong>Keep the loop tight.</strong><span>Format, inspect, convert, repeat.</span></div>
                </section>

                <section class="stats-strip" aria-label="Workspace summary">
                    <div class="stat-card">
                        <span class="stat-label">Tools available</span>
                        <strong>{{ count($tools) }}</strong>
                        <small>Across the current workspace</small>
                    </div>
                    <div class="stat-card">
                        <span class="stat-label">Ready now</span>
                        <strong>{{ collect($tools)->where('status', 'ready')->count() }}</strong>
                        <small>Production-ready utilities</small>
                    </div>
                    <div class="stat-card">
                        <span class="stat-label">Popular picks</span>
                        <strong>{{ count($popularTools) }}</strong>
                        <small>Most-used in the toolkit</small>
                    </div>
                </section>

                <section class="section-block popular-block">
                    <div class="section-heading"><div><span class="section-kicker">Popular right now</span><h2>Fast access</h2></div></div>
                    <div class="popular-row">
                        @foreach ($popularTools as $tool)
                            <button type="button" class="popular-item" @click="openTool('{{ $tool['slug'] }}')">
                                <span class="tool-icon small">{{ $tool['icon'] }}</span>
                                <span>
                                    <strong>{{ $tool['name'] }}</strong>
                                    <small>{{ $tool['description'] }}</small>
                                </span>
                                <span class="popular-arrow">↗</span>
                            </button>
                        @endforeach
                    </div>
                </section>

                <section class="workspace-toolbar" aria-label="Tool filters">
                    <label class="filter-search"><span class="search-icon">⌕</span><input type="search" wire:model.live.debounce.200ms="search" placeholder="Find a tool by name, category, or tag..." aria-label="Search tools"></label>
                    <div class="category-tabs" role="tablist" aria-label="Filter by category">
                        <button type="button" :class="category === 'All tools' ? 'selected' : ''" @click="setCategory('All tools')">All tools</button>
                        @foreach ($categories as $toolCategory)
                            <button type="button" :class="category === '{{ $toolCategory }}' ? 'selected' : ''" @click="setCategory('{{ $toolCategory }}')">{{ $toolCategory }}</button>
                        @endforeach
                    </div>
                </section>

                <section class="section-block" x-show="recent.length > 0" x-cloak>
                    <div class="section-heading"><div><span class="section-kicker">Your workspace</span><h2>Recently used</h2></div><span class="section-count" x-text="recent.length + ' tools'"></span></div>
                    <div class="recent-row"><template x-for="slug in recent" :key="slug"><button type="button" class="recent-item" @click="openTool(slug)"><span class="recent-dot"></span><span x-text="toolName(slug)"></span><span class="recent-arrow">↗</span></button></template></div>
                </section>

                <section class="section-block">
                    <div class="section-heading"><div><span class="section-kicker">Browse the collection</span><h2 x-text="category === 'All tools' ? 'All tools' : category"></h2></div><span class="section-count">{{ count($tools) }} available</span></div>
                    <div class="tool-grid">
                        @forelse ($tools as $tool)
                            <article class="tool-card {{ $tool['status'] === 'planned' ? 'planned' : '' }}" data-tool-slug="{{ $tool['slug'] }}" tabindex="0" @click="openTool('{{ $tool['slug'] }}')" @keydown.enter="openTool('{{ $tool['slug'] }}')">
                                <div class="tool-card-top"><span class="tool-icon">{{ $tool['icon'] }}</span><button type="button" class="favorite-button" :class="isFavorite('{{ $tool['slug'] }}') ? 'is-favorite' : ''" @click.stop="toggleFavorite('{{ $tool['slug'] }}')" :aria-label="isFavorite('{{ $tool['slug'] }}') ? 'Remove from favorites' : 'Add to favorites'"><span x-text="isFavorite('{{ $tool['slug'] }}') ? '★' : '☆'"></span></button></div>
                                <div class="tool-card-body"><span class="tool-category">{{ $tool['category'] }}</span><h3>{{ $tool['name'] }}</h3><p>{{ $tool['description'] }}</p></div>
                                <div class="tool-card-footer"><span>{{ $tool['status'] === 'ready' ? 'Open tool' : 'Coming soon' }}</span><span class="card-arrow">↗</span></div>
                            </article>
                        @empty
                            <div class="empty-state"><span class="tool-icon">⌕</span><h3>No tools found</h3><p>Try a different search or category.</p></div>
                        @endforelse
                    </div>
                </section>
            </div>
        </main>
    </div>

    <div class="palette-layer" x-show="paletteOpen" x-cloak x-transition.opacity @click.self="paletteOpen = false">
        <section class="command-palette" role="dialog" aria-modal="true" aria-label="Search tools" @keydown.escape.window="paletteOpen = false">
            <div class="palette-input"><span class="search-icon">⌕</span><input x-ref="paletteInput" type="search" x-model="paletteQuery" placeholder="Search tools..." @keydown.arrow-down.prevent="movePalette(1)" @keydown.arrow-up.prevent="movePalette(-1)" @keydown.enter.prevent="openPaletteResult()"><kbd>ESC</kbd></div>
            <div class="palette-list"><template x-for="(tool, index) in paletteResults" :key="tool.slug"><button type="button" class="palette-item" :class="paletteIndex === index ? 'highlighted' : ''" @mouseenter="paletteIndex = index" @click="openTool(tool.slug)"><span class="tool-icon small" x-text="tool.icon"></span><span><strong x-text="tool.name"></strong><small x-text="tool.category + ' / ' + tool.description"></small></span><span>↗</span></button></template><p x-show="paletteResults.length === 0" class="palette-empty">No matching tools.</p></div>
            <div class="palette-footer"><span><kbd>↑</kbd><kbd>↓</kbd> navigate</span><span><kbd>↵</kbd> open</span><span><kbd>esc</kbd> close</span></div>
        </section>
    </div>
</div>