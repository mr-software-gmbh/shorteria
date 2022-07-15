<?php if (!$isAuthenticated): ?>
    <form method="post">
        <input type="password" name="token" placeholder="auth token" required="">
        <input type="url" name="url" placeholder="the url which you want to shorten" required=""
               value="<?= $_POST['url'] ?? '' ?>">
        <input type="text" name="shortUrl" readonly=""
               value="<?= $_POST['shortUrl'] ?? $shortUrl ?>">
        <input type="text" name="comment" placeholder="comment"
               value="<?= $_POST['comment'] ?? '' ?>">
        <button type="submit">Submit</button>
    </form>
<?php else: ?>
    <input type="text" value="<?= $storedShortUrl ?>" id="shortendUrl">
    <div class="grid">
        <div>
            <button onclick="copyToClipboard()">Copy shortend url to clipboard</button>
        </div>
        <div>
            <a target="_blank" href="<?= $storedShortUrl ?>">
                <button onclick="copyToClipboard()">Open shortend url</button>
            </a>
        </div>
    </div>
    <script>
        function copyToClipboard() {
            const copyShortendUrl = document.getElementById("shortendUrl");
            copyShortendUrl.select();
            copyShortendUrl.setSelectionRange(0, 99999);
            navigator.clipboard.writeText(copyShortendUrl.value);
        }
    </script>
    <hr>
    <a href="">Shorten a new url</a>
<?php endif; ?>
