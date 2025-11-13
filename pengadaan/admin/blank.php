  <?php
    include 'header&menu_admin.php';
    ?>

  <!-- CONTENT -->
  <div class="container mt-4">
      <div class="alert alert-success shadow-sm">
          <h4>Selamat datang, <?= htmlspecialchars($username) ?>!</h4>
          <p>Anda login sebagai: <strong><?= $role ?></strong></p>
      </div>
  </div>

  <?php
    include 'footer_admin.php';
    ?>