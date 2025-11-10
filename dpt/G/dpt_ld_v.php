<?php
include 'menu.php';
include '../link.php';
?>

<div>
<ul class="breadcrumb">
    <li class="breadcrumb-item"><a href="index"><i class="fas fa-home"></i></a></li>
    <li class="breadcrumb-item"><a href="#">DPT</a></li>
    <li class="breadcrumb-item"><a href="dpt_ld">Lihat Data</a></li>
    <li class="breadcrumb-item"><a href="#">Detail Data</a></li>
  </ul>
</div>

<?php
$id=$_GET['id'];

if(!$id){
  echo "<script>alert('Belum ada dokumen yang diupload!!'); window.location='dpt_ld' </script>";
  
} else {
?>
<div class="container-fluid">
<!-- <a href="dpt_ld.php" class="btn btn-info glyphicon glyphicon-backward" role="button"> Kembali</a> -->
<p>
<div class="embed-responsive embed-responsive-16by9">
    <iframe class='embed-responsive-item' src="../dokumenVendor/<?php echo $id; ?>"></iframe>
</div>
 <?php } ?>
   
</div>



<?php
include '../footer.php';
?>