    
<p>&nbsp;</p>
<hr>
<h2>FOOTER INFO</h2>

<h4>User info</h4>
<?php
    wts($user);
?>

<h4>SESSION</h4>
<?php wts($_SESSION); ?>
<?= "Session ID:" . session_id(); ?>

<h4>COOKIE</h4>
<?php wts($_COOKIE); ?>
</body>
</html>