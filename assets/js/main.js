/* global Swiper */

(() => {
  // BreakPoint size
  const mediumBreakPointSize = 992

  // Function definitions
  const hideDescription = () => {
    const descriptionElements = Array.from(document.querySelectorAll('.show-description'))

    descriptionElements.forEach(description => {
      description.classList.remove('show-description')
    })
  }

  const mobilePaginationSetText = () => {
    const slideInfoElements = Array.from(document.querySelectorAll('.slide-info'))

    slideInfoElements.forEach(slideInfo => {
      const paginationElement = slideInfo.parentElement.querySelector('.swiper-pagination')
      const paginationText = paginationElement.textContent

      slideInfo.querySelector('.slide-info__mobile-pagination').textContent = paginationText
    })
  }

  const swiperImagesMouseHandler = (event, eventType) => {
    const target = event.target

    if (!target.classList.contains('slide-img-container')) {
      return
    }

    const paginationElement = target.parentElement.parentElement.parentElement.querySelector('.swiper-pagination')
    const paginationStyles = paginationElement.style

    let paginationTransform
    let paginationDisplay

    if (eventType === 'mousemove') {
      paginationTransform = `translate3D(${event.offsetX}px, ${event.offsetY}px, 0)`
      paginationDisplay = 'block'
    } else if (eventType === 'mouseout') {
      paginationTransform = 'initial'
      paginationDisplay = 'none'
    }

    paginationStyles.transform = paginationTransform
    paginationStyles.display = paginationDisplay
  }

  const slideClickHandler = event => {
    const target = event.target

    if (!target.classList.contains('slide-info__title') && !target.classList.contains('slide-info__mobile-icon')) {
      return
    }

    const slideInfoElement = target.parentElement

    slideInfoElement.classList.toggle('show-description')
  }

  const detectSize = () => {
    const swipers = Array.from(document.querySelectorAll('.swiper-container-v'))

    swipers.forEach(swiper => {
      const parentWidth = swiper.parentElement.getBoundingClientRect().width

      if (parentWidth > mediumBreakPointSize) {
        swiper.addEventListener('mousemove', event => {
          swiperImagesMouseHandler(event, event.type)
        })

        swiper.addEventListener('mouseout', event => {
          swiperImagesMouseHandler(event, event.type)
        })

        swiper.classList.remove('md-breakpoint')
      } else {
        swiper.classList.add('md-breakpoint')
      }

      swiper.addEventListener('click', slideClickHandler)
    })
  }

  // Sliders initialization
  window.addEventListener('DOMContentLoaded', () => {
    // Create sliders
    new Swiper('.swiper-container-v', {// eslint-disable-line
      direction: 'vertical',
      on: {
        init: () => {
          detectSize()
        },
        resize: () => {
          detectSize()
        },
        slideChangeTransitionEnd: () => {
          hideDescription()
        }
      }
    })

    new Swiper('.swiper-container-h', {// eslint-disable-line
      lazy: {
        loadPrevNext: true
      },
      pagination: {
        el: '.swiper-pagination',
        type: 'custom',
        renderCustom: (swiper, current, total) => {
          return current + ' of ' + total
        }
      },
      on: {
        init: () => {
          mobilePaginationSetText()
        },
        slideChange: () => {
          mobilePaginationSetText()
        },
        touchStart: () => {
          hideDescription()
        }
      }
    })
  })
})()
