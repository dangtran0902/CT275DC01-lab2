<?php
define('TITLE', 'Thêm Trích dẫn mới');
require_once __DIR__ . '/../partials/header.php';

$has_access = ensure_admin_access();
$success_message = null;
$error_message = null;
$reason = null;

$form_data = [
    'quote' => trim($_POST['quote'] ?? ''),
    'source' => trim($_POST['source'] ?? ''),
    'favorite' => !empty($_POST['favorite'])
];

if ($has_access && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($form_data['quote'] !== '' && $form_data['source'] !== '') {
        $query = 'INSERT INTO quotes (quote, source, favorite) VALUES (?, ?, ?)';

        try {
            $pdo = get_database_connection();
            $statement = $pdo->prepare($query);
            $statement->bindValue(1, $form_data['quote'], PDO::PARAM_STR);
            $statement->bindValue(2, $form_data['source'], PDO::PARAM_STR);
            $statement->bindValue(3, (int) $form_data['favorite'], PDO::PARAM_INT);
            $statement->execute();

            if ($statement->rowCount() === 1) {
                $success_message = 'Trích dẫn của bạn đã được lưu trữ.';
                $form_data = ['quote' => '', 'source' => '', 'favorite' => false];
            } else {
                $error_message = 'Không thể lưu trữ trích dẫn';
            }
        } catch (PDOException $e) {
            $error_message = 'Không thể lưu trữ trích dẫn';
            $reason = $e->getMessage();
        }
    } else {
        $error_message = 'Hãy gõ vào cả Trích dẫn và Nguồn của nó!';
    }
} elseif (!$has_access) {
    $error_message = 'Bạn không có quyền truy cập trang này';
}
?>

<?php render_page_header(); ?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
<div class="container my-5" style="max-width: 600px;">
    <h2 class="mb-4 text-center fw-bold text-primary">Thêm Trích Dẫn Mới</h2>
    <?php if (!empty($success_message)): ?>
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i><?= html_escape($success_message) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (!empty($error_message)): ?>
        <?php if (file_exists(__DIR__ . '/../partials/show_error.php')): ?>
            <?php include __DIR__ . '/../partials/show_error.php'; ?>
        <?php else: ?>
            <div class="alert alert-danger shadow-sm" role="alert">
                <?= html_escape($error_message) ?>
                <?php if ($reason): ?>
                    <br><small>Chi tiết: <?= html_escape($reason) ?></small>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
    <?php if ($has_access): ?>
        <div class="card shadow-sm border-0 bg-light p-4 rounded-3">
            <form action="add_quote.php" method="post">
                   <a href="javascript:history.back()" class="btn btn-outline-secondary btn-sm">
                        &larr; Quay lại
                    </a>
                <div class="mb-3">
                    <label for="quote" class="form-label fw-semibold">Trích dẫn</label>
                    <textarea id="quote" name="quote" class="form-control" rows="4" placeholder="Nhập câu trích dẫn..." required><?= html_escape($form_data['quote']) ?></textarea>
                </div>

                <div class="mb-3">
                    <label for="source" class="form-label fw-semibold">Nguồn</label>
                    <input type="text" id="source" name="source" class="form-control" placeholder="Tên tác giả hoặc tác phẩm..." value="<?= html_escape($form_data['source']) ?>" required>
                </div>

                <div class="form-check mb-4">
                    <input type="checkbox" id="favorite" name="favorite" class="form-check-input" value="yes" <?= $form_data['favorite'] ? 'checked' : '' ?>>
                    <label for="favorite" class="form-check-label">Đánh dấu là trích dẫn yêu thích</label>
                </div>

                <button type="submit" name="submit" class="btn btn-primary w-100 fw-semibold shadow-sm">Thêm Trích dẫn này!</button>
            </form>
        </div>
        
    <?php endif; ?>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
<?php 
// require_once __DIR__ . '/../partials/footer.php';
?>