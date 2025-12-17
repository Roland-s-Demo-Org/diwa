<div class="row">
    <div class="col-lg-12">
        <?php if(isset($_GET['message']) && !empty(isset($_GET['message']))) { ?>
            <div class="alert alert-warning"><?php echo htmlentities($_GET['message'], ENT_QUOTES); ?></div>
        <?php } else { ?>
            <div class="alert alert-success">Your message has been sent.</div>
        <?php } ?>
    </div>
</div>