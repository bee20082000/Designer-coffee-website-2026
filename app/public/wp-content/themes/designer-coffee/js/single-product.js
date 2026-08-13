/**
 * Designer Coffee — Single Product Page JS (js/single-product.js)
 * Nomad Coffee Style Split-Screen Layout (Left: Image Stack, Right: Sticky Info Panel)
 *
 * @package DesignerCoffee
 */
document.addEventListener('DOMContentLoaded', function () {

    /* --------------------------------------------------------------------------
       0. MAIN PHOTO & VIDEO GALLERY ARROW SLIDER (MIXED GALLERY PLUGIN SUPPORT)
       -------------------------------------------------------------------------- */
    const gallerySlider = document.getElementById('sp-gallery-slider');
    if (gallerySlider) {
        const mainBox = gallerySlider.querySelector('.sp-gallery-main-box');
        const prevBtn = gallerySlider.querySelector('.sp-gallery-prev');
        const nextBtn = gallerySlider.querySelector('.sp-gallery-next');
        const dots = gallerySlider.querySelectorAll('.sp-gallery-dot');
        
        let items = [];
        try {
            const rawItems = gallerySlider.getAttribute('data-gallery-items') || gallerySlider.getAttribute('data-images');
            if (rawItems) {
                const parsed = JSON.parse(rawItems);
                items = parsed.map(function(item) {
                    if (typeof item === 'string') {
                        return { type: 'image', url: item };
                    }
                    return item;
                });
            }
        } catch (e) {
            items = [];
        }

        let currentIndex = 0;

        function updateGalleryItem(index) {
            if (!mainBox || !items || items.length <= 0) return;
            
            if (index < 0) index = items.length - 1;
            if (index >= items.length) index = 0;
            
            currentIndex = index;
            const currentItem = items[currentIndex];
            
            if (!currentItem || !currentItem.url) return;

            if (currentItem.type === 'youtube') {
                const oldImg = document.getElementById('sp-main-img');
                if (oldImg) oldImg.remove();
                let mainFrame = document.getElementById('sp-main-frame');
                if (!mainFrame) {
                    mainFrame = document.createElement('iframe');
                    mainFrame.id = 'sp-main-frame';
                    mainFrame.setAttribute('allow', 'autoplay; encrypted-media; picture-in-picture');
                    mainFrame.setAttribute('allowfullscreen', 'true');
                    mainFrame.setAttribute('title', 'Product video');
                    mainBox.prepend(mainFrame);
                }
                mainFrame.src = currentItem.url;
            } else {
                const oldFrame = document.getElementById('sp-main-frame');
                if (oldFrame) oldFrame.remove();

                let mainImg = document.getElementById('sp-main-img');
                if (!mainImg) {
                    mainImg = document.createElement('img');
                    mainImg.id = 'sp-main-img';
                    mainImg.alt = 'Product image';
                    mainBox.prepend(mainImg);
                }
                
                mainImg.style.opacity = '0';
                setTimeout(function () {
                    mainImg.src = currentItem.url;
                    mainImg.style.opacity = '1';
                }, 150);
            }

            if (dots && dots.length > 0) {
                dots.forEach((dot, idx) => {
                    if (idx === currentIndex) {
                        dot.classList.add('active');
                    } else {
                        dot.classList.remove('active');
                    }
                });
            }
        }

        if (prevBtn) {
            prevBtn.addEventListener('click', function (e) {
                e.preventDefault();
                updateGalleryItem(currentIndex - 1);
            });
        }

        if (nextBtn) {
            nextBtn.addEventListener('click', function (e) {
                e.preventDefault();
                updateGalleryItem(currentIndex + 1);
            });
        }

        if (dots && dots.length > 0) {
            dots.forEach(function (dot) {
                dot.addEventListener('click', function (e) {
                    e.preventDefault();
                    const idx = parseInt(this.getAttribute('data-index'), 10) || 0;
                    updateGalleryItem(idx);
                });
            });
        }
    }


    const spSummary = document.querySelector('.sp-right-info-col') || document.querySelector('.sp-summary-container');
    if (!spSummary) return;


    /* --------------------------------------------------------------------------
       1. QUANTITY SELECTOR (+ / -) & DYNAMIC PRICE CALCULATION
       -------------------------------------------------------------------------- */
    const qtyInput = spSummary.querySelector('.sp-qty-input');
    const minusBtn = spSummary.querySelector('.sp-qty-minus');
    const plusBtn = spSummary.querySelector('.sp-qty-plus');
    const sizeSelect = spSummary.querySelector('.sp-size-select');
    const priceWrapper = document.getElementById('sp-dynamic-price');
    const rawPriceAttr = spSummary.getAttribute('data-raw-price');
    const basePrice = parseFloat(rawPriceAttr) || 0;

    let variationsData = [];
    try {
        const varString = spSummary.getAttribute('data-variations');
        if (varString) {
            variationsData = JSON.parse(varString);
        }
    } catch (err) {
        variationsData = [];
    }

    function strClean(str) {
        return (str || '').toString().toLowerCase().replace(/[^a-z0-9]/g, '');
    }

    function calculateUnitPrice(sizeVal) {
        if (sizeVal && Array.isArray(variationsData) && variationsData.length > 0) {
            const matchedVar = variationsData.find(function (v) {
                if (!v.attributes) return false;
                return Object.values(v.attributes).some(function (attrVal) {
                    return strClean(attrVal) === strClean(sizeVal);
                });
            });

            if (matchedVar && matchedVar.display_price) {
                return parseFloat(matchedVar.display_price);
            }
        }

        if (basePrice > 0) {
            let multiplier = 1;
            const cleanVal = strClean(sizeVal);
            if (cleanVal.includes('500g')) {
                multiplier = 1.8;
            } else if (cleanVal.includes('1kg') || cleanVal.includes('1000g')) {
                multiplier = 3.2;
            } else if (cleanVal.includes('250g')) {
                multiplier = 1;
            }
            return basePrice * multiplier;
        }

        return basePrice;
    }

    const updateTotalPrice = function () {
        if (!priceWrapper) return;
        const qty = qtyInput ? (parseInt(qtyInput.value, 10) || 1) : 1;
        const sizeVal = sizeSelect ? sizeSelect.value : '';
        const unitPrice = calculateUnitPrice(sizeVal);

        if (unitPrice > 0) {
            const totalPrice = unitPrice * qty;
            const formattedTotal = Math.round(totalPrice).toLocaleString('vi-VN');
            priceWrapper.innerHTML = '<span class="woocommerce-Price-amount amount"><bdi>' + formattedTotal + '&nbsp;<span class="woocommerce-Price-currencySymbol">VND</span></bdi></span>';
        }
    };

    if (qtyInput) {
        if (minusBtn) {
            minusBtn.addEventListener('click', function (e) {
                e.preventDefault();
                let currentVal = parseInt(qtyInput.value, 10) || 1;
                let minVal = parseInt(qtyInput.getAttribute('min'), 10) || 1;
                if (currentVal > minVal) {
                    qtyInput.value = currentVal - 1;
                    updateTotalPrice();
                }
            });
        }

        if (plusBtn) {
            plusBtn.addEventListener('click', function (e) {
                e.preventDefault();
                let currentVal = parseInt(qtyInput.value, 10) || 1;
                let maxVal = parseInt(qtyInput.getAttribute('max'), 10) || 99;
                if (currentVal < maxVal) {
                    qtyInput.value = currentVal + 1;
                    updateTotalPrice();
                }
            });
        }

        qtyInput.addEventListener('input', updateTotalPrice);
        qtyInput.addEventListener('change', updateTotalPrice);
    }

    if (sizeSelect) {
        sizeSelect.addEventListener('change', function () {
            const val = this.value;
            if (val) {
                updateTotalPrice();

                // Sync with native WooCommerce variation select if present
                const wooSelects = document.querySelectorAll('select[name^="attribute_"]');
                wooSelects.forEach(function (wooSelect) {
                    if (wooSelect.options) {
                        for (let i = 0; i < wooSelect.options.length; i++) {
                            if (strClean(wooSelect.options[i].value) === strClean(val) || strClean(wooSelect.options[i].text) === strClean(val)) {
                                wooSelect.selectedIndex = i;
                                wooSelect.dispatchEvent(new Event('change', { bubbles: true }));
                                break;
                            }
                        }
                    }
                });
            }
        });
    }

    // Initial price calculation
    updateTotalPrice();

    /* --------------------------------------------------------------------------
       4. RADIO BUTTON GRIND SELECTION LIST LISTENER
       -------------------------------------------------------------------------- */
    const grindRadioItems = spSummary.querySelectorAll('.sp-grind-radio-item');
    if (grindRadioItems && grindRadioItems.length > 0) {
        grindRadioItems.forEach(function (item) {
            const radioInput = item.querySelector('input[type="radio"]');
            if (radioInput) {
                radioInput.addEventListener('change', function () {
                    grindRadioItems.forEach(function (el) {
                        el.classList.remove('is-active');
                    });
                    if (this.checked) {
                        item.classList.add('is-active');
                    }
                });
            }
        });
    }



    /* --------------------------------------------------------------------------
       3. AJAX ADD TO CART BUTTON (NOMAD PILL BUTTON)
       -------------------------------------------------------------------------- */
    const addToCartBtn = spSummary.querySelector('.sp-add-to-cart-pill-btn') || spSummary.querySelector('.sp-add-to-cart-btn');
    if (addToCartBtn) {
        addToCartBtn.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();

            if (addToCartBtn.classList.contains('disabled') || addToCartBtn.classList.contains('is-loading')) {
                return;
            }

            const productId = addToCartBtn.getAttribute('data-product-id');
            const qty = qtyInput ? (parseInt(qtyInput.value, 10) || 1) : 1;
            const selectedSize = sizeSelect ? sizeSelect.value : '';
            const btnSpan = addToCartBtn.querySelector('span');

            if (!productId) return;

            // Trigger Fly-To-Cart parabolic arc animation
            const spMainImg = document.getElementById('sp-main-img') || document.querySelector('.sp-gallery-item img');
            if (spMainImg && typeof window.animateFlyToCart === 'function') {
                window.animateFlyToCart(spMainImg);
            }

            // GSAP Click animation
            if (typeof gsap !== 'undefined') {
                gsap.timeline()
                    .to(addToCartBtn, { scale: 0.96, duration: 0.08 })
                    .to(addToCartBtn, { scale: 1, duration: 0.1 });
            }

            addToCartBtn.classList.add('is-loading');
            if (btnSpan) btnSpan.textContent = 'Adding...';

            const ajaxUrl = (typeof dc_ajax !== 'undefined' && dc_ajax.ajax_url)
                ? dc_ajax.ajax_url
                : (window.location.origin + '/wp-admin/admin-ajax.php');

            const grindRadio = spSummary.querySelector('input[name="cgs_grind"]:checked') || spSummary.querySelector('input[name="sp_grind_choice"]:checked');
            const grindVal = grindRadio ? grindRadio.value : '';

            const formData = new FormData();
            formData.append('action', 'dc_add_to_cart');
            formData.append('nonce', dc_ajax.nonce);
            formData.append('product_id', productId);
            formData.append('quantity', qty);
            if (selectedSize) {
                formData.append('size', selectedSize);
            }
            if (grindVal) {
                formData.append('cgs_grind', grindVal);
            }

            fetch(ajaxUrl, {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                addToCartBtn.classList.remove('is-loading');

                if (!data.success) {
                    const errMsg = (data.data && data.data.message) ? data.data.message : 'Please select your options.';
                    alert(errMsg);
                    if (btnSpan) btnSpan.textContent = 'Add to Cart';
                    return;
                }

                addToCartBtn.classList.add('is-added');
                if (btnSpan) btnSpan.textContent = 'Added ✓';


                // Open Header Minicart Drawer
                const cartWrapper = document.querySelector('.header-cart-wrapper');
                if (cartWrapper) {
                    cartWrapper.classList.add('is-open');
                }

                // Fragment update
                const countVal = (typeof data.count !== 'undefined') ? data.count : (data.data ? data.data.count : 0);
                const fragments = data.fragments || (data.data ? data.data.fragments : null);

                if (fragments && typeof window.applyCartFragmentsWithoutFlashing === 'function') {
                    window.applyCartFragmentsWithoutFlashing(fragments, countVal);
                } else {
                    fetch(window.location.origin + '/?wc-ajax=get_refreshed_fragments', { method: 'POST' })
                        .then(res => res.json())
                        .then(fragData => {
                            if (fragData && fragData.fragments && typeof window.applyCartFragmentsWithoutFlashing === 'function') {
                                window.applyCartFragmentsWithoutFlashing(fragData.fragments);
                            }
                        });
                }

                setTimeout(function () {
                    addToCartBtn.classList.remove('is-added');
                    if (btnSpan) btnSpan.textContent = 'Add to Cart';
                }, 2000);
            })
            .catch(() => {
                addToCartBtn.classList.remove('is-loading');
                if (btnSpan) btnSpan.textContent = 'Add to Cart';
            });

        });
    }

});
