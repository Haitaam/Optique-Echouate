import Alpine from 'alpinejs';

window.Alpine = Alpine;

// --- Live Update Store ---
// Shared reactive store that holds the latest hashes from /live/hash
Alpine.store('live', {
    hash: null,
    productsHash: null,
    ordersHash: null,
    settingsHash: null,
    notifHash: null,
    adminData: null,
    settingsData: null,
    _timer: null,
    _paused: false,

    start() {
        if (this._timer) return;
        this.poll();
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) { this._paused = true; if (this._timer) { clearTimeout(this._timer); this._timer = null; } }
            else { this._paused = false; this.poll(); }
        });
    },

    poll() {
        if (this._paused) return;
        fetch('/live/hash', { headers: { 'Accept': 'application/json' } })
            .then(r => r.json())
            .then(data => {
                if (this.hash && data.combined !== this.hash) {
                    if (data.products !== this.productsHash) {
                        this.productsHash = data.products;
                        window.dispatchEvent(new CustomEvent('live-update:products'));
                    }
                    if (data.orders !== this.ordersHash) {
                        this.ordersHash = data.orders;
                        window.dispatchEvent(new CustomEvent('live-update:orders'));
                    }
                    if (data.settings !== this.settingsHash) {
                        this.settingsHash = data.settings;
                        window.dispatchEvent(new CustomEvent('live-update:settings'));
                        if (this.settingsData) {
                            fetch('/live/settings', { headers: { 'Accept': 'application/json' } })
                                .then(r => r.json()).then(s => { this.settingsData = s; window.dispatchEvent(new CustomEvent('live-update:settings-data', { detail: s })); })
                                .catch(() => {});
                        }
                    }
                    if (data.notifications !== this.notifHash) {
                        this.notifHash = data.notifications;
                        window.dispatchEvent(new CustomEvent('live-update:notifications'));
                    }
                }
                if (!this.hash) {
                    this.productsHash = data.products;
                    this.ordersHash = data.orders;
                    this.settingsHash = data.settings;
                    this.notifHash = data.notifications;
                }
                this.hash = data.combined;
                this._timer = setTimeout(() => this.poll(), 30000);
            })
            .catch(() => { this._timer = setTimeout(() => this.poll(), 30000); });
    },

    stop() {
        if (this._timer) { clearTimeout(this._timer); this._timer = null; }
    }
});

// --- Global Poller Component ---
// Attach x-data="livePoll" to <body> to start the background poller
Alpine.data('livePoll', () => ({
    init() {
        Alpine.store('live').start();
    }
}));

// --- Live Order Status (Tracking Page) ---
Alpine.data('liveOrderStatus', () => ({
    orderId: null,
    orderData: null,
    polling: false,
    _interval: null,

    init() {
        this.orderId = this.$el.dataset.orderId;
        if (!this.orderId) return;
        this.orderData = {
            status: this.$el.dataset.status || '',
            label: this.$el.dataset.label || '',
            badgeClass: this.$el.dataset.badgeClass || '',
            icon: this.$el.dataset.icon || '',
        };
        window.addEventListener('live-update:orders', () => this.refresh());
        this._interval = setInterval(() => this.refresh(), 15000);
    },

    refresh() {
        if (!this.orderId) return;
        fetch('/live/order/' + this.orderId, { headers: { 'Accept': 'application/json' } })
            .then(r => r.json())
            .then(data => {
                if (data.status !== this.orderData.status) {
                    this.orderData = {
                        status: data.status,
                        label: data.status_label,
                        badgeClass: data.badge_class,
                        icon: data.status_icon,
                    };
                    window.dispatchEvent(new CustomEvent('toast', {
                        detail: { message: 'La commande #' + this.orderId + ' est maintenant : ' + data.status_label, type: 'success' }
                    }));
                }
            })
            .catch(() => {});
    },

    destroy() {
        if (this._interval) clearInterval(this._interval);
    }
}));

// --- Live Admin Dashboard ---
Alpine.data('liveAdmin', () => ({
    pendingOrders: 0,
    unreadNotifications: 0,
    totalOrders: 0,
    totalRevenue: '',
    revenueToday: '',
    loading: false,

    init() {
        this.pendingOrders = parseInt(this.$el.dataset.pending || '0');
        this.unreadNotifications = parseInt(this.$el.dataset.notifs || '0');
        this.totalOrders = parseInt(this.$el.dataset.total || '0');
        this.totalRevenue = this.$el.dataset.revenue || '';
        this.revenueToday = this.$el.dataset.revenueToday || '';
        window.addEventListener('live-update:orders', () => this.refresh());
        window.addEventListener('live-update:notifications', () => this.refresh());
    },

    destroy() {
        window.removeEventListener('live-update:orders', this.refresh);
        window.removeEventListener('live-update:notifications', this.refresh);
    },
    refresh() {
        this.loading = true;
        fetch('/live/admin', { headers: { 'Accept': 'application/json' } })
            .then(r => r.json())
            .then(data => {
                this.pendingOrders = data.pending_orders;
                this.unreadNotifications = data.unread_notifications;
                this.totalOrders = data.total_orders;
                this.totalRevenue = data.total_revenue;
                this.revenueToday = data.revenue_today;
                this.loading = false;
            })
            .catch(() => { this.loading = false; });
    }
}));

// --- Live Footer Settings ---
Alpine.data('liveFooter', () => ({
    profession: '',
    city: '',
    phone: '',
    email: '',
    hours: '',
    mapsUrl: '',
    siteName: '',

    init() {
        this.profession = this.$el.dataset.profession || '';
        this.city = this.$el.dataset.city || '';
        this.phone = this.$el.dataset.phone || '';
        this.email = this.$el.dataset.email || '';
        this.hours = this.$el.dataset.hours || '';
        this.mapsUrl = this.$el.dataset.mapsUrl || '';
        this.siteName = this.$el.dataset.siteName || '';
        window.addEventListener('live-update:settings-data', e => {
            const s = e.detail;
            if (s.profession) this.profession = s.profession;
            if (s.city) this.city = s.city;
            if (s.contact_phone) this.phone = s.contact_phone;
            if (s.contact_email) this.email = s.contact_email;
            if (s.working_hours) this.hours = s.working_hours;
            if (s.maps_url) this.mapsUrl = s.maps_url;
            if (s.site_name) this.siteName = s.site_name;
        });
    }
}));

Alpine.data('productFilter', () => ({
    filters: {
        brand: '',
        color: '',
        frame_shape: '',
        gender: '',
        material: '',
        category: '',
        search: '',
    },
    loading: false,
    searchTimeout: null,
    init() {
        this.$watch('filters.search', val => {
            clearTimeout(this.searchTimeout);
            this.searchTimeout = setTimeout(() => this.fetchProducts(), 350);
        });
        this.$watch('filters.brand', () => this.fetchProducts());
        this.$watch('filters.color', () => this.fetchProducts());
        this.$watch('filters.frame_shape', () => this.fetchProducts());
        this.$watch('filters.gender', () => this.fetchProducts());
        this.$watch('filters.material', () => this.fetchProducts());
        this.$watch('filters.category', () => this.fetchProducts());
        window.addEventListener('live-update:products', () => this.fetchProducts());
    },
    destroy() {
        if (this.searchTimeout) clearTimeout(this.searchTimeout);
    },
    fetchProducts() {
        this.loading = true;
        const params = new URLSearchParams();
        Object.entries(this.filters).forEach(([key, value]) => {
            if (value) params.append(key, value);
        });

        fetch(`/products/filter?${params.toString()}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(data => {
            document.getElementById('product-grid').innerHTML = data.html;
            this.loading = false;
        })
        .catch(() => this.loading = false);
    }
}));

Alpine.data('quizWizard', () => ({
    step: 1,
    totalSteps: 6,
    loading: false,
    results: null,
    resultsHtml: '',
    answers: {
        glasses_type: '', style: '', shape: '',
        color: '', material: '', lifestyle: '',
    },
    shapePaths: {
        round: 'M 30 8 C 18 8 10 16 10 24 C 10 32 18 40 30 40 C 42 40 50 32 50 24 C 50 16 42 8 30 8 Z',
        square: 'M 15 10 L 45 10 L 48 28 L 45 38 L 15 38 L 12 28 Z',
        oval: 'M 30 6 C 45 6 52 18 52 24 C 52 30 45 42 30 42 C 15 42 8 30 8 24 C 8 18 15 6 30 6 Z',
        heart: 'M 30 10 C 35 4 50 14 46 24 C 42 34 30 42 30 42 C 30 42 18 34 14 24 C 10 14 25 4 30 10 Z',
        diamond: 'M 30 4 L 52 24 L 30 44 L 8 24 Z',
    },
    getShapeSvgStyle(value) {
        if (this.answers.shape !== value) return { opacity: '0.3' };
        return { opacity: '1', filter: 'drop-shadow(0 0 4px rgba(251,146,60,0.5))' };
    },
    options: {
        glasses_type: [
            { value: 'optical', label: 'Optical Glasses', icon: '👓' },
            { value: 'sunglasses', label: 'Sunglasses', icon: '🕶️' },
            { value: 'blue_light', label: 'Blue Light', icon: '💻' },
        ],
        style: [
            { value: 'men', label: 'Men', icon: '🧑' },
            { value: 'women', label: 'Women', icon: '👩' },
            { value: 'unisex', label: 'Unisex', icon: '✨' },
        ],
        shape: [
            { value: 'round', label: 'Round Face', icon: '⭕' },
            { value: 'square', label: 'Square Face', icon: '🔲' },
            { value: 'oval', label: 'Oval Face', icon: '🥚' },
            { value: 'heart', label: 'Heart Face', icon: '💜' },
            { value: 'diamond', label: 'Diamond Face', icon: '💎' },
        ],
        color: [
            { value: 'gold', label: 'Gold / Rose Gold', icon: '🌟' },
            { value: 'silver', label: 'Silver / Gunmetal', icon: '⚪' },
            { value: 'black', label: 'Black / Matte', icon: '⚫' },
            { value: 'tortoise', label: 'Tortoise', icon: '🐢' },
            { value: 'crystal', label: 'Crystal / White', icon: '💎' },
            { value: 'blue', label: 'Blue', icon: '🔵' },
        ],
        material: [
            { value: 'titanium', label: 'Titanium', icon: '🔩' },
            { value: 'acetate', label: 'Acetate', icon: '🧊' },
            { value: 'stainless', label: 'Stainless Steel', icon: '⚙️' },
            { value: 'polycarbonate', label: 'Polycarbonate', icon: '🛡️' },
            { value: 'metal', label: 'Metal', icon: '🔗' },
        ],
        lifestyle: [
            { value: 'digital', label: 'Digital Life', icon: '💻' },
            { value: 'active', label: 'Active / Sports', icon: '🏃' },
            { value: 'fashion', label: 'Fashion Forward', icon: '👗' },
            { value: 'professional', label: 'Professional', icon: '💼' },
        ],
    },
    nextStep() {
        if (this.step < this.totalSteps) this.step++;
    },
    prevStep() {
        if (this.step > 1) this.step--;
    },
    selectAnswer(category, value) {
        this.answers[category] = value;
        if (this.step < this.totalSteps) {
            setTimeout(() => this.nextStep(), 300);
        }
    },
    submitQuiz() {
        this.loading = true;
        fetch('/quiz/analyze', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            body: JSON.stringify(this.answers),
        })
        .then(r => r.json())
        .then(data => {
            this.results = data.results;
            this.resultsHtml = data.html;
            this.loading = false;
            this.step = 7;
        })
        .catch(() => this.loading = false);
    },
    restartQuiz() {
        this.step = 1;
        this.results = null;
        this.answers = {
            glasses_type: '', style: '', shape: '',
            color: '', material: '', lifestyle: '',
        };
    }
}));

Alpine.data('appointmentForm', () => ({
    form: { name: '', email: '', phone: '', service_type: '', appointment_date: '', notes: '' },
    submitting: false,
    success: false,
    error: null,
    submit() {
        this.submitting = true;
        this.error = null;
        fetch('/appointments', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            body: JSON.stringify(this.form),
        })
        .then(r => r.json().then(d => ({ ok: r.ok, data: d })))
        .then(({ ok, data }) => {
            if (ok) {
                this.success = true;
                this.form = { name: '', email: '', phone: '', service_type: '', appointment_date: '', notes: '' };
            } else {
                this.error = data.message || 'Something went wrong.';
            }
            this.submitting = false;
        })
        .catch(() => { this.error = 'Network error. Please try again.'; this.submitting = false; });
    }
}));

Alpine.data('navbar', () => ({
    scrolled: false,
    mobileOpen: false,
    searchOpen: false,
    searchQuery: '',
    searchResults: [],
    searchBrands: [],
    searchCategories: [],
    searchTotal: 0,
    searching: false,
    hasSearched: false,
    highlightIndex: -1,
    searchCache: {},
    recentSearches: [],

    init() {
        window.addEventListener('scroll', () => {
            this.scrolled = window.scrollY > 20;
        });
        try {
            const saved = localStorage.getItem('recent_searches');
            if (saved) this.recentSearches = JSON.parse(saved);
        } catch(e) {}
    },

    get hasResults() {
        return this.searchProducts.length > 0 || this.searchBrands.length > 0 || this.searchCategories.length > 0;
    },

    get searchProducts() {
        return this.searchResults || [];
    },

    get totalResults() {
        const p = this.searchProducts.length;
        const b = this.searchBrands.length;
        const c = this.searchCategories.length;
        return p + b + c;
    },

    get flatResults() {
        const items = [];
        if (this.searchProducts.length) {
            items.push({ type: 'header', label: 'Produits' });
            this.searchProducts.forEach(r => items.push({ type: 'product', ...r }));
        }
        if (this.searchBrands.length) {
            items.push({ type: 'header', label: 'Marques' });
            this.searchBrands.forEach(r => items.push({ type: 'brand', ...r }));
        }
        if (this.searchCategories.length) {
            items.push({ type: 'header', label: 'Catégories' });
            this.searchCategories.forEach(r => items.push({ type: 'category', ...r }));
        }
        return items;
    },

    doSearch(go) {
        const q = this.searchQuery.trim();
        if (!q) return;
        this.saveRecent(q);
        if (go !== false) {
            window.location.href = '/products?search=' + encodeURIComponent(q);
        }
    },

    saveRecent(q) {
        this.recentSearches = this.recentSearches.filter(s => s !== q);
        this.recentSearches.unshift(q);
        if (this.recentSearches.length > 5) this.recentSearches.pop();
        localStorage.setItem('recent_searches', JSON.stringify(this.recentSearches));
    },

    searchLive() {
        const q = this.searchQuery.trim();
        if (q.length < 2) {
            this.searchResults = [];
            this.searchBrands = [];
            this.searchCategories = [];
            this.searchTotal = 0;
            this.hasSearched = false;
            this.highlightIndex = -1;
            return;
        }
        if (this.searchCache[q]) {
            const cached = this.searchCache[q];
            this.searchResults = cached.products;
            this.searchBrands = cached.brands;
            this.searchCategories = cached.categories;
            this.searchTotal = cached.total;
            this.hasSearched = true;
            this.searching = false;
            return;
        }
        this.searching = true;
        this.hasSearched = true;
        const params = new URLSearchParams({ q });
        fetch('/products/search-json?' + params.toString(), {
            headers: { 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(data => {
            this.searchResults = data.products || [];
            this.searchBrands = data.brands || [];
            this.searchCategories = data.categories || [];
            this.searchTotal = data.total || 0;
            this.searchCache[q] = {
                products: this.searchResults,
                brands: this.searchBrands,
                categories: this.searchCategories,
                total: this.searchTotal,
            };
            this.searching = false;
        })
        .catch(() => {
            this.searchResults = [];
            this.searchBrands = [];
            this.searchCategories = [];
            this.searchTotal = 0;
            this.searching = false;
        });
    },

    onKeydown(e) {
        if (!this.searchOpen || !this.hasSearched) return;
        const total = this.totalResults;
        if (total === 0) return;

        if (e.key === 'ArrowDown') {
            e.preventDefault();
            this.highlightIndex = this.highlightIndex < total - 1 ? this.highlightIndex + 1 : 0;
            this.scrollToHighlight();
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            this.highlightIndex = this.highlightIndex > 0 ? this.highlightIndex - 1 : total - 1;
            this.scrollToHighlight();
        } else if (e.key === 'Enter' && this.highlightIndex >= 0) {
            e.preventDefault();
            this.selectHighlighted();
        } else if (e.key === 'Escape') {
            this.closeSearch();
        }
    },

    scrollToHighlight() {
        this.$nextTick(() => {
            const el = document.querySelector('[data-search-idx="' + this.highlightIndex + '"]');
            if (el) el.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
        });
    },

    selectHighlighted() {
        if (this.highlightIndex < 0) return;
        const flat = this.flatResults;
        const item = flat[this.highlightIndex];
        if (!item) return;
        if (item.type === 'product') {
            this.saveRecent(item.name);
            window.location.href = '/products?search=' + encodeURIComponent(item.name);
        } else if (item.type === 'brand') {
            window.location.href = '/products?brand=' + encodeURIComponent(item.name);
        } else if (item.type === 'category') {
            window.location.href = '/products?category=' + item.id;
        }
    },

    closeSearch() {
        this.searchOpen = false;
        this.searchQuery = '';
        this.searchResults = [];
        this.searchBrands = [];
        this.searchCategories = [];
        this.searchTotal = 0;
        this.hasSearched = false;
        this.highlightIndex = -1;
    },

    toggleSearch() {
        this.searchOpen = !this.searchOpen;
        if (this.searchOpen) {
            this.$nextTick(() => {
                const input = this.$el.querySelector('input[type="text"]');
                if (input) input.focus();
            });
        }
    },

    get showDropdown() {
        if (!this.searchOpen) return false;
        if (!this.hasSearched) return false;
        if (this.searching) return true;
        if (this.searchQuery.trim().length < 2) return false;
        return true;
    }
}));

Alpine.data('accountDropdown', () => ({
    open: false,
}));

Alpine.data('toast', () => ({
    visible: false,
    message: '',
    type: 'success',
    init() {
        window.addEventListener('toast', e => {
            this.message = e.detail.message;
            this.type = e.detail.type || 'success';
            this.visible = true;
            setTimeout(() => this.visible = false, 4000);
        });
    }
}));

Alpine.data('productGrid', () => ({
    hoveredId: null,
    setHovered(id) {
        this.hoveredId = id;
    },
    clearHovered() {
        this.hoveredId = null;
    },
    isHovered(id) {
        return this.hoveredId === id;
    },
}));

Alpine.data('facePreview', () => ({
    open: false,
    product: null,
    faceData: {},
    selectedShape: 'round',
    shapeLabels: {
        round: 'Rond', oval: 'Ovale', square: 'Carré',
        heart: 'Cœur', diamond: 'Diamant',
    },
    shapeOrder: ['round', 'oval', 'square', 'heart', 'diamond'],
    facePaths: {
        round: 'M 120 80 C 120 35 170 20 220 80 C 270 140 270 200 220 250 C 170 300 120 285 120 240 C 120 200 70 140 120 80 Z',
        oval: 'M 170 60 C 200 30 250 40 270 90 C 290 140 290 200 270 240 C 250 280 200 290 170 260 C 140 230 110 160 130 110 C 150 60 140 50 170 60 Z',
        square: 'M 140 70 L 260 70 L 280 180 L 270 260 L 130 260 L 120 180 Z',
        heart: 'M 170 60 C 220 20 290 70 270 130 C 250 190 200 240 200 280 C 200 240 150 190 130 130 C 110 70 120 30 170 60 Z',
        diamond: 'M 200 40 L 280 150 L 200 270 L 120 150 Z',
    },
    faceColors: {
        round: { fill: '#fef3c7', stroke: '#f59e0b' },
        oval: { fill: '#fce7f3', stroke: '#ec4899' },
        square: { fill: '#dbeafe', stroke: '#3b82f6' },
        heart: { fill: '#fce7f3', stroke: '#ef4444' },
        diamond: { fill: '#ede9fe', stroke: '#8b5cf6' },
    },
    get bestLabel() {
        if (!this.faceData || Object.keys(this.faceData).length === 0) return '';
        const entries = Object.entries(this.faceData);
        entries.sort((a, b) => b[1].percentage - a[1].percentage);
        return this.shapeLabels[entries[0][0]] || entries[0][0];
    },
    init() {
        window.addEventListener('face-preview-open', e => {
            this.openFor(e.detail.product, e.detail.faceData);
        });
        this.$watch('open', val => {
            if (val) {
                document.body.style.overflow = 'hidden';
                const keys = Object.keys(this.faceData);
                if (keys.length) this.selectedShape = keys[0];
                window.addEventListener('keydown', this._keyHandler);
            } else {
                document.body.style.overflow = '';
                window.removeEventListener('keydown', this._keyHandler);
            }
        });
    },
    _keyHandler(e) {
        const idx = this.shapeOrder.indexOf(this.selectedShape);
        if (e.key === 'ArrowRight' && idx < this.shapeOrder.length - 1) {
            this.selectedShape = this.shapeOrder[idx + 1];
        } else if (e.key === 'ArrowLeft' && idx > 0) {
            this.selectedShape = this.shapeOrder[idx - 1];
        }
    },
    openFor(product, faceData) {
        this.product = product;
        this.faceData = faceData;
        const keys = Object.keys(faceData);
        this.selectedShape = keys.length ? keys[0] : 'round';
        this.open = true;
    },
    close() {
        this.open = false;
        this.product = null;
        this.faceData = {};
    },
    scrollToShape(shape) {
        this.$nextTick(() => {
            const btn = document.querySelector('[data-shape="' + shape + '"]');
            if (btn) btn.scrollIntoView({ behavior: 'smooth', inline: 'center', block: 'nearest' });
        });
    },
}));

Alpine.data('productDetail', () => ({
    open: false,
    product: null,
    _liveTimer: null,
    init() {
        window.addEventListener('product-detail-open', e => {
            this.product = e.detail;
            this.open = true;
            this.startLiveRefresh();
        });
        this.$watch('open', val => {
            document.body.style.overflow = val ? 'hidden' : '';
            if (val) this.startLiveRefresh();
            else this.stopLiveRefresh();
        });
        window.addEventListener('live-update:products', () => {
            if (this.open && this.product) this.refreshProduct();
        });
    },
    startLiveRefresh() {
        if (this._liveTimer) clearInterval(this._liveTimer);
        this._liveTimer = setInterval(() => {
            if (this.open && this.product) this.refreshProduct();
        }, 30000);
    },
    stopLiveRefresh() {
        if (this._liveTimer) { clearInterval(this._liveTimer); this._liveTimer = null; }
    },
    refreshProduct() {
        if (!this.product) return;
        fetch('/live/product/' + this.product.id, { headers: { 'Accept': 'application/json' } })
            .then(r => r.json())
            .then(data => {
                if (data.price) this.product.price = data.price;
                if (data.stock !== undefined) this.product.stock = data.stock;
                if (data.name) this.product.name = data.name;
                if (data.image) this.product.image = data.image;
            })
            .catch(() => {});
    },
    destroy() {
        this.stopLiveRefresh();
    },
    close() {
        this.open = false;
        this.product = null;
        this.stopLiveRefresh();
    },
    formatPrice(price) {
        return parseFloat(price).toLocaleString('fr-FR', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + ' MAD';
    },
    colorSwatch(color) {
        const map = {
            gold: '#FFD700', 'rose-gold': '#B76E79', 'rose gold': '#B76E79',
            silver: '#C0C0C0', gunmetal: '#2C3539', black: '#000',
            'matte black': '#1a1a1a', tortoise: '#8B6914', crystal: '#E8E8E8',
            white: '#fff', blue: '#3B82F6', red: '#EF4444', brown: '#8B4513',
            green: '#22C55E',
        };
        return map[color?.toLowerCase()] || '#f97316';
    },
}));

Alpine.data('heroParallax', () => ({
    rotateX: 0,
    rotateY: 0,
    scrollY: 0,
    mouseX: 0,
    mouseY: 0,
    glowX: 50,
    glowY: 50,
    currentProduct: 0,
    products: [],
    autoplayInterval: null,

    init() {
        const container = this.$el.querySelector('[data-hero-products]');
        if (container) {
            this.products = JSON.parse(container.dataset.products || '[]');
        }
        if (this.products.length > 1) {
            this.autoplayInterval = setInterval(() => this.nextProduct(), 5000);
        }
        window.addEventListener('scroll', () => {
            this.scrollY = window.scrollY;
        });
    },

    destroy() {
        if (this.autoplayInterval) clearInterval(this.autoplayInterval);
    },

    handleMouse(e) {
        const rect = this.$el.getBoundingClientRect();
        const centerX = rect.left + rect.width / 2;
        const centerY = rect.top + rect.height / 2;
        const deltaX = (e.clientX - centerX) / rect.width;
        const deltaY = (e.clientY - centerY) / rect.height;
        this.rotateX = deltaX * 8;
        this.rotateY = -deltaY * 8;
        this.mouseX = e.clientX;
        this.mouseY = e.clientY;
        this.glowX = ((e.clientX - rect.left) / rect.width) * 100;
        this.glowY = ((e.clientY - rect.top) / rect.height) * 100;
    },

    get scrollTransform() {
        const offset = Math.min(this.scrollY * 0.15, 60);
        return `translateY(${offset}px)`;
    },

    get productTransform() {
        return `perspective(1200px) rotateY(${this.rotateX}deg) rotateX(${this.rotateY}deg)`;
    },

    get glowStyle() {
        return {
            left: `${this.glowX}%`,
            top: `${this.glowY}%`,
        };
    },

    nextProduct() {
        if (this.products.length < 2) return;
        this.currentProduct = (this.currentProduct + 1) % this.products.length;
    },

    prevProduct() {
        if (this.products.length < 2) return;
        this.currentProduct = (this.currentProduct - 1 + this.products.length) % this.products.length;
    },

    selectProduct(idx) {
        this.currentProduct = idx;
        if (this.autoplayInterval) {
            clearInterval(this.autoplayInterval);
            this.autoplayInterval = setInterval(() => this.nextProduct(), 5000);
        }
    },
}));

Alpine.data('adminDashboard', () => ({
    selected: new Set(),
    selectAll: false,
    filterOpen: false,
    loading: false,
    smartFixing: false,
    filters: {
        brand: '',
        gender: '',
        category: '',
        color: '',
        frame_shape: '',
        search: '',
    },
    searchTimeout: null,
    showingBulkForm: false,
    bulkForm: { brand: '', tags: '', category_id: '' },
    categories: [],

    init() {
        this.categories = JSON.parse(this.$el.dataset.categories || '[]');
    },

    get selectedCount() { return this.selected.size; },
    get hasSelection() { return this.selected.size > 0; },
    get totalItems() { return parseInt(this.$el.dataset.total || '0'); },

    toggleSelect(id) {
        if (this.selected.has(id)) this.selected.delete(id);
        else this.selected.add(id);
        this.selectAll = this.selected.size === this.totalItems;
    },

    toggleSelectAll() {
        if (this.selectAll) {
            this.selected.clear();
            this.selectAll = false;
        } else {
            const allIds = JSON.parse(this.$el.dataset.allIds || '[]');
            allIds.forEach(id => this.selected.add(id));
            this.selectAll = true;
        }
    },

    searchInput() {
        clearTimeout(this.searchTimeout);
        this.searchTimeout = setTimeout(() => this.$el.closest('form').submit(), 400);
    },

    showConfirm(msg, cb) {
        this.$dispatch('admin-confirm', { message: msg, callback: cb });
    },

    confirmBulkDelete() {
        if (!this.hasSelection) return;
        this.showConfirm(
            `Supprimer ${this.selectedCount} produit(s) ?`,
            () => this.bulkAction('/admin/glasses/bulk-delete', { ids: [...this.selected].join(','), delete_file: true })
        );
    },

    confirmBulkDeleteDbOnly() {
        if (!this.hasSelection) return;
        this.showConfirm(
            `Retirer ${this.selectedCount} produit(s) de la base (conserver les images) ?`,
            () => this.bulkAction('/admin/glasses/bulk-delete', { ids: [...this.selected].join(',') })
        );
    },

    confirmBulkSyncFix() {
        if (!this.hasSelection) return;
        this.showConfirm(
            `Re-vérifier les images de ${this.selectedCount} produit(s) ?`,
            () => this.bulkAction('/admin/glasses/bulk-sync-fix', { ids: [...this.selected].join(',') })
        );
    },

    bulkAction(url, data) {
        this.loading = true;
        const formData = new FormData();
        Object.entries(data).forEach(([k, v]) => formData.append(k, v));
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

        fetch(url, { method: 'POST', body: formData, headers: { 'Accept': 'application/json' } })
            .then(r => r.json())
            .then(res => {
                this.loading = false;
                this.selected.clear();
                this.selectAll = false;
                this.showingBulkForm = false;
                if (res.redirect) window.location.href = res.redirect;
                else window.location.reload();
            })
            .catch(() => { this.loading = false; window.location.reload(); });
    },

    showBulkEdit() {
        if (!this.hasSelection) return;
        this.showingBulkForm = !this.showingBulkForm;
        this.bulkForm = { brand: '', tags: '', category_id: '' };
    },

    submitBulkEdit() {
        const data = { ids: [...this.selected].join(',') };
        if (this.bulkForm.brand) data.brand = this.bulkForm.brand;
        if (this.bulkForm.tags) data.tags = this.bulkForm.tags;
        if (this.bulkForm.category_id) data.category_id = this.bulkForm.category_id;
        if (Object.keys(data).length === 1) { this.showingBulkForm = false; return; }

        this.showConfirm(
            `Appliquer les modifications à ${this.selectedCount} produit(s) ?`,
            () => this.bulkAction('/admin/glasses/bulk-update', data)
        );
    },

    smartFix() {
        this.smartFixing = true;
        const formData = new FormData();
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

        fetch('/admin/glasses/smart-fix', { method: 'POST', body: formData, headers: { 'Accept': 'application/json' } })
            .then(r => r.json())
            .then(res => {
                this.smartFixing = false;
                if (res.redirect) window.location.href = res.redirect;
                else window.location.reload();
            })
            .catch(() => { this.smartFixing = false; window.location.reload(); });
    },

    exportCSV() { window.location.href = '/admin/glasses/export?format=csv'; },
    exportJSON() { window.location.href = '/admin/glasses/export?format=json'; },
}));

Alpine.data('adminConfirm', () => ({
    open: false,
    message: '',
    callback: null,
    init() {
        window.addEventListener('admin-confirm', e => {
            this.open = true;
            this.message = e.detail.message;
            this.callback = e.detail.callback;
        });
    },
    confirm() {
        if (typeof this.callback === 'function') this.callback();
        this.open = false;
        this.callback = null;
    },
    cancel() {
        this.open = false;
        this.callback = null;
    },
}));

Alpine.data('pageLoader', () => ({
    visible: true,
    init() {
        if (document.readyState === 'complete') {
            this.hide();
        } else {
            window.addEventListener('load', () => this.hide());
            document.addEventListener('DOMContentLoaded', () => {
                setTimeout(() => {
                    if (document.readyState === 'complete') this.hide();
                }, 800);
            });
        }
    },
    hide() {
        setTimeout(() => { this.visible = false; }, 400);
    },
}));

Alpine.data('cart', () => ({
    items: [],
    open: false,
    showCheckoutForm: false,
    checkoutError: '',
    init() {
        const saved = localStorage.getItem('cart_items');
        if (saved) {
            try { this.items = JSON.parse(saved); } catch(e) { this.items = []; }
        }
        const hasError = this.$el?.dataset?.checkoutError === '1';
        if (hasError && this.items.length > 0) {
            this.open = true;
            this.showCheckoutForm = true;
            this.checkoutError = this.$el.dataset.checkoutMsg || '';
        }
        window.addEventListener('cart-toggle', () => { this.open = !this.open; this.showCheckoutForm = false; this.checkoutError = ''; });
        window.addEventListener('cart-add', e => this.add(e.detail));
        this.$watch('items', () => {
            localStorage.setItem('cart_items', JSON.stringify(this.items));
            window.dispatchEvent(new CustomEvent('cart-count-updated', { detail: this.count }));
        });
    },
    get count() {
        return this.items.reduce((sum, item) => sum + item.quantity, 0);
    },
    get total() {
        return this.items.reduce((sum, item) => sum + (item.price || 0) * item.quantity, 0).toFixed(2);
    },
    add(product) {
        const existing = this.items.find(i => i.id === product.id);
        if (existing) {
            existing.quantity++;
        } else {
            this.items.push({
                id: product.id,
                name: product.name,
                price: product.price,
                image: product.image,
                brand: product.brand,
                color: product.color,
                quantity: 1,
            });
        }
        this.open = true;
        window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Ajouté au panier', type: 'success' } }));
    },
    remove(id) {
        this.items = this.items.filter(i => i.id !== id);
        window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Retiré du panier', type: 'success' } }));
    },
    updateQuantity(id, qty) {
        const item = this.items.find(i => i.id === id);
        if (item) {
            qty = parseInt(qty);
            if (qty <= 0) this.remove(id);
            else item.quantity = qty;
        }
    },
    clear() {
        this.items = [];
    },
    formatPrice(price) {
        return parseFloat(price).toLocaleString('fr-FR', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + ' MAD';
    },
}));

Alpine.data('cartBadge', () => ({
    count: 0,
    init() {
        const saved = localStorage.getItem('cart_items');
        if (saved) {
            try {
                const items = JSON.parse(saved);
                this.count = items.reduce((sum, i) => sum + i.quantity, 0);
            } catch(e) {}
        }
        window.addEventListener('cart-count-updated', e => { this.count = e.detail; });
    },
    toggle() {
        window.dispatchEvent(new CustomEvent('cart-toggle'));
    },
    addToCart(product) {
        window.dispatchEvent(new CustomEvent('cart-add', { detail: product }));
    },
}));

Alpine.data('notifPanel', () => ({
    open: false,
    notifications: [],
    unreadCount: 0,
    init(initialCount) {
        this.unreadCount = initialCount || 0;
        window.addEventListener('notif-count-updated', e => {
            this.unreadCount = e.detail;
            localStorage.setItem('notif_unread_count', e.detail);
        });
    },
    toggle() {
        this.open = !this.open;
        if (this.open) {
            this.fetchNotifications();
        }
    },
    close() {
        this.open = false;
    },
    fetchNotifications() {
        fetch('/admin/notifications/json', {
            headers: { 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(data => {
            this.notifications = data.notifications;
            this.unreadCount = data.unread_count;
            localStorage.setItem('notif_unread_count', data.unread_count);
        })
        .catch(() => {});
    },
    markRead(n) {
        if (n.read) {
            window.location.href = n.url;
            return;
        }
        fetch('/admin/notifications/' + n.id + '/read', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
        })
        .then(r => r.json())
        .then(data => {
            n.read = true;
            this.unreadCount = data.unread_count;
            localStorage.setItem('notif_unread_count', data.unread_count);
            window.dispatchEvent(new CustomEvent('notif-count-updated', { detail: data.unread_count }));
            window.location.href = n.url;
        })
        .catch(() => {
            window.location.href = n.url;
        });
    },
    readAll() {
        const url = this.$el?.dataset?.readAllUrl || '/admin/notifications/read-all';
        fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
        })
        .then(r => r.json())
        .then(data => {
            this.notifications.forEach(n => { n.read = true; });
            this.unreadCount = 0;
            localStorage.setItem('notif_unread_count', '0');
            window.dispatchEvent(new CustomEvent('notif-count-updated', { detail: 0 }));
        })
        .catch(() => {});
    },
}));


Alpine.start();
