jQuery(document).on(
  "input change",
  'input[name="form_fields[name_filde]"]',
  function () {
    sessionStorage.setItem("ef_name_filde", jQuery(this).val());
  },
);

jQuery(document).on("elementor/popup/show", function (event, id, instance) {
  setTimeout(function () {
    var nameVal = sessionStorage.getItem("ef_name_filde");
    if (nameVal) {
      jQuery(".elementor-popup-modal *").each(function () {
        var el = jQuery(this);
        if (
          el.children().length === 0 &&
          el.text().indexOf("name_filde") !== -1
        ) {
          el.html(el.html().replace(/\[field id="name_filde"\]/g, nameVal));
        }
      });
    }
  }, 200);
});
