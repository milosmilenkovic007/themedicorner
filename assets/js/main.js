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
      this.initializePackagesDetails();
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

    initializePackagesDetails() {
      const setActive = ($root, index) => {
        const idx = Number(index);
        if (Number.isNaN(idx)) return;

        // Remove previous is-active-* classes
        const classes = ($root.attr('class') || '').split(/\s+/);
        const next = classes.filter(c => !/^is-active-\d+$/.test(c));
        next.push(`is-active-${idx}`);
        $root.attr('class', next.join(' '));

        $root.find('.packages-details__tab').each(function() {
          const $btn = $(this);
          const isCurrent = Number($btn.data('index')) === idx;
          $btn.toggleClass('is-active', isCurrent);
          $btn.attr('aria-selected', isCurrent ? 'true' : 'false');
        });
      };

      const resetSectionsState = ($root) => {
        // Desktop rows
        const $rows = $root.find('.packages-details__grid-row[data-pd-row]');
        if ($rows.length) {
          $rows.each(function(i) {
            const $row = $(this);
            const $toggle = $row.find('[data-pd-row-toggle]').first();
            const shouldOpen = i === 0;
            $row.toggleClass('is-collapsed', !shouldOpen);
            if ($toggle.length) {
              $toggle.attr('aria-expanded', shouldOpen ? 'true' : 'false');
            }
          });
        }

        // Mobile <details>
        const $details = $root.find('.packages-details__accordion-item');
        if ($details.length) {
          $details.each(function(i) {
            if (i === 0) {
              $(this).attr('open', 'open');
            } else {
              $(this).removeAttr('open');
            }
          });
        }
      };

      // Init each module
      $('[data-packages-details]').each(function() {
        const $root = $(this);
        const $first = $root.find('.packages-details__tab').first();
        const initIdx = $first.length ? Number($first.data('index')) : 0;
        setActive($root, Number.isNaN(initIdx) ? 0 : initIdx);
        resetSectionsState($root);
      });

      // Click handler
      $(document).on('click', '.packages-details__tab', function() {
        const $btn = $(this);
        const $root = $btn.closest('[data-packages-details]');
        if (!$root.length) return;
        setActive($root, $btn.data('index'));
        resetSectionsState($root);
      });

      const toggleRow = ($root, $toggle) => {
        const $row = $toggle.closest('.packages-details__grid-row');
        if (!$row.length) return;
        const willOpen = $row.hasClass('is-collapsed');

        if (willOpen) {
          // Close all others (accordion behavior)
          $root.find('.packages-details__grid-row[data-pd-row]').each(function() {
            const $r = $(this);
            const $t = $r.find('[data-pd-row-toggle]').first();
            $r.addClass('is-collapsed');
            if ($t.length) $t.attr('aria-expanded', 'false');
          });

          // Open current
          $row.removeClass('is-collapsed');
          $toggle.attr('aria-expanded', 'true');
        } else {
          // Allow closing the current row
          $row.addClass('is-collapsed');
          $toggle.attr('aria-expanded', 'false');
        }
      };

      // Desktop: collapse/expand section rows (accordion-like)
      $(document).on('click', '[data-packages-details] .packages-details__section-title[data-pd-row-toggle]', function(e) {
        // Avoid interfering with any nested interactive elements (if introduced later)
        if ($(e.target).closest('a, button, input, select, textarea').length) return;
        const $toggle = $(this);
        const $root = $toggle.closest('[data-packages-details]');
        if (!$root.length) return;
        toggleRow($root, $toggle);
      });

      $(document).on('keydown', '[data-packages-details] .packages-details__section-title[data-pd-row-toggle]', function(e) {
        const key = e.key || e.keyCode;
        if (key === 'Enter' || key === ' ' || key === 13 || key === 32) {
          e.preventDefault();
          const $toggle = $(this);
          const $root = $toggle.closest('[data-packages-details]');
          if (!$root.length) return;
          toggleRow($root, $toggle);
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
