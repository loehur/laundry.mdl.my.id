<?php

class Order_Delivery extends Controller
{
   public function __construct()
   {
      $this->session_cek();
      $this->operating_data();
      $this->table = 'pelanggan';
   }

   public function index($mode = 0)
   {
      $viewData = 'order_delivery/main';
      if ($mode == 0) {
         $data_operasi = ['title' => 'Delivery Jemput'];
      } else {
         $data_operasi = ['title' => 'Delivery Antar'];
      }

      $data['mode'] = $mode;
      $data['pelanggan'] = $this->db(0)->get_where("pelanggan", $this->wCabang . " AND latt <> ''");
      $this->view('layout', ['data_operasi' => $data_operasi]);
      $this->view($viewData, $data);
   }

   function tarif()
   {
      $d = $this->db(0)->get_where_row("pelanggan", "id_pelanggan = " . $_POST['id']);
      $ongkir = $this->model("Biteship")->cek_ongkir($this->dCabang, $d['area_id'], $d['latt'], $d['longt']);
      echo "<pre>";
      print_r($ongkir);
      echo "</pre>";
   }
}
