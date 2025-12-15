<?php

class NonTunai extends Controller
{
   public function __construct()
   {
      $this->session_cek();
      $this->operating_data();
   }

   public function index()
   {
      $limit = 12;
      $view = 'non_tunai/nt_main';
      $cols = "ref_finance, note, id_user, id_client, status_mutasi, jenis_transaksi, SUM(jumlah) as total";
      $where = $this->wCabang . " AND metode_mutasi = 2 AND status_mutasi = 2 AND ref_finance <> '' GROUP BY ref_finance ORDER BY ref_finance DESC LIMIT $limit";
      $list['cek'] = $this->db($_SESSION[URL::SESSID]['user']['book'])->get_cols_where('kas', $cols, $where, 1);

      $this->view($view, $list);
   }

   public function operasi($tipe)
   {
      $id = $_POST['id'];
      $set = [
         'status_mutasi' => $tipe
      ];
      $where = $this->wCabang . " AND ref_finance = '" . $id . "'";
      $up = $this->db($_SESSION[URL::SESSID]['user']['book'])->update('kas', $set, $where);
      if($up['errno'] <> 0){
         $this->model('Log')->write('[NonTunai::operasi] Update Kas Error: ' . $up['error']);
         return $up['error'];
      }else{
         //delete tracker webhooks
         $delete = $this->db(100)->delete('wh_moota', "trx_id = '" . $id . "'");
         if($delete['errno'] <> 0){
            $this->model('Log')->write('[NonTunai::operasi] Delete Wh Moota Error: ' . $delete['error']);
            return $delete['error'];
         }
      }
      return 0;
   }

   public function tokopayBalance()
   {
      header('Content-Type: application/json');
      
      try {
         $response = $this->model('Tokopay')->merchant();
         $responseData = json_decode($response, true);
         
         // Log API response
         $status = ($responseData['status'] ?? '') === 'Success' ? 'success' : 'error';
         $this->model('Log')->apiLog('Tokopay/merchant/balance', null, $response, $status);
         
         echo $response;
      } catch (Exception $e) {
         $errorResponse = ['status' => 'error', 'message' => $e->getMessage()];
         $this->model('Log')->apiLog('Tokopay/merchant/balance', null, $errorResponse, 'error');
         echo json_encode($errorResponse);
      }
   }
}
