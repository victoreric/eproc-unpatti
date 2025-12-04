<?php
include 'public_header.php';
include 'public_menu.php';

?>
<!-- CONTENT -->
<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">


                    <!-- please start from here -->
                    <div class="card-body">

                        <div class="me-md-3 me-xl-5">
                            <h2>Selamat Datang, <?= htmlspecialchars($_SESSION['username']) ?></h2>
                            <p>Status akun Anda: <span class="text-warning">Belum Diverifikasi</span></p>
                            <div class="card shadow-sm">
                            </div>
                        </div>



                    </div>
                    <!-- please end here -->

                </div>
            </div>
        </div>
    </div>
    <!-- content-wrapper ends -->
    <!-- CONTENT END -->

    <?php
    include 'public_footer.php';
    ?>