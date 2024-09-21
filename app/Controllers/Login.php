<?php
class Login extends Controller
{
   public function index()
   {
      $this->cek_cookie();

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

   function cek_cookie()
   {
      $cookie_user = $this->model("Enc")->enc("user_londri");
      if (isset($_COOKIE[$cookie_user])) {
         $cookie_value = $this->model("Enc")->dec_2($_COOKIE[$cookie_user]);
         $user_data = unserialize($cookie_value);
         if (isset($user_data['username']) && isset($user_data['no_user'])) {
            $no_user = $user_data['no_user'];
            $username = $this->model("Enc")->username($no_user);
            if ($username == $user_data['username']) {
               $_SESSION['login_laundry'] = TRUE;
               $this->data_user = $user_data;
               $this->parameter();
               $this->save_cookie();
            }
         }
      }
   }

   public function cek_login()
   {

      if (isset($_SESSION['login_laundry'])) {
         if ($_SESSION['login_laundry'] == TRUE) {
            header('Location: ' . $this->BASE_URL . "Penjualan/i");
         }
      }

      $no_user = $_POST["HP"];
      if (strlen($no_user) < 10 || strlen($no_user) > 13) {
         $res = [
            'code' => 0,
            'msg' => "Nomor HP tidak valid"
         ];
         print_r(json_encode($res));
         exit();
      }

      $pin = $_POST["pin"];
      $otp = md5(md5(md5($pin + 6252)));
      if (strlen($pin) == 0) {
         $res = [
            'code' => 0,
            'msg' => "PIN tidak valid"
         ];
         print_r(json_encode($res));
         exit();
      }

      $cap = $_POST["cap"];
      if ($_SESSION['captcha'] <> $cap) {
         $res = [
            'code' => 10,
            'msg' => "Captcha Salah"
         ];
         print_r(json_encode($res));
         exit();
      }

      $username = $this->model("Enc")->username($no_user);
      $today = date("Ymd");

      $where = "username = '" . $username . "' AND otp = '" . $otp . "' AND otp_active = '" . $today . "' AND en = 1";
      $this->data_user = $this->db(0)->get_where_row('user', $where);

      if ($this->data_user) {
         // LAST LOGIN
         $dateTime = date('Y-m-d H:i:s');
         $set = "last_login = '" . $dateTime . "'";
         $this->db(0)->update('user', $set, $where);
         $this->db(0)->query("SET GLOBAL time_zone = '+07:00'");

         //LOGIN
         $_SESSION['login_laundry'] = TRUE;
         $this->parameter();
         $this->save_cookie();
         $res = [
            'code' => 11,
            'msg' => "Login Success"
         ];
         print_r(json_encode($res));
      } else {
         $_SESSION['captcha'] = "HJFASD7FD89AS7FSDHFD68FHF7GYG7G47G7G7G674GRGVFTGB7G6R74GHG3Q789631765YGHJ7RGEYBF67";
         $res = [
            'code' => 10,
            'msg' => "Nomor HP dan PIN tidak cocok"
         ];
         print_r(json_encode($res));
      }
   }

   function save_cookie()
   {
      $device = $_SERVER['HTTP_USER_AGENT'];
      $this->data_user['device'] = $device;
      $cookie_user = $this->model("Enc")->enc("user_londri");
      $cookie_value = $this->model("Enc")->enc_2(serialize($this->data_user));
      setcookie($cookie_user, $cookie_value, time() + 86400, "/");
   }

   function req_pin()
   {
      $hp_input = $_POST["hp"];
      $hp = (int) filter_var($hp_input, FILTER_SANITIZE_NUMBER_INT);
      //cek

      if (strlen($hp_input) < 10 || strlen($hp_input) > 13) {
         $res = [
            'code' => 0,
            'msg' => "Nomor HP tidak valid"
         ];
         print_r(json_encode($res));
         exit();
      }

      $username = $this->model("Enc")->username($hp);
      $where = "username = '" . $username . "' AND en = 1";
      $today = date("Ymd");
      $cek = $this->db(0)->get_where_row('user', $where);
      if ($cek) {
         if ($cek['otp_active'] == $today) {
            $res = [
               'code' => 1,
               'msg' => "Gunakan PIN yang didapat hari ini"
            ];
         } else {
            $otp = rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9);
            $otp_enc = md5(md5(md5($otp + 6252)));

            $res = $this->model("M_WA")->send($cek['no_user'], $otp, URL::WA_TOKEN);
            if (isset($res["id"])) {
               $set = "otp = '" . $otp_enc . "', otp_active = '" . $today . "'";
               $up = $this->db(0)->update('user', $set, $where);
               if ($up['errno'] == 0) {
                  $res = [
                     'code' => 1,
                     'msg' => "Permintaan PIN berhasil, aktif 1 hari"
                  ];
               } else {
                  $res = [
                     'code' => 0,
                     'msg' => $up['error']
                  ];
               }
            } else {
               $res = [
                  'code' => 0,
                  'msg' => "MDL Whatsapp Error"
               ];
            }
         }
      } else {
         $_SESSION['captcha'] = "HJFASD7FD89AS7FSDHFD68FHF7GYG7G47G7G7G674GRGVFTGB7G6R74GHG3Q789631765YGHJ7RGEYBF67";
         $res = [
            'code' => 10,
            'msg' => "Nomor HP tidak terdaftar"
         ];
      }
      print_r(json_encode($res));
   }

   public function logout()
   {
      $cookie_user = $this->model("Enc")->enc("user_londri");
      setcookie($cookie_user, "");
      session_destroy();
      header('Location: ' . $this->BASE_URL . "Penjualan/i");
   }

   public function captcha()
   {
      $captcha_code = rand(0, 9) . rand(0, 9);
      $_SESSION['captcha'] = $captcha_code;

      $target_layer = imagecreatetruecolor(25, 24);
      $captcha_background = imagecolorallocate($target_layer, 255, 255, 200);
      imagefill($target_layer, 0, 0, $captcha_background);
      $captcha_text_color = imagecolorallocate($target_layer, 0, 0, 0);
      imagestring($target_layer, 5, 5, 5, $captcha_code, $captcha_text_color);
      header("Content-type: image/jpeg");
      imagejpeg($target_layer);
   }

   public function log_mode()
   {
      $mode = $_POST['mode'];
      unset($_SESSION['log_mode']);
      $_SESSION['log_mode'] = $mode;
   }
}
