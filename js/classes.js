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

  let currentIndex = 0;
  let autoplayId = null;
  let cursorInsideCarousel = false;

  function getVisibleSlides() {
    if (window.matchMedia('(max-width: 30rem)').matches) {
      return 1;
    }

    if (window.matchMedia('(max-width: 60rem)').matches) {
      return 2;
    }

    return 3;
  }

  function getMaxIndex() {
    return Math.max(slides.length - getVisibleSlides(), 0);
  }

  function moveTo(index, animate = true) {
    const slideWidth = slides[0].getBoundingClientRect().width;
    const gap = parseFloat(getComputedStyle(track).gap) || 0;

    track.style.transition = animate ? 'transform 0.35s ease' : 'none';
    track.style.transform = `translateX(-${index * (slideWidth + gap)}px)`;
  }

  function nextSlide() {
    const visibleSlides = getVisibleSlides();
    const maxIndex = getMaxIndex();

    if (currentIndex >= maxIndex) {
      currentIndex = 0;
      moveTo(currentIndex, false);
      return;
    }

    currentIndex = Math.min(currentIndex + visibleSlides, maxIndex);
    moveTo(currentIndex, true);
  }

  function prevSlide() {
    const visibleSlides = getVisibleSlides();
    const maxIndex = getMaxIndex();

    if (currentIndex === 0) {
      currentIndex = maxIndex;
      moveTo(currentIndex, false);
      return;
    }

    currentIndex = Math.max(currentIndex - visibleSlides, 0);
    moveTo(currentIndex, true);
  }

  function startAutoplay() {
    if (cursorInsideCarousel) {
      return;
    }

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

  carousel.addEventListener('mouseenter', () => {
    cursorInsideCarousel = true;
    stopAutoplay();
  });

  carousel.addEventListener('mouseleave', () => {
    cursorInsideCarousel = false;
    startAutoplay();
  });

  nextButton.addEventListener('click', () => {
    nextSlide();
document.addEventListener('DOMContentLoaded', () => {
  const carousel = document.querySelector('.classes_carousel');

  if (carousel === null) {
    return;
  }

  const track = carousel.querySelector('.carousel_track');
  const prevButton = carousel.querySelector('[data-carousel-prev]');
  const nextButton = carousel.querySelector('[data-carousel-next]');

  if (track === null || prevButton === null || nextButton === null) {
    return;
  }

  let originalSlides = [];
  let pageStarts = [];
  let currentPage = 0;
  let autoplayId = null;
  let cursorInsideCarousel = false;
  let isAnimating = false;

  function getVisibleSlides() {
    if (window.matchMedia('(max-width: 30rem)').matches) {
      return 1;
    }

    if (window.matchMedia('(max-width: 60rem)').matches) {
      return 2;
    }

    return 3;
  }

  function removeClones() {
    track.querySelectorAll('.carousel_clone').forEach((clone) => {
      clone.remove();
    });
  }

  function calculatePageStarts() {
    const visibleSlides = getVisibleSlides();
    const maxStart = Math.max(originalSlides.length - visibleSlides, 0);

    const starts = [];

    for (let i = 0; i < maxStart; i += visibleSlides) {
      starts.push(i);
    }

    starts.push(maxStart);

    return [...new Set(starts)];
  }

  function buildClones() {
    removeClones();

    originalSlides = Array.from(track.querySelectorAll('.class_card'));
    pageStarts = calculatePageStarts();

    const visibleSlides = getVisibleSlides();
    const lastStart = pageStarts[pageStarts.length - 1];

    const clonesBefore = originalSlides
      .slice(lastStart, lastStart + visibleSlides)
      .map((slide) => {
        const clone = slide.cloneNode(true);
        clone.classList.add('carousel_clone');
        return clone;
      });

    const clonesAfter = originalSlides
      .slice(0, visibleSlides)
      .map((slide) => {
        const clone = slide.cloneNode(true);
        clone.classList.add('carousel_clone');
        return clone;
      });

    clonesBefore.reverse().forEach((clone) => {
      track.prepend(clone);
    });

    clonesAfter.forEach((clone) => {
      track.append(clone);
    });
  }

  function getSlideWidth() {
    const slide = track.querySelector('.class_card');

    if (slide === null) {
      return 0;
    }

    const gap = parseFloat(getComputedStyle(track).gap) || 0;

    return slide.getBoundingClientRect().width + gap;
  }

  function moveToTrackIndex(trackIndex, animate = true) {
    track.style.transition = animate ? 'transform 0.35s ease' : 'none';
    track.style.transform = `translateX(-${trackIndex * getSlideWidth()}px)`;
  }

  function moveToCurrentPage(animate = true) {
    const visibleSlides = getVisibleSlides();
    const trackIndex = visibleSlides + pageStarts[currentPage];

    moveToTrackIndex(trackIndex, animate);
  }

  function nextSlide() {
    if (isAnimating) {
      return;
    }

    const visibleSlides = getVisibleSlides();

    if (currentPage === pageStarts.length - 1) {
      isAnimating = true;

      const cloneStartIndex = visibleSlides + originalSlides.length;

      moveToTrackIndex(cloneStartIndex, true);

      track.addEventListener('transitionend', () => {
        currentPage = 0;
        moveToCurrentPage(false);
        isAnimating = false;
      }, { once: true });

      return;
    }

    currentPage++;
    isAnimating = true;
    moveToCurrentPage(true);

    track.addEventListener('transitionend', () => {
      isAnimating = false;
    }, { once: true });
  }

  function prevSlide() {
    if (isAnimating) {
      return;
    }

    if (currentPage === 0) {
      isAnimating = true;

      moveToTrackIndex(0, true);

      track.addEventListener('transitionend', () => {
        currentPage = pageStarts.length - 1;
        moveToCurrentPage(false);
        isAnimating = false;
      }, { once: true });

      return;
    }

    currentPage--;
    isAnimating = true;
    moveToCurrentPage(true);

    track.addEventListener('transitionend', () => {
      isAnimating = false;
    }, { once: true });
  }

  function startAutoplay() {
    if (cursorInsideCarousel) {
      return;
    }

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

  carousel.addEventListener('mouseenter', () => {
    cursorInsideCarousel = true;
    stopAutoplay();
  });

  carousel.addEventListener('mouseleave', () => {
    cursorInsideCarousel = false;
    startAutoplay();
  });

  nextButton.addEventListener('click', () => {
    nextSlide();
  });

  prevButton.addEventListener('click', () => {
    prevSlide();
  });

  window.addEventListener('resize', () => {
    currentPage = 0;
    buildClones();
    moveToCurrentPage(false);
  });

  buildClones();
  moveToCurrentPage(false);
  startAutoplay();
});
    if (!cursorInsideCarousel) {
      startAutoplay();
    }
  });

  prevButton.addEventListener('click', () => {
    prevSlide();

    if (!cursorInsideCarousel) {
      startAutoplay();
    }
  });

  window.addEventListener('resize', () => {
    currentIndex = 0;
    moveTo(currentIndex, false);
  });

  moveTo(currentIndex, false);
  startAutoplay();
});