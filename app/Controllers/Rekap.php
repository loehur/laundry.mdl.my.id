<?php

class Rekap extends Controller
{
   public function __construct()
   {
      $this->session_cek();
      $this->data();
      $this->table = 'penjualan';
   }

   public function i($mode)
   {
      $dataTanggal = array();
      $data_main = array();
      $gaji = array();
      $whereCabang = "";

      switch ($mode) {
         case 1:
            $data_operasi = ['title' => 'Harian Cabang - Rekap'];
            $viewData = 'rekap/rekap_harian';

            if (isset($_POST['Y'])) {
               $today = $_POST['Y'] . "-" . $_POST['m'] . "-" . $_POST['d'];
               $dataTanggal = array('tanggal' => $_POST['d'], 'bulan' => $_POST['m'], 'tahun' => $_POST['Y']);
            } else {
               $today = date('Y-m-d');
            }

            $whereCabang = $this->wCabang . " AND ";
            break;
         case 2:
            $data_operasi = ['title' => 'Bulanan Cabang - Rekap'];
            $viewData = 'rekap/rekap_bulanan';

            if (isset($_POST['Y'])) {
               $today = $_POST['Y'] . "-" . $_POST['m'];
               $dataTanggal = array('bulan' => $_POST['m'], 'tahun' => $_POST['Y']);
            } else {
               $today = date('Y-m');
            }

            $whereCabang = $this->wCabang . " AND ";
            break;
         case 3:
            $data_operasi = ['title' => 'Bulanan Laundry - Rekap', 'vLaundry' => true];
            $viewData = 'rekap/rekap_bulanan';

            if (isset($_POST['Y'])) {
               $today = $_POST['Y'] . "-" . $_POST['m'];
               $dataTanggal = array('bulan' => $_POST['m'], 'tahun' => $_POST['Y']);
            } else {
               $today = date('Y-m');
            }

            $cabangs = "";
            $cCabangs = count($this->listCabang);
            foreach ($this->listCabang as $lc) {
               if ($cCabangs == 1) {
                  $cabangs .= $lc['id_cabang'];
               } else {
                  $cabangs .= $lc['id_cabang'] . ",";
               }
               $cCabangs -= 1;
            }

            $whereCabang = "id_cabang IN (" . $cabangs . ") AND ";
            break;
         case 4:
            $data_operasi = ['title' => 'Harian Laundry - Rekap', 'vLaundry' => true];
            $viewData = 'rekap/rekap_harian';

            if (isset($_POST['Y'])) {
               $today = $_POST['Y'] . "-" . $_POST['m'] . "-" . $_POST['d'];
               $dataTanggal = array('tanggal' => $_POST['d'], 'bulan' => $_POST['m'], 'tahun' => $_POST['Y']);
            } else {
               $today = date('Y-m-d');
            }

            $cabangs = "";
            $cCabangs = count($this->listCabang);
            foreach ($this->listCabang as $lc) {
               if ($cCabangs == 1) {
                  $cabangs .= $lc['id_cabang'];
               } else {
                  $cabangs .= $lc['id_cabang'] . ",";
               }
               $cCabangs -= 1;
            }

            $whereCabang = "id_cabang IN (" . $cabangs . ") AND ";
            break;
      }
      //PENDAPATAN
      $where = $whereCabang . "bin = 0 AND insertTime LIKE '%" . $today . "%'";
      $data_main = $this->model('M_DB_1')->get_where($this->table, $where);

      $cols = "sum(jumlah) as total";
      $where = $whereCabang . "jenis_transaksi = 1 AND status_mutasi = 3 AND insertTime LIKE '%" . $today . "%'";
      $where_umum = $where;
      $kas_laundry = 0;
      $kas_laundry = $this->model('M_DB_1')->get_cols_where("kas", $cols, $where, 0)['total'];

      $where = $whereCabang . "jenis_transaksi = 3 AND status_mutasi = 3 AND insertTime LIKE '%" . $today . "%'";
      $where_member = $where;
      $kas_member = 0;
      $kas_member = $this->model('M_DB_1')->get_cols_where("kas", $cols, $where, 0)['total'];

      //PENGELUARAN
      $cols = "note_primary, sum(jumlah) as total";
      $where = $whereCabang . "jenis_transaksi = 4 AND status_mutasi = 3 AND insertTime LIKE '%" . $today . "%' GROUP BY note_primary";
      $where_keluar =  $whereCabang . "jenis_transaksi = 4 AND status_mutasi = 3 AND insertTime LIKE '%" . $today . "%'";
      $kas_keluar = $this->model('M_DB_1')->get_cols_where("kas", $cols, $where, 1);

      //GAJI KARYAWAN
      $cols = "sum(jumlah) as total";
      $where = $whereCabang . "tipe = 1 AND tgl = '" . $today . "'";

      $gaji = $this->model('M_DB_1')->get_cols_where("gaji_result", $cols, $where, 0);
      if (isset($gaji['total'])) {
         $gaji = $gaji['total'];
      } else {
         $gaji = 0;
      }

      $this->view('layout', ['data_operasi' => $data_operasi]);
      $this->view($viewData, [
         'data_main' => $data_main,
         'dataTanggal' => $dataTanggal,
         'kasLaundry' => $kas_laundry,
         'whereUmum' => $where_umum,
         'whereKeluar' => $where_keluar,
         'whereMember' => $where_member,
         'kasMember' => $kas_member,
         'kas_keluar' => $kas_keluar,
         'gaji' => $gaji
      ]);
   }

   function detail($where, $mode = 1)
   {
      $viewData = 'rekap/rekap_bulanan_detail';
      $data_operasi = ['title' => 'Bulanan Cabang - Rekap'];
      $this->view('layout', ['data_operasi' => $data_operasi]);

      $data = [];
      $where =  base64_decode($where);
      $data = $this->model('M_DB_1')->get_where("kas", $where);

      $this->view($viewData, [
         'data' => $data,
         'mode' => $mode
      ]);
   }
}
