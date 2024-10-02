<?php

class Absen extends Controller
{
   public function __construct()
   {
      $this->session_cek();
      $this->operating_data();
   }

   public function index()
   {
      $data_operasi = ['title' => 'Karyawan Absen'];
      $viewData = __CLASS__ . '/form';
      $this->view('layout', ['data_operasi' => $data_operasi]);
      $this->view($viewData);
   }

   public function load()
   {
      $viewData = __CLASS__ . '/content';
      $tgl = date('Y-m-d');
      $data = $this->db(0)->get_where('absen', 'id_cabang = ' . $_SESSION['user']['id_cabang'] . " AND tanggal LIKE '" . $tgl . "%'");

      $this->view($viewData, $data);
   }

   function absen()
   {
      $hp = $_POST['karyawan'];
      $pin = $_POST['pin'];
      $jenis = $_POST['jenis'];

      $username = $this->model("Enc")->username($hp);
      $otp = $this->model("Enc")->otp($pin);
      $user_absen = $this->data('User')->pin_today($username, $otp);

      if (!$user_absen) {
         $cek_admin = $this->data('User')->pin_admin_today($otp);
         if ($cek_admin > 0) {
            $user_absen = $this->data('User')->get_data_user($username);
         }
      }

      $tgl = date('Y-m-d');
      $jam = date('H:i');

      if ($user_absen) {
         $cols = "id_karyawan,jenis,tanggal,jam,id_cabang";
         $vals = $user_absen['id_user'] . "," . $jenis . ",'" . $tgl . "','" . $jam . "'," . $_SESSION['user']['id_cabang'];


         //CEK HARIAN
         $where_user = "id_karyawan = " . $user_absen['id_user'] . " AND (jenis = 0 OR jenis = 2) AND tanggal = '" . $tgl . "'";
         $cek_user = $this->db(0)->count_where('absen', $where_user);
         if ($cek_user > 0) {
            $res = [
               'code' => 0,
               'msg' => "Gagal, melebihi batas Absen Harian"
            ];
            print_r(json_encode($res));
            exit();
         }

         //CEK MALAM
         $where_user = "id_karyawan = " . $user_absen['id_user'] . " AND jenis = 1 AND tanggal = '" . $tgl . "'";
         $cek_user = $this->db(0)->count_where('absen', $where_user);
         if ($cek_user > 0) {
            $res = [
               'code' => 0,
               'msg' => "Gagal, melebihi batas Absen Harian"
            ];
            print_r(json_encode($res));
            exit();
         }

         //CEK MAX PER CABANG
         if ($jenis == 0) {
            $where = "id_cabang = " . $_SESSION['user']['id_cabang'] . " AND jenis = " . $jenis . " AND tanggal = '" . $tgl . "'";
            $max = $_SESSION['data']['cabang'][$jenis . '_max'];
         } else if ($jenis == 1) {
            $where = "id_cabang = " . $_SESSION['user']['id_cabang'] . " AND jenis = " . $jenis . " AND tanggal = '" . $tgl . "'";
            $max = $_SESSION['data']['cabang'][$jenis . '_max'];
         } else if ($jenis == 2) {
            $where = "jenis = " . $jenis . " AND tanggal = '" . $tgl . "'";
            $max = 1;
         }
         $cek = $this->db(0)->count_where('absen', $where);

         if ($cek < $max) {
            $in = $this->db(0)->insertCols('absen', $cols, $vals);
            if ($in['errno'] == 0) {
               $res = [
                  'code' => 1,
                  'msg' => "Absen Sukses"
               ];
               print_r(json_encode($res));
            } else {
               $res = [
                  'code' => 0,
                  'msg' => $in['error']
               ];
               print_r(json_encode($res));
            }
         } else {
            $res = [
               'code' => 0,
               'msg' => "Gagal, melebihi batas Absen Harian"
            ];
            print_r(json_encode($res));
         }
      } else {
         echo "PIN salah";
      }
   }
}
