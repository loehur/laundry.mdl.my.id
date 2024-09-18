<?php

class Gaji extends Controller
{
   public function __construct()
   {
      $this->session_cek();
      $this->data();
   }

   public function index()
   {
      $viewData = 'gaji/rekap_gaji_bulanan';

      $user['id'] = 0;
      $user['kasbon'] = 0;
      $dataTanggal = [];
      $dataGajiLaundry = [];

      $data_main = [];
      $data_terima = [];
      $data_kembali = [];

      //KINERJA
      if (isset($_POST['m'])) {
         $user['id'] = $_POST['user'];
         $date = $_POST['Y'] . "-" . $_POST['m'];
         $dataTanggal = array('bulan' => $_POST['m'], 'tahun' => $_POST['Y']);
      } else {
         $date = date('Y-m');
      }

      $data_operasi = ['title' => 'Gaji Bulanan - Rekap'];

      foreach (URL::cabang_list_id as $cbi) {
         $join_where = "operasi.id_penjualan = sale_" . $cbi . ".id_penjualan";
         $where = "sale_" . $cbi . ".bin = 0 AND operasi.id_user_operasi = " . $user['id'] . " AND operasi.insertTime LIKE '" . $date . "%'";
         $data_lain1 = $this->db(1)->innerJoin1_where('operasi', 'sale_' . $cbi, $join_where, $where);
         foreach ($data_lain1 as $dl1) {
            array_push($data_main, $dl1);
         }

         $cols = "id_user, id_cabang, COUNT(id_user) as terima";
         $where = "id_user = " . $user['id'] . " AND  insertTime LIKE '" . $date . "%' GROUP BY id_user, id_cabang";
         $data_lain2 = $this->db(1)->get_cols_where('sale_' . $cbi, $cols, $where, 1);
         foreach ($data_lain2 as $dl2) {
            array_push($data_terima, $dl2);
         }

         $cols = "id_user_ambil, id_cabang, COUNT(id_user_ambil) as kembali";
         $where = "id_user_ambil = " . $user['id'] . " AND tgl_ambil LIKE '" . $date . "%' GROUP BY id_user_ambil, id_cabang";
         $data_lain3 = $this->db(1)->get_cols_where('sale_' . $cbi, $cols, $where, 1);
         foreach ($data_lain3 as $dl3) {
            array_push($data_kembali, $dl3);
         }
      }

      //KASBON
      $cols = "id_kas, jumlah, insertTime";
      $where = "jenis_transaksi = 5 AND jenis_mutasi = 2 AND status_mutasi = 3 AND id_client = " . $user['id'];
      $user['kasbon'] = $this->db(1)->get_cols_where('kas', $cols, $where, 1);

      foreach ($user['kasbon'] as $key => $k) {
         $ref = $k['id_kas'];
         $where = "ref = '" . $ref . "'";
         $countPotong = $this->db(0)->count_where('gaji_result', $where);
         if ($countPotong == 1) {
            unset($user['kasbon'][$key]);
         }
      }

      $gaji = array();
      $gaji['gaji_laundry'] = $this->db(0)->get('gaji_laundry');
      $gaji['pengali_list'] = $this->db(0)->get('gaji_pengali_jenis');
      $gaji['gaji_pengali'] = $this->db(0)->get('gaji_pengali');
      $gaji['gaji_pengali_data'] = $this->db(0)->get_where('gaji_pengali_data', "tgl = '" . $date . "'");
      $gaji['fix'] = $this->db(0)->get_where('gaji_result', "tgl = '" . $date . "' AND id_karyawan = " . $user['id'] . " ORDER BY tipe ASC ");

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
      $penjualan = $_POST['sale_' . $this->id_cabang];
      $id_layanan = $_POST['layanan'];
      $id_user = $_POST['id_user'];
      $fee = $_POST['fee'];
      $target = $_POST['target'];
      $bonus_target = $_POST['bonus_target'];
      $max_target = $_POST['max_target'];

      $cols = 'id_karyawan, jenis_penjualan, id_layanan, gaji_laundry, target, bonus_target, max_target';
      $vals = $id_user . "," . $penjualan . "," . $id_layanan . "," . $fee . "," . $target . "," . $bonus_target . "," . $max_target;

      $where = "id_karyawan = " . $id_user . " AND jenis_penjualan = " . $penjualan . " AND id_layanan = " . $id_layanan;
      $data_main = $this->db(0)->count_where('gaji_laundry', $where);

      if ($data_main < 1) {
         $do = $this->db(0)->insertCols('gaji_laundry', $cols, $vals);
         if ($do['errno'] == 0) {
            echo 1;
         } else {
            echo $do['error'];
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

      $cols = 'id_karyawan, id_pengali, gaji_pengali';
      $vals = $id_user . "," . $id_pengali . "," . $fee;

      $where = "id_karyawan = " . $id_user . " AND id_pengali = " . $id_pengali;
      $data_main = $this->db(0)->count_where('gaji_pengali', $where);

      if ($data_main < 1) {
         $do = $this->db(0)->insertCols('gaji_pengali', $cols, $vals);
         if ($do['errno'] == 0) {
            echo 1;
         } else {
            echo $do['error'];
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

      $cols = 'id_karyawan, id_pengali, qty, tgl';
      $vals = $id_user . "," . $id_pengali . "," . $qty . ",'" . $tgl . "'";

      $where = "id_karyawan = " . $id_user . " AND id_pengali = " . $id_pengali . " AND tgl = '" . $tgl . "'";
      $data_main = $this->db(0)->count_where($table, $where);

      if ($data_main < 1) {
         $do = $this->db(0)->insertCols($table, $cols, $vals);
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
            $where = "id_gaji_laundry = " . $id;
            break;
         case 'gaji_pengali':
            $where = "id_gaji_pengali = " . $id;
            break;
         case 'gaji_pengali_data':
            $where = "id_pengali_data = " . $id;
            break;
      }

      $set = $col . " = '" . $value . "'";
      $this->db(0)->update($table, $set, $where);
   }

   public function tetapkan($id_user, $dateOn)
   {
      $table = "gaji_result";
      $data = unserialize($_POST['data_inject']);
      if (count($data) > 0) {
         foreach ($data as $a) {
            $tipe = $a['tipe'];
            $ref = $a['ref'];
            $jumlah = $a['jumlah'];
            $qty = $a['qty'];

            $where = "id_karyawan = " . $id_user . " AND tgl = '" . $dateOn . "' AND ref = '" . $ref . "' AND tipe = " . $tipe;
            $data_main = $this->db(0)->count_where('gaji_result', $where);

            if ($data_main < 1) {
               if ($jumlah <> 0) {
                  $cols = "id_karyawan, tgl, tipe, deskripsi, ref, jumlah, qty";
                  $vals = $id_user . ",'" . $dateOn . "'," . $tipe . ",'" . $a['deskripsi'] . "','" . $ref . "'," . $jumlah . "," . $qty;
                  $do = $this->db(0)->insertCols($table, $cols, $vals);
               }
            } else {
               if ($jumlah == 0 || $qty == 0) {
                  $do = $this->db(0)->delete_where('gaji_result', $where);
               } else {
                  $set = "jumlah = " . $jumlah . ", qty = " . $qty;
                  $do = $this->db(0)->update($table, $set, $where);
               }
            }
         }
      }
      if ($do['errno'] == 0) {
         $return = 0;
      } else {
         $return = $do['error'];
      }
      echo $return;
   }
}
