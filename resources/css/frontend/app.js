var feHeaderCloseDropdowns = function() {};

document.addEventListener('DOMContentLoaded', function() {
    initMobileMenu();
    initHeaderNavDropdowns();
    initScrollAnimations();
    initStylePicker();
    initPlatformSlider();
});

// --- Mobile Menu ---

function initMobileMenu() {
    var toggle = document.getElementById('feHeaderMenuToggle');
    var nav = document.getElementById('feHeaderNav');
    var overlay = document.getElementById('feHeaderNavOverlay');
    if (!toggle || !nav) return;

    var mobileBreakpoint = 768;
    var scrollY = 0;
    // Mobile browsers often fire `resize` when only the viewport height changes (e.g. URL bar).
    // Closing the drawer on every such event caused erratic scroll / jump-to-top; only react to width changes.
    var lastInnerWidth = window.innerWidth;

    function readRestoreScrollY() {
        var topStyle = document.body.style.top;
        if (topStyle && /^-\d+(\.\d+)?px$/.test(topStyle)) {
            return Math.round(Math.abs(parseFloat(topStyle)));
        }
        return scrollY;
    }

    function closeMenu() {
        // Only undo scroll lock / restore position when the mobile drawer was actually open.
        // Otherwise every desktop nav link click ran scrollTo(0, 0) because scrollY was never set.
        if (!nav.classList.contains('is-open')) {
            return;
        }
        var restoreY = readRestoreScrollY();
        feHeaderCloseDropdowns();
        nav.classList.remove('is-open');
        toggle.classList.remove('is-open');
        toggle.setAttribute('aria-expanded', 'false');
        if (overlay) { overlay.classList.remove('is-open'); overlay.setAttribute('aria-hidden', 'true'); }
        document.body.classList.remove('fe-nav-open');
        document.body.style.top = '';
        window.scrollTo(0, restoreY);
        requestAnimationFrame(function() {
            window.scrollTo(0, restoreY);
        });
    }

    function openMenu() {
        scrollY = window.scrollY || window.pageYOffset;
        document.body.style.top = '-' + scrollY + 'px';
        feHeaderCloseDropdowns();
        nav.classList.add('is-open');
        toggle.classList.add('is-open');
        toggle.setAttribute('aria-expanded', 'true');
        if (overlay) { overlay.classList.add('is-open'); overlay.setAttribute('aria-hidden', 'false'); }
        document.body.classList.add('fe-nav-open');
    }

    function isMobile() { return window.innerWidth <= mobileBreakpoint; }

    window.addEventListener('resize', function() {
        var w = window.innerWidth;
        if (w === lastInnerWidth) {
            return;
        }
        lastInnerWidth = w;
        if (isMobile()) closeMenu();
    });

    toggle.addEventListener('click', function() {
        if (!isMobile()) return;
        if (nav.classList.contains('is-open')) closeMenu();
        else openMenu();
    });

    if (overlay) overlay.addEventListener('click', closeMenu);

    var closeBtn = document.getElementById('feHeaderNavClose');
    if (closeBtn) closeBtn.addEventListener('click', closeMenu);

    nav.querySelectorAll('[data-fe-close-on-nav-click]').forEach(function(link) {
        link.addEventListener('click', closeMenu);
    });
}

// --- Header dropdowns (only one open at a time) ---

function initHeaderNavDropdowns() {
    var dropdowns = Array.prototype.slice.call(document.querySelectorAll('[data-fe-header-dropdown]'));

    function closeDropdown(dropdown) {
        var trigger = dropdown.querySelector('[data-fe-header-dropdown-trigger]');
        var menu = dropdown.querySelector('[data-fe-header-dropdown-menu]');
        dropdown.classList.remove('is-open');
        if (trigger) {
            trigger.setAttribute('aria-expanded', 'false');
        }
        if (menu) {
            menu.setAttribute('aria-hidden', 'true');
        }
    }

    function openDropdown(dropdown) {
        var trigger = dropdown.querySelector('[data-fe-header-dropdown-trigger]');
        var menu = dropdown.querySelector('[data-fe-header-dropdown-menu]');
        dropdown.classList.add('is-open');
        if (trigger) {
            trigger.setAttribute('aria-expanded', 'true');
        }
        if (menu) {
            menu.setAttribute('aria-hidden', 'false');
        }
    }

    function closeAllDropdowns() {
        dropdowns.forEach(function(dropdown) {
            closeDropdown(dropdown);
        });
    }

    feHeaderCloseDropdowns = closeAllDropdowns;

    if (!dropdowns.length) {
        return;
    }

    dropdowns.forEach(function(dropdown) {
        var trigger = dropdown.querySelector('[data-fe-header-dropdown-trigger]');
        var menu = dropdown.querySelector('[data-fe-header-dropdown-menu]');
        if (!trigger || !menu) return;

        trigger.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();

            if (dropdown.classList.contains('is-open')) {
                closeDropdown(dropdown);
                return;
            }

            closeAllDropdowns();
            openDropdown(dropdown);
        });

        dropdown.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    });

    document.addEventListener('click', closeAllDropdowns);
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeAllDropdowns();
        }
    });

    var closeBtn = document.getElementById('feHeaderNavClose');
    if (closeBtn) closeBtn.addEventListener('click', closeAllDropdowns);
}

// --- Scroll Animations (IntersectionObserver) ---

function initScrollAnimations() {
    var animElements = document.querySelectorAll('.fe-scroll-animate');
    var feElements = document.querySelectorAll(
        '.fe-capabilities-visual, .fe-goodnews-visual, .fe-mobile-preview-overlay, .fe-partner-visual, .fe-products-grid'
    );
    if (!animElements.length && !feElements.length) return;

    // One-way reveal: do not remove is-visible when leaving view — toggling on mobile can fight
    // scroll anchoring and feels like the page snaps or bounces. Unobserve after first show.
    var observer = new IntersectionObserver(function(entries) {
        entries.forEach(function(entry) {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-visible');
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.08, rootMargin: '0px 0px 8% 0px' });

    requestAnimationFrame(function() {
        feElements.forEach(function(el) { observer.observe(el); });
        animElements.forEach(function(el) { observer.observe(el); });
    });
}

// --- Style Picker (Homepage) ---

function initStylePicker() {
    var data = window.feStyleData;
    if (!data) return;

    var stylePalettes = data.stylePalettes || {};
    var colorImageMap = data.colorImageMap || {};
    var defaultPreview = data.defaultPreview || '';

    var styleTriggers = document.querySelectorAll('.fe-style-trigger');
    var colorPicker = document.getElementById('feStyleColorPicker');
    if (!styleTriggers.length || !colorPicker) return;

    var activeStyle = null;
    var selectedBar = document.getElementById('feStyleSelectedBar');
    var selectedPill = document.getElementById('feStyleSelectedPill');
    var previewImage = document.getElementById('feStylePreviewImage');
    var previewDesktopMobile = document.getElementById('feStylePreviewDesktopMobile');
    var variantsImage = document.getElementById('feStyleVariantsImage');

    function onPreviewLoad(el) { el.classList.add('is-loaded'); }
    if (previewImage) previewImage.addEventListener('load', function() { onPreviewLoad(this); });
    if (previewDesktopMobile) previewDesktopMobile.addEventListener('load', function() { onPreviewLoad(this); });

    function onSwatchClick() {
        var color = this.getAttribute('data-hex');
        var colorName = this.getAttribute('data-name') || color;
        var colorImg = this.getAttribute('data-image') || colorImageMap[colorName] || defaultPreview;
        if (!activeStyle || !color) return;

        // Theme accent stays on :root; only the visible selected bar uses the swatch color.
        if (selectedBar && selectedPill) {
            selectedBar.querySelector('.fe-style-selected-label').textContent = 'Style' + activeStyle;
            selectedPill.textContent = colorName;
            selectedBar.style.setProperty('--fe-style-bar-accent', color);
            selectedBar.classList.add('is-visible');
        }
        if (variantsImage) variantsImage.classList.add('has-content');
        if (previewImage) previewImage.classList.remove('is-loaded');
        if (previewDesktopMobile) previewDesktopMobile.classList.remove('is-loaded');
        setTimeout(function() {
            if (previewImage) previewImage.src = colorImg;
            if (previewDesktopMobile) previewDesktopMobile.src = colorImg;
        }, 350);
    }

    function applyPaletteToSwatches(styleId) {
        var palette = stylePalettes[styleId];
        var container = document.querySelector('.fe-style-color-swatches');
        if (!palette || !container) return;
        container.innerHTML = '';
        palette.forEach(function(c) {
            var span = document.createElement('span');
            span.className = 'fe-style-swatch';
            span.setAttribute('data-hex', c.hex);
            span.setAttribute('data-name', c.name || c.hex);
            span.setAttribute('data-image', c.image || '');
            span.style.background = c.hex;
            span.addEventListener('click', onSwatchClick);
            container.appendChild(span);
        });
    }

    styleTriggers.forEach(function(trigger) {
        trigger.addEventListener('click', function() {
            activeStyle = this.getAttribute('data-style');
            colorPicker.classList.add('is-open');
            colorPicker.setAttribute('data-active-style', activeStyle);
            applyPaletteToSwatches(activeStyle);
        });
    });
}

// --- Platform page image slider ---

function initPlatformSlider() {
    document.querySelectorAll('[data-fe-platform-slider]').forEach(function(root) {
        if (root._fePlatformSliderInit) return;
        root._fePlatformSliderInit = true;

        var track = root.querySelector('[data-fe-platform-track]');
        var originals = track.querySelectorAll('.fe-platform-slide:not(.fe-platform-slide--clone)');
        var dots = root.querySelectorAll('[data-fe-platform-dot]');
        var prev = root.querySelector('[data-fe-platform-prev]');
        var next = root.querySelector('[data-fe-platform-next]');
        var n = originals.length;
        var pTotal = n + 1;
        if (n < 2 || !track) return;

        var logicalIdx = 0;
        var wrapPending = false;
        var autoplayMs = parseInt(root.getAttribute('data-autoplay-ms') || '0', 10) || 0;
        var timer = null;

        function setTrackPositionPhysical(physical, instant) {
            if (instant) {
                track.classList.add('fe-platform-slider-track--no-transition');
            }
            track.style.transform = 'translateX(calc(-' + physical + ' * 100% / ' + pTotal + '))';
            if (instant) {
                void track.offsetWidth;
                track.classList.remove('fe-platform-slider-track--no-transition');
            }
        }

        function updateAriaAndDots() {
            originals.forEach(function(s, j) {
                s.setAttribute('aria-hidden', j === logicalIdx ? 'false' : 'true');
            });
            dots.forEach(function(d, j) {
                var on = j === logicalIdx;
                d.classList.toggle('is-active', on);
                d.setAttribute('aria-selected', on ? 'true' : 'false');
            });
        }

        function onTrackTransitionEnd(e) {
            if (e.target !== track || e.propertyName !== 'transform') {
                return;
            }
            if (!wrapPending) {
                return;
            }
            wrapPending = false;
            setTrackPositionPhysical(0, true);
            updateAriaAndDots();
        }

        track.addEventListener('transitionend', onTrackTransitionEnd);

        function advanceNext() {
            if (logicalIdx === n - 1) {
                setTrackPositionPhysical(n, false);
                wrapPending = true;
                logicalIdx = 0;
                updateAriaAndDots();
            } else {
                logicalIdx++;
                setTrackPositionPhysical(logicalIdx, false);
                updateAriaAndDots();
            }
        }

        function goPrev() {
            logicalIdx = (logicalIdx - 1 + n) % n;
            setTrackPositionPhysical(logicalIdx, false);
            updateAriaAndDots();
        }

        function goToLogical(j) {
            var target = (j + n) % n;
            if (target === logicalIdx) {
                return;
            }
            wrapPending = false;
            logicalIdx = target;
            setTrackPositionPhysical(logicalIdx, false);
            updateAriaAndDots();
        }

        function startAutoplay() {
            stopAutoplay();
            if (autoplayMs > 0) {
                timer = setInterval(function() { advanceNext(); }, autoplayMs);
            }
        }

        function stopAutoplay() {
            if (timer) {
                clearInterval(timer);
                timer = null;
            }
        }

        setTrackPositionPhysical(0, true);
        updateAriaAndDots();

        if (prev) prev.addEventListener('click', function() { stopAutoplay(); goPrev(); startAutoplay(); });
        if (next) next.addEventListener('click', function() { stopAutoplay(); advanceNext(); startAutoplay(); });

        dots.forEach(function(dot) {
            dot.addEventListener('click', function() {
                var j = parseInt(dot.getAttribute('data-fe-platform-dot') || '0', 10);
                if (!isNaN(j)) {
                    stopAutoplay();
                    goToLogical(j);
                    startAutoplay();
                }
            });
        });

        root.addEventListener('mouseenter', stopAutoplay);
        root.addEventListener('mouseleave', startAutoplay);
        root.addEventListener('focusin', stopAutoplay);
        root.addEventListener('focusout', startAutoplay);

        startAutoplay();
    });
}
