import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.data('productFilter', () => ({
    filters: {
        brand: '',
        color: '',
        frame_shape: '',
        gender: '',
        material: '',
        price_min: '',
        price_max: '',
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
        this.$watch('filters.price_min', () => this.fetchProducts());
        this.$watch('filters.price_max', () => this.fetchProducts());
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
    init() {
        window.addEventListener('scroll', () => {
            this.scrolled = window.scrollY > 20;
        });
    }
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
    init() {
        window.addEventListener('product-detail-open', e => {
            this.product = e.detail;
            this.open = true;
        });
        this.$watch('open', val => {
            document.body.style.overflow = val ? 'hidden' : '';
        });
    },
    close() {
        this.open = false;
        this.product = null;
    },
    formatPrice(price) {
        const num = (parseFloat(price) * 10).toLocaleString('fr-FR');
        return num + ' MAD';
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

window.openFacePreview = function(product, faceData) {
    window.dispatchEvent(new CustomEvent('face-preview-open', { detail: { product, faceData } }));
};

Alpine.start();
