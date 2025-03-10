/**
 * TAGG - Frontend Scripts
 */
(function ($) {
  "use strict";

  // Initialize on document ready
  $(document).ready(function () {
    // Apply any hover effects or animations
    $(".tagg-logo").hover(
      function () {
        $(this).addClass("tagg-logo-hover");
      },
      function () {
        $(this).removeClass("tagg-logo-hover");
      }
    );

    // Optional: Add lightbox functionality for logos
    if (typeof $.fn.magnificPopup !== "undefined") {
      $(".tagg-logo a").magnificPopup({
        type: "image",
        gallery: {
          enabled: true,
        },
      });
    }
  });
})(jQuery);
