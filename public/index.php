<?php
/* Đoạn mã xử lý PHP. */

require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../partials/footer.php';

$query = 'SELECT id, quote, source, favorite FROM quotes ORDER BY date_entered DESC LIMIT 1';

if (isset($_GET['random'])) {
    $query = 'SELECT id, quote, source, favorite FROM quotes ORDER BY RANDOM() LIMIT 1';
} elseif (isset($_GET['favorite'])) {
    $query = 'SELECT id, quote, source, favorite FROM quotes WHERE favorite = true ORDER BY RANDOM() LIMIT 1';
}
$latest_quote = null;
$error_message = null;
$reason = null;
$pdo = null;
try {
    $pdo = get_database_connection();
} catch (PDOException $e) {
    $error_message = 'Không thể kết nối đến cơ sở dữ liệu';
    $reason = $e->getMessage();
}

if ($pdo instanceof PDO) {
    try {
        $statement = $pdo->prepare($query);
        $statement->execute();
        $latest_quote = $statement->fetch();
    } catch (PDOException $e) {
        $error_message = 'Không thể lấy dữ liệu';
        $reason = $e->getMessage();
    }
}

?>

<!--
    Đoạn mã HTML trình bày nội dung trang web.
-->
<?php render_page_header(); ?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
<p class="d-flex gap-3 align-items-center">
        <a href="index.php" class="link-primary">Mới nhất</a>
        <a href="index.php?random=true" class="link-primary">Ngẫu nhiên</a>
        <a href="index.php?favorite=true" class="link-primary">Yêu thích</a>
    </p>
  
<div class="container mt-4">
    <?php if (!empty($latest_quote)): ?>
        <div class="card shadow-sm p-4 mb-3">
            <blockquote class="blockquote mb-2">
                <?= html_escape($latest_quote['quote']) ?>
            </blockquote>
            <p class="mb-2">
                &mdash; <?= html_escape($latest_quote['source']) ?>
                <?php if (!empty($latest_quote['favorite'])): ?>
                    <strong class="text-danger"> | Yêu thích!</strong>
                <?php endif; ?>
            </p>

            <?php if (is_administrator()): ?>
                <p class="mb-0">
                    <strong>Quản trị Trích dẫn:</strong>
                    <a href="edit_quote.php?id=<?= urlencode($latest_quote['id']) ?>" class="link-primary">Sửa</a>
                    <span class="mx-1">&harr;</span>
                    <a href="delete_quote.php?id=<?= urlencode($latest_quote['id']) ?>" class="link-danger">Xóa</a>
                </p>
            <?php endif; ?>
        </div>
    <?php elseif (!empty($error_message)): ?>
        <?php include __DIR__ . '/../partials/show_error.php'; ?>
    <?php else: ?>
        <p class="text-muted">Không có trích dẫn nào để hiển thị.</p>
    <?php endif; ?>

    

</div>

<?php render_page_footer(); ?>