/**
 * Designer Coffee — Homepage Continuous Infinite Marquee Carousel (js/home-carousel.js)
 * Smooth 60fps hardware-accelerated continuous simultaneous linear marquee.
 *
 * @package DesignerCoffee
 */
document.addEventListener('DOMContentLoaded', function () {
    const homeTrack = document.getElementById('home-carousel-track');
    if (!homeTrack) return;

    // Ensure continuous marquee class is active
    homeTrack.classList.add('marquee-running');

    const wrapper = homeTrack.closest('.home-carousel-wrapper') || homeTrack;

    // Touch interaction handlers to pause/resume marquee on mobile
    wrapper.addEventListener('touchstart', function () {
        homeTrack.style.animationPlayState = 'paused';
    }, { passive: true });

    wrapper.addEventListener('touchend', function () {
        homeTrack.style.animationPlayState = 'running';
    }, { passive: true });
});
