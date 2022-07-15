<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Shorteria</title>
    <link rel="stylesheet" href="https://unpkg.com/@picocss/pico@1.5.0/css/pico.min.css">
</head>
<body>
<main class="container">
    <?php if (!empty($flash_message)): ?>
        <article id="flash-message"><?php echo $flash_message['message']; ?></article>
    <?php endif; ?>
