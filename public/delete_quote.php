<?php
require_once __DIR__ . '/../partials/header.php';
$has_access = ensure_admin_access();
$success_message = null;
$error_message = null;
$reason = null;
$delete_complete = null;
$quote_detail = null;
$form_data = [];

if ($has_access) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $quote_id = isset($_POST['id']) && is_numeric($_POST['id']) ? (int) $_POST['id'] : null;

        if (!empty($quote_id)) {
            $query = 'DELETE FROM quotes WHERE id = ?';

            try {
                $pdo = get_database_connection();
                $statement = $pdo->prepare($query);
                $statement->execute([$quote_id]);

                if ($statement->rowCount() === 1) {
                    $delete_complete = true;
                    $success_message = 'Trích dẫn này đã được xóa thành công.';
                } else {
                    $error_message = 'Không tìm thấy trích dẫn để xóa.';
                }
            } catch (PDOException $e) {
                $error_message = 'Không thể xóa trích dẫn này';
                $reason = $e->getMessage();
            }
        } else {
            $error_message = 'Không tìm thấy trích dẫn để xóa.';
        }
    } elseif (isset($_GET['id']) && is_numeric($_GET['id']) && (int) $_GET['id'] > 0) {
        $form_data['id'] = (int) $_GET['id'];

        // Đã thêm id vào câu lệnh SELECT
        $query = 'SELECT id, quote, source, favorite FROM quotes WHERE id = ?';

        try {
            $pdo = get_database_connection();
            $statement = $pdo->prepare($query);
            $statement->execute([$form_data['id']]);
            $quote_detail = $statement->fetch();

            if (!$quote_detail) {
                $error_message = 'Không thể lấy trích dẫn này';
                $form_data['id'] = null;
            }
        } catch (PDOException $e) {
            $error_message = 'Không thể lấy trích dẫn này';
            $reason = $e->getMessage();
            $form_data['id'] = null;
        }
    } else {
        $error_message = 'Không tìm thấy trích dẫn để xóa.';
    }
} else {
    $error_message = 'Bạn không có quyền truy cập trang này';
}
?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">  
<!-- Đoạn mã HTML trình bày nội dung trang web -->
<?php if ($has_access): ?>
    <?php if ($error_message): ?>
        <p class="error"><?= html_escape($error_message) ?></p>
    <?php elseif ($success_message): ?>
        <p class="success"><?= html_escape($success_message) ?></p>
    <?php endif; ?>

  <?php if ($delete_complete): ?>
    <p>Trích dẫn đã bị xóa.</p>
    <script>
        alert('Trích dẫn đã bị xóa.');
        window.location.href = 'view_quotes.php';
    </script>
    <?php elseif (!empty($quote_detail)): ?>
        <form action="delete_quote.php" method="post">
            <p>Bạn có chắc là muốn xóa trích dẫn này?</p>
            <blockquote><?= html_escape($quote_detail['quote']) ?></blockquote>
            <div>
                <p><?= html_escape($quote_detail['source']) ?></p>
                <?php if (!empty($quote_detail['favorite'])): ?>
                    <strong>| Yêu thích!</strong>
                <?php endif; ?>
            </div>
            <input type="hidden" name="id" value="<?= html_escape((string) $quote_detail['id']) ?>">
            <input type="submit" name="submit" value="Xóa trích dẫn này!">
        </form>
        
    <?php endif; ?>

<?php else: ?>
    <p class="error"><?= html_escape($error_message) ?></p>
<?php endif; ?>
<?php 
// require_once __DIR__ . '/../partials/footer.php'; 
?>