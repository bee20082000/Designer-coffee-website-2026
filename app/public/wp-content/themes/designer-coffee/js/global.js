/**
 * Designer Coffee — global.js
 * Loaded on EVERY page.
 *
 * Responsibilities:
 *  1. Site loader — red screen fills a white progress bar, then GSAP morphs
 *     #site-loader into the .intro-brand-screen shape, fades, reveals the page.
 *     Dispatches "dc:loaderDone" when complete so home.js can animate content.
 *  2. Sticky navigation "scrolled" class
 *  3. GSAP header entrance (fires after loader exits)
 *  4. Mobile menu toggle
 *  5. Back-to-top button
 *  6. Smooth anchor scrolling
 */

document.addEventListener('DOMContentLoaded', function () {
  'use strict';

  function dispatch(name) {
    setTimeout(function () {
      document.dispatchEvent(new CustomEvent(name));
    }, 50); // Defer by 50ms to ensure all other files (home.js) have registered their listeners
  }

  /* =========================================================================
     PRESERVE CART STATE (ANTI-FLASHING) & FLY-TO-CART ANIMATION HELPERS
     ========================================================================= */
  window.applyCartFragmentsWithoutFlashing = function (fragments, count) {
    if (!fragments) return;

    const currentCartWrapper = document.querySelector('.header-cart-wrapper');
    const isCartOpen = currentCartWrapper && currentCartWrapper.classList.contains('is-open');

    Object.keys(fragments).forEach(function (key) {
      if (key.includes('header-cart-wrapper')) {
        const targetEl = document.querySelector(key);
        if (!targetEl) return;

        const tempContainer = document.createElement('div');
        tempContainer.innerHTML = fragments[key];
        const newEl = tempContainer.firstElementChild || tempContainer;

        if (isCartOpen && newEl.classList) {
          newEl.classList.add('is-open');
        }

        targetEl.outerHTML = newEl.outerHTML || fragments[key];
      }
    });

    if (typeof count !== 'undefined' && count !== null) {
      document.querySelectorAll('.cart-count, .cart-count-badge').forEach(function (el) {
        el.textContent = count;
      });
      document.querySelectorAll('.cart-dropdown-count').forEach(function (el) {
        el.textContent = count + (count === 1 ? ' item' : ' items');
      });
    }
  };



  window.animateFlyToCart = function (sourceImgEl) {
    const cartIcon = document.querySelector('.nav-cart-text, .header-cart-wrapper, .header-cart-btn, .cart-toggle-btn, .header-cart-icon, .site-header .cart-icon, .header-bag-btn');
    if (!sourceImgEl || !cartIcon || typeof gsap === 'undefined') return;


    const imgRect = sourceImgEl.getBoundingClientRect();
    const cartRect = cartIcon.getBoundingClientRect();

    if (!imgRect.width || !cartRect.width) return;

    const flyImg = sourceImgEl.cloneNode(true);
    flyImg.classList.add('fly-to-cart-clone');
    flyImg.style.position = 'fixed';
    flyImg.style.top = imgRect.top + 'px';
    flyImg.style.left = imgRect.left + 'px';
    flyImg.style.width = imgRect.width + 'px';
    flyImg.style.height = imgRect.height + 'px';
    flyImg.style.zIndex = '999999';
    flyImg.style.pointerEvents = 'none';
    flyImg.style.borderRadius = '16px';
    flyImg.style.boxShadow = '0 12px 30px rgba(0,0,0,0.25)';
    flyImg.style.objectFit = 'cover';

    document.body.appendChild(flyImg);

    const targetX = cartRect.left + (cartRect.width / 2) - (imgRect.left + imgRect.width / 2);
    const targetY = cartRect.top + (cartRect.height / 2) - (imgRect.top + imgRect.height / 2);

    // Parabolic Arc Flight Path to Header Cart Icon
    gsap.timeline()
      .to(flyImg, {
        x: targetX,
        y: targetY,
        scale: 0.15,
        opacity: 0.85,
        rotation: 15,
        duration: 0.65,
        ease: 'power2.inOut',
        onComplete: function () {
          flyImg.remove();

          // Elastic bounce on header cart icon
          gsap.timeline()
            .to(cartIcon, { scale: 1.35, duration: 0.12, ease: 'power1.out' })
            .to(cartIcon, { scale: 1, duration: 0.25, ease: 'back.out(2)' });

          const badge = document.querySelector('.cart-count-badge, .cart-count');
          if (badge) {
            gsap.timeline()
              .to(badge, { scale: 1.5, backgroundColor: '#ffffff', color: '#CB2026', duration: 0.15 })
              .to(badge, { scale: 1, duration: 0.25, ease: 'back.out(2)' });
          }

        }
      });
  };


  /* =========================================================================
     BLOCK PAGE REFRESH ON ALL FORM SUBMISSIONS & REMOVE ITEM LINKS (CAPTURE PHASE)
     ========================================================================= */
  document.addEventListener('submit', function (e) {
    const form = e.target;
    if (form && (form.classList.contains('cart') || form.classList.contains('variations_form') || form.querySelector('.ajax-add-to-cart-btn, .sp-add-to-cart-btn'))) {
      e.preventDefault();
      e.stopPropagation();
      e.stopImmediatePropagation();

      const btn = form.querySelector('.ajax-add-to-cart-btn, .sp-add-to-cart-btn');
      if (btn && !btn.classList.contains('is-loading')) {
        btn.click();
      }
      return false;
    }
  }, true);







  /* =========================================================================
     3. SOLID RED FLOATING HEADER — Fixed at Top with 1.5rem Top Margin
     ========================================================================= */
  // Header background remains solid red wrapped around header content with zero scroll animation.

  /* =========================================================================
     5. DROPDOWN MENU & MINI-CART TOGGLE (DESKTOP & MOBILE TOUCH)
     ========================================================================= */
  const dropdownWrapper = document.querySelector('.menu-dropdown-wrapper');
  const dropdownTrigger = document.querySelector('.menu-dropdown-trigger');

  if (dropdownTrigger && dropdownWrapper) {
    dropdownTrigger.addEventListener('click', function (e) {
      e.stopPropagation();
      const isOpen = dropdownWrapper.classList.toggle('is-open');
      dropdownTrigger.setAttribute('aria-expanded', String(isOpen));
    });
  }

  // Body scroll lock helpers that preserve scrollbar width to avoid layout shift
  function getScrollbarGap() {
    return window.innerWidth - document.documentElement.clientWidth || 0;
  }

  function unlockBodyScroll() {
    const prev = document.body.getAttribute('data-prev-padding-right');
    if (typeof prev !== 'undefined' && prev !== null) {
      document.body.style.paddingRight = prev;
      document.body.removeAttribute('data-prev-padding-right');
    } else {
      document.body.style.paddingRight = '';
    }
    document.body.style.overflow = '';
  }

  // Handle Mini-Cart Toggle (Click Cart Icon -> Toggle Overlay Drawer) & Menu Outside Click
  document.addEventListener('click', function (e) {
    const cartWrapper = document.querySelector('.header-cart-wrapper');
    const cartTrigger = e.target.closest('.nav-cart-text, .nav-cart-btn, .header-cart-wrapper > button, .header-cart-wrapper > a');

    if (cartTrigger && cartWrapper) {
      e.preventDefault();
      e.stopPropagation();
      const isOpen = cartWrapper.classList.toggle('is-open');
      cartTrigger.setAttribute('aria-expanded', String(isOpen));
        if (isOpen) {
          lockBodyScroll();
        } else {
          unlockBodyScroll();
        }
    } else if (cartWrapper && !cartWrapper.contains(e.target)) {
      cartWrapper.classList.remove('is-open');
        unlockBodyScroll();
      const cartLink = cartWrapper.querySelector('.nav-cart-text, .nav-cart-btn');
      if (cartLink) cartLink.setAttribute('aria-expanded', 'false');
    }

    if (dropdownWrapper && !dropdownWrapper.contains(e.target) && dropdownTrigger) {
      dropdownWrapper.classList.remove('is-open');
      dropdownTrigger.setAttribute('aria-expanded', 'false');
    }
  });

  /* Lock page scrolling when mini-cart is hovered or open */
  const cartWrapperEl = document.querySelector('.header-cart-wrapper');
  if (cartWrapperEl) {
    cartWrapperEl.addEventListener('mouseenter', function () {
    });
    cartWrapperEl.addEventListener('mouseleave', function () {
      if (!cartWrapperEl.classList.contains('is-open')) {
        unlockBodyScroll();
      }
    });
  }


  /* =========================================================================
     MINI-CART QUANTITY (-) (+) BUTTON LISTENER
     ========================================================================= */
  document.addEventListener('click', function (e) {
    const minusBtn = e.target.closest('.mini-qty-minus');
    const plusBtn = e.target.closest('.mini-qty-plus');
    const btn = minusBtn || plusBtn;

    if (btn) {
      e.preventDefault();
      e.stopPropagation();

      const key = btn.getAttribute('data-cart_item_key');
      const container = btn.closest('.mini-qty-capsule');
      const valSpan = container ? container.querySelector('.mini-qty-val') : null;
      let currentQty = valSpan ? (parseInt(valSpan.textContent, 10) || 1) : 1;

      if (minusBtn) {
        currentQty = currentQty - 1;
      } else if (plusBtn) {
        currentQty = currentQty + 1;
      }

      const ajaxUrl = (typeof dc_ajax !== 'undefined' && dc_ajax.ajax_url)
        ? dc_ajax.ajax_url
        : (window.location.origin + '/wp-admin/admin-ajax.php');

      const formData = new FormData();
      formData.append('action', 'dc_update_cart_qty');
      formData.append('nonce', dc_ajax.nonce);
      formData.append('cart_item_key', key);
      formData.append('quantity', currentQty);

      fetch(ajaxUrl, {
        method: 'POST',
        body: formData
      })
        .then(res => res.json())
        .then(data => {
          if (data.success && data.data && data.data.fragments) {
            if (typeof window.applyCartFragmentsWithoutFlashing === 'function') {
              window.applyCartFragmentsWithoutFlashing(data.data.fragments, data.data.count);
            } else {
              location.reload();
            }
          }
        });

    }
  });

  /* Trap scroll inside mini cart body when open */
  document.addEventListener('wheel', function (e) {
    const cartBody = e.target.closest('.cart-dropdown-body');
    if (cartBody) {
      const scrollTop = cartBody.scrollTop;
      const scrollHeight = cartBody.scrollHeight;
      const height = cartBody.clientHeight;
      const delta = e.deltaY;

      if ((delta < 0 && scrollTop <= 0) || (delta > 0 && scrollTop + height >= scrollHeight)) {
        e.preventDefault();
      }
    }
  }, { passive: false });



  /* =========================================================================
     6. BACK-TO-TOP BUTTON
     ========================================================================= */
  const toTopBtn = document.querySelector('.to-top-btn');
  if (toTopBtn) {
    window.addEventListener('scroll', function () {
      toTopBtn.classList.toggle('visible', window.scrollY > 400);
    }, { passive: true });

    toTopBtn.addEventListener('click', function (e) {
      e.preventDefault();
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });
  }

  /* =========================================================================
     7. SMOOTH ANCHOR SCROLLING
     ========================================================================= */
  document.querySelectorAll('a[href^="#"]').forEach(function (anchor) {
    anchor.addEventListener('click', function (e) {
      const targetId = this.getAttribute('href');
      if (!targetId || targetId === '#') return;
      const targetEl = document.querySelector(targetId);
      if (targetEl) {
        e.preventDefault();
        const top = targetEl.getBoundingClientRect().top + window.pageYOffset - 80;
        window.scrollTo({ top: top, behavior: 'smooth' });
      }
    });
  });

  /* =========================================================================
     8. PRODUCT CARD QUANTITY SELECTOR & AJAX ADD TO CART
     ========================================================================= */
  document.addEventListener('click', function (e) {
    // A. Size Swatch Pill Click
    const sizePill = e.target.closest('.sp-size-pill');
    if (sizePill) {
      e.preventDefault();
      const box = sizePill.closest('.sp-size-selector-box');
      if (box) {
        const pills = box.querySelectorAll('.sp-size-pill');
        const valEl = box.querySelector('.sp-size-selected-val');
        const val = sizePill.getAttribute('data-value');

        pills.forEach(function (p) {
          p.classList.remove('active');
          p.setAttribute('aria-checked', 'false');
        });

        sizePill.classList.add('active');
        sizePill.setAttribute('aria-checked', 'true');

        if (valEl && val) {
          valEl.textContent = val;
        }

        // Dynamic Price Update based on selected size
        const summaryContainer = document.querySelector('.sp-summary-container');
        const priceWrapper = document.getElementById('sp-dynamic-price');
        if (summaryContainer && priceWrapper && val) {
          const rawPrice = parseFloat(summaryContainer.getAttribute('data-raw-price')) || 18.00;
          const currencySymbol = summaryContainer.getAttribute('data-currency-symbol') || '$';
          const variationsAttr = summaryContainer.getAttribute('data-variations');

          let updatedPriceHtml = '';

          // 1. Check if WooCommerce variations exist
          if (variationsAttr && variationsAttr !== '[]') {
            try {
              const variations = JSON.parse(variationsAttr);
              if (Array.isArray(variations)) {
                const matchedVar = variations.find(function (v) {
                  if (!v.attributes) return false;
                  return Object.values(v.attributes).some(function (attrVal) {
                    return String(attrVal).toLowerCase() === val.toLowerCase();
                  });
                });
                if (matchedVar && matchedVar.price_html) {
                  updatedPriceHtml = matchedVar.price_html;
                }
              }
            } catch (err) {
              console.log('Error parsing variations:', err);
            }
          }

          // 2. Fallback dynamic calculation if not matched in variations
          if (!updatedPriceHtml) {
            let multiplier = 1.0;
            const valLower = val.toLowerCase();
            if (valLower.includes('500g') || valLower.includes('500 g') || valLower.includes('lb')) {
              multiplier = 1.8;
            } else if (valLower.includes('1kg') || valLower.includes('1 kg') || valLower.includes('2lb')) {
              multiplier = 3.2;
            } else if (valLower.includes('250g')) {
              multiplier = 1.0;
            }
            const calcPrice = (rawPrice * multiplier).toFixed(2);
            updatedPriceHtml = '<span class="woocommerce-Price-amount amount"><bdi><span class="woocommerce-Price-currencySymbol">' + currencySymbol + '</span>' + calcPrice + '</bdi></span>';
          }

          priceWrapper.innerHTML = updatedPriceHtml;
        }
      }
      return;
    }


    // B. Quantity Minus Button
    const minusBtn = e.target.closest('.qty-minus, .minus');
    if (minusBtn) {
      e.preventDefault();
      e.stopPropagation();
      const selector = minusBtn.closest('.product-qty-selector, .quantity');
      if (selector) {
        const input = selector.querySelector('.product-qty-input, input.qty, input[type="number"]');
        if (input) {
          let currentVal = parseInt(input.value, 10) || 1;
          let minVal = parseInt(input.getAttribute('min'), 10) || 1;
          if (currentVal > minVal) {
            input.value = currentVal - 1;
            input.dispatchEvent(new Event('change', { bubbles: true }));
          }
        }
      }
      return;
    }

    // C. Quantity Plus Button
    const plusBtn = e.target.closest('.qty-plus, .plus');

    if (plusBtn) {
      e.preventDefault();
      e.stopPropagation();
      const selector = plusBtn.closest('.product-qty-selector, .sp-qty-selector, .quantity');
      if (selector) {
        const input = selector.querySelector('.product-qty-input, .sp-qty-input, input.qty, input[type="number"]');
        if (input) {
          let currentVal = parseInt(input.value, 10) || 1;
          let maxVal = parseInt(input.getAttribute('max'), 10) || 99;
          if (currentVal < maxVal) {
            input.value = currentVal + 1;
            input.dispatchEvent(new Event('change', { bubbles: true }));
          }
        }
      }
      return;
    }



    // 3. AJAX Add to Cart Button (Fly-To-Cart Animation & Anti-Flashing Cart Fragment Update)
    const addToCartBtn = e.target.closest('.ajax-add-to-cart-btn');
    if (addToCartBtn && !addToCartBtn.classList.contains('is-loading')) {
      e.preventDefault();
      e.stopPropagation();

      const productId = addToCartBtn.getAttribute('data-product-id');
      const card = addToCartBtn.closest('.shop-product-card, .carousel-card-item');
      const spSummary = addToCartBtn.closest('.sp-summary-container') || document.querySelector('.sp-summary-container');

      const qtyInput = card
        ? card.querySelector('.product-qty-input')
        : (spSummary ? spSummary.querySelector('.sp-qty-input') : null);

      const quantity = qtyInput ? (parseInt(qtyInput.value, 10) || 1) : 1;

      const activePill = spSummary ? spSummary.querySelector('.sp-size-pill.active') : null;
      const selectedSize = activePill ? activePill.getAttribute('data-value') : '';

      const btnText = addToCartBtn.querySelector('.btn-text') || addToCartBtn.querySelector('span');

      if (!productId) return;

      // Trigger Fly-To-Cart animation using product image
      const prodImg = (card ? card.querySelector('.product-image-box img, img') : null) || (spSummary ? document.querySelector('#sp-main-img, .sp-main-image-box img') : null);
      if (prodImg && typeof animateFlyToCart === 'function') {
        animateFlyToCart(prodImg);
      }

      // Button morph loading state
      if (typeof gsap !== 'undefined') {
        gsap.killTweensOf(addToCartBtn);
        gsap.timeline()
          .to(addToCartBtn, { scale: 0.94, duration: 0.08, ease: 'power1.out' })
          .to(addToCartBtn, { scale: 1, duration: 0.12, ease: 'power1.out' });
      }

      addToCartBtn.classList.add('is-loading');
      if (btnText) btnText.textContent = 'Adding...';

      const ajaxUrl = (typeof dc_ajax !== 'undefined' && dc_ajax.ajax_url)
        ? dc_ajax.ajax_url
        : (window.location.origin + '/wp-admin/admin-ajax.php');

      const formData = new FormData();
      formData.append('action', 'dc_add_to_cart');
      formData.append('nonce', dc_ajax.nonce);
      formData.append('product_id', productId);
      formData.append('quantity', quantity);
      if (selectedSize) {
        formData.append('size', selectedSize);
      }

      fetch(ajaxUrl, {
        method: 'POST',
        body: formData
      })
        .then(function (response) { return response.json(); })
        .then(function (data) {
          addToCartBtn.classList.remove('is-loading');
          if (btnText) btnText.textContent = 'Added ✓';
          addToCartBtn.classList.add('is-added');

          // Open cart drawer seamlessly
          const currentWrapper = document.querySelector('.header-cart-wrapper');
          if (currentWrapper) {
            currentWrapper.classList.add('is-open');
          }

          // Anti-flashing cart fragment update
          const countVal = (typeof data.count !== 'undefined') ? data.count : (data.data ? data.data.count : 0);
          const fragments = data.fragments || (data.data ? data.data.fragments : null);

          if (fragments) {
            applyCartFragmentsWithoutFlashing(fragments, countVal);
          } else {
            fetch(window.location.origin + '/?wc-ajax=get_refreshed_fragments', { method: 'POST' })
              .then(function (res) { return res.json(); })
              .then(function (fragData) {
                if (fragData && fragData.fragments) {
                  applyCartFragmentsWithoutFlashing(fragData.fragments);
                }
              });
          }


          setTimeout(function () {
            if (btnText) btnText.textContent = 'Add to Cart';
            addToCartBtn.classList.remove('is-added');
          }, 2000);
        })
        .catch(function () {
          addToCartBtn.classList.remove('is-loading');
          if (btnText) btnText.textContent = 'Add to Cart';
        });
    }
    // 4. AJAX Remove Cart Item (Zero Page Refresh, Real-Time Fragment Update)
    const removeBtn = e.target.closest('.cart-item-remove, a[href*="remove_item"], a[href*="remove_cart_item"]');
    if (removeBtn) {
      e.preventDefault();
      e.stopPropagation();

      let cartItemKey = removeBtn.getAttribute('data-cart_item_key');
      if (!cartItemKey) {
        const href = removeBtn.getAttribute('href') || '';
        const match = href.match(/remove_item=([a-f0-9]+)/i);
        if (match) cartItemKey = match[1];
      }

      const cartItemRow = removeBtn.closest('.cart-item, li.cart-item');
      const currentWrapper = document.querySelector('.header-cart-wrapper');

      if (currentWrapper) {
        currentWrapper.classList.add('is-open');
      }

      if (cartItemRow) {
        cartItemRow.style.overflow = 'hidden';
        if (typeof gsap !== 'undefined') {
          gsap.timeline({
            onComplete: function () {
              cartItemRow.remove();
            }
          })
            .to(cartItemRow, {
              opacity: 0,
              x: -40,
              duration: 0.2,
              ease: 'power2.in'
            })
            .to(cartItemRow, {
              height: 0,
              paddingTop: 0,
              paddingBottom: 0,
              marginTop: 0,
              marginBottom: 0,
              duration: 0.25,
              ease: 'power2.inOut'
            });
        } else {
          cartItemRow.remove();
        }
      }

      if (!cartItemKey) return;

      const ajaxUrl = (typeof dc_ajax !== 'undefined' && dc_ajax.ajax_url)
        ? dc_ajax.ajax_url
        : (window.location.origin + '/wp-admin/admin-ajax.php');

      const formData = new FormData();
      formData.append('action', 'dc_remove_cart_item');
      formData.append('nonce', dc_ajax.nonce);
      formData.append('cart_item_key', cartItemKey);

      fetch(ajaxUrl, {
        method: 'POST',
        body: formData
      })
        .then(function (res) { return res.json(); })
        .then(function (data) {
          const wrapperEl = document.querySelector('.header-cart-wrapper');
          if (wrapperEl) {
            wrapperEl.classList.add('is-open');
          }

          if (data && data.success) {
            const count = (typeof data.count !== 'undefined') ? data.count : (data.data ? data.data.count : 0);
            const fragments = data.fragments || (data.data ? data.data.fragments : null);

            if (fragments) {
              applyCartFragmentsWithoutFlashing(fragments, count);
            } else {
              document.querySelectorAll('.cart-count, .nav-cart-text .cart-count, .cart-dropdown-count').forEach(function (el) {
                el.textContent = count;
              });
            }
          }
        })
        .catch(function () {
          fetch(window.location.origin + '/?wc-ajax=get_refreshed_fragments', { method: 'POST' })
            .then(function (res) { return res.json(); })
            .then(function (fragData) {
              if (fragData && fragData.fragments) {
                applyCartFragmentsWithoutFlashing(fragData.fragments);
              }
            });
        });
    }
  });



  /* =========================================================================
     SHOP CATEGORY SWITCHER (BEANS VS MERCHANDISE) & 3-COLUMN FILTERS
     ========================================================================= */
  const catBtns = document.querySelectorAll('.shop-cat-btn');
  const filterCols = document.querySelectorAll('.shop-filter-col');
  const shopCards = document.querySelectorAll('.shop-grid .shop-product-card');

  if (catBtns.length > 0 || filterCols.length > 0) {
    let currentMainCategory = 'beans';

    // Category Switcher Button Click
    catBtns.forEach(function (btn) {
      btn.addEventListener('click', function () {
        catBtns.forEach(function (b) {
          b.classList.remove('active');
          b.setAttribute('aria-selected', 'false');
        });
        this.classList.add('active');
        this.setAttribute('aria-selected', 'true');
        currentMainCategory = this.getAttribute('data-cat-filter');

        const filterSys = document.querySelector('.shop-filter-system');
        if (filterSys) {
          filterSys.style.display = (currentMainCategory === 'merchandise') ? 'none' : '';
        }

        applyShopFilters();
      });
    });

    // 1. Accordion Header Toggle
    document.querySelectorAll('.shop-filter-header').forEach(function (header) {
      header.addEventListener('click', function () {
        const col = this.closest('.shop-filter-col');
        if (col) {
          const isCollapsed = col.classList.toggle('collapsed');
          this.setAttribute('aria-expanded', String(!isCollapsed));
        }
      });

      header.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' || e.key === ' ') {
          e.preventDefault();
          this.click();
        }
      });
    });

    // 2. Multi-Filter Change Handler
    function applyShopFilters() {
      const activeBeansInput = document.querySelector('input[name="filter_beans"]:checked');
      const activeProcessInput = document.querySelector('input[name="filter_process"]:checked');
      const activeBrewInput = document.querySelector('input[name="filter_brew"]:checked');

      const activeBeans = activeBeansInput ? activeBeansInput.value.toLowerCase() : 'all';
      const activeProcess = activeProcessInput ? activeProcessInput.value.toLowerCase() : 'all';
      const activeBrew = activeBrewInput ? activeBrewInput.value.toLowerCase() : 'all';

      // Update label active class
      document.querySelectorAll('.filter-radio-label').forEach(function (lbl) {
        const rad = lbl.querySelector('input[type="radio"]');
        if (rad && rad.checked) {
          lbl.classList.add('active');
        } else {
          lbl.classList.remove('active');
        }
      });

      let visibleCount = 0;

      shopCards.forEach(function (card) {
        const categories = (card.getAttribute('data-categories') || '').toLowerCase();
        const title = (card.getAttribute('data-title') || '').toLowerCase();
        const beans = (card.getAttribute('data-beans') || categories || 'all').toLowerCase();
        const process = (card.getAttribute('data-process') || 'all').toLowerCase();
        const brew = (card.getAttribute('data-brew') || 'all').toLowerCase();

        // Main Category check (Beans vs Merchandise)
        let matchMain = false;
        if (currentMainCategory === 'merchandise') {
          matchMain = categories.includes('merch') || categories.includes('apparel') || categories.includes('gear') || categories.includes('accessory') || title.includes('t-shirt') || title.includes('mug') || title.includes('cup') || title.includes('hat') || title.includes('bag') || title.includes('tote');
        } else {
          matchMain = !categories.includes('merch') && !categories.includes('apparel') && !title.includes('t-shirt') && !title.includes('mug') && !title.includes('tote');
        }

        const matchBeans = (activeBeans === 'all' || beans.includes(activeBeans));
        const matchProcess = (activeProcess === 'all' || process.includes(activeProcess));
        const matchBrew = (activeBrew === 'all' || brew.includes(activeBrew));

        if (matchMain && (currentMainCategory === 'merchandise' || (matchBeans && matchProcess && matchBrew))) {
          card.classList.remove('is-hidden');
          card.style.display = '';
          visibleCount++;
        } else {
          card.classList.add('is-hidden');
          card.style.display = 'none';
        }
      });

      // Check if any non-default filter is active to show/hide Clear Filters button
      const hasActiveFilters = (activeBeans !== 'all' || activeProcess !== 'all' || activeBrew !== 'all' || currentMainCategory !== 'beans');
      const clearBtn = document.getElementById('shop-clear-filters');
      if (clearBtn) {
        if (hasActiveFilters) {
          clearBtn.classList.add('is-active');
        } else {
          clearBtn.classList.remove('is-active');
        }
      }

      const emptyState = document.getElementById('shop-empty-state');
      if (emptyState) {
        emptyState.style.display = (visibleCount === 0) ? 'block' : 'none';
      }
    }

    document.querySelectorAll('.shop-filter-options input[type="radio"]').forEach(function (radio) {
      radio.addEventListener('change', applyShopFilters);
    });

    // Clear Filters Click Handler
    const clearBtn = document.getElementById('shop-clear-filters');
    if (clearBtn) {
      clearBtn.addEventListener('click', function () {
        // Reset all radios to 'all'
        document.querySelectorAll('.shop-filter-options input[value="all"]').forEach(function (r) {
          r.checked = true;
        });

        // Reset Category Switcher to Beans
        currentMainCategory = 'beans';
        catBtns.forEach(function (b) {
          if (b.getAttribute('data-cat-filter') === 'beans') {
            b.classList.add('active');
            b.setAttribute('aria-selected', 'true');
          } else {
            b.classList.remove('active');
            b.setAttribute('aria-selected', 'false');
          }
        });

        const filterSys = document.querySelector('.shop-filter-system');
        if (filterSys) {
          filterSys.style.display = '';
        }

        applyShopFilters();
      });
    }

    const resetBtn = document.getElementById('shop-reset-filters');
    if (resetBtn) {
      resetBtn.addEventListener('click', function () {
        if (clearBtn) clearBtn.click();
      });
    }

    // Initial filter apply
    applyShopFilters();
  }


});
