<?php
class Login extends Controller
{
   public function index()
   {
      $this->cek_cookie();
      $data = [];
      if (isset($_COOKIE['MDLNUMS'])) {
         $data = unserialize($this->model("Enc")->dec_2($_COOKIE['MDLNUMS']));
      }
      if (isset($_SESSION['login_laundry'])) {
         if ($_SESSION['login_laundry'] == TRUE) {
            header('Location: ' . URL::BASE_URL . "Penjualan");
         } else {
            $this->view('login', $data);
         }
      } else {
         $this->view('login', $data);
      }
   }

   function cek_cookie()
   {
      if (isset($_COOKIE["MDLSESSID"])) {
         $cookie_value = $this->model("Enc")->dec_2($_COOKIE["MDLSESSID"]);

         $user_data = unserialize($cookie_value);
         if (isset($user_data['username']) && isset($user_data['no_user']) && isset($user_data['ip']) && isset($user_data['device'])) {
            $no_user = $user_data['no_user'];
            $username = $this->model("Enc")->username($no_user);

            $device = $_SERVER['HTTP_USER_AGENT'];
            if ($username == $user_data['username'] && $user_data['device'] == $device && $user_data['ip'] == $this->get_client_ip()) {
               $_SESSION['login_laundry'] = TRUE;
               $this->data_user = $user_data;
               $this->parameter();
               $this->save_cookie($no_user);
            }
         }
      }
   }

   function save_cookie()
   {
      $device = $_SERVER['HTTP_USER_AGENT'];
      $this->data_user['device'] = $device;
      $this->data_user['ip'] = $this->get_client_ip();
      $cookie_value = $this->model("Enc")->enc_2(serialize($this->data_user));
      setcookie("MDLSESSID", $cookie_value, time() + 86400, "/");
   }

   function save_nums($usernum)
   {
      //simpan list hp
      if (!isset($_COOKIE['MDLNUMS'])) {
         $mdlnums = [1 => $usernum];
         $nums_value = $this->model("Enc")->enc_2(serialize($mdlnums));
         setcookie("MDLNUMS", $nums_value, time() + (86400 * 7), "/");
      } else {
         $nums = $this->model("Enc")->dec_2($_COOKIE['MDLNUMS']);
         $nums = unserialize($nums);
         if (is_array($nums)) {
            $cek = 0;
            foreach ($nums as $key => $n) {
               if ($n == $usernum) {
                  $cek = $key;
               }
            }

            $max = max(array_keys($nums));

            if ($cek > 0) {
               //hapus diri sendiri dulu
               unset($nums[$cek]);

               if (count($nums) > 5) {
                  $min = min(array_keys($nums));
                  unset($nums[$min]);
               }
               $nums[$max + 1] = $usernum;
            } else {
               if (count($nums) >= 3) {
                  $min = min(array_keys($nums));
                  unset($nums[$min]);
               }
               $nums[$max + 1] = $usernum;
            }
         }
         $nums_value = $this->model("Enc")->enc_2(serialize($nums));
         setcookie("MDLNUMS", $nums_value, time() + (86400 * 7), "/");
      }
   }

   public function cek_login()
   {
      $no_user = $_POST["username"];
      if (strlen($no_user) < 10 || strlen($no_user) > 13) {
         $res = [
            'code' => 0,
            'msg' => "Nomor Whatsapp tidak valid"
         ];
         print_r(json_encode($res));
         exit();
      }

      $pin = $_POST["pin"];
      $otp = $this->model("Enc")->otp($pin);
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
      $this->data_user = $this->data('User')->pin_today($username, $otp);
      if ($this->data_user) {
         // LAST LOGIN
         $this->data('User')->last_login($username);

         //LOGIN
         $_SESSION['login_laundry'] = TRUE;
         $this->parameter();
         $this->save_cookie();
         $this->save_nums($no_user);
         $res = [
            'code' => 11,
            'msg' => "Login Success"
         ];
         print_r(json_encode($res));
      } else {
         $_SESSION['captcha'] = "HJFASD7FD89AS7FSDHFD68FHF7GYG7G47G7G7G674GRGVFTGB7G6R74GHG3Q789631765YGHJ7RGEYBF67";
         $res = [
            'code' => 10,
            'msg' => "Nomor Whatsapp dan PIN tidak cocok"
         ];
         print_r(json_encode($res));
      }
   }

   function req_pin()
   {
      $hp_input = $_POST["hp"];
      $hp = (int) filter_var($hp_input, FILTER_SANITIZE_NUMBER_INT);
      //cek

      if (strlen($hp_input) < 10 || strlen($hp_input) > 13) {
         $res = [
            'code' => 0,
            'msg' => "Nomor Whatsapp tidak valid"
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
            $otp_enc = $this->model("Enc")->otp($otp);;

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
            } elseif (isset($res['reason'])) {
               $res = [
                  'code' => 0,
                  'msg' => $res['reason']
               ];
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
            'msg' => "Nomor Whatsapp tidak terdaftar"
         ];
      }
      print_r(json_encode($res));
   }

   public function logout()
   {
      setcookie("MDLSESSID", 0, time() + 1, "/");
      session_destroy();
      header('Location: ' . URL::BASE_URL . "Penjualan/i");
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

   function get_client_ip()
   {
      $ipaddress = '';
      if (isset($_SERVER['HTTP_CLIENT_IP']))
         $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
      else if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
         $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
      else if (isset($_SERVER['HTTP_X_FORWARDED']))
         $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
      else if (isset($_SERVER['HTTP_FORWARDED_FOR']))
         $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
      else if (isset($_SERVER['HTTP_FORWARDED']))
         $ipaddress = $_SERVER['HTTP_FORWARDED'];
      else if (isset($_SERVER['REMOTE_ADDR']))
         $ipaddress = $_SERVER['REMOTE_ADDR'];
      else
         $ipaddress = 'UNKNOWN';
      return $ipaddress;
   }
}
