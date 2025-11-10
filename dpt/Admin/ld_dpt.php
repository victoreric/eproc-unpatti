<?php
include 'menuA.php';
include '../link.php';
?>

<div class="small">
<ul class="breadcrumb">
    <li class="breadcrumb-item"><a href="index"><i class="fas fa-home"></i></a></li>
    <li class="breadcrumb-item"><a href="#">DPT</a></li>
    <li class="breadcrumb-item"><a href="#">Lihat Data</a></li>
  </ul>
</div>


<div class="container-fluid small"> 
<div class="card mb-5">
  <div class="card-header bg-primary text-white text-center h6"> Daftar Calon Penyedia
  </div>
  <div class="card-body table-responsive">
  <!-- <a href="" class="btn-sm btn-info far fa-file"> Tambah data </a> -->
  <!-- <p></p> -->
  <table id="example1" class="table table-bordered table-hover table-responsive-justify">
		<thead>
			<tr class="bg-primary text-center">
				<th>No.</th>
				<th>ID-Perusahaan</th>
				<th>Nama Perusahaan</th>
				<th>Kepemilikan</th>
				<th>Tanggal Berdiri</th>
                <th>Nomor Telepon</th>
				<th>Website</th>
				<th>Aksi</th>
			</tr>
		</thead>
		<?php
		$no=0;
		$query="SELECT * FROM data_vendor ORDER BY id_vendor DESC";
		$sql = mysqli_query($conn,$query);
		while($hasil=mysqli_fetch_array($sql)){
			$no++;
		?>
			<tr>
				<td> <?php echo $no;  ?></td>
				<td><?php echo $hasil['id_vendor']; ?></td>
				<td><?php echo $hasil['nama_awal']." ". $hasil['nama_vendor'].". ". $hasil['nama_akhir']; ?></td>

				<td><?php echo $hasil['kepemilikan'];  ?></td>

                <td><?php echo $hasil['tanggal_berdiri'];  ?></td>
                <td><?php echo $hasil['telp'];  ?></td>
				<td><?php echo $hasil['website'];  ?></td>

				<td class='text-center' > <a href='ld_dpt_d?aksi=edit&id=<?php echo $hasil['id_vendor'];?>' class='btn btn-sm btn-info fas fa-edit' > detail </a>
     
           		<!-- <a href="mu?aksi=delete&id=<?php echo $hasil['id_vendor'];?>" class="btn-sm btn-danger fas fa-trash-alt mt-2" onclick="javascript:return confirm('Anda Yakin menghapus data ini?')" > hapus </a>  -->
				</td>
			</tr>
		
		<?php } ?>
		</table>
  </div>
</div>
</div>



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