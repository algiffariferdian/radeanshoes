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
