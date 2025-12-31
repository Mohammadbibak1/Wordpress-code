<?php
add_action('wp_footer', function () {
    ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.title-comments, .comment-reply-title').forEach(function(el){
                const p = document.createElement('p');
                p.className = el.className;
                p.innerHTML = el.innerHTML;
                el.replaceWith(p);
            });
        });
    </script>
    <?php
});
