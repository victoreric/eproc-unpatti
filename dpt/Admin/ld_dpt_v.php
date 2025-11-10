<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">

<?php
$id=$_GET['id'];

?>
    <div class="container-fluid">
        <div class="embed-responsive embed-responsive-4by3">
            <iframe class='embed-responsive-item' src="../dokumenVendor/<?php echo $id; ?>" ></iframe>
        </div>
    </div>