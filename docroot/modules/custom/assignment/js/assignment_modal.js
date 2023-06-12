(function ($) {
  Drupal.behaviors.assignmentModal = {
    attach: function (context, settings) {
      // Listen for clicks on links with the 'my-link-class' CSS class.
      $(context).find('.my-link-class').once('my-link-modal').click(function (e) {
        e.preventDefault();

        // Get the URL from the data-url attribute.
        var url = $(this).data('url');

        // Open the URL in a modal.
        Drupal.Modal.dialog(url, {
          width: '80%',
          height: '80%'
        });
      });
    }
  };
})(jQuery);
