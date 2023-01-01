<?php

class Gaji extends Controller
{
   public function __construct()
   {
      $this->session_cek();
      $this->data();
      $this->table = 'penjualan';
   }

   public function index()
   {
      $viewData = 'gaji/rekap_gaji_bulanan';

      $user['id'] = 0;
      $user['kasbon'] = 0;
      $dataTanggal = array();
      $dataGajiLaundry = array();
      //KINERJA
      if (isset($_POST['m'])) {
         $user['id'] = $_POST['user'];
         $date = $_POST['Y'] . "-" . $_POST['m'];
         $dataTanggal = array('bulan' => $_POST['m'], 'tahun' => $_POST['Y']);
      } else {
         $date = date('Y-m');
      }
      $table = "operasi";
      $tb_join = $this->table;
      $join_where = "operasi.id_penjualan = penjualan.id_penjualan";
      $where = "operasi.id_laundry = " . $this->id_laundry . " AND penjualan.bin = 0 AND operasi.id_user_operasi = " . $user['id'] . " AND operasi.insertTime LIKE '" . $date . "%'";
      $data_operasi = ['title' => 'Gaji Bulanan - Rekap'];
      $data_main = $this->model('M_DB_1')->innerJoin1_where($table, $tb_join, $join_where, $where);

      $cols = "id_user, id_cabang, COUNT(id_user) as terima";
      $where = $this->wLaundry . " AND id_user = " . $user['id'] . " AND  insertTime LIKE '" . $date . "%' GROUP BY id_user, id_cabang";
      $data_terima = $this->model('M_DB_1')->get_cols_where($this->table, $cols, $where, 1);

      $cols = "id_user_ambil, id_cabang, COUNT(id_user_ambil) as kembali";
      $where = $this->wLaundry . " AND id_user_ambil = " . $user['id'] . " AND tgl_ambil LIKE '" . $date . "%' GROUP BY id_user_ambil, id_cabang";
      $data_kembali = $this->model('M_DB_1')->get_cols_where($this->table, $cols, $where, 1);

      //KASBON
      $cols = "id_kas, jumlah, insertTime";
      $where = $this->wCabang . " AND jenis_transaksi = 5 AND jenis_mutasi = 2 AND status_mutasi = 3 AND id_client = " . $user['id'];
      $user['kasbon'] = $this->model('M_DB_1')->get_cols_where("kas", $cols, $where, 1);

      foreach ($user['kasbon'] as $key => $k) {
         $ref = $k['id_kas'];
         $where = "ref = '" . $ref . "'";
         $countPotong = $this->model('M_DB_1')->count_where('gaji_result', $where);
         if ($countPotong == 1) {
            unset($user['kasbon'][$key]);
         }
      }

      $gaji = array();
      $gaji['gaji_laundry'] = $this->model('M_DB_1')->get_where('gaji_laundry', 'id_laundry = ' . $this->id_laundry);
      $gaji['pengali_list'] = $this->model('M_DB_1')->get('gaji_pengali_jenis');
      $gaji['gaji_pengali'] = $this->model('M_DB_1')->get_where('gaji_pengali', 'id_laundry = ' . $this->id_laundry);
      $gaji['gaji_pengali_data'] = $this->model('M_DB_1')->get_where('gaji_pengali_data', 'id_laundry = ' . $this->id_laundry . " AND tgl = '" . $date . "'");
      $gaji['fix'] = $this->model('M_DB_1')->get_where('gaji_result', 'id_laundry = ' . $this->id_laundry . " AND tgl = '" . $date . "' AND id_karyawan = " . $user['id'] . " ORDER BY tipe ASC ");

      $this->view('layout', ['data_operasi' => $data_operasi]);

      $this->view($viewData, [
         'data_main' => $data_main,
         'dataTanggal' => $dataTanggal,
         'dTerima' => $data_terima,
         'dKembali' => $data_kembali,
         'user' => $user,
         'gaji' => $gaji,
         'gajiLaundry' => $dataGajiLaundry,
      ]);
   }

   public function set_gaji_laundry()
   {
      $penjualan = $_POST['penjualan'];
      $id_layanan = $_POST['layanan'];
      $id_user = $_POST['id_user'];
      $fee = $_POST['fee'];
      $target = $_POST['target'];
      $bonus_target = $_POST['bonus_target'];
      $max_target = $_POST['max_target'];

      $cols = 'id_laundry, id_karyawan, jenis_penjualan, id_layanan, gaji_laundry, target, bonus_target, max_target';
      $vals = $this->id_laundry . "," . $id_user . "," . $penjualan . "," . $id_layanan . "," . $fee . "," . $target . "," . $bonus_target . "," . $max_target;

      $setOne = "id_karyawan = " . $id_user . " AND jenis_penjualan = " . $penjualan . " AND id_layanan = " . $id_layanan;
      $where = $this->wLaundry . " AND " . $setOne;
      $data_main = $this->model('M_DB_1')->count_where('gaji_laundry', $where);

      if ($data_main < 1) {
         $do = $this->model('M_DB_1')->insertCols('gaji_laundry', $cols, $vals);
         if ($do['errno'] == 0) {
            echo 1;
         }
      } else {
         echo "Data sudah ter-Set!";
      }
   }

   public function set_gaji_pengali()
   {
      $id_pengali = $_POST['pengali'];
      $id_user = $_POST['id_user'];
      $fee = $_POST['fee'];

      $cols = 'id_laundry, id_karyawan, id_pengali, gaji_pengali';
      $vals = $this->id_laundry . "," . $id_user . "," . $id_pengali . "," . $fee;

      $setOne = "id_karyawan = " . $id_user . " AND id_pengali = " . $id_pengali;
      $where = $this->wLaundry . " AND " . $setOne;
      $data_main = $this->model('M_DB_1')->count_where('gaji_pengali', $where);

      if ($data_main < 1) {
         $do = $this->model('M_DB_1')->insertCols('gaji_pengali', $cols, $vals);
         if ($do['errno'] == 0) {
            echo 1;
         }
      } else {
         echo "Data sudah ter-Set!";
      }
   }

   public function set_harian_tunjangan()
   {
      $table = "gaji_pengali_data";
      $id_pengali = $_POST['pengali'];
      $id_user = $_POST['id_user'];
      $tgl = $_POST['tgl'];
      $qty = $_POST['qty'];

      $cols = 'id_laundry, id_karyawan, id_pengali, qty, tgl';
      $vals = $this->id_laundry . "," . $id_user . "," . $id_pengali . "," . $qty . ",'" . $tgl . "'";

      $setOne = "id_karyawan = " . $id_user . " AND id_pengali = " . $id_pengali . " AND tgl = '" . $tgl . "'";
      $where = $this->wLaundry . " AND " . $setOne;
      $data_main = $this->model('M_DB_1')->count_where($table, $where);

      if ($data_main < 1) {
         $do = $this->model('M_DB_1')->insertCols($table, $cols, $vals);
         if ($do['errno'] == 0) {
            echo 1;
         }
      } else {
         echo "Data sudah ter-Set!";
      }
   }

   public function updateCell()
   {
      $table  = $_POST['table'];
      $id = $_POST['id'];
      $value = $_POST['value'];
      $col = $_POST['col'];

      $where = "";
      switch ($table) {
         case 'gaji_laundry':
            $where = $this->wLaundry . " AND id_gaji_laundry = " . $id;
            break;
         case 'gaji_pengali':
            $where = $this->wLaundry . " AND id_gaji_pengali = " . $id;
            break;
         case 'gaji_pengali_data':
            $where = $this->wLaundry . " AND id_pengali_data = " . $id;
            break;
      }

      $set = $col . " = '" . $value . "'";
      $this->model('M_DB_1')->update($table, $set, $where);
   }

   public function tetapkan($id_user, $dateOn)
   {
      $table = "gaji_result";
      $data = unserialize($_POST['data_inject']);
      $return = 1;
      if (count($data) > 0) {
         foreach ($data as $a) {
            $tipe = $a['tipe'];
            $ref = $a['ref'];
            $jumlah = $a['jumlah'];
            $qty = $a['qty'];

            $setOne = "id_karyawan = " . $id_user . " AND tgl = '" . $dateOn . "' AND ref = '" . $ref . "' AND tipe = " . $tipe;
            $where = $this->wLaundry . " AND " . $setOne;
            $data_main = $this->model('M_DB_1')->count_where('gaji_result', $where);

            if ($data_main < 1) {
               if ($jumlah <> 0) {
                  $cols = "id_laundry, id_cabang, id_karyawan, tgl, tipe, deskripsi, ref, jumlah, qty";
                  $vals = $this->id_laundry . "," . $this->id_cabang . "," . $id_user . ",'" . $dateOn . "'," . $tipe . ",'" . $a['deskripsi'] . "','" . $ref . "'," . $jumlah . "," . $qty;
                  $do = $this->model('M_DB_1')->insertCols($table, $cols, $vals);
               }
            } else {
               if ($jumlah == 0) {
                  $do = $this->model('M_DB_1')->delete_where('gaji_result', $where);
               } else {
                  $set = "jumlah = " . $jumlah . ", qty = " . $qty;
                  $do = $this->model('M_DB_1')->update($table, $set, $where);
               }
            }
         }
      }
      if ($do['errno'] == 0) {
         $return = 1;
      } else {
         $return = $do['error'];
      }
      echo $return;
   }
}
