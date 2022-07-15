<?php if (!$isAuthenticated): ?>
    <h3>Edit</h3>
    <form method="post">
        <input type="password" name="token" placeholder="auth token" required="">
        <input type="url" name="url" placeholder="the url which you want to shorten" required=""
               value="<?= $_POST['url'] ?? $data['redirect_to'] ?>">
        <input type="text" name="shortUrl" readonly=""
               value="<?= $_POST['shortUrl'] ?? $data['short'] ?>">
        <input type="text" name="comment" placeholder="comment"
               value="<?= $_POST['comment'] ?? $data['comment'] ?>">
        <button type="submit">Save</button>
        <a href="/__details">Cancel</a>
    </form>
<?php else: ?>
<?php endif; ?>
