<?php

class Setting extends Controller
{
   public $page = __CLASS__;

   public function __construct()
   {
      $this->session_cek();
      $this->data();
      $this->v_content = $this->page . "/content";
      $this->v_viewer = $this->page . "/viewer";
   }

   public function index()
   {
      $this->view("layout", [
         "content" => $this->v_content,
         "data_operasi" => ['title' => "Setting"]
      ]);

      $this->viewer();
   }

   public function viewer()
   {
      $this->view($this->v_viewer, ["page" => $this->page]);
   }

   public function content()
   {
      $this->view($this->v_content);
   }

   public function updateCell()
   {
      $value = $_POST['value'];
      $mode = $_POST['mode'];

      $whereCount = $this->wLaundry . " AND " . $this->wCabang . " AND " . $mode . " >= 0";
      $dataCount = $this->model('M_DB_1')->count_where('setting', $whereCount);
      if ($dataCount >= 1) {
         $set = $mode . " = '" . $value . "'";
         $where = $this->wLaundry . " AND " . $this->wCabang;
         $query = $this->model('M_DB_1')->update("setting", $set, $where);
         if ($query) {
            $this->dataSynchrone();
         }
      } else {
         $cols = "id_laundry, id_cabang, print_ms";
         $vals = $this->id_laundry . "," . $this->id_cabang . "," . $value;
         $this->model('M_DB_1')->insertCols('setting', $cols, $vals);
         $this->dataSynchrone();
      }
   }

   function salin_gaji()
   {
      $id_sumber = $_POST['sumber'];
      $id_target = $_POST['target'];

      if ($id_target == 0) {
         $table = "user";
         $where = $this->wLaundry;
         $where = $this->wLaundry . " AND en = 1";
         $karyawan = $this->model('M_DB_1')->get_where($table, $where);
      }

      $gaji['laundry'] = $this->model('M_DB_1')->get_where('gaji_laundry', 'id_karyawan = ' . $id_sumber);
      foreach ($gaji['laundry'] as $gl) {
         $penjualan = $gl['jenis_penjualan'];
         $id_layanan = $gl['id_layanan'];
         $fee = $gl['gaji_laundry'];
         $target = $gl['target'];
         $bonus_target = $gl['bonus_target'];
         $max_target = $gl['max_target'];

         if ($id_target <> 0) {
            $setOne = "id_karyawan = " . $id_target . " AND jenis_penjualan = " . $penjualan . " AND id_layanan = " . $id_layanan;
            $where = $this->wLaundry . " AND " . $setOne;
            $data_main = $this->model('M_DB_1')->count_where('gaji_laundry', $where);
            if ($data_main < 1) {
               $cols = 'id_laundry, id_karyawan, jenis_penjualan, id_layanan, gaji_laundry, target, bonus_target, max_target';
               $vals = $this->id_laundry . "," . $id_target . "," . $penjualan . "," . $id_layanan . "," . $fee . "," . $target . "," . $bonus_target . "," . $max_target;
               $this->model('M_DB_1')->insertCols('gaji_laundry', $cols, $vals);
            } else {
               $set = 'gaji_laundry = ' . $fee;
               $this->model('M_DB_1')->update('gaji_laundry', $set, $where);
            }
         } else {
            foreach ($karyawan as $k) {
               $id_target = $k['id_user'];
               $setOne = "id_karyawan = " . $id_target . " AND jenis_penjualan = " . $penjualan . " AND id_layanan = " . $id_layanan;
               $where = $this->wLaundry . " AND " . $setOne;
               $data_main = $this->model('M_DB_1')->count_where('gaji_laundry', $where);
               if ($data_main < 1) {
                  $cols = 'id_laundry, id_karyawan, jenis_penjualan, id_layanan, gaji_laundry, target, bonus_target, max_target';
                  $vals = $this->id_laundry . "," . $id_target . "," . $penjualan . "," . $id_layanan . "," . $fee . "," . $target . "," . $bonus_target . "," . $max_target;
                  $this->model('M_DB_1')->insertCols('gaji_laundry', $cols, $vals);
               } else {
                  $set = 'gaji_laundry = ' . $fee;
                  $this->model('M_DB_1')->update('gaji_laundry', $set, $where);
               }
            }
         }
      }

      $gaji['pengali'] = $this->model('M_DB_1')->get_where('gaji_pengali', 'id_karyawan = ' . $id_sumber);
      foreach ($gaji['pengali'] as $gl) {
         $id_pengali = $gl['id_pengali'];
         $fee = $gl['gaji_pengali'];

         //Abaikan Jika Tunjangan
         if ($id_pengali == 4) {
            continue;
         }

         if ($id_target <> 0) {
            $cols = 'id_laundry, id_karyawan, id_pengali, gaji_pengali';
            $vals = $this->id_laundry . "," . $id_target . "," . $id_pengali . "," . $fee;
            $setOne = "id_karyawan = " . $id_target . " AND id_pengali = " . $id_pengali;
            $where = $this->wLaundry . " AND " . $setOne;
            $data_main = $this->model('M_DB_1')->count_where('gaji_pengali', $where);
            if ($data_main < 1) {
               $this->model('M_DB_1')->insertCols('gaji_pengali', $cols, $vals);
            } else {
               $set = 'gaji_pengali = ' . $fee;
               $this->model('M_DB_1')->update('gaji_pengali', $set, $where);
            }
         } else {
            foreach ($karyawan as $k) {
               $id_target = $k['id_user'];
               $setOne = "id_karyawan = " . $id_target . " AND jenis_penjualan = " . $penjualan . " AND id_layanan = " . $id_layanan;
               $where = $this->wLaundry . " AND " . $setOne;
               $data_main = $this->model('M_DB_1')->count_where('gaji_laundry', $where);
               if ($data_main < 1) {
                  $cols = 'id_laundry, id_karyawan, jenis_penjualan, id_layanan, gaji_laundry, target, bonus_target, max_target';
                  $vals = $this->id_laundry . "," . $id_target . "," . $penjualan . "," . $id_layanan . "," . $fee . "," . $target . "," . $bonus_target . "," . $max_target;
                  $this->model('M_DB_1')->insertCols('gaji_laundry', $cols, $vals);
               } else {
                  $set = 'gaji_laundry = ' . $fee;
                  $this->model('M_DB_1')->update('gaji_laundry', $set, $where);
               }
            }
         }
      }
   }

   public function upload_qris()
   {
      function compressImage($source, $destination, $quality)
      {
         $imgInfo = getimagesize($source);
         $mime = $imgInfo['mime'];
         switch ($mime) {
            case 'image/jpeg':
               $image = imagecreatefromjpeg($source);
               break;
            case 'image/png':
               $image = imagecreatefrompng($source);
               break;
            case 'image/gif':
               $image = imagecreatefromgif($source);
               break;
            default:
               $image = imagecreatefromjpeg($source);
         }

         imagejpeg($image, $destination, $quality);
         return $destination;
      }

      $uploads_dir = "files/qris_" . $this->id_laundry . "/";
      $file_name = basename($_FILES['resi']['name']);

      //hapus semua jika sudah ada, karna mau diganti file baru
      if (file_exists($uploads_dir)) {
         $cek_files = glob($uploads_dir . '*'); // get all file names
         foreach ($cek_files as $f) { // iterate files
            if (is_file($f)) {
               unlink($f); // delete file
            }
         }
      } else {
         mkdir($uploads_dir, 0777, TRUE);
      }

      $imageUploadPath =  $uploads_dir . '/' . $file_name;
      $allowExt   = array('png', 'jpg', 'jpeg', 'PNG', 'JPG', 'JPEG');
      $fileType = pathinfo($imageUploadPath, PATHINFO_EXTENSION);
      $imageTemp = $_FILES['resi']['tmp_name'];
      $fileSize   = $_FILES['resi']['size'];

      if (in_array($fileType, $allowExt) === true) {
         if ($fileSize < 1000000) {

            //COMPRESS
            // compressImage($imageTemp, $imageUploadPath, 20);
            // $query = $this->model('M_DB_1')->update("laundry", $cols, $vals);
            // if ($query == true) {
            //    echo "1";
            // } else {
            //    echo $query['error'];
            // }

            $set = "qris_path = '" . $uploads_dir . $file_name . "'";
            move_uploaded_file($imageTemp, $imageUploadPath);
            $query = $this->model('M_DB_1')->update("laundry", $set, $this->wLaundry);
            if ($query == true) {
               echo "1";
            } else {
               echo $query['error'];
            }
         } else {
            echo "FILE BIGGER THAN 10MB FORBIDDEN";
         }
      } else {
         echo "FILE EXT/TYPE FORBIDDEN";
      }
   }

   function update_metode_bayar()
   {
      $metode_bayar = $_POST['metode_bayar'];
      $set = "metode_bayar = '" . $metode_bayar . "'";
      $this->model('M_DB_1')->update("laundry", $set, $this->wLaundry);
   }
}
