<?php

class Setting extends Controller
{
   public $page = __CLASS__;

   public function __construct()
   {
      $this->session_cek(1);
      $this->operating_data();
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

      $whereCount = $this->wCabang . " AND " . $mode . " >= 0";
      $dataCount = $this->db(0)->count_where('setting', $whereCount);
      if ($dataCount >= 1) {
         $set = $mode . " = '" . $value . "'";
         $where = $this->wCabang;
         $query = $this->db(0)->update("setting", $set, $where);
         if ($query['errno'] == 0) {
            $this->dataSynchrone($_SESSION['user']['id_user']);
         }
      } else {
         $cols = "id_cabang, print_ms";
         $vals = $this->id_cabang . "," . $value;
         $this->db(0)->insertCols('setting', $cols, $vals);
         $this->dataSynchrone($_SESSION['user']['id_user']);
      }
   }

   function salin_gaji()
   {
      $id_sumber = $_POST['sumber'];
      $id_target = $_POST['target'];

      if ($id_target == 0) {
         $table = "user";
         $where = "en = 1";
         $karyawan = $this->db(0)->get_where($table, $where);
      }

      $gaji['laundry'] = $this->db(0)->get_where('gaji_laundry', 'id_karyawan = ' . $id_sumber);
      foreach ($gaji['laundry'] as $gl) {
         $penjualan = $gl['jenis_penjualan'];
         $id_layanan = $gl['id_layanan'];
         $fee = $gl['gaji_laundry'];
         $target = $gl['target'];
         $bonus_target = $gl['bonus_target'];
         $max_target = $gl['max_target'];

         if ($id_target <> 0) {
            $where = "id_karyawan = " . $id_target . " AND jenis_penjualan = " . $penjualan . " AND id_layanan = " . $id_layanan;
            $data_main = $this->db(0)->count_where('gaji_laundry', $where);
            if ($data_main < 1) {
               $cols = 'id_karyawan, jenis_penjualan, id_layanan, gaji_laundry, target, bonus_target, max_target';
               $vals = $id_target . "," . $penjualan . "," . $id_layanan . "," . $fee . "," . $target . "," . $bonus_target . "," . $max_target;
               $this->db(0)->insertCols('gaji_laundry', $cols, $vals);
            } else {
               $set = 'gaji_laundry = ' . $fee;
               $this->db(0)->update('gaji_laundry', $set, $where);
            }
         } else {
            foreach ($karyawan as $k) {
               $id_target = $k['id_user'];
               $where = "id_karyawan = " . $id_target . " AND jenis_penjualan = " . $penjualan . " AND id_layanan = " . $id_layanan;
               $data_main = $this->db(0)->count_where('gaji_laundry', $where);
               if ($data_main < 1) {
                  $cols = 'id_karyawan, jenis_penjualan, id_layanan, gaji_laundry, target, bonus_target, max_target';
                  $vals = $id_target . "," . $penjualan . "," . $id_layanan . "," . $fee . "," . $target . "," . $bonus_target . "," . $max_target;
                  $this->db(0)->insertCols('gaji_laundry', $cols, $vals);
               } else {
                  $set = 'gaji_laundry = ' . $fee;
                  $this->db(0)->update('gaji_laundry', $set, $where);
               }
            }
         }
      }

      $gaji['pengali'] = $this->db(0)->get_where('gaji_pengali', 'id_karyawan = ' . $id_sumber);
      foreach ($gaji['pengali'] as $gl) {
         $id_pengali = $gl['id_pengali'];
         $fee = $gl['gaji_pengali'];

         //Abaikan Jika Tunjangan
         if ($id_pengali == 4) {
            continue;
         }

         if ($id_target <> 0) {
            $cols = 'id_karyawan, id_pengali, gaji_pengali';
            $vals = $id_target . "," . $id_pengali . "," . $fee;
            $where = "id_karyawan = " . $id_target . " AND id_pengali = " . $id_pengali;
            $data_main = $this->db(0)->count_where('gaji_pengali', $where);
            if ($data_main < 1) {
               $this->db(0)->insertCols('gaji_pengali', $cols, $vals);
            } else {
               $set = 'gaji_pengali = ' . $fee;
               $this->db(0)->update('gaji_pengali', $set, $where);
            }
         } else {
            foreach ($karyawan as $k) {
               $id_target = $k['id_user'];
               $where = "id_karyawan = " . $id_target . " AND jenis_penjualan = " . $penjualan . " AND id_layanan = " . $id_layanan;
               $data_main = $this->db(0)->count_where('gaji_laundry', $where);
               if ($data_main < 1) {
                  $cols = 'id_karyawan, jenis_penjualan, id_layanan, gaji_laundry, target, bonus_target, max_target';
                  $vals = $id_target . "," . $penjualan . "," . $id_layanan . "," . $fee . "," . $target . "," . $bonus_target . "," . $max_target;
                  $this->db(0)->insertCols('gaji_laundry', $cols, $vals);
               } else {
                  $set = 'gaji_laundry = ' . $fee;
                  $this->db(0)->update('gaji_laundry', $set, $where);
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

      $uploads_dir = "assets/img/qris/";
      $file_name = "qris.jpg";

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
      $allowExt = array('jpg');
      $fileType = pathinfo($imageUploadPath, PATHINFO_EXTENSION);
      $imageTemp = $_FILES['resi']['tmp_name'];
      $fileSize = $_FILES['resi']['size'];

      if (in_array($fileType, $allowExt) === true) {
         if ($fileSize < 600000) {
            move_uploaded_file($imageTemp, $imageUploadPath);
            echo 1;
         } else {
            echo "FILE BIGGER THAN 10MB FORBIDDEN";
         }
      } else {
         echo "jpg ONLY";
      }
   }
}
