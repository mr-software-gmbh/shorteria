</main>
<footer class="container">
    <div class="grid">
        <div><a href="#">Shorteria</a> &copy; <a href="https://mrsoft.gmbh">MR Software
                GmbH</a> <?php echo date('Y'); ?></div>
    </div>
</footer>
<script>
    const flashArticle = document.getElementById('flash-message');
    setTimeout(_ => {
        flashArticle.remove();
    }, 5000);
</script>
</body>
</html>