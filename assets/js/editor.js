/**
 * Admin/Editor JavaScript
 * Entry point za WordPress admin i editor
 */

(function() {
  'use strict';

  // ACF Flexible Content hooks
  if (typeof acf !== 'undefined') {
    acf.addAction('load_field_group_form', function(form, group) {
      // Custom ACF hooks here
    });
  }

  console.log('Admin scripts loaded');

})();
