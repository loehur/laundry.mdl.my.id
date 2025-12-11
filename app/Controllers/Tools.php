<?php

class Tools extends Controller
{
   function cek_wa($hp = '081268098300', $text = 'test')
   {
      $res = $this->helper('Notif')->send_wa($hp, $text);
      echo "<pre>";
      print_r($res);
      echo "</pre>";
   }

   function cek_cookie()
   {
      if (isset($_COOKIE[URL::SESSID])) {
         $cookie_value = $this->model("Enc")->dec_2($_COOKIE[URL::SESSID]);

         $user_data = unserialize($cookie_value);
         if (isset($user_data['username']) && isset($user_data['no_user']) && isset($user_data['ip']) && isset($user_data['device'])) {
            $no_user = $user_data['no_user'];
            $username = $this->model("Enc")->username($no_user);

            $device = $_SERVER['HTTP_USER_AGENT'];
            if ($username == $user_data['username'] && $user_data['device'] == $device && $user_data['ip'] == $this->get_client_ip()) {
               echo "Valid";
            }
         } else {
            echo "tidak Valid";
         }
      } else {
         echo "tidak bias unseriliaze";
      }
   }

   function enc($text)
   {
      echo $this->model('Enc')->enc($text);
   }

   function enc_2($text)
   {
      echo $this->model('Enc')->enc_2($text);
   }

   function dec_2($text)
   {
      echo $this->model('Enc')->dec_2($text);
   }

   function username($hp)
   {
      if (is_numeric($hp)) {
         echo md5(md5(md5($hp + 8117686252)));
      } else {
         echo md5(md5(md5($hp)));
      }
   }

   function browser()
   {
      echo $_SERVER['HTTP_USER_AGENT'];
   }

   function cek_session()
   {
      echo "<pre>";
      print_r($_SESSION[URL::SESSID]['user']);
      echo "</pre>";
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
      echo $ipaddress;
   }

   function tes_model($model, $method, $value)
   {
      echo $this->model($model)->$method($value);
   }
}
