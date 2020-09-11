'use strict';
/* global Swiper */
(function (window, document) {
  // BreakPoint size
  var mediumBreakPointSize = 992
  // Function definitions
  var hideDescription = function () {
    var descriptionElements = Array.from(document.querySelectorAll('.show-description'))
    descriptionElements.forEach(function (description) {
      description.classList.remove('show-description')
    })
  }
  var mobilePaginationSetText = function () {
    var slideInfoElements = Array.from(document.querySelectorAll('.slide-info'))
    slideInfoElements.forEach(function (slideInfo) {
      var paginationElement = slideInfo.parentElement.querySelector('.swiper-pagination')
      var paginationText = paginationElement.textContent
      slideInfo.querySelector('.slide-info__mobile-pagination').textContent = paginationText
    })
  }
  var swiperImagesMouseHandler = function (event, eventType) {
    var target = event.target
    if (!target.classList.contains('slide-img-container')) {
      return
    }
    var paginationElement = target.parentElement.parentElement.parentElement.querySelector('.swiper-pagination')
    var paginationStyles = paginationElement.style
    var paginationTransform
    var paginationDisplay
    if (eventType === 'mousemove') {
      paginationTransform = 'translate3D(' + event.offsetX + 'px, ' + event.offsetY + 'px, 0)'
      paginationDisplay = 'block'
    } else if (eventType === 'mouseout') {
      paginationTransform = 'initial'
      paginationDisplay = 'none'
    }
    paginationStyles.transform = paginationTransform
    paginationStyles.display = paginationDisplay
  }
  var slideClickHandler = function (event) {
    var target = event.target
    if (!target.classList.contains('slide-info__title') && !target.classList.contains('slide-info__mobile-icon')) {
      return
    }
    var slideInfoElement = target.parentElement
    slideInfoElement.classList.toggle('show-description')
  }
  var detectSize = function () {
    var swipers = Array.from(document.querySelectorAll('.swiper-container-v'))
    swipers.forEach(function (swiper) {
      var parentWidth = swiper.parentElement.getBoundingClientRect().width
      if (parentWidth > mediumBreakPointSize) {
        swiper.addEventListener('mousemove', function (event) {
          swiperImagesMouseHandler(event, event.type)
        })
        swiper.addEventListener('mouseout', function (event) {
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
  window.addEventListener('DOMContentLoaded', function () {
    // Create sliders
    new Swiper('.swiper-container-v', {
      direction: 'vertical',
      on: {
        init: function () {
          detectSize()
        },
        resize: function () {
          detectSize()
        },
        slideChangeTransitionEnd: function () {
          hideDescription()
        }
      }
    })
    new Swiper('.swiper-container-h', {
      lazy: {
        loadPrevNext: true
      },
      pagination: {
        el: '.swiper-pagination',
        type: 'custom',
        renderCustom: function (swiper, current, total) {
          return current + ' of ' + total
        }
      },
      on: {
        init: function () {
          mobilePaginationSetText()
        },
        slideChange: function () {
          mobilePaginationSetText()
        },
        touchStart: function () {
          hideDescription()
        }
      }
    })
  })
})(window, document)
