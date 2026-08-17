<?php
define('TITLE', 'Tìm kiếm trích dẫn');
require_once __DIR__ . '/../partials/header.php';

$error_message = null;
$reason = null;
$results = [];
$sources = [];

$keyword = trim($_GET['keyword'] ?? '');
$selected_source = trim($_GET['source'] ?? '');

// Kiểm tra xem người dùng đã thực hiện tìm kiếm chưa
$has_searched = isset($_GET['submit']) || isset($_GET['keyword']) || isset($_GET['source']);

try {
    $pdo = get_database_connection();

    // Lấy danh sách nguồn/tác giả cho Combobox
    $source_statement = $pdo->query("SELECT DISTINCT source FROM quotes WHERE source IS NOT NULL AND source <> '' ORDER BY source ASC");
    $sources = $source_statement->fetchAll(PDO::FETCH_COLUMN);

    if ($has_searched) {
        $conditions = [];
        $params = [];

        if ($keyword !== '') {
            $conditions[] = 'quote LIKE ?';
            $params[] = '%' . $keyword . '%';
        }

        if ($selected_source !== '') {
            $conditions[] = 'source = ?';
            $params[] = $selected_source;
        }

        $query = 'SELECT quote, source, favorite FROM quotes';
        if (!empty($conditions)) {
            $query .= ' WHERE ' . implode(' AND ', $conditions);
        }
        $query .= ' ORDER BY id DESC';

        $statement = $pdo->prepare($query);
        $statement->execute($params);
        $results = $statement->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) {
    $error_message = "Không thể tải danh sách nguồn/tác giả";
    $reason = $e->getMessage();
}
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">

<div class="container my-5" style="max-width: 750px;">
    <h2 class="mb-4 text-center fw-bold text-primary">Tìm Kiếm Trích Dẫn</h2>
    <a href="javascript:history.back()" class="btn btn-outline-secondary btn-sm">
        &larr; Quay lại
    </a>
    <?php if (!empty($error_message)): ?>
        <?php if (file_exists(__DIR__ . '/../partials/show_error.php')): ?>
            <?php include __DIR__ . '/../partials/show_error.php'; ?>
        <?php else: ?>
            <div class="alert alert-danger shadow-sm" role="alert">
                <?= html_escape($error_message) ?>
                <?php if (!empty($reason)): ?>
                    <br><small>Chi tiết: <?= html_escape($reason) ?></small>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <div class="card shadow-sm border-0 bg-light p-4 rounded-3 mb-4">
        <!-- <a href="javascript:history.back()" class="btn btn-outline-secondary btn-sm">
            &larr; Quay lại
        </a> -->
        <form action="search.php" method="get">
            <div class="row g-3">
                <div class="col-md-7">
                    <label for="keyword" class="form-label fw-semibold">Từ khóa trích dẫn</label>
                    <input type="text" id="keyword" name="keyword" class="form-control" placeholder="Nhập từ khóa cần tìm..." value="<?= html_escape($keyword) ?>">
                </div>
                <div class="col-md-5">
                    <label for="source" class="form-label fw-semibold">Nguồn / Tác giả</label>
                    <select id="source" name="source" class="form-select">
                        <option value="">-- Tất cả nguồn --</option>
                        <?php foreach ($sources as $src): ?>
                            <option value="<?= html_escape($src) ?>" <?= $selected_source === $src ? 'selected' : '' ?>>
                                <?= html_escape($src) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-12 mt-3">
                    <button type="submit" name="submit" value="1" class="btn btn-primary w-100 fw-semibold shadow-sm">
                        <i class="bi bi-search"></i> Tìm kiếm
                    </button>
                </div>
            </div>
        </form>
    </div>
    <?php if ($has_searched): ?>
        <div class="results-container">
            <h4 class="fw-bold mb-3 text-secondary">
                Kết quả tìm kiếm (<?= count($results) ?>)
            </h4>

            <?php if (empty($results)): ?>
                <div class="alert alert-warning shadow-sm" role="alert">
                    Không tìm thấy trích dẫn nào phù hợp với yêu cầu của bạn.
                </div>
            <?php else: ?>
                <div class="d-flex flex-column gap-3">
                    <?php foreach ($results as $item): ?>
                        <div class="card shadow-sm border-0 rounded-3">
                            <div class="card-body">
                                <blockquote class="blockquote mb-2 fs-6">
                                    "<?= html_escape($item['quote']) ?>"
                                </blockquote>
                                <div class="d-flex justify-content-between align-items-center mt-2">
                                    <span class="text-muted small">— <strong><?= html_escape($item['source']) ?></strong></span>
                                    <?php if (!empty($item['favorite'])): ?>
                                        <span class="badge bg-warning text-dark">♥ Yêu thích</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>

<?php
require_once __DIR__ . '/../partials/footer.php';
?>