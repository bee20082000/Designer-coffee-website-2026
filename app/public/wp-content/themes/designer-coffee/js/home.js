/**
 * Designer Coffee — home.js
 * Loaded on the HOME PAGE ONLY.
 *
 * All animations are deferred until "dc:loaderDone" fires from global.js,
 * ensuring they play AFTER the loader curtain exit animation completes.
 *
 * Responsibilities:
 *  1. GSAP intro brand screen animation
 *     — Character split-text on headline
 *     — Description fade-up
 *     — Floating sticker entrance + continuous bob + hover FX
 *  2. GSAP hero slideshow (60fps group carousel)
 *     — Cinematic intro animation on page load
 *     — Prev / Next arrow navigation
 *     — Auto-play timer with reset on interaction
 *  3. GSAP letter card split-text reveal (IntersectionObserver)
 *     — Schoolbell font character stagger on `.full-red-letter`
 */

document.addEventListener('DOMContentLoaded', function () {
  'use strict';

  let initialized = false;

  function initHomePage() {
    if (initialized) return;
    initialized = true;

    // Register GSAP TextPlugin and ScrollTrigger if present
    if (typeof gsap !== 'undefined') {
      if (typeof TextPlugin !== 'undefined') {
        gsap.registerPlugin(TextPlugin);
      }
      if (typeof ScrollTrigger !== 'undefined') {
        gsap.registerPlugin(ScrollTrigger);
      }
    }


    /* =========================================================================
       1. INTRO BRAND SCREEN ANIMATION
       ─────────────────────────────────────────────────────────────────────────
       Flash fix: headline, desc, and stickers are pre-hidden in home.css via
       opacity:0/visibility:hidden. GSAP's autoAlpha inline style overrides CSS,
       so there is zero content-visible gap when the loader disappears.
  
       Timeline (t=0 = moment dc:loaderDone fires):
         t 0.0 → Headline words clip-slide up, word by word
         t 0.6 → Description fades up from y:16
         t 0.7 → Sticker left swings in (back.out bounce)
         t 0.85→ Sticker right swings in (back.out bounce)
         done  → Continuous floating bob on both stickers
       ========================================================================= */
    const introScreen = document.querySelector('.intro-brand-screen');

    if (introScreen && typeof gsap !== 'undefined') {
      const headline = introScreen.querySelector('.intro-statement-headline');
      const desc = introScreen.querySelector('.intro-statement-desc');
      const introCta = introScreen.querySelector('.intro-cta-wrapper');
      const stickerLeft = introScreen.querySelector('.sticker-left');
      const stickerRight = introScreen.querySelector('.sticker-right');

      // ── Headline, desc, cta and stickers starting points (very fast & clean, no split text) ──
      if (headline)     gsap.set(headline,     { y: 35, scale: 0.85, rotate: -2, autoAlpha: 0 });
      if (desc)         gsap.set(desc,         { y: 25, scale: 0.95, autoAlpha: 0 });
      if (introCta)     gsap.set(introCta,     { y: 20, scale: 0.95, autoAlpha: 0 });
      if (stickerLeft)  gsap.set(stickerLeft,  { x: -100, y: 100, rotate: -35, scale: 0, autoAlpha: 0 });
      if (stickerRight) gsap.set(stickerRight, { x:  100, y: 100, rotate:  35, scale: 0, autoAlpha: 0 });

      // ── GSAP timeline — explicit absolute positions, no relative offsets ──────
      const introTl = gsap.timeline({
        defaults: { ease: 'power4.out', force3D: true },
        onComplete: function () {
          // Enable pointer events so hover actions work after they arrive
          if (stickerLeft) gsap.set(stickerLeft, { pointerEvents: 'auto' });
          if (stickerRight) gsap.set(stickerRight, { pointerEvents: 'auto' });

          // Continuous bobbing on the INNER images (leaves the wrapper free for parallax)
          if (stickerLeft) {
            const imgL = stickerLeft.querySelector('.intro-sticker-img');
            if (imgL) {
              gsap.to(imgL, { y: '-=12', rotation: '+=4', duration: 2.9, repeat: -1, yoyo: true, ease: 'sine.inOut', force3D: true });
            }
          }
          if (stickerRight) {
            const imgR = stickerRight.querySelector('.intro-sticker-img');
            if (imgR) {
              gsap.to(imgR, { y: '+=12', rotation: '-=4', duration: 3.2, repeat: -1, yoyo: true, ease: 'sine.inOut', force3D: true });
            }
          }

          // Parallax scroll effect on the WRAPPERS (multi-directional drifting parallax)
          if (typeof ScrollTrigger !== 'undefined') {
            if (stickerLeft) {
              gsap.to(stickerLeft, {
                y: 160,       // Drifts down slower
                x: -70,       // Drifts outwards to the left
                rotate: -20,  // Rotates counter-clockwise
                ease: 'none',
                scrollTrigger: {
                  trigger: '.intro-brand-screen',
                  start: 'top top',
                  end: 'bottom top',
                  scrub: true
                }
              });
            }
            if (stickerRight) {
              gsap.to(stickerRight, {
                y: 420,       // Drifts down much faster (overlaying the next section)
                x: 80,        // Drifts outwards to the right
                rotate: 28,   // Rotates clockwise
                ease: 'none',
                scrollTrigger: {
                  trigger: '.intro-brand-screen',
                  start: 'top top',
                  end: 'bottom top',
                  scrub: true
                }
              });
            }
          }
        }
      });

      // 1. Headline pops and bounces in
      if (headline) {
        introTl.to(headline, {
          autoAlpha: 1,
          y: 0,
          scale: 1,
          rotate: 0,
          duration: 0.45,
          ease: 'back.out(2.4)',
        }, 0);
      }

      // 2. Description fades and bounces up
      if (desc) {
        introTl.to(desc, {
          autoAlpha: 1,
          y: 0,
          scale: 1,
          duration: 0.45,
          ease: 'back.out(2.2)',
        }, 0.12);
      }

      // 2.5. Shop Now CTA button fades and bounces up
      if (introCta) {
        introTl.to(introCta, {
          autoAlpha: 1,
          y: 0,
          scale: 1,
          duration: 0.45,
          ease: 'back.out(2.2)',
        }, 0.20);
      }

      // 3. Stickers pop up and swing in very quickly with a springy overshoot
      if (stickerLeft) {
        introTl.to(stickerLeft, {
          autoAlpha: 1, x: 0, y: 0, rotate: -6, scale: 1,
          duration: 0.6, ease: 'back.out(3.2)',
        }, 0.24);
      }
      if (stickerRight) {
        introTl.to(stickerRight, {
          autoAlpha: 1, x: 0, y: 0, rotate: 8, scale: 1,
          duration: 0.6, ease: 'back.out(3.2)',
        }, 0.34);
      }

      // ── Sticker hover FX — applied ONLY to inner img to prevent transform clashes ──
      if (stickerLeft) {
        const imgL = stickerLeft.querySelector('.intro-sticker-img');
        if (imgL) {
          stickerLeft.addEventListener('mouseenter', function () {
            gsap.to(imgL, { scale: 1.12, duration: 0.25, ease: 'power2.out', force3D: true });
          });
          stickerLeft.addEventListener('mouseleave', function () {
            gsap.to(imgL, { scale: 1, duration: 0.3, ease: 'power2.out', force3D: true });
          });
        }
      }
      if (stickerRight) {
        const imgR = stickerRight.querySelector('.intro-sticker-img');
        if (imgR) {
          stickerRight.addEventListener('mouseenter', function () {
            gsap.to(imgR, { scale: 1.12, duration: 0.25, ease: 'power2.out', force3D: true });
          });
          stickerRight.addEventListener('mouseleave', function () {
            gsap.to(imgR, { scale: 1, duration: 0.3, ease: 'power2.out', force3D: true });
          });
        }
      }
    }


    /* =========================================================================
       2. GSAP HERO SLIDESHOW — 60fps hardware-accelerated group carousel
       ========================================================================= */
    const slideGroups = document.querySelectorAll('.hero-slide-group');
    const prevArrow = document.querySelector('.hero-arrow-prev');
    const nextArrow = document.querySelector('.hero-arrow-next');

    if (slideGroups.length > 0 && typeof gsap !== 'undefined') {
      let currentIndex = 0;
      let isAnimating = false;
      let slideTimer;

      // Helper: clear inline GSAP props on a slide's inner elements
      function resetSlideElements(group) {
        if (!group) return;
        const els = [
          group.querySelector('.hero-bottom-headline'),
          group.querySelector('.hero-bottom-desc'),
          group.querySelector('.hero-cta-group'),
          group.querySelector('.hero-slide-bg'),
        ];
        els.forEach(function (el) {
          if (el) gsap.set(el, { clearProps: 'transform,x,y,scale,opacity' });
        });
      }

      // A. Cinematic intro animation on first load
      function runPageIntroAnimation() {
        const slide0 = slideGroups[0];
        if (!slide0) return;

        const bg0 = slide0.querySelector('.hero-slide-bg');
        const headline0 = slide0.querySelector('.hero-bottom-headline');
        const desc0 = slide0.querySelector('.hero-bottom-desc');
        const cta0 = slide0.querySelector('.hero-cta-group');

        const logo = document.querySelector('.brand-logo');
        const navCapsule = document.querySelector('.nav-capsule');
        const headerActions = document.querySelector('.header-actions');
        const arrows = document.querySelectorAll('.hero-arrow');

        gsap.set(slide0, { autoAlpha: 1, xPercent: 0 });

        const introTl = gsap.timeline({ defaults: { force3D: true } });

        if (bg0) {
          introTl.fromTo(bg0,
            { autoAlpha: 0 },
            { autoAlpha: 1, duration: 0.5, ease: 'power2.out' },
            0.00
          );
        }

        if (logo || navCapsule || headerActions) {
          introTl.fromTo([logo, navCapsule, headerActions].filter(Boolean),
            { y: -25, autoAlpha: 0 },
            { y: 0, autoAlpha: 1, duration: 0.45, stagger: 0.05, ease: 'power3.out' },
            0.05
          );
        }

        if (headline0) {
          introTl.fromTo(headline0,
            { y: 40, autoAlpha: 0 },
            { y: 0, autoAlpha: 1, duration: 0.45, ease: 'power4.out' },
            0.12
          );
        }

        if (desc0) {
          introTl.fromTo(desc0,
            { y: 25, autoAlpha: 0 },
            { y: 0, autoAlpha: 1, duration: 0.40, ease: 'power3.out' },
            0.20
          );
        }

        if (cta0) {
          introTl.fromTo(cta0,
            { y: 18, autoAlpha: 0 },
            { y: 0, autoAlpha: 1, duration: 0.38, ease: 'power3.out' },
            0.26
          );
        }

        if (arrows.length > 0) {
          introTl.fromTo(arrows,
            { autoAlpha: 0 },
            { autoAlpha: 1, duration: 0.3, stagger: 0.05, ease: 'power2.out' },
            0.32
          );
        }
      }

      // B. Zero-lag snappy group carousel transition
      function goToSlide(targetIndex, direction) {
        if (isAnimating) return;
        isAnimating = true;

        const currentGroup = slideGroups[currentIndex];
        const nextIndex = ((targetIndex % slideGroups.length) + slideGroups.length) % slideGroups.length;
        const nextGroup = slideGroups[nextIndex];

        if (currentIndex === nextIndex) {
          isAnimating = false;
          return;
        }

        const isNext = direction !== 'prev';
        const xOutPercent = isNext ? -50 : 50;
        const xInPercent = isNext ? 100 : -100;

        resetSlideElements(currentGroup);
        resetSlideElements(nextGroup);

        const nextHeadline = nextGroup.querySelector('.hero-bottom-headline');
        const nextDesc = nextGroup.querySelector('.hero-bottom-desc');
        const nextCta = nextGroup.querySelector('.hero-cta-group');

        gsap.set(nextGroup, { xPercent: xInPercent, autoAlpha: 0, zIndex: 3 });
        gsap.set(currentGroup, { zIndex: 2 });
        nextGroup.classList.add('active');

        const masterTl = gsap.timeline({
          defaults: { force3D: true },
          onComplete: function () {
            currentGroup.classList.remove('active');
            gsap.set(currentGroup, { autoAlpha: 0, xPercent: 0, scale: 1, zIndex: 1 });
            resetSlideElements(currentGroup);
            gsap.set(nextGroup, { zIndex: 2, xPercent: 0, autoAlpha: 1, scale: 1 });
            resetSlideElements(nextGroup);
            currentIndex = nextIndex;
            isAnimating = false;
          }
        });

        masterTl.to(currentGroup, {
          xPercent: xOutPercent, autoAlpha: 0, scale: 0.97,
          duration: 0.38, ease: 'power2.inOut'
        }, 0);

        masterTl.to(nextGroup, {
          xPercent: 0, autoAlpha: 1,
          duration: 0.38, ease: 'power4.out'
        }, 0.04);

        if (nextHeadline) {
          masterTl.fromTo(nextHeadline,
            { y: 30, scale: 0.94, autoAlpha: 0 },
            { y: 0, scale: 1, autoAlpha: 1, duration: 0.35, ease: 'power4.out' },
            0.12
          );
        }

        if (nextDesc) {
          masterTl.fromTo(nextDesc,
            { y: 20, autoAlpha: 0 },
            { y: 0, autoAlpha: 1, duration: 0.32, ease: 'power3.out' },
            0.18
          );
        }

        if (nextCta) {
          masterTl.fromTo(nextCta,
            { y: 15, scale: 0.85, autoAlpha: 0 },
            { y: 0, scale: 1, autoAlpha: 1, duration: 0.32, ease: 'power4.out' },
            0.24
          );
        }
      }

      function startTimer() {
        slideTimer = setInterval(function () {
          goToSlide(currentIndex + 1, 'next');
        }, 5000);
      }

      function resetTimer() {
        clearInterval(slideTimer);
        startTimer();
      }

      if (prevArrow) {
        prevArrow.addEventListener('click', function () {
          goToSlide(currentIndex - 1, 'prev');
          resetTimer();
        });
        prevArrow.addEventListener('mouseenter', function () {
          gsap.to(prevArrow, { scale: 1.25, duration: 0.15, ease: 'power2.out' });
        });
        prevArrow.addEventListener('mouseleave', function () {
          gsap.to(prevArrow, { scale: 1, duration: 0.15 });
        });
      }

      if (nextArrow) {
        nextArrow.addEventListener('click', function () {
          goToSlide(currentIndex + 1, 'next');
          resetTimer();
        });
        nextArrow.addEventListener('mouseenter', function () {
          gsap.to(nextArrow, { scale: 1.25, duration: 0.15, ease: 'power2.out' });
        });
        nextArrow.addEventListener('mouseleave', function () {
          gsap.to(nextArrow, { scale: 1, duration: 0.15 });
        });
      }

      // Kick off
      runPageIntroAnimation();
      startTimer();
    }

    /* =========================================================================
       3. GSAP SPLIT-TEXT CHARACTER REVEAL — Letter Card (.real-letter-paper)
       Uses IntersectionObserver so it fires only when the card is in view.
       ========================================================================= */
    const salutationEl = document.getElementById('typewriter-salutation');
    const quoteEl = document.getElementById('typewriter-quote');
    const letterCard = document.querySelector('.real-letter-paper');

    if (salutationEl && quoteEl && letterCard && typeof gsap !== 'undefined') {

      /**
       * Split an element's text into per-character <span> elements.
       * Schoolbell font is force-applied via inline style to survive global overrides.
       * @param {HTMLElement} element
       * @returns {HTMLElement[]} array of char spans
       */
      function splitToCharSpans(element) {
        const text = element.textContent.trim();
        element.textContent = '';
        const chars = [];

        text.split(' ').forEach(function (wordText, wIdx, words) {
          const wordSpan = document.createElement('span');
          wordSpan.className = 'gsap-word';
          wordSpan.style.setProperty('font-family', "'Schoolbell', cursive, sans-serif", 'important');

          for (let i = 0; i < wordText.length; i++) {
            const charSpan = document.createElement('span');
            charSpan.className = 'gsap-char';
            charSpan.textContent = wordText[i];
            charSpan.style.opacity = '0';
            charSpan.style.setProperty('font-family', "'Schoolbell', cursive, sans-serif", 'important');
            wordSpan.appendChild(charSpan);
            chars.push(charSpan);
          }

          element.appendChild(wordSpan);
          if (wIdx < words.length - 1) {
            element.appendChild(document.createTextNode(' '));
          }
        });

        return chars;
      }

      const salChars = splitToCharSpans(salutationEl);
      const quoteChars = splitToCharSpans(quoteEl);
      let hasAnimated = false;

      function runLetterAnimation() {
        if (hasAnimated) return;
        hasAnimated = true;

        gsap.timeline()
          .fromTo(salChars,
            { opacity: 0, y: 18, scale: 0.75, rotate: -4 },
            { opacity: 1, y: 0, scale: 1, rotate: 0, duration: 0.35, stagger: 0.03, ease: 'back.out(1.6)' }
          )
          .fromTo(quoteChars,
            { opacity: 0, y: 14, scale: 0.8 },
            { opacity: 1, y: 0, scale: 1, duration: 0.3, stagger: 0.018, ease: 'power2.out' },
            '-=0.1'
          );
      }

      if ('IntersectionObserver' in window) {
        const observer = new IntersectionObserver(function (entries) {
          entries.forEach(function (entry) {
            if (entry.isIntersecting) {
              runLetterAnimation();
              observer.unobserve(entry.target);
            }
          });
        }, { threshold: 0.2 });

        observer.observe(letterCard);
      } else {
        // Fallback for old browsers
        runLetterAnimation();
      }
    }

    /* =========================================================================
       3B. PARALLAX SCROLLING — SUSTAINABLE AGRICULTURE & FAIR TRADE GALLERY
       ========================================================================= */
    const pinSection = document.querySelector('.core-beliefs-pin-section');
    const galleryItems = document.querySelectorAll('.core-beliefs-pin-gallery .gallery-item');

    if (pinSection && galleryItems.length > 0 && typeof ScrollTrigger !== 'undefined') {
      galleryItems.forEach(function (item, index) {
        const dir = (index % 2 === 0) ? -1 : 1;

        // Subtle, elegant grouped parallax shift (25px offset)
        gsap.fromTo(item,
          { y: dir * 25 },
          {
            y: dir * -25,
            ease: 'none',
            scrollTrigger: {
              trigger: item,
              start: 'top bottom',
              end: 'bottom top',
              scrub: 1.5
            }
          }
        );
      });
    }

    /* =========================================================================
       4. DRAG-TO-SCROLL HORIZONTAL PRODUCT STRIP
       — Wheel over strip → scrolls strip horizontally, page does NOT scroll
       — Click & drag → grab cursor, smooth drag scroll
       ========================================================================= */
    const scrollContainer = document.querySelector('.shop-products-scroll-container');
    const scrollTrack = document.querySelector('.shop-scrollbar-track');
    const scrollThumb = document.querySelector('.shop-scrollbar-thumb');

    if (scrollContainer) {

      /* ── Scrollbar progress tracker ─────────────────────────────────────── */
      function updateScrollbarProgress() {
        if (!scrollTrack || !scrollThumb) return;
        const containerWidth = scrollContainer.clientWidth;
        const scrollWidth    = scrollContainer.scrollWidth;
        const scrollLeft     = scrollContainer.scrollLeft;
        const trackWidth     = scrollTrack.clientWidth;
        const maxScroll      = scrollWidth - containerWidth;

        const thumbWidth = Math.max(30, (containerWidth / scrollWidth) * trackWidth);
        scrollThumb.style.width = thumbWidth + 'px';

        if (maxScroll > 0) {
          const progress    = Math.min(1, Math.max(0, scrollLeft / maxScroll));
          const maxTranslate = trackWidth - thumbWidth;
          scrollThumb.style.transform = 'translateX(' + (progress * maxTranslate) + 'px)';
        } else {
          scrollThumb.style.transform = 'translateX(0)';
        }
      }

      updateScrollbarProgress();
      scrollContainer.addEventListener('scroll', updateScrollbarProgress, { passive: true });
      window.addEventListener('resize', updateScrollbarProgress, { passive: true });

      /* ── Click-and-drag to scroll horizontally (desktop mouse) ────────── */
      let isDragging = false;
      let startX = 0;
      let startScrollLeft = 0;
      let hasDragged = false;

      // Prevent native browser link/image dragging ghost
      scrollContainer.addEventListener('dragstart', function (e) {
        e.preventDefault();
        return false;
      });

      scrollContainer.addEventListener('mousedown', function (e) {
        if (e.button !== 0) return;
        isDragging = true;
        hasDragged = false;
        startX = e.clientX;
        startScrollLeft = scrollContainer.scrollLeft;
        scrollContainer.classList.add('is-dragging');
      });

      document.addEventListener('mouseup', function () {
        if (!isDragging) return;
        isDragging = false;
        scrollContainer.classList.remove('is-dragging');
      });

      document.addEventListener('mousemove', function (e) {
        if (!isDragging) return;
        const deltaX = e.clientX - startX;
        if (Math.abs(deltaX) > 5) {
          hasDragged = true;
        }
        scrollContainer.scrollLeft = startScrollLeft - deltaX;
        updateScrollbarProgress();
      });

      // Prevent link navigation if user dragged
      scrollContainer.addEventListener('click', function (e) {
        if (hasDragged) {
          e.preventDefault();
          e.stopPropagation();
          hasDragged = false;
        }
      }, true);
    }
  }


  document.addEventListener('dc:loaderDone', initHomePage);
  initHomePage();
  setTimeout(initHomePage, 100);
});

