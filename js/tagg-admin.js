/**
 * TAGG - Admin Scripts
 */
(function ($) {
  "use strict";

  // Initialize on document ready
  $(document).ready(function () {
    // Display confirmation dialog when deleting logos
    $(".tagg-delete-logo").on("click", function (e) {
      if (!confirm("Are you sure you want to delete this logo?")) {
        e.preventDefault();
      }
    });

    // Form validation for logo details
    $("#post").on("submit", function (e) {
      if ($("#post_type").val() === "tagg_logo") {
        // Check if featured image is set
        if (!$("#_thumbnail_id").val()) {
          alert("Please set a featured image for this logo.");
          e.preventDefault();
          return false;
        }
      }
    });

    // Initialize any media uploader buttons
    $(".tagg-media-upload").on("click", function (e) {
      e.preventDefault();

      var button = $(this);
      var field = button.prev();

      // Create the media frame
      var file_frame = (wp.media.frames.file_frame = wp.media({
        title: "Select or Upload Media",
        button: {
          text: "Use this media",
        },
        multiple: false,
      }));

      // When a file is selected, run a callback
      file_frame.on("select", function () {
        var attachment = file_frame.state().get("selection").first().toJSON();
        field.val(attachment.url);
      });

      // Open the media uploader
      file_frame.open();
    });
  });
})(jQuery);
