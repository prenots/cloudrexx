cx.jQuery(document).ready(function() {
  cx.jQuery(".language-icons.dropdown").click(function() {
    cx.jQuery(this).children(".language-icons-expanded").toggle();
  }).live("mouseleave", function(event) {
    if (!cx.jQuery(event.target).is('li.language-icon') &&
      cx.jQuery('.language-icons-expanded').length > 0
    ) {
      cx.jQuery('.language-icons-expanded').each(function() {
        cx.jQuery(this).parent().parent().children().css('z-index', 'auto');
        cx.jQuery(this).hide();
      });
    }
  });
});