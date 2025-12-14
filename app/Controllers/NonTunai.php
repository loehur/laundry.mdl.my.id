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

      $where = $this->wCabang . " AND metode_mutasi = 2 AND status_mutasi <> 2 AND ref_finance <> '' GROUP BY ref_finance ORDER BY ref_finance DESC LIMIT $limit";
      $list['done'] = $this->db($_SESSION[URL::SESSID]['user']['book'])->get_cols_where('kas', $cols, $where, 1);

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
         //update wh_moota

            $where = "trx_id = '" . $id . "'";
         $tipe = $tipe == 3 ? "PAID" : "FAILED";
         $up = $this->db(100)->update('wh_moota', "state = '$tipe'", $where);
         if($up['errno'] <> 0){
            $this->model('Log')->write('[NonTunai::operasi] Update Wh Moota Error: ' . $up['error']);
            return $up['error'];
         }else{
            $this->model('Log')->write('[NonTunai::operasi] Update Wh Moota Success');
            $moota_row = $this->db(100)->get_where_row('wh_moota', "trx_id = '" . $id . "'");
            if($moota_row['conflict'] == 1){
               $update = $this->db(100)->update('wh_moota', "state = 'PAID'", "conflict = 1 AND amount = " . $moota_row['amount'] . " AND bank_id = " . $moota_row['bank_id']);
               $this->model('Log')->write('[NonTunai::operasi] Update Wh Moota Conflict');
            }
         }
      }
      return 0;
   }
}
