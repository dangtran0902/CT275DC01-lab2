<?php

function render_page_footer(bool $is_loggedin = false): void
{
?>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <!-- END CHANGEABLE CONTENT. -->
    <?php if ((is_administrator() && (basename($_SERVER['PHP_SELF']) !== 'logout.php')) || $is_loggedin): ?>
        <hr>
        <p class="d-flex gap-3 align-items-center">
            <a href="add_quote.php" class="link-primary">Thêm Trích dẫn</a>
            <span>&harr;</span>
            <a href="view_quotes.php" class="link-primary">Xem tất cả Trích dẫn</a>
            <span>&harr;</span>
            <a href="search.php" class="link-primary">Tim kiem</a>
            <span>&harr;</span>
            <a href="logout.php" class="link-danger">Đăng xuất</a>
        </p>
    <?php else: ?>
        <hr>
        <p class="d-flex gap-3 align-items-center">
            <a href="/" class="link-primary">Trang chủ</a>
            <span>&harr;</span>
            <a href="login.php" class="link-primary">Đăng nhập</a>
        </p>
    <?php endif; ?>
    </div><!-- container -->
    <footer id="footer" class="text-center text-muted py-3 mt-4 border-top">
        Content &copy; 2025
    </footer>
    </body>

    </html>
<?php
}