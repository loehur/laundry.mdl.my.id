<?php

class KasModel extends Controller
{
    use Attributes;
    
    public function __construct() {
        $this->db(0); // Initialize DB connection
    }

    public function bayarMulti($data_rekap, $dibayar, $id_pelanggan, $id_cabang, $id_user, $metode = 2, $note = "", $jenis_mutasi = 1)
    {
        $total_dibayar = 0;

        $use_bayar = true;
        if($dibayar == 0) {
            $use_bayar = false;
        }

        $minute = date('Y-m-d H:');

        if (count($data_rekap) == 0) {
            return false;
        }

        if($metode == 1){
            if($note == ""){
                $note = "CASH";
            }
        }else{
            if($note == ""){
                return "Pembayaran Non Tunai wajib memilih Tujuan Bayar";
            }
        }

        arsort($data_rekap);
        $ref_f = (date('Y') - 2024) . date('mdHis') . rand(0, 9) . rand(0, 9) . $id_cabang;
       
        foreach ($data_rekap as $key => $value) {
            if ($use_bayar && $dibayar == 0) {
                return 0; // Or specific code indicating processed partial/full
            }

            $xNoref = $key;

            $jumlah = $value;

            if ($jumlah == 0) {
                continue;
            }

            $ref = substr($xNoref, 2);
            $tipe = substr($xNoref, 0, 1);

            if ($use_bayar) {
                if ($dibayar < $jumlah) {
                    $jumlah = $dibayar;
                }
            } else {
                $jumlah = $value;
            }

            if ($metode == 3) {
                $sisaSaldo = $this->helper('Saldo')->getSaldoTunai($id_pelanggan);
                if ($sisaSaldo > 0) {
                    if ($jumlah > $sisaSaldo) {
                        $jumlah = $sisaSaldo;
                    }
                } else {
                    return "Saldo tidak cukup";
                }
                $jenis_mutasi = 2;
            }

            $status_mutasi = 3;
            switch ($metode) {
                case "2":
                    $status_mutasi = 2;
                    break;
                default:
                    $status_mutasi = 3;
                    break;
            }

            $jt = $tipe == "M" ? 3 : 1;
            $setOne = "ref_transaksi = '" . $ref . "' AND jumlah = " . $jumlah . " AND insertTime LIKE '%" . $minute . "%'";
            $wCabang = "id_cabang = " . $id_cabang;
            $where = $wCabang . " AND " . $setOne;
            $data_main = $this->db(date('Y'))->count_where('kas', $where);
            
            if ($data_main < 1) {
                $data = [
                    'id_cabang' => $id_cabang,
                    'jenis_mutasi' => 1,
                    'jenis_transaksi' => $jt,
                    'ref_transaksi' => $ref,
                    'metode_mutasi' => $metode,
                    'note' => $note,
                    'status_mutasi' => $status_mutasi,
                    'jumlah' => $jumlah,
                    'id_user' => $id_user,
                    'id_client' => $id_pelanggan,
                    'ref_finance' => $ref_f,
                    'insertTime' => $GLOBALS['now']
                ];
                $do = $this->db(date('Y'))->insert('kas', $data);
                if ($do['errno'] == 0) {
                    if($use_bayar) {
                        $dibayar -= $jumlah;
                    }
                    $total_dibayar += $jumlah;
                } else {
                    $this->write("[KasModel::bayarMulti] Insert Kas Error: " . $do['error']);
                    return $do['error'];
                }
            } else {
                return "Pembayaran dengan jumlah yang sama terkunci, lakukan di jam berikutnya.";
            }
        }
        
        if ($total_dibayar > 0 && $metode == 2 && $note <> "QRIS") {
            $dPelanggan = $this->db(0)->get_where_row("pelanggan", "id_pelanggan = " . $id_pelanggan);
            
            $bank_acc_id = isset(URL::MOOTA_BANK_ID[$note]) ? URL::MOOTA_BANK_ID[$note] : '';
            
            if(empty($bank_acc_id)){
                 $this->write("[KasModel::bayarMulti] Moota Error: Bank ID not found in URL::MOOTA_BANK_ID for note: $note");
                 return 0; // Or handle error? existing logic just returns 0 on success/ignore
            }

            $curl = curl_init();

            $total_dibayar -= 1;
            $payload = [
                "order_id" => $ref_f,
                "bank_account_id" => $bank_acc_id,
                "customers" => [
                    "name" => $dPelanggan['nama_pelanggan'],
                    "email" => $id_pelanggan . "@mdl.id", // Using ID as email as requested in prompt "email" : {$id_pelanggan}
                    "phone" => $dPelanggan['nomor_pelanggan']
                ],
                "items" => [[
                    "name" => "Laundry",
                    "description" => $ref_f,
                    "qty" => 1,
                    "price" => $total_dibayar,
                ]],
                "description" => "MDL PAYMENT",
                "note" => null,
                "redirect_url" => "",
                "total" => $total_dibayar
            ];

            curl_setopt_array($curl, array(
              CURLOPT_URL => 'https://api.moota.co/api/v2/create-transaction',
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => '',
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 0,
              CURLOPT_FOLLOWLOCATION => true,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => 'POST',
              CURLOPT_POSTFIELDS => json_encode($payload),
              CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . URL::MOOTA_TOKEN,
                'Content-Type: application/json',
                'Accept: application/json'
              ),
            ));

            $response = curl_exec($curl);
            $err = curl_error($curl);
            curl_close($curl);

            if ($err) {
                $this->write("[KasModel::bayarMulti] cURL Error: " . $err);
                return "Jaringan Error (Moota)";
            } 
            
            $resp = json_decode($response, true);
            
            if(isset($resp['status']) && $resp['status'] == 'success') {
                $amount_unique = $resp['data']['total'];
                $data_moota = [
                    'trx_id' => $ref_f,
                    'amount' => $amount_unique,
                    'target' => 'kas_laundry',
                    'book' => date('Y'),
                    'state' => 'PENDING',
                ];
                $do = $this->db(100)->insert('wh_moota', $data_moota);
                if ($do['errno'] != 0) {
                   $this->write("[KasModel::bayarMulti] Insert Moota Error: " . $do['error']);
                   return $do['error'];
                }
            } else {
                 $msg = isset($resp['message']) ? $resp['message'] : 'Unknown Error';
                 $this->write("[KasModel::bayarMulti] Moota API Error: " . $msg);
                 return $msg;
            }
        }

        return 0;
    }
}
