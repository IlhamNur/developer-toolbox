import './bootstrap';

window.toolboxShell = () => ({
	theme: 'system',
	get themeClass() { return this.theme === 'dark' || (this.theme === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches) ? 'dark' : ''; },
	init() { this.theme = localStorage.getItem('hmnr-theme') || 'system'; },
	cycleTheme() { this.theme = this.theme === 'system' ? 'light' : this.theme === 'light' ? 'dark' : 'system'; localStorage.setItem('hmnr-theme', this.theme); document.documentElement.classList.toggle('dark', this.themeClass === 'dark'); },
});

window.dashboardTools = () => ({
	sidebarOpen: false,
	paletteOpen: false,
	paletteQuery: '',
	paletteIndex: 0,
	category: 'All tools',
	favorites: JSON.parse(localStorage.getItem('hmnr-favorites') || '[]'),
	recent: JSON.parse(localStorage.getItem('hmnr-recent') || '[]'),
	catalog: [],
	init() { this.catalog = [...document.querySelectorAll('[data-tool-slug]')].map((card) => ({ slug: card.dataset.toolSlug, name: card.querySelector('h3')?.textContent || '', category: card.querySelector('.tool-category')?.textContent || '', description: card.querySelector('p')?.textContent || '', icon: card.querySelector('.tool-icon')?.textContent.trim() || '{}' })); },
	get paletteResults() { const query = this.paletteQuery.toLowerCase().trim(); return this.catalog.filter((tool) => !query || `${tool.name} ${tool.category} ${tool.description}`.toLowerCase().includes(query)); },
	handleKeydown(event) { if ((event.metaKey || event.ctrlKey) && event.key.toLowerCase() === 'k') { event.preventDefault(); this.paletteOpen = true; this.$nextTick(() => this.$refs.paletteInput?.focus()); } if (event.key === 'Escape') this.paletteOpen = false; },
	movePalette(step) { this.paletteIndex = Math.max(0, Math.min(this.paletteResults.length - 1, this.paletteIndex + step)); },
	openPaletteResult() { const tool = this.paletteResults[this.paletteIndex]; if (tool) this.openTool(tool.slug); },
	openTool(slug) { if (!this.recent.includes(slug)) this.recent.unshift(slug); this.recent = this.recent.slice(0, 8); localStorage.setItem('hmnr-recent', JSON.stringify(this.recent)); this.paletteOpen = false; this.sidebarOpen = false; window.location.href = `/tools/${slug}`; },
	toolName(slug) { return this.catalog.find((tool) => tool.slug === slug)?.name || slug; },
	isFavorite(slug) { return this.favorites.includes(slug); },
	toggleFavorite(slug) { this.favorites = this.isFavorite(slug) ? this.favorites.filter((item) => item !== slug) : [...this.favorites, slug]; localStorage.setItem('hmnr-favorites', JSON.stringify(this.favorites)); },
	setCategory(category) { this.category = category; this.$wire.set('category', category); this.sidebarOpen = false; },
});
