<?php

class PackLabel extends Controller
{
   function __construct()
   {
      $this->session_cek();
      $this->operating_data();
   }

   function index($cetak = [])
   {
      $data_operasi = ['title' => __CLASS__];
      $this->view('layout', ['data_operasi' => $data_operasi]);

      $table = "pelanggan";
      $data['cetak'] = $cetak;
      $data['all'] = $this->db(0)->get($table);
      $this->view(__CLASS__ . '/content', $data);
   }

   function cetak()
   {
      $post = explode("_EXP_", $_POST['pelanggan']);
      $data['pelanggan'] = $post[0];
      $data['cabang'] = $post[1];
      $this->index($data);
   }
}
