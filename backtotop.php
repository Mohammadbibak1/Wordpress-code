function custom_back_to_top_script() {
    ?>
    <script>
    document.addEventListener("DOMContentLoaded", function() {
      const backToTopBtn = document.querySelector(".back-to-top");

      // پنهان کردن دکمه در ۲۰٪ اول صفحه
      window.addEventListener("scroll", function() {
        const scrollTop = window.scrollY;
        const docHeight = document.documentElement.scrollHeight - window.innerHeight;
        const scrollPercent = (scrollTop / docHeight) * 100;

        if (backToTopBtn) {
          if (scrollPercent > 20) {
            backToTopBtn.style.opacity = "1";
            backToTopBtn.style.visibility = "visible";
          } else {
            backToTopBtn.style.opacity = "0";
            backToTopBtn.style.visibility = "hidden";
          }
        }
      });

      // کلیک روی دکمه: اسکرول نرم + فید اوت
      if (backToTopBtn) {
        backToTopBtn.addEventListener("click", function(e) {
          e.preventDefault();
          window.scrollTo({ top: 0, behavior: "smooth" });

          // بعد از کمی تاخیر (برای همزمانی با اسکرول نرم) دکمه محو میشه
          setTimeout(() => {
            backToTopBtn.style.opacity = "0";
            backToTopBtn.style.visibility = "hidden";
          }, 800);
        });
      }
    });
    </script>
    <style>
      .back-to-top {
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.4s ease, visibility 0.4s ease;
      }
    </style>
    <?php
}
add_action('wp_footer', 'custom_back_to_top_script');