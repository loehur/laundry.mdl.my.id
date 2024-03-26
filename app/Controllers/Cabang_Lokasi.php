<?php

class Cabang_Lokasi extends Controller
{
   public function __construct()
   {
      $this->session_cek();
      $this->data();
      $this->table = 'pelanggan';
   }

   public function index()
   {
      $viewData = 'cabang/lokasi';
      $data_operasi = ['title' => 'Lokasi Cabang'];
      $this->view('layout', ['data_operasi' => $data_operasi]);
      $this->view($viewData);
   }

   function content()
   {
      $get_kota = $this->model("M_DB_1")->get_where_row("kota", "id_kota = '" . $this->dCabang['id_kota'] . "'");
      $kota = $get_kota['nama_kota'];
      $kota = str_replace(" ", "+", $kota);

      if (!isset($_SESSION['lokasi']['kecamatan'])) {
         $data['kec'] = $this->model("Place")->kecamatan($kota);
         $_SESSION['lokasi']['kecamatan'] = $data['kec'];
      } else {
         $data['kec'] = $_SESSION['lokasi']['kecamatan'];
      }

      if ($this->dCabang['latt'] <> "") {
         $data['geo']['lat'] = $this->dCabang['latt'];
         $data['geo']['long'] = $this->dCabang['longt'];
      } else {
         $data['geo']['lat'] = $get_kota['latt'];
         $data['geo']['long'] = $get_kota['longt'];
      }

      $view = 'cabang/content';
      $this->view($view, $data);
   }

   function update()
   {
      $nama = $_POST['nama'];
      $hp = $_POST['hp'];
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
      $where = "id_cabang = " . $this->dCabang['id_cabang'];
      $set = "nama = '" . $nama . "', hp = '" . $hp . "', alamat = '" . $alamat . "', area_name = '" . $area_name . "', area_id = '" . $area_id . "', postal_code = '" . $postal_code . "', latt = '" . $lat . "', longt = '" . $long . "'";
      $update = $this->model("M_DB_1")->update("cabang", $set, $where);
      print_r($update);

      $this->dataSynchrone();
   }
}
