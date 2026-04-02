import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

const wishlistStorageKey = 'radeanshoes:wishlist';

const readWishlist = () => {
    try {
        const raw = window.localStorage.getItem(wishlistStorageKey);
        const parsed = raw ? JSON.parse(raw) : [];

        return Array.isArray(parsed) ? parsed : [];
    } catch {
        return [];
    }
};

const writeWishlist = (items) => {
    window.localStorage.setItem(wishlistStorageKey, JSON.stringify(items));
};

document.addEventListener('alpine:init', () => {
    Alpine.store('wishlist', {
        items: readWishlist(),

        count() {
            return this.items.length;
        },

        has(productId) {
            return this.items.some((item) => Number(item.id) === Number(productId));
        },

        toggle(product) {
            const exists = this.has(product.id);

            this.items = exists
                ? this.items.filter((item) => Number(item.id) !== Number(product.id))
                : [
                    {
                        id: Number(product.id),
                        name: product.name,
                        url: product.url,
                        image: product.image,
                        price: product.price,
                    },
                    ...this.items,
                ].slice(0, 24);

            writeWishlist(this.items);
        },
    });

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

        get galleryImages() {
            const images = [
                this.coverImage,
                ...(Array.isArray(this.currentVariant?.images) ? this.currentVariant.images : []),
            ].filter(Boolean);

            return [...new Set(images)];
        },

        selectGalleryImage(image) {
            this.currentImage = image;
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
