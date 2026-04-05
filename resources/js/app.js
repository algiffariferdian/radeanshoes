import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

document.addEventListener('alpine:init', () => {
    Alpine.data('storefrontHeader', (categories = []) => ({
        categories,
        mobileMenu: false,
        profileMenu: false,
        categoryMenu: false,
        activeCategoryId: categories[0]?.id ?? null,
        currentCategoryPreviewOffset: 0,

        init() {
            this.ensureActiveCategory();
        },

        get hasCategories() {
            return this.categories.length > 0;
        },

        get activeCategory() {
            return this.categories.find((category) => Number(category.id) === Number(this.activeCategoryId))
                ?? this.categories[0]
                ?? null;
        },

        get activeCategoryPreviewImages() {
            return Array.isArray(this.activeCategory?.previewImages)
                ? this.activeCategory.previewImages.filter((image) => image?.imageUrl)
                : [];
        },

        get visibleCategoryPreviewImages() {
            return this.activeCategoryPreviewImages.slice(
                this.currentCategoryPreviewOffset,
                this.currentCategoryPreviewOffset + 5,
            );
        },

        ensureActiveCategory(categoryId = null) {
            if (!this.hasCategories) {
                this.activeCategoryId = null;
                this.currentCategoryPreviewOffset = 0;

                return;
            }

            const nextCategoryId = categoryId ?? this.activeCategoryId;
            const matchedCategory = this.categories.find((category) => Number(category.id) === Number(nextCategoryId));

            this.activeCategoryId = matchedCategory?.id ?? this.categories[0].id;
            this.currentCategoryPreviewOffset = 0;
        },

        activateCategory(categoryId) {
            this.ensureActiveCategory(categoryId);
        },

        toggleMobileMenu() {
            this.mobileMenu = !this.mobileMenu;

            if (this.mobileMenu) {
                this.profileMenu = false;
                this.categoryMenu = false;
            }
        },

        closeMobileMenu() {
            this.mobileMenu = false;
        },

        toggleProfileMenu() {
            this.profileMenu = !this.profileMenu;

            if (this.profileMenu) {
                this.mobileMenu = false;
                this.categoryMenu = false;
            }
        },

        closeProfileMenu() {
            this.profileMenu = false;
        },

        toggleCategoryMenu() {
            if (!this.hasCategories) {
                return;
            }

            this.categoryMenu = !this.categoryMenu;

            if (this.categoryMenu) {
                this.mobileMenu = false;
                this.profileMenu = false;
                this.ensureActiveCategory();
            }
        },

        closeCategoryMenu() {
            this.categoryMenu = false;
        },

        closeAllMenus() {
            this.closeMobileMenu();
            this.closeProfileMenu();
            this.closeCategoryMenu();
        },

        prevCategoryPreview() {
            if (this.activeCategoryPreviewImages.length <= 5) {
                return;
            }

            const lastOffset = Math.max(this.activeCategoryPreviewImages.length - 5, 0);

            this.currentCategoryPreviewOffset = this.currentCategoryPreviewOffset <= 0
                ? lastOffset
                : this.currentCategoryPreviewOffset - 1;
        },

        nextCategoryPreview() {
            if (this.activeCategoryPreviewImages.length <= 5) {
                return;
            }

            const lastOffset = Math.max(this.activeCategoryPreviewImages.length - 5, 0);

            this.currentCategoryPreviewOffset = this.currentCategoryPreviewOffset >= lastOffset
                ? 0
                : this.currentCategoryPreviewOffset + 1;
        },
    }));

    Alpine.data('quantityStepper', (initialValue = 1, min = 1, max = 20) => ({
        value: Number(initialValue),
        min: Number(min),
        max: Number(max),

        decrease() {
            this.value = Math.max(this.min, this.value - 1);
        },

        increase() {
            this.value = Math.min(this.max, this.value + 1);
        },
    }));

    Alpine.data('catalogPage', () => ({
        filtersOpen: false,
        isLoading: false,
        autoSubmitTimer: null,
        abortController: null,

        init() {
            this.$el.addEventListener('click', (event) => {
                const link = event.target.closest('[data-catalog-link]');
                if (!link) {
                    return;
                }

                const href = link.getAttribute('href');
                if (!href) {
                    return;
                }

                event.preventDefault();
                this.fetchCatalog(href, true);
            });
        },

        scheduleSubmit(delay = 600) {
            window.clearTimeout(this.autoSubmitTimer);
            this.autoSubmitTimer = window.setTimeout(() => {
                this.submitFilters();
            }, delay);
        },

        submitFilters() {
            const form = this.$refs.filtersForm;
            if (!form) {
                return;
            }

            const url = new URL(form.action, window.location.origin);
            const formData = new FormData(form);

            for (const [key, value] of formData.entries()) {
                const cleaned = typeof value === 'string' ? value.trim() : value;
                if (cleaned !== '' && cleaned !== null) {
                    url.searchParams.set(key, cleaned);
                }
            }

            const sortSelect = this.$refs.sortSelect;
            if (sortSelect?.value) {
                url.searchParams.set('sort', sortSelect.value);
            }

            this.fetchCatalog(url.toString(), true);
        },

        async fetchCatalog(url, updateHistory = true) {
            if (!url) {
                return;
            }

            if (this.abortController) {
                this.abortController.abort();
            }

            this.isLoading = true;
            this.abortController = new AbortController();

            const requestUrl = new URL(url, window.location.origin);
            requestUrl.searchParams.set('ajax', '1');

            try {
                const response = await fetch(requestUrl.toString(), {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    signal: this.abortController.signal,
                });

                if (!response.ok) {
                    throw new Error('Gagal memuat katalog');
                }

                const data = await response.json();
                if (this.$refs.listing && typeof data.listing === 'string') {
                    this.$refs.listing.innerHTML = data.listing;
                }

                if (updateHistory) {
                    window.history.replaceState({}, '', url);
                }
            } catch (error) {
                if (error?.name !== 'AbortError') {
                    window.location.href = url;
                }
            } finally {
                this.isLoading = false;
            }
        },
    }));

    Alpine.data('checkoutPage', (config = {}) => ({
        addresses: Array.isArray(config.addresses) ? config.addresses : [],
        shippingOptions: Array.isArray(config.shippingOptions) ? config.shippingOptions : [],
        selectedAddressId: config.selectedAddressId ?? null,
        selectedShippingId: config.selectedShippingId ?? null,
        cartSubtotal: Number(config.cartSubtotal ?? 0),
        voucherCode: config.voucherCode ?? '',
        voucherDiscount: Number(config.voucherDiscount ?? 0),
        voucherError: config.voucherError ?? '',
        voucherPreviewUrl: config.voucherPreviewUrl ?? '',
        previewTimer: null,

        get selectedAddress() {
            return this.addresses.find((address) => Number(address.id) === Number(this.selectedAddressId)) ?? null;
        },

        get selectedShipping() {
            return this.shippingOptions.find((option) => Number(option.id) === Number(this.selectedShippingId)) ?? null;
        },

        get shippingPrice() {
            return Number(this.selectedShipping?.price ?? 0);
        },

        get totalAmount() {
            return Math.max(0, this.cartSubtotal + this.shippingPrice - Number(this.voucherDiscount || 0));
        },

        formatCurrency(value) {
            return new Intl.NumberFormat('id-ID').format(Number(value || 0));
        },

        previewVoucher(immediate = false) {
            if (!this.voucherPreviewUrl) {
                return;
            }

            if (this.previewTimer) {
                window.clearTimeout(this.previewTimer);
            }

            const run = () => {
                const code = String(this.voucherCode || '').trim();

                if (!code) {
                    this.voucherDiscount = 0;
                    this.voucherError = '';
                    return;
                }

                fetch(`${this.voucherPreviewUrl}?voucher_code=${encodeURIComponent(code)}`, {
                    headers: { 'Accept': 'application/json' },
                })
                    .then((response) => response.json())
                    .then((data) => {
                        this.voucherDiscount = Number(data.discount_amount || 0);
                        this.voucherError = data.error || '';
                    })
                    .catch(() => {
                        this.voucherDiscount = 0;
                        this.voucherError = '';
                    });
            };

            if (immediate) {
                run();
                return;
            }

            this.previewTimer = window.setTimeout(run, 350);
        },
    }));

    Alpine.data('cartPage', (items = []) => ({
        items: Object.fromEntries(items.map((item) => [Number(item.id), { ...item }])),
        syncTimers: {},

        formatCurrency(value) {
            return new Intl.NumberFormat('id-ID').format(Number(value || 0));
        },

        get totalQty() {
            return Object.values(this.items)
                .reduce((total, item) => total + Number(item.qty || 0), 0);
        },

        get subtotalAmount() {
            return Object.values(this.items)
                .reduce((total, item) => total + (Number(item.qty || 0) * Number(item.unitPrice || 0)), 0);
        },

        changeQty(itemId, delta) {
            const item = this.items[itemId];
            if (!item) {
                return;
            }

            this.setQty(itemId, Number(item.qty || 0) + Number(delta || 0));
        },

        setQty(itemId, value) {
            const item = this.items[itemId];
            if (!item) {
                return;
            }

            const min = 1;
            const max = Math.min(Number(item.maxQty || 1), 20);
            let nextValue = Number(value);

            if (Number.isNaN(nextValue)) {
                nextValue = min;
            }

            nextValue = Math.max(min, Math.min(max, nextValue));
            item.qty = nextValue;

            this.queueSync(itemId);
        },

        queueSync(itemId) {
            if (this.syncTimers[itemId]) {
                window.clearTimeout(this.syncTimers[itemId]);
            }

            this.syncTimers[itemId] = window.setTimeout(() => {
                this.syncItem(itemId);
            }, 350);
        },

        async syncItem(itemId) {
            const item = this.items[itemId];
            if (!item?.updateUrl) {
                return;
            }

            try {
                await fetch(item.updateUrl, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '',
                    },
                    body: JSON.stringify({ qty: item.qty }),
                });
            } catch (error) {
                // silent fail; the server will enforce stock rules on next reload
            }
        },
    }));

    Alpine.data('bannerCarousel', (items = []) => ({
        items,
        activeIndex: 0,
        touchStartX: 0,
        intervalId: null,

        init() {
            if (this.items.length > 1) {
                this.startAutoplay();
            }
        },

        destroy() {
            this.stopAutoplay();
        },

        startAutoplay() {
            if (this.items.length < 2) {
                return;
            }

            this.stopAutoplay();
            this.intervalId = window.setInterval(() => this.next(), 5500);
        },

        stopAutoplay() {
            if (this.intervalId) {
                window.clearInterval(this.intervalId);
                this.intervalId = null;
            }
        },

        next() {
            if (this.items.length < 2) {
                return;
            }

            this.activeIndex = (this.activeIndex + 1) % this.items.length;
        },

        prev() {
            if (this.items.length < 2) {
                return;
            }

            this.activeIndex = (this.activeIndex - 1 + this.items.length) % this.items.length;
        },

        goTo(index) {
            this.activeIndex = Number(index);
        },

        get translateX() {
            return `translateX(-${this.activeIndex * 100}%)`;
        },

        onTouchStart(event) {
            this.touchStartX = event.changedTouches[0]?.clientX ?? 0;
        },

        onTouchEnd(event) {
            const endX = event.changedTouches[0]?.clientX ?? 0;
            const delta = endX - this.touchStartX;

            if (Math.abs(delta) < 40) {
                return;
            }

            if (delta < 0) {
                this.next();
                return;
            }

            this.prev();
        },
    }));

    Alpine.data('productConfigurator', (config) => ({
        variants: config.variants ?? [],
        coverImage: config.coverImage ?? null,
        currentImage: config.coverImage ?? null,
        qty: Number(config.qty ?? 1),
        selectedColor: null,
        selectedSize: null,

        init() {
            const preferred = this.variants.find((variant) => Number(variant.stock_qty) > 0) ?? this.variants[0] ?? null;

            this.selectedColor = preferred?.color ?? null;
            this.selectedSize = preferred?.size ?? null;
            this.syncCurrentImage();
            this.clampQty();
        },

        get colors() {
            return [...new Map(this.variants.map((variant) => [variant.color, {
                name: variant.color,
                available: Number(variant.stock_qty) > 0,
            }])).values()];
        },

        sizesForSelectedColor() {
            return this.variants
                .filter((variant) => !this.selectedColor || variant.color === this.selectedColor)
                .map((variant) => ({
                    name: variant.size,
                    available: Number(variant.stock_qty) > 0,
                }))
                .filter((item, index, items) => items.findIndex((entry) => entry.name === item.name) === index);
        },

        selectColor(color) {
            this.selectedColor = color;

            if (!this.currentVariant) {
                const fallback = this.variants.find((variant) => variant.color === color);
                this.selectedSize = fallback?.size ?? null;
            }

            this.syncCurrentImage();
            this.clampQty();
        },

        selectSize(size) {
            this.selectedSize = size;
            this.syncCurrentImage();
            this.clampQty();
        },

        get currentVariant() {
            return this.variants.find((variant) => variant.color === this.selectedColor && variant.size === this.selectedSize)
                ?? null;
        },

        get selectedVariantId() {
            return this.currentVariant?.id ?? '';
        },

        get currentPrice() {
            return Number(this.currentVariant?.effective_price ?? 0);
        },

        get comparePrice() {
            const basePrice = Number(this.currentVariant?.original_price ?? 0);

            return this.currentVariant?.discount_percentage > 0 ? basePrice : null;
        },

        get discountPercentage() {
            return Number(this.currentVariant?.discount_percentage ?? 0);
        },

        get availableStock() {
            return Number(this.currentVariant?.stock_qty ?? 0);
        },

        get galleryItems() {
            const items = this.variants
                .map((variant) => {
                    const image = Array.isArray(variant.images)
                        ? variant.images.find((entry) => Boolean(entry))
                        : null;

                    if (!image) {
                        return null;
                    }

                    return {
                        id: Number(variant.id),
                        image,
                        color: variant.color,
                        size: variant.size,
                    };
                })
                .filter(Boolean);

            if (items.length > 0) {
                return items;
            }

            return this.coverImage ? [{ id: 0, image: this.coverImage, color: null, size: null }] : [];
        },

        selectGalleryItem(item) {
            if (!item) {
                return;
            }

            if (item.id) {
                this.selectedColor = item.color ?? this.selectedColor;
                this.selectedSize = item.size ?? this.selectedSize;
            }

            this.currentImage = item.image ?? this.currentImage;
            this.clampQty();
        },

        decrementQty() {
            this.qty = Math.max(1, this.qty - 1);
        },

        incrementQty() {
            const limit = Math.max(1, this.availableStock || 20);
            this.qty = Math.min(limit, this.qty + 1);
        },

        clampQty() {
            const limit = Math.max(1, this.availableStock || 20);
            this.qty = Math.min(Math.max(1, this.qty), limit);
        },

        syncCurrentImage() {
            const variantImages = Array.isArray(this.currentVariant?.images) ? this.currentVariant.images.filter(Boolean) : [];
            this.currentImage = variantImages[0] ?? this.coverImage ?? null;
        },
    }));
});

Alpine.start();
