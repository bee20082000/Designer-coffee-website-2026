document.addEventListener('DOMContentLoaded', function () {
  'use strict';

  // 0. Force Page Load to Top
  if ('scrollRestoration' in history) {
    history.scrollRestoration = 'manual';
  }

  // 0.B PURE GSAP CREATIVE INTRO OPENING (GSAP Character SplitText + Interactive Floating Stickers)
  const introScreen = document.querySelector('.intro-brand-screen');
  if (introScreen && typeof gsap !== 'undefined') {
    const headline = introScreen.querySelector('.intro-statement-headline');
    const desc = introScreen.querySelector('.intro-statement-desc');
    const stickerLeft = introScreen.querySelector('.sticker-left');
    const stickerRight = introScreen.querySelector('.sticker-right');

    // 1. GSAP Split Text for Intro Headline
    let headlineChars = [];
    if (headline) {
      const text = headline.textContent.trim();
      headline.textContent = '';
      const words = text.split(' ');
      words.forEach((wordText, wIdx) => {
        const wordSpan = document.createElement('span');
        wordSpan.style.display = 'inline-block';
        wordSpan.style.whiteSpace = 'nowrap';
        for (let i = 0; i < wordText.length; i++) {
          const charSpan = document.createElement('span');
          charSpan.className = 'gsap-intro-char';
          charSpan.textContent = wordText[i];
          charSpan.style.display = 'inline-block';
          wordSpan.appendChild(charSpan);
          headlineChars.push(charSpan);
        }
        headline.appendChild(wordSpan);
        if (wIdx < words.length - 1) {
          const space = document.createTextNode(' ');
          headline.appendChild(space);
        }
      });
    }

    // 2. Set Initial Hidden States (Instant GSAP AutoAlpha Reset)
    if (headlineChars.length > 0) {
      gsap.set(headlineChars, { autoAlpha: 0, y: 25, scale: 0.9, rotate: -2 });
    }
    if (desc) {
      gsap.set(desc, { autoAlpha: 0, y: 15 });
    }
    if (stickerLeft) {
      gsap.set(stickerLeft, { autoAlpha: 0, scale: 1.2, rotate: -14, x: -30 });
    }
    if (stickerRight) {
      gsap.set(stickerRight, { autoAlpha: 0, scale: 1.1, rotate: 14, x: 30 });
    }

    // 3. Master Intro GSAP Timeline
    const introTl = gsap.timeline({
      delay: 0.05,
      onComplete: function () {
        // Start Continuous Floating Bobbing Motion ONLY AFTER Entrance Finishes
        if (stickerLeft) {
          gsap.to(stickerLeft, {
            y: "-=10",
            rotation: "+=3",
            duration: 2.6,
            repeat: -1,
            yoyo: true,
            ease: "sine.inOut",
            force3D: true
          });
        }
        if (stickerRight) {
          gsap.to(stickerRight, {
            y: "+=10",
            rotation: "-=3",
            duration: 2.9,
            repeat: -1,
            yoyo: true,
            ease: "sine.inOut",
            force3D: true
          });
        }
      }
    });

    if (headlineChars.length > 0) {
      introTl.to(headlineChars, {
        autoAlpha: 1,
        y: 0,
        scale: 1,
        rotate: 0,
        duration: 0.5,
        stagger: 0.02,
        ease: "power2.out",
        force3D: true
      });
    }

    if (desc) {
      introTl.to(desc, {
        autoAlpha: 1,
        y: 0,
        duration: 0.45,
        ease: "power2.out",
        force3D: true
      }, "-=0.25");
    }

    if (stickerLeft) {
      introTl.to(stickerLeft, {
        autoAlpha: 1,
        scale: 1.5,
        rotate: -6,
        x: 0,
        duration: 0.5,
        ease: "power2.out",
        force3D: true
      }, "-=0.3");
    }

    if (stickerRight) {
      introTl.to(stickerRight, {
        autoAlpha: 1,
        scale: 1.8,
        rotate: 8,
        x: 0,
        duration: 0.5,
        ease: "power2.out",
        force3D: true
      }, "-=0.4");
    }

    // 5. Interactive Pure GSAP Hover FX
    if (stickerLeft) {
      stickerLeft.addEventListener('mouseenter', () => {
        gsap.to(stickerLeft, { scale: 1.7, duration: 0.3, ease: "power2.out" });
      });
      stickerLeft.addEventListener('mouseleave', () => {
        gsap.to(stickerLeft, { scale: 1.5, duration: 0.3, ease: "power2.out" });
      });
    }

    if (stickerRight) {
      stickerRight.addEventListener('mouseenter', () => {
        gsap.to(stickerRight, { scale: 1.95, duration: 0.3, ease: "power2.out" });
      });
      stickerRight.addEventListener('mouseleave', () => {
        gsap.to(stickerRight, { scale: 1.8, duration: 0.3, ease: "power2.out" });
      });
    }
  }

  // 1. Sticky Navigation Scrolled Class
  const header = document.querySelector('.site-header');
  if (header) {
    window.addEventListener('scroll', function () {
      if (window.scrollY > 50) {
        header.classList.add('scrolled');
      } else {
        header.classList.remove('scrolled');
      }
    });
  }

  // 2. Mobile Menu Toggle
  const mobileToggle = document.querySelector('.mobile-toggle');
  const mainNav = document.querySelector('.main-nav');
  if (mobileToggle && mainNav) {
    mobileToggle.addEventListener('click', function () {
      mainNav.classList.toggle('active');
      const isExpanded = mainNav.classList.contains('active');
      mobileToggle.setAttribute('aria-expanded', isExpanded);
    });

    const navLinks = mainNav.querySelectorAll('a');
    navLinks.forEach(function (link) {
      link.addEventListener('click', function () {
        mainNav.classList.remove('active');
      });
    });
  }

  // 3. Back to Top Button
  const toTopBtn = document.querySelector('.to-top-btn');
  if (toTopBtn) {
    window.addEventListener('scroll', function () {
      if (window.scrollY > 400) {
        toTopBtn.classList.add('visible');
      } else {
        toTopBtn.classList.remove('visible');
      }
    });

    toTopBtn.addEventListener('click', function (e) {
      e.preventDefault();
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });
  }

  // 4. Smooth Anchor Scrolling
  document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
      const targetId = this.getAttribute('href');
      if (targetId && targetId !== '#') {
        const targetElement = document.querySelector(targetId);
        if (targetElement) {
          e.preventDefault();
          const offset = 80;
          const targetPosition = targetElement.getBoundingClientRect().top + window.pageYOffset - offset;
          window.scrollTo({
            top: targetPosition,
            behavior: 'smooth'
          });
        }
      }
    });
  });

  // 5. GSAP HERO SLIDESHOW: 60FPS Hardware-Accelerated Zero-Lag Intro & Group Motion
  const slideGroups = document.querySelectorAll('.hero-slide-group');
  const prevArrow = document.querySelector('.hero-arrow-prev');
  const nextArrow = document.querySelector('.hero-arrow-next');

  if (slideGroups.length > 0 && typeof gsap !== 'undefined') {
    let currentIndex = 0;
    let isAnimating = false;
    let slideTimer;

    // Helper: Clean reset inline props
    function resetSlideElements(group) {
      if (!group) return;
      const headline = group.querySelector('.hero-bottom-headline');
      const desc = group.querySelector('.hero-bottom-desc');
      const cta = group.querySelector('.hero-cta-group');
      const bg = group.querySelector('.hero-slide-bg');

      if (headline) gsap.set(headline, { clearProps: "transform,x,y,scale,opacity" });
      if (desc) gsap.set(desc, { clearProps: "transform,x,y,scale,opacity" });
      if (cta) gsap.set(cta, { clearProps: "transform,x,y,scale,opacity" });
      if (bg) gsap.set(bg, { clearProps: "transform,x,y,scale,opacity" });
    }

    // A. 60FPS CINEMATIC INTRO ANIMATION (Immediate 0ms Delay Slide-Up)
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

      // Step 1: Background Photo Soft Fade-In
      if (bg0) {
        introTl.fromTo(bg0,
          { autoAlpha: 0 },
          { autoAlpha: 1, duration: 0.5, ease: "power2.out" },
          0.00
        );
      }

      // Step 2: Header Elements Drop Down
      if (logo || navCapsule || headerActions) {
        introTl.fromTo([logo, navCapsule, headerActions],
          { y: -25, autoAlpha: 0 },
          { y: 0, autoAlpha: 1, duration: 0.45, stagger: 0.05, ease: "power3.out" },
          0.05
        );
      }

      // Step 3: Headline Instant Smooth Slide Up
      if (headline0) {
        introTl.fromTo(headline0,
          { y: 40, autoAlpha: 0 },
          { y: 0, autoAlpha: 1, duration: 0.45, ease: "power4.out" },
          0.12
        );
      }

      // Step 4: Paragraph Description Slide Up
      if (desc0) {
        introTl.fromTo(desc0,
          { y: 25, autoAlpha: 0 },
          { y: 0, autoAlpha: 1, duration: 0.4, ease: "power3.out" },
          0.2
        );
      }

      // Step 5: CTA Button Slide Up
      if (cta0) {
        introTl.fromTo(cta0,
          { y: 18, autoAlpha: 0 },
          { y: 0, autoAlpha: 1, duration: 0.38, ease: "power3.out" },
          0.26
        );
      }

      // Step 6: Navigation Arrows Fade In
      if (arrows.length > 0) {
        introTl.fromTo(arrows,
          { autoAlpha: 0 },
          { autoAlpha: 1, duration: 0.3, stagger: 0.05, ease: "power2.out" },
          0.32
        );
      }
    }

    // B. ZERO-LAG ULTRA-SNAPPY GROUP CAROUSEL MOTION
    function goToSlide(targetIndex, direction = null) {
      if (isAnimating) return;
      isAnimating = true;

      const currentGroup = slideGroups[currentIndex];
      const nextIndex = (targetIndex + slideGroups.length) % slideGroups.length;
      const nextGroup = slideGroups[nextIndex];

      if (currentIndex === nextIndex) {
        isAnimating = false;
        return;
      }

      // Determine Direction
      let isNext = true;
      if (direction === 'prev') {
        isNext = false;
      } else if (direction === 'next') {
        isNext = true;
      } else {
        isNext = !(nextIndex < currentIndex || (currentIndex === 0 && nextIndex === slideGroups.length - 1));
      }

      const xOutPercent = isNext ? -50 : 50;
      const xInPercent = isNext ? 100 : -100;

      // Clean prior props before running animation
      resetSlideElements(currentGroup);
      resetSlideElements(nextGroup);

      const nextHeadline = nextGroup.querySelector('.hero-bottom-headline');
      const nextDesc = nextGroup.querySelector('.hero-bottom-desc');
      const nextCta = nextGroup.querySelector('.hero-cta-group');

      // Prepare incoming slide
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

      // 1. Outgoing Slide Fade-Out & Shift
      masterTl.to(currentGroup, {
        xPercent: xOutPercent,
        autoAlpha: 0,
        scale: 0.97,
        duration: 0.38,
        ease: "power2.inOut"
      }, 0);

      // 2. Incoming Slide Slide-In
      masterTl.to(nextGroup, {
        xPercent: 0,
        autoAlpha: 1,
        duration: 0.38,
        ease: "power4.out"
      }, 0.04);

      // 3. Staggered Inner Text Entrance
      if (nextHeadline) {
        masterTl.fromTo(nextHeadline,
          { y: 30, scale: 0.94, autoAlpha: 0 },
          { y: 0, scale: 1, autoAlpha: 1, duration: 0.35, ease: "power4.out" },
          0.12
        );
      }

      if (nextDesc) {
        masterTl.fromTo(nextDesc,
          { y: 20, autoAlpha: 0 },
          { y: 0, autoAlpha: 1, duration: 0.32, ease: "power3.out" },
          0.18
        );
      }

      if (nextCta) {
        masterTl.fromTo(nextCta,
          { y: 15, scale: 0.85, autoAlpha: 0 },
          { y: 0, scale: 1, autoAlpha: 1, duration: 0.32, ease: "power4.out" },
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
      prevArrow.addEventListener('mouseenter', () => gsap.to(prevArrow, { scale: 1.25, duration: 0.15, ease: "power2.out" }));
      prevArrow.addEventListener('mouseleave', () => gsap.to(prevArrow, { scale: 1, duration: 0.15 }));
    }

    if (nextArrow) {
      nextArrow.addEventListener('click', function () {
        goToSlide(currentIndex + 1, 'next');
        resetTimer();
      });
      nextArrow.addEventListener('mouseenter', () => gsap.to(nextArrow, { scale: 1.25, duration: 0.15, ease: "power2.out" }));
      nextArrow.addEventListener('mouseleave', () => gsap.to(nextArrow, { scale: 1, duration: 0.15 }));
    }

    // D. GSAP SPLIT TEXT CHARACTER REVEAL ANIMATION (SCHOOLBELL FONT PRESERVED)
    const salutationEl = document.getElementById('typewriter-salutation');
    const quoteEl = document.getElementById('typewriter-quote');
    const letterCard = document.querySelector('.full-red-letter');

    if (salutationEl && quoteEl && letterCard && typeof gsap !== 'undefined') {
      const splitToCharSpans = (element) => {
        const text = element.textContent.trim();
        element.textContent = '';
        const chars = [];

        const words = text.split(' ');
        words.forEach((wordText, wIdx) => {
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
            const spaceNode = document.createTextNode(' ');
            element.appendChild(spaceNode);
          }
        });

        return chars;
      };

      const salChars = splitToCharSpans(salutationEl);
      const quoteChars = splitToCharSpans(quoteEl);

      let hasAnimated = false;

      const runGSAPSplitAnimation = () => {
        if (hasAnimated) return;
        hasAnimated = true;

        const tl = gsap.timeline();

        // Butter-smooth GSAP SplitText Character Stagger Reveal
        tl.fromTo(salChars,
          { opacity: 0, y: 18, scale: 0.75, rotate: -4 },
          { opacity: 1, y: 0, scale: 1, rotate: 0, duration: 0.35, stagger: 0.03, ease: "back.out(1.6)" }
        )
          .fromTo(quoteChars,
            { opacity: 0, y: 14, scale: 0.8 },
            { opacity: 1, y: 0, scale: 1, duration: 0.3, stagger: 0.018, ease: "power2.out" },
            "-=0.1"
          );
      };

      if ('IntersectionObserver' in window) {
        const observer = new IntersectionObserver((entries) => {
          entries.forEach(entry => {
            if (entry.isIntersecting) {
              runGSAPSplitAnimation();
              observer.unobserve(entry.target);
            }
          });
        }, { threshold: 0.2 });
        observer.observe(letterCard);
      } else {
        runGSAPSplitAnimation();
      }
    }

    // Run Initial Page Load Intro Animation
    runPageIntroAnimation();
    startTimer();
  }
});
