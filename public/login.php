<?php
/* Đoạn mã xử lý PHP. */

define('TITLE', 'Login');

require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../partials/footer.php';

$loggedin = isset($_SESSION['user']);
$error_message = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = strtolower(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';

    if ($email !== '' && $password !== '') {
        if ($email === 'me@example.com' && $password === 'testpass') {
            $_SESSION['user'] = 'me';
            header('Location: index.php');
            exit;
        } else {
            $error_message = 'Địa chỉ email và mật khẩu không khớp!';
        }
    } else {
        $error_message = 'Hãy đảm bảo rằng bạn cung cấp đầy đủ địa chỉ email và mật khẩu!';
    }
}
?>

<!--
    Đoạn mã HTML trình bày nội dung trang web.
-->
<?php render_page_header(); ?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
<?php if (!empty($error_message)): ?>
    <?php include __DIR__ . '/../partials/show_error.php'; ?>
<?php endif; ?>
<?php if ($loggedin): ?>
    <p class="alert alert-success">Bạn đã đăng nhập!</p>
<?php else: ?>
    <h2 class="mb-3">Form Đăng nhập</h2>
    <form action="login.php" method="post" class="col-md-4">
        <div class="mb-3">
            <label for="email" class="form-label">Địa chỉ Email</label>
            <input type="email" id="email" name="email" class="form-control"
                value="<?= html_escape($_POST['email'] ?? '') ?>">
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Mật khẩu</label>
            <input type="password" id="password" name="password" class="form-control">
        </div>
        <button type="submit" name="submit" class="btn btn-primary">Đăng nhập!</button>
    </form>
<?php endif; ?>
<?php
render_page_footer($loggedin);
?>