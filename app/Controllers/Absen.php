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
      $data_operasi = ['title' => 'Absen'];
      $viewData = __CLASS__ . '/form';
      $this->view('layout', ['data_operasi' => $data_operasi]);
      $this->view($viewData);
   }

   public function load()
   {
      $viewData = __CLASS__ . '/content';

      $data = $this->db(0)->get_where('absen', 'id_cabang = ' . $_SESSION['user']['id_cabang']);

      $this->view($viewData, $data);
   }

   function absen()
   {
      //cek perangkat
      if ($_SESSION['user']['last_device'] <> $_SESSION['data']['cabang']['verified_device']) {
         echo 'Perangkat absen di tolak, hubungi Admin';
         exit();
      }

      $hp = $_POST['karyawan'];
      $pin = $_POST['pin'];
      $jenis = $_POST['jenis'];

      $username = $this->model("Enc")->username($hp);
      $otp = $this->model("Enc")->otp($pin);
      $user_absen = $this->data('User')->pin_today($username, $otp);

      $tgl = date('Y-m-d');
      $jam = date('H:i');

      if ($user_absen) {
         $cols = "id_karyawan,jenis,tanggal,jam,id_cabang";
         $vals = $user_absen['id_user'] . "," . $jenis . ",'" . $tgl . "','" . $jam . "'," . $_SESSION['user']['id_cabang'];

         $where = "id_karyawan = " . $user_absen['id_user'] . " AND jenis = " . $jenis . " AND tanggal = '" . $tgl . "'";
         $cek = $this->db(0)->count_where('absen', $where);
         if ($cek == 0) {
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
               'msg' => "Absen hari ini sudah dilakukan"
            ];
            print_r(json_encode($res));
         }
      } else {
         echo "PIN salah";
      }
   }
}
