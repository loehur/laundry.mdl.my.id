<?php

class Prepaid extends Controller
{
   public function __construct()
   {
      $this->session_cek();
      $this->operating_data();
   }

   public function index()
   {
      $this->session_cek(1);
      $view = 'prepaid/content';
      $data_operasi = ['title' => 'Prepaid'];
      $this->view('layout', ['data_operasi' => $data_operasi]);
      $data['list'] = $this->db(0)->get_where("prepaid_list", "id_cabang = " . $_SESSION['user']['id_cabang']);
      $this->view($view, $data);
   }

   function buy()
   {
      $pin = $_POST['pin'];
      $id = $_POST['id'];
      if (!is_numeric($pin)) {
         $res = [
            'code' => 0,
            'msg' => "PIN tidak valid"
         ];
         print_r(json_encode($res));
         exit();
      }
      $otp = $this->model("Enc")->otp($pin);
      //cekpin
      $user_data = $this->data('User')->pin_today($_SESSION['user']['username'], $otp);
      if (isset($user_data['otp'])) {
         //pin ok
         //cek limit
         $pakai_bulan_ini = $this->data('Pre')->bulan_ini();
         $pre_list = $this->db(0)->get_where_row("prepaid_list", "pre_id = " . $id . " AND id_cabang = " . $_SESSION['user']['id_cabang']);
         $customer_id = $pre_list['customer_id'];
         $product_code = $pre_list['product_code'];
         $akan_dipakai = $pre_list['nominal'];
         $limit = $pre_list['monthly_limit'];
         $total_pakai = $akan_dipakai + $pakai_bulan_ini;

         if ($total_pakai > $limit) {
            $res = [
               'code' => 0,
               'msg' => "Pembelian sudah mencapai limit bulanan"
            ];
         } else {
            $ref_id = "mdlpre-" . date('YmdHis') . "-" . $_SESSION['user']['id_cabang'];

            $col = "id_cabang, ref_id, product_code, customer_id";
            $val = "'" . $_SESSION['user']['id_cabang'] . "','" . $ref_id . "','" . $product_code . "','" . $customer_id . "'";
            $do = $this->db(0)->insertCols("prepaid", $col, $val);

            if ($do['errno'] == 0) {
               $a = $this->db(0)->get_where_row("prepaid", "ref_id = '" . $ref_id . "'");
               $proses = $this->model('IAK')->pre_pay($ref_id, $customer_id, $product_code);
               if (isset($proses['data'])) {
                  $d = $proses['data'];

                  $tr_status = isset($d['status']) ? $d['status'] : $a['tr_status'];
                  $price = isset($d['price']) ? $d['price'] : $a['price'];
                  $message = isset($d['message']) ? $d['message'] : $a['message'];
                  $balance = isset($d['balance']) ? $d['balance'] : $a['balance'];
                  $tr_id = isset($d['tr_id']) ? $d['tr_id'] : $a['tr_id'];
                  $rc = isset($d['rc']) ? $d['rc'] : $a['rc'];
                  $sn = isset($d['sn']) ? $d['sn'] : $a['sn'];

                  $where = "ref_id = '" . $ref_id . "'";
                  $set =  "sn = '" . $sn . "', tr_status = " . $tr_status . ", price = " . $price . ", message = '" . $message . "', balance = " . $balance . ", tr_id = '" . $tr_id . "', rc = '" . $rc . "'";
                  $update = $this->db(0)->update('prepaid', $set, $where);
                  if ($update['errno'] == 0) {
                     $res = [
                        'code' => 1,
                        'msg' => "Pembelian diproses"
                     ];
                  } else {
                     $res = [
                        'code' => 0,
                        'msg' => $update['error']
                     ];
                  }
               } else {
                  $res = [
                     'code' => 0,
                     'msg' => "Server sedang gangguan, silahkan reload halaman dan klik [cek status] transaksi"
                  ];
               }
            } else {
               $res = [
                  'code' => 0,
                  'msg' => $do['error']
               ];
            }
         }
      } else {
         $res = [
            'code' => 0,
            'msg' => "PIN tidak valid"
         ];
      }
      print_r(json_encode($res));
   }

   function cek_status()
   {
      $ref_id = $_POST['ref_id'];
      $a = $this->db(0)->get_where_row("prepaid", "ref_id = '" . $ref_id . "'");
      $response = $this->model('IAK')->pre_cek($ref_id);
      if (isset($response['data'])) {
         $d = $response['data'];

         $tr_status = isset($d['status']) ? $d['status'] : $a['tr_status'];
         $price = isset($d['price']) ? $d['price'] : $a['price'];
         $message = isset($d['message']) ? $d['message'] : $a['message'];
         $balance = isset($d['balance']) ? $d['balance'] : $a['balance'];
         $tr_id = isset($d['tr_id']) ? $d['tr_id'] : $a['tr_id'];
         $rc = isset($d['rc']) ? $d['rc'] : $a['rc'];
         $sn = isset($d['sn']) ? $d['sn'] : $a['sn'];

         $where = "ref_id = '" . $ref_id . "'";
         $set =  "sn = '" . $sn . "', tr_status = " . $tr_status . ", price = " . $price . ", message = '" . $message . "', balance = " . $balance . ", tr_id = '" . $tr_id . "', rc = '" . $rc . "'";
         $update = $this->db(0)->update('prepaid', $set, $where);
         if ($update['errno'] == 0) {
            echo 0;
         } else {
            echo $update['error'];
         }
      } else {
         echo  "API server response error";
      }
   }

   function load_data()
   {
      $view = 'prepaid/data';
      $data = $this->db(0)->get_where("Prepaid", "id_cabang = " . $_SESSION['user']['id_cabang'] . " ORDER BY id DESC LIMIT 10");
      $this->view($view, $data);
   }
}
