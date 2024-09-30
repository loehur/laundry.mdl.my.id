<?php

class Kas extends Controller
{
   public function __construct()
   {
      $this->session_cek();
      $this->operating_data();
   }

   public function index()
   {
      $view = 'kas/kas_main';
      $data_operasi = ['title' => 'Kas'];

      $where = $this->wCabang . " AND jenis_mutasi = 1 AND metode_mutasi = 1 AND status_mutasi = 3";
      $cols = "SUM(jumlah) as jumlah";
      $kredit = $this->db(1)->get_cols_where('kas', $cols, $where, 0)['jumlah'];

      $where = $this->wCabang . " AND jenis_mutasi = 2 AND metode_mutasi = 1 AND status_mutasi <> 4";
      $cols = "SUM(jumlah) as jumlah";
      $debit = $this->db(1)->get_cols_where('kas', $cols, $where, 0)['jumlah'];

      $saldo = $kredit - $debit;
      $limit = 10;
      if ($this->id_privilege == 100) {
         $limit = 25;
      }
      $where = $this->wCabang . " AND jenis_mutasi = 2 ORDER BY id_kas DESC LIMIT $limit";
      $debit_list = $this->db(1)->get_where('kas', $where);

      //KASBON
      $where = $this->wCabang . " AND jenis_transaksi = 5 AND jenis_mutasi = 2 AND status_mutasi = 3 ORDER BY id_kas DESC LIMIT 25";
      $kasbon = $this->db(1)->get_where('kas', $where);

      $dataPotong = array();
      foreach ($kasbon as $k) {
         $ref = $k['id_kas'];
         $where = "ref = '" . $ref . "'";
         $countPotong = $this->db(0)->count_where('gaji_result', $where);
         if ($countPotong == 1) {
            $dataPotong[$ref] = 1;
         } else {
            $dataPotong[$ref] = 0;
         }
      }

      $this->view('layout', ['data_operasi' => $data_operasi]);
      $this->view($view, [
         'saldo' => $saldo,
         'debit_list' => $debit_list,
         'kasbon' => $kasbon,
         'dataPotong' => $dataPotong
      ]);
   }

   public function insert()
   {
      $keterangan = $_POST['f1'];
      $jumlah = $_POST['f2'];
      $penarik = $_POST['f3'];
      $today = date('Y-m-d');
      $status_mutasi = 2;

      if ($this->id_privilege == 100) {
         $status_mutasi = 3;
      }

      $cols = 'id_cabang, jenis_mutasi, jenis_transaksi, metode_mutasi, note, status_mutasi, jumlah, id_user, id_client, note_primary';
      $vals = $this->id_cabang . ",2,2,1,'" . $keterangan . "'," . $status_mutasi . "," . $jumlah . "," . $penarik . ",0,'Penarikan'";

      $setOne = "note = '" . $keterangan . "' AND jumlah = " . $jumlah . " AND insertTime LIKE '" . $today . "%'";
      $where = $this->wCabang . " AND " . $setOne;
      $data_main = $this->db(1)->count_where('kas', $where);

      if ($data_main < 1) {
         $do = $this->db(1)->insertCols('kas', $cols, $vals);
         if ($do['errno'] == 0) {
            echo 1;
         } else {
            $this->model('Log')->write($do['error']);
         }
      } else {
         echo "Duplicate Entry!";
      }
   }

   public function insert_pengeluaran()
   {
      $keterangan = $_POST['f1'];
      $jumlah = $_POST['f2'];
      $penarik = $_POST['f3'];
      $today = date('Y-m-d');
      $jenis = $_POST['f1a'];

      $jenisEXP = explode("<explode>", $jenis);
      $id_jenis = $jenisEXP[0];
      $jenis = $jenisEXP[1];

      $status_mutasi = 2;
      if ($this->id_privilege == 100) {
         $status_mutasi = 3;
      }

      $cols = 'id_cabang, jenis_mutasi, jenis_transaksi, metode_mutasi, note, note_primary, status_mutasi, jumlah, id_user, id_client, ref_transaksi';
      $vals = $this->id_cabang . ",2,4,1,'" . $keterangan . "','" . $jenis . "'," . $status_mutasi . "," . $jumlah . "," . $penarik . ",0," . $id_jenis;

      $setOne = "note = '" . $keterangan . "' AND jumlah = " . $jumlah . " AND insertTime LIKE '" . $today . "%'";
      $where = $this->wCabang . " AND " . $setOne;
      $data_main = $this->db(1)->count_where('kas', $where);

      if ($data_main < 1) {
         $do = $this->db(1)->insertCols('kas', $cols, $vals);
         if ($do['errno'] <> 0) {
            $this->model('Log')->write($do['error']);
         }
      }
   }

   function qris_instant($reff_id)
   {
      $cek = $this->db(1)->get_where_row('kas', "ref_finance = '" . $reff_id . "' AND qr_string <> '' AND (status_mutasi <> 3 OR status_mutasi <> 4)");
      $total = $this->db(1)->sum_col_where('kas', 'jumlah', "ref_finance ='" . $reff_id . "'");
      if (count($cek) > 0) {
         $par['jumlah'] = $total;
         $par['qr_link'] = $cek['qr_link'];
         $this->view('operasi/qr_print', $par);
      } else {
         $qr_req = $this->model('Tokopay')->createOrder($total, $reff_id, 'QRIS');
         $data = json_decode($qr_req, true);
         if (isset($data['status'])) {
            if ($data['status'] == 'Success') {
               if (isset($data['data'])) {
                  $d = $data['data'];
                  $set = "pay_url = '" . $d['pay_url'] . "', qr_link = '" . $d['qr_link'] . "', qr_string = '" . $d['qr_string'] . "', trx_id = '" . $d['trx_id'] . "'";
                  $up = $this->db(1)->update('kas', $set, "ref_finance = '" . $reff_id . "'");
                  if ($up['errno'] == 0) {
                     $par['jumlah'] = $total;
                     $par['qr_link'] = $d['qr_link'];
                     $this->view('operasi/qr_print', $par);
                  } else {
                     print_r($up['error']);
                  }
               }
            } else {
               print_r($data);
            }
         } else {
            print_r($data);
         }
      }
   }

   function cek_qris($reff_id, $jumlah)
   {
      $cek = $this->db(1)->get_where_row('kas', "ref_finance = '" . $reff_id . "'");
      if ($cek['status_mutasi'] == 3) {
         echo 0;
      } else {
         $qr_cek = $this->model('Tokopay')->createOrder($jumlah, $reff_id, 'QRIS');
         $data = json_decode($qr_cek, true);
         if (isset($data['status'])) {
            if ($data['status'] == 'Success') {
               if (isset($data['data'])) {
                  $d = $data['data'];
                  if ($d['status'] == 'Paid') {
                     $set = "status_mutasi = 3";
                     $up = $this->db(1)->update('kas', $set, "ref_finance = '" . $reff_id . "'");
                     if ($up['errno'] == 0) {
                        print_r($data);
                     } else {
                        echo ($up['error']);
                     }
                  } else {
                     print_r($data);
                  }
               } else {
                  print_r($data);
               }
            } else {
               print_r($data);
            }
         } else {
            print_r($data);
         }
      }
   }
}
