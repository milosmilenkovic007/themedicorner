/**
 * Frontend JavaScript
 * Main entry point za frontend JS
 */
import '../scss/main.scss';

(function($) {
  'use strict';

  // Initialize modules
  const App = {
    init() {
      console.log('Hello Elementor Child Theme initialized');
      
      // Initialize components
      this.initializeModals();
      this.initializeAccordions();
      this.initializeCarousels();
      this.initializeScrollAnimations();
    },

    initializeModals() {
      // Modal functionality
      $(document).on('click', '[data-modal-toggle]', function(e) {
        e.preventDefault();
        const modalId = $(this).data('modal-toggle');
        $(`#${modalId}`).toggleClass('active');
      });

      $(document).on('click', '.modal__close', function() {
        $(this).closest('.modal').removeClass('active');
      });
    },

    initializeAccordions() {
      // Accordion functionality
      $(document).on('click', '.accordion__trigger', function() {
        const item = $(this).closest('.accordion__item');
        const isActive = item.hasClass('active');
        
        $(this).closest('.accordion').find('.accordion__item').removeClass('active');
        
        if (!isActive) {
          item.addClass('active');
        }
      });
    },

    initializeCarousels() {
      // Carousel functionality (if using a carousel library)
      $('.carousel').each(function() {
        // Initialize carousel
      });
    },

    initializeScrollAnimations() {
      // Observe elements for scroll animations
      if ('IntersectionObserver' in window) {
        const observer = new IntersectionObserver((entries) => {
          entries.forEach(entry => {
            if (entry.isIntersecting) {
              entry.target.classList.add('animate');
              observer.unobserve(entry.target);
            }
          });
        }, {
          threshold: 0.1
        });

        document.querySelectorAll('[data-animate]').forEach(el => {
          observer.observe(el);
        });
      }
    }
  };

  $(document).ready(() => App.init());

})(jQuery);
