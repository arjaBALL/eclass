<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= isset($title) ? $title : 'RFIC | VMS Operators Reporting System' ?></title>
    <link rel="icon" type="image/png" href="<?= base_url('public/logo.png') ?>">


    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?= base_url('public/css/main.css?v=' . time()) ?>">

    <script>
    const BASE_URL = "<?= base_url() ?>";
    </script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body style="display: flex; margin: 0;">
    <?php $this->load->view('main/sidebar'); ?>

    <div id="main-content">
        <?php $this->load->view($content, isset($data) ? $data : []); ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= base_url('public/js/main.js') ?>"></script>
    <link rel="stylesheet" href="<?= base_url('public/css/main.css?v=' . time()) ?>">

    <?php if (!empty($scripts)): ?>
    <?php foreach ($scripts as $script): ?>
    <script src="<?= base_url('public/js/' . $script . '.js') ?>"></script>
    <?php endforeach; ?>
    <?php endif; ?>
</body>

</html>