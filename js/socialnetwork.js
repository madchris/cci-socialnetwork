(function ($, Drupal, window, document) {
  'use strict';

  // Example of Drupal behavior loaded.
  Drupal.behaviors.socialnetwork = {
    attach: function (context, settings) {
      if (typeof context['location'] !== 'undefined') { // Only fire on document load.
        var slider_arrow_path = drupalSettings.slider_arrow_path;

        $('#block-views-block-social-network-block-socialnetwork .view-content').slick({
          infinite: true,
          slidesToShow: 3,
          slidesToScroll: 1,
          prevArrow: '<img src="'+slider_arrow_path+'" class="slick-prev" title="Previous" alt="Previous"/>',
          nextArrow: '<img src="'+slider_arrow_path+'" class="slick-next" title="Next" alt="Next"/>',
        });

      }
    }
  };

})(jQuery, Drupal, this, this.document);