<?php
include 'menuA.php';
include '../link.php';
?>
<?php
// Program menu utama
if (isset($_GET['aksi'])){
    switch($_GET['aksi']){
        case "edit":
            edit($conn);
            break;
        case "delete":
            delete($conn);
            break;
        default:
            view($conn);
        }
} else {
    view($conn);
}
?>

<?php
// fungsi view data
function view($conn){ ?>

<div>
<ul class="breadcrumb small">
    <li class="breadcrumb-item"><a href="index"><i class="fas fa-home"></i></a></li>
    <li class="breadcrumb-item"><a href="#">Master data</a></li>
    <li class="breadcrumb-item"><a href="mu">Management User</a></li>
  </ul>
</div>


<div class="container-fluid small"> 
<div class="card mb-5">
  <div class="card-header bg-primary text-white text-center h6"> Managemen akun pengguna 
  </div>
  <div class="card-body table-responsive">
  <!-- <a href="" class="btn-sm btn-info far fa-file"> Tambah data </a> -->
  <!-- <p></p> -->
  <table id="example1" class="table table-bordered table-hover table-responsive-justify">
		<thead>
			<tr class="bg-primary text-center">
				<th>No.</th>
				<th>ID-Perusahaan</th>
        <th>User-id</th>
				<th>Nama Perusahaan</th>
				<th>Email</th>
        <th>Active</th>
				<th>Aksi</th>
			</tr>
		</thead>
		<?php
		$no=0;
		$query="SELECT * FROM register ORDER BY id_register_vendor DESC";
		$sql = mysqli_query($conn,$query);
		while($hasil=mysqli_fetch_array($sql)){
			$no++;
		?>
			<tr>
				<td> <?php echo $no;  ?></td>
				<td><?php echo $hasil['id_vendor']; ?></td>
        <td><?php echo $hasil['user_id']; ?></td>
				<td><?php echo $hasil['nama_vendor']; ?></td>
				<td><?php echo $hasil['email_vendor'];  ?></td>
				<td><?php echo $hasil['active'];  ?></td>
				<td class='text-center' > <a href='mu?aksi=edit&id= <?php echo $hasil['id_register_vendor'] ;?> ' class='btn btn-sm btn-success fas fa-edit' > edit</a>
     
          <a href="mu?aksi=delete&id=<?php echo $hasil['id_register_vendor'] ;?>" class="btn btn-sm btn-danger fas fa-trash-alt" onclick="javascript:return confirm('Anda Yakin menghapus data ini?')" > hapus </a> 
				</td>
			</tr>
		
		<?php } ?>
		</table>
  </div>
</div>
</div>

<?php } 
// endFungsiViewData
?>


<?php
// fungsi edit data
function edit($conn){  ?>
	<div>
	<ul class="breadcrumb">
		<li class="breadcrumb-item"><a href="index"><i class="fas fa-home"></i></a></li>
		<li class="breadcrumb-item"><a href="#">Master Data</a></li>
		<li class="breadcrumb-item"><a href="mu?aksi=view">Manajemen Akun Pengguna</a></li>
		<li class="breadcrumb-item"><a href="#">Update Akun Pengguna</a></li>
	</ul>
	</div>
<?php	
$i=$_GET['id'];
$query="SELECT * FROM register where id_register_vendor=$i";
$sql=mysqli_query($conn,$query);
$hasil=mysqli_fetch_array($sql);
?>

<div class="container">
<div class="card mb-5">
  <div class="card-header bg-primary text-white text-center h5">Edit User Account</div>
  <div class="card-body">
  <form method="POST" action="" enctype="multipart/form-data">  
 		 <label for="id_vendor" class="">ID Perusahaan:</label>
         <input name="id_vendor" type="text" class="form-control" id="id_vendor" placeholder="ID Vendor" value="<?php echo $hasil['id_vendor']; ?>">
         <br>  
     
      <label for="user_id" class="">User id:</label>
         <input name="user_id" type="text" class="form-control" id="user_id" placeholder="User id" value="<?php echo $hasil['user_id']; ?>">
         <br>
         
        
  		<label for="nama" class="">Nama Perusahaan:</label>
         <input name="nama" type="text" class="form-control" id="nama" placeholder="Nama Perusahaan" value="<?php echo $hasil['nama_vendor']; ?>">
         <br>

         <label for="email" class="">Email Perusahaan:</label>
         <input name="email" type="email" class="form-control" id="email" placeholder="email Perusahaan" value="<?php echo $hasil['email_vendor']; ?>">

    
         <!-- <label for='level'>Level</label>
         <select name="level" id="level" class="form-control">
            <option value="1" <?php if($hasil['level']=='1'){echo 'selected';} ?>>Administrator</option>
            <option value="2" <?php if($hasil['level']=='2'){echo 'selected';}  ?>>User</option>
         </select> -->
         
         <br>
         <label for='active'> Active</label>
         <select name='active' id='active' class="form-control">
            <option value="Y" <?php if($hasil['active']=='Y'){echo 'selected'; }  ?>> Ya</option>
            <option value="N" <?php if($hasil['active']=='N'){echo 'selected';}  ?> >Tidak</option>
         </select>
         <br>

         <a href="#demo" class="btn btn-warning" data-toggle="collapse">Klik disini untuk ganti password</a>
         <div id="demo" class="collapse">
           
            <input type="checkbox" size="30px" name="klikubah" value="true"> Ceklis jika ingin mengubah password<br>
            <label for="inputpassword" class="">Masukan Password Baru:</label>
                <input name="inputpassword" type="password" class="form-control" id="inputpassword" placeholder="Password baru">
         </div>
         <br>
         <div class='text-center'>
         <input class="btn btn-success btn-submit" type="submit" name="ubah" value="Ubah">
         <a href="mu" ><input class="btn btn-success btn-danger" type="button" value="Batal"></a>
         </div>
      </form>
  </div>
</div>
</div>


<?php  
//proses edit data  
if (isset($_POST['ubah']))
{
    $id_vendor=$_POST['id_vendor'];
    $user_id=$_POST['user_id'];
    $nama_vendor = $_POST['nama'];
    $email = $_POST['email'];
    $active=$_POST['active'];
   
   $query = "UPDATE register set id_vendor='$id_vendor', user_id='$user_id' ,nama_vendor='$nama_vendor', email_vendor='$email', active='$active' WHERE id_register_vendor=$i ";

   $sql= mysqli_query($conn,$query);

   if(isset($_POST['klikubah'])){
      $newpass=md5($_POST['inputpassword']);
      $id_vendor=$_POST['id_vendor'];
      $user_id=$_POST['user_id'];
      $nama_vendor = $_POST['nama'];
      $email = $_POST['email'];
      $active=$_POST['active'];

      $query2 = "UPDATE register set id_vendor='$id_vendor', user_id='$user_id', nama_vendor='$nama_vendor', email_vendor='$email', password='$newpass', active='$active' WHERE id_register_vendor=$i ";

      $sql2= mysqli_query($conn,$query2);

      if($sql2){ // Cek jika proses simpan ke database sukses atau tidak
         // Jika Sukses, Lakukan :   
       echo "<script> alert ('User Account dan password berhasil diubah');window.location='mu';</script>"; 
      }
else {
     echo "gagal";
     } 
   }
    if($sql){ // Cek jika proses simpan ke database sukses atau tidak
                // Jika Sukses, Lakukan :   
              echo "<script> alert ('User Account berhasil diubah');window.location='mu';</script>"; 
             }
      else {
            echo "gagal";
            }           
}

}?>
<!-- endFungsiEditData -->


<?php
// fungsi Delete data
function delete($conn){ 
  if(isset($_GET['aksi']) && isset($_GET['id']) ){
          $id=$_GET['id'];
          $queri="DELETE FROM register WHERE id_register_vendor=$id ";
          $sql=mysqli_query($conn,$queri);

          if($sql){
              echo "<script>alert('Berhasil menghapus data');window.location='mu'; </script>";
          }else{
            echo "terjadi kesalahan";
          }
      }
} ?>


<script type="text/javascript">  
    $(function () {  
     $("#example1").dataTable();  
     $('#example2').dataTable({  
      "bPaginate": true,  
      "bLengthChange": false,  
      "bFilter": false,  
      "bSort": true,  
      "bInfo": true,  
      "bAutoWidth": false  
     });  
    });  
   </script> 



<?php
include '../footer.php';
?>
