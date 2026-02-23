// ==============================
// دکمه بازگشت به بالا (Back to Top) با انیمیشن نرم ورود و خروج
// ==============================
function custom_back_to_top_script() {
    ?>
    <style>
    .back-to-top {
        opacity: 0;
        visibility: hidden;
        transform: translateY(20px) scale(0.95); /* کمی کوچک و پایین‌تر */
        transition: opacity 0.4s ease, visibility 0.4s ease, transform 0.4s ease;
    }

    .back-to-top.show {
        opacity: 1;
        visibility: visible;
        transform: translateY(0) scale(1); /* جای اصلی و اندازه اصلی */
    }
    </style>

    <script>
    document.addEventListener("DOMContentLoaded", function() {
        const backToTopBtn = document.querySelector(".back-to-top");

        if (!backToTopBtn) return;

        function toggleButton() {
            const scrollTop = window.scrollY;
            const docHeight = document.documentElement.scrollHeight - window.innerHeight;
            const scrollPercent = (scrollTop / docHeight) * 100;

            if (scrollPercent > 20) {
                backToTopBtn.classList.add("show");
            } else {
                backToTopBtn.classList.remove("show");
            }
        }

        window.addEventListener("scroll", toggleButton);
        toggleButton(); // اجرای اولیه

        // اسکرول نرم به بالا
        backToTopBtn.addEventListener("click", function(e) {
            e.preventDefault();
            window.scrollTo({ top: 0, behavior: "smooth" });
        });
    });
    </script>
    <?php
}
add_action('wp_footer', 'custom_back_to_top_script');
