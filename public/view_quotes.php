<?php
define('TITLE', 'Xem tất cả các Trích dẫn');
require_once __DIR__ . '/../partials/header.php';

$has_access = ensure_admin_access();
$error_message = null;
$reason = null;
$quotes = [];

if ($has_access) {
    $query = 'SELECT id, quote, source, favorite FROM quotes ORDER BY date_entered DESC';
    try {
        $pdo = get_database_connection();
        $statement = $pdo->prepare($query);
        $statement->execute();
        $quotes = $statement->fetchAll();
    } catch (PDOException $se) {
        $error_message = 'Không thể lấy dữ liệu từ hệ thống.';
        $reason = $se->getMessage();
    }
} else {
    $error_message = 'Bạn không có quyền truy cập trang này.';
}
?>

<?php render_page_header(); ?>

<!-- Thêm CDN Bootstrap 5 nếu header.php chưa có -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<div class="container my-5">
    <h2 class="mb-4 text-center fw-bold text-dark">Tất Cả Trích Dẫn</h2>
   <a href="javascript:history.back()" class="btn btn-outline-secondary btn-sm">
                        &larr; Quay lại
                    </a>
    <?php if ($has_access && empty($error_message)): ?>

        <?php if (!empty($quotes)): ?>
            <div class="row g-4">
                <?php foreach ($quotes as $quote): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100 shadow-sm border-0 bg-light">
                            <div class="card-body d-flex flex-column justify-content-between">
                                <div>
                                    <figure class="mb-3">
                                        <blockquote class="blockquote fs-5">
                                            <p class="mb-2">“<?= html_escape($quote['quote']) ?>”</p>
                                        </blockquote>
                                        <figcaption class="blockquote-footer mb-0">
                                            <?= html_escape($quote['source']) ?>
                                        </figcaption>
                                    </figure>

                                    <?php if (!empty($quote['favorite'])): ?>
                                        <span class="badge bg-warning text-dark mb-2">♥ Yêu thích!</span>
                                    <?php endif; ?>
                                </div>

                                <div class="pt-3 border-top mt-3 d-flex justify-content-between align-items-center">
                                    <small class="text-muted fw-semibold">Quản trị:</small>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="edit_quote.php?id=<?= urlencode($quote['id']) ?>" class="btn btn-outline-primary">Sửa</a>
                                        <a href="delete_quote.php?id=<?= urlencode($quote['id']) ?>" class="btn btn-outline-danger" onclick="return confirm('Bạn có chắc chắn muốn xóa trích dẫn này?');">Xóa</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-info text-center">Chưa có trích dẫn nào trong cơ sở dữ liệu.</div>
        <?php endif; ?>

    <?php else: ?>
        <div class="alert alert-danger shadow-sm" role="alert">
            <h5 class="alert-heading fw-bold">Thông báo lỗi</h5>
            <p class="mb-0"><?= html_escape($error_message) ?></p>
            <?php if ($reason): ?>
                <hr>
                <p class="mb-0"><small>Chi tiết: <?= html_escape($reason) ?></small></p>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<?php 
// require_once __DIR__ . '/../partials/footer.php';
// render_page_footer();
?>