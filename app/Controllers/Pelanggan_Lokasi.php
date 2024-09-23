<?php

class Pelanggan_Lokasi extends Controller
{
   public function __construct()
   {
      $this->session_cek(1);
      $this->operating_data();
      $this->table = 'pelanggan';
   }

   public function index()
   {
      $viewData = 'pelanggan/lokasi';
      $data_operasi = ['title' => 'Lokasi Pelanggan'];
      $this->view('layout', ['data_operasi' => $data_operasi]);
      $this->view($viewData);
   }

   function content($id)
   {
      $get_kota = $this->db(0)->get_where_row("kota", "id_kota = '" . $this->dCabang['id_kota'] . "'");
      $kota = $get_kota['nama_kota'];
      $kota = str_replace(" ", "+", $kota);

      if (!isset($_SESSION['lokasi']['kecamatan'])) {
         $data['kec'] = $this->model("Place")->kecamatan($kota);
         $_SESSION['lokasi']['kecamatan'] = $data['kec'];
      } else {
         $data['kec'] = $_SESSION['lokasi']['kecamatan'];
      }

      $data['pelanggan'] = $this->db(0)->get_where_row("pelanggan", "id_pelanggan = " . $id);

      if ($data['pelanggan']['latt'] <> "") {
         $data['geo']['lat'] = $data['pelanggan']['latt'];
         $data['geo']['long'] = $data['pelanggan']['longt'];
      } else {
         $data['geo']['lat'] = $get_kota['latt'];
         $data['geo']['long'] = $get_kota['longt'];
      }

      $view = 'pelanggan/content';
      $this->view($view, $data);
   }

   function kode_pos()
   {
      $input = $_POST['input'];
      $kota = $this->db(0)->get_where_row("kota", "id_kota = '" . $this->dCabang['id_kota'] . "'")['nama_kota'];

      $data = [];
      foreach ($_SESSION['lokasi']['kecamatan'] as $key => $kp) {
         if ($input == $key) {
            $g1 = $this->model("Biteship")->get_area($key);
            if (count($g1) > 0) {
               $find1 = 0;
               foreach ($g1 as $kg1 => $g_1) {
                  if (str_replace("+", " ", $kota) == strtoupper($g_1['administrative_division_level_2_name'])) {
                     $find1 += 1;
                     array_push($data, $g1[$kg1]);
                  }
               }
               if ($find1 > 0) {
                  break;
               }
            }

            foreach ($kp as $k) {
               $g = $this->model("Biteship")->get_area($k);
               if (count($g) > 1) {
                  $find = 0;
                  foreach ($g as $kg => $g_) {
                     if (str_replace("+", " ", $kota) == strtoupper($g_['administrative_division_level_2_name'])) {
                        $find += 1;
                        array_push($data, $g[$kg]);
                        break;
                     }
                  }
                  if ($find == 0) {
                     array_push($data, $g[0]);
                     $text = "ERROR. Kode Pos " . $k . " tidak valid dengan kecamatan " . str_replace("+", " ", $key);
                     $this->model('Log')->write($text);
                  }
               } elseif (count($g) == 1) {
                  array_push($data, $g[0]);
               }
               sleep(1);
            }
            break;
         }
      }
      $this->view("pelanggan/list_kodepos", $data);
   }

   function update($id_pelanggan)
   {
      $alamat = $_POST['alamat'];
      $area_id = $_POST['kodepos'];
      $lat = $_POST['lat'];
      $long = $_POST['long'];

      $res = $this->model("Biteship")->get_area_id($area_id);
      if (isset($res[0]['id'])) {
         $area_id = $res[0]['id'];
         $area_name = $res[0]['name'];
         $postal_code = $res[0]['postal_code'];
      }
      $where = "id_pelanggan = " . $id_pelanggan;
      $set = "alamat = '" . $alamat . "', area_name = '" . $area_name . "', area_id = '" . $area_id . "', postal_code = '" . $postal_code . "', latt = '" . $lat . "', longt = '" . $long . "'";
      $update = $this->db(0)->update("pelanggan", $set, $where);
      print_r($update);
   }
}
