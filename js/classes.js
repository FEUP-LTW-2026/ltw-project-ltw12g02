document.addEventListener('DOMContentLoaded', () => {
  const carousel = document.querySelector('.classes_carousel');

  if (carousel === null) {
    return;
  }

  const track = carousel.querySelector('.carousel_track');
  const slides = Array.from(carousel.querySelectorAll('.class_card'));
  const prevButton = carousel.querySelector('[data-carousel-prev]');
  const nextButton = carousel.querySelector('[data-carousel-next]');

  if (track === null || slides.length === 0 || prevButton === null || nextButton === null) {
    return;
  }

  let currentPage = 0;
  let autoplayId = null;

  function getVisibleSlides() {
    if (window.matchMedia('(max-width: 30rem)').matches) {
      return 1;
    }

    if (window.matchMedia('(max-width: 60rem)').matches) {
      return 2;
    }

    return 3;
  }

  function getMaxPage() {
    return Math.ceil(slides.length / getVisibleSlides()) - 1;
  }

  function updateCarousel() {
    const visibleSlides = getVisibleSlides();
    const maxPage = getMaxPage();

    if (currentPage > maxPage) {
      currentPage = 0;
    }

    if (currentPage < 0) {
      currentPage = maxPage;
    }

    const slideWidth = slides[0].getBoundingClientRect().width;
    const gap = parseFloat(getComputedStyle(track).gap) || 0;

    const distance = currentPage * visibleSlides * (slideWidth + gap);

    track.style.transform = `translateX(-${distance}px)`;
  }

  function nextSlide() {
    currentPage++;
    updateCarousel();
  }

  function prevSlide() {
    currentPage--;
    updateCarousel();
  }

  function startAutoplay() {
    stopAutoplay();

    autoplayId = setInterval(() => {
      nextSlide();
    }, 3000);
  }

  function stopAutoplay() {
    if (autoplayId !== null) {
      clearInterval(autoplayId);
      autoplayId = null;
    }
  }

  nextButton.addEventListener('click', () => {
    nextSlide();
    startAutoplay();
  });

  prevButton.addEventListener('click', () => {
    prevSlide();
    startAutoplay();
  });

  carousel.addEventListener('mouseenter', stopAutoplay);
  carousel.addEventListener('mouseleave', startAutoplay);

  window.addEventListener('resize', () => {
    currentPage = 0;
    updateCarousel();
  });

  updateCarousel();
  startAutoplay();
});