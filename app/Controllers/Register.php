<?php

class Register extends Controller
{
   public function index()
   {
      if (isset($_SESSION['login_laundry'])) {
         if ($_SESSION['login_laundry'] == TRUE) {
            header('Location: ' . $this->BASE_URL . "Penjualan");
         }
      }

      $data_kota = $this->model('M_DB_1')->get('kota');
      $this->view('register', ['data_kota' => $data_kota]);
   }

   public function reset_pass()
   {
      if (isset($_SESSION['login_laundry'])) {
         if ($_SESSION['login_laundry'] == TRUE) {
            header('Location: ' . $this->BASE_URL . "Home");
         }
      }

      $this->view('forget_pass');
   }

   public function insert()
   {
      if (isset($_SESSION['login_laundry'])) {
         if ($_SESSION['login_laundry'] == TRUE) {
            header('Location: ' . $this->BASE_URL . "Home");
         }
      }

      $table = "user";
      $email = $_POST["email"];
      $where = "email = '" . $email . "'";
      $do_count = $this->model('M_DB_1')->count_where($table, $where);

      if ($do_count > 0) {
         echo "Email Sudah Terdaftar!";
      } else {
         $table  = 'user';
         $activation = md5($_POST['HP']);
         $columns = 'no_user, nama_user, email, id_kota, password, id_privilege, activation_key';
         $values = "'" . $_POST["HP"] . "','" . $_POST["nama"] . "','" . $email . "','" . $_POST["kota"] . "','" . md5($_POST["password"]) . "', 100, '" . $activation . "'";
         $do = $this->model('M_DB_1')->insertCols($table, $columns, $values);

         if ($do == TRUE) {
            echo $do['errno'];
         } else {
            echo "Nomor Handphone Sudah Terdaftar!";
         }
      }
   }

   public function req_code()
   {
      if (isset($_SESSION['login_laundry'])) {
         if ($_SESSION['login_laundry'] == TRUE) {
            header('Location: ' . $this->BASE_URL . "Home");
         }
      }

      $table = "user";
      $email = $_POST["email"];
      $where = "email = '" . $email . "'";
      $do_count = $this->model('M_DB_1')->count_where($table, $where);

      if ($do_count > 0) {
         $where2 = "email = '" . $email . "' AND LENGTH(reset_code) = 0";
         $do_count2 = $this->model('M_DB_1')->count_where($table, $where2);
         if ($do_count2 > 0) {
            $reset_code = rand(1000, 9999);
            $set = "reset_code = '" . md5($reset_code) . "'";
            $this->model('M_DB_1')->update($table, $set, $where);
            $body = "Kode Reset Password: " . $reset_code . " JANGAN BERIKAN KODE INI KEPADA SIAPAPUN!";
            $this->model('M_Mailer')->sendMail($email, "Reset Code", $body);
            echo 1;
         } else {
            echo "Kode Reset telah dikirim ke Email Anda, silahkan cek!";
         }
      } else {
         echo "Email Tidak Terdaftar!";
      }
   }

   public function updatePass()
   {

      session_start();
      if ($_SESSION['login'] == TRUE) {
         header('Location: ' . $this->BASE_URL . "Home");
      }

      $table = "user";
      $email = $_POST["email"];
      $code = $_POST["reset_code"];
      $pass = $_POST["password"];
      $where = "email = '" . $email . "' AND reset_code = '" . md5($code) . "'";

      $do_count = $this->model('M_DB_1')->count_where($table, $where);

      if ($do_count > 0) {
         $set = "password = '" . md5($pass) . "', reset_code = ''";
         $this->model('M_DB_1')->update($table, $set, $where);
         echo 1;
      } else {
         echo "Kode Reset Salah!";
      }
   }

   public function emailVerification($activation)
   {
      $table = "user";
      $set = "email_verification = 1";
      $where = "activation_key = '" . $activation . "'";
      $update = $this->model('M_DB_1')->update($table, $set, $where);
      if ($update) {
         header('location: ' . $this->BASE_URL . 'Login');
      } else {
         echo 'ACTIVATION FAILED';
      }
   }
}
