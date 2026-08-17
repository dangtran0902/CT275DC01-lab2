<?php
require_once __DIR__ . '/../partials/header.php';
$has_access = ensure_admin_access();
$success_message = null;
$error_message = null;
$reason = null;

$form_data = [
    'id' => null,
    'quote' => '',
    'source' => '',
    'favorite' => false
];

if ($has_access) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $form_data['id'] = isset($_POST['id']) && is_numeric($_POST['id']) ? (int) $_POST['id'] : null;
        $form_data['quote'] = trim($_POST['quote'] ?? '');
        $form_data['source'] = trim($_POST['source'] ?? '');
        $form_data['favorite'] = !empty($_POST['favorite']);

        if (!empty($form_data['id'])) {
            if ($form_data['quote'] !== '' && $form_data['source'] !== '') {
                $query = 'UPDATE quotes SET quote = ?, source = ?, favorite = ? WHERE id = ?';

                try {
                    $pdo = get_database_connection();
                    $statement = $pdo->prepare($query);
                    $statement->bindValue(1, $form_data['quote'], PDO::PARAM_STR);
                    $statement->bindValue(2, $form_data['source'], PDO::PARAM_STR);
                    $statement->bindValue(3, $form_data['favorite'], PDO::PARAM_BOOL);
                    $statement->bindValue(4, $form_data['id'], PDO::PARAM_INT);
                    $statement->execute();

                    if ($statement->rowCount() >= 0) {
                        $success_message = 'Trích dẫn này đã được cập nhật.';
                    }
                } catch (PDOException $e) {
                    $error_message = 'Không thể cập nhật Trích dẫn này';
                    $reason = $e->getMessage();
                }
            } else {
                $error_message = 'Hãy gõ vào cả Trích dẫn và Nguồn của nó!';
            }
        } else {
            $error_message = 'Không tìm thấy trích dẫn để sửa.';
        }
    } elseif (isset($_GET['id']) && is_numeric($_GET['id']) && (int) $_GET['id'] > 0) {
        $form_data['id'] = (int) $_GET['id'];

        $query = 'SELECT quote, source, favorite FROM quotes WHERE id = ?';

        try {
            $pdo = get_database_connection();
            $statement = $pdo->prepare($query);
            $statement->execute([$form_data['id']]);
            $row = $statement->fetch();

            if ($row) {
                $form_data['quote'] = $row['quote'];
                $form_data['source'] = $row['source'];
                $form_data['favorite'] = (bool) $row['favorite'];
            } else {
                $error_message = 'Không thể lấy trích dẫn này';
                $form_data['id'] = null;
            }
        } catch (PDOException $e) {
            $error_message = 'Không thể lấy trích dẫn này';
            $reason = $e->getMessage();
            $form_data['id'] = null;
        }
    } else {
        $error_message = 'Không tìm thấy trích dẫn để sửa.';
    }
} else {
    $error_message = 'Bạn không có quyền truy cập trang này';
}
?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
<div class="container my-5" style="max-width: 600px;">
    <?php if (!empty($error_message)): ?>
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <div><?= html_escape($error_message) ?></div>
            <?php if (!empty($reason)): ?>
                <div class="small text-secondary mt-1">Chi tiết: <?= html_escape($reason) ?></div>
            <?php endif; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if ($has_access && !empty($form_data['id'])): ?>
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="fw-bold text-primary m-0">Chỉnh sửa trích dẫn</h2>
                    <a href="javascript:history.back()" class="btn btn-outline-secondary btn-sm">
                        &larr; Quay lại
                    </a>
                </div>

                <?php if (!empty($success_message)): ?>
                    <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                        <?= html_escape($success_message) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <form action="edit_quote.php" method="post">
                    <input type="hidden" name="id" value="<?= html_escape((string) $form_data['id']) ?>">

                    <div class="mb-3">
                        <label for="quote" class="form-label fw-semibold">Trích dẫn</label>
                        <textarea name="quote" id="quote" rows="5" class="form-control" required><?= html_escape($form_data['quote']) ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="source" class="form-label fw-semibold">Nguồn</label>
                        <input type="text" name="source" id="source" class="form-control" value="<?= html_escape($form_data['source']) ?>" required>
                    </div>

                    <div class="mb-4 form-check">
                        <input type="checkbox" name="favorite" id="favorite" value="yes" class="form-check-input" <?= $form_data['favorite'] ? 'checked' : '' ?>>
                        <label class="form-check-label" for="favorite">
                            Đây là trích dẫn được yêu thích?
                        </label>
                    </div>

                    <div class="d-grid">
                        <input type="submit" name="submit" value="Cập nhật Trích dẫn này!" class="btn btn-primary btn-lg">
                    </div>
                </form>
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    <?php endif; ?>
</div>

<?php
// require_once __DIR__ . '/../partials/footer.php';
?>