<?php if (!$isAuthenticated): ?>
    <form method="post">
        <input type="password" name="token" placeholder="auth token" required="">
        <button type="submit">Submit</button>
    </form>
<?php else: ?>
    <details open>
        <summary role="button">Overview</summary>
        <table role="grid">
            <thead>
            <tr>
                <th scope="col">Redirect to</th>
                <th scope="col">Short url</th>
                <th scope="col">Hits</th>
                <th scope="col">Comment</th>
                <th scope="col">Created at</th>
                <th scope="col">Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($details as $detail): ?>
                <tr>
                    <td><a target="_blank" href="<?= $detail['redirect_to'] ?>"><?= $detail['redirect_to'] ?></a></td>
                    <td><a target="_blank" href="<?= $detail['short'] ?>"><?= $detail['short'] ?></a></td>
                    <td><?= $detail['counter'] ?></td>
                    <td><?= $detail['comment'] ?></td>
                    <td><?= $detail['created_at'] ?></td>
                    <td>
                        <a href="/__edit?shortcode=<?= $detail['short'] ?>">Edit</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </details>
<?php endif; ?>