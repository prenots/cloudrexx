cx.jQuery(document).ready(function() {
  var languageIconsDropdown = cx.jQuery(".language-icons.dropdown");
  cx.jQuery(document).click(function() {
    cx.jQuery(".language-icons-expanded").hide();
  });
  languageIconsDropdown.click(function(e) {
    e.stopPropagation();
    cx.jQuery(this).children(".language-icons-expanded").toggle();
    languageIconsDropdown.not(this).children(".language-icons-expanded").hide();
  });
});