<?php

class KasModel extends Controller
{
    use Attributes;
    
    public function __construct() {
        $this->db(0); // Initialize DB connection
    }

    public function bayarMulti($data_rekap, $dibayar, $id_pelanggan, $id_cabang, $id_user, $metode = 2, $note = "")
    {
        $minute = date('Y-m-d H:');

        if (count($data_rekap) == 0) {
            return false;
        }

        $note = str_replace("_SPACE_", " ", $note);

        if (strlen($note) == 0) {
            switch ($metode) {
                case 2:
                    $note = "Non_Tunai";
                    break;
                case 3:
                    $note = "Saldo";
                    break;
                default:
                    $note = "";
                    break;
            }
        }

        arsort($data_rekap);
        $ref_f = (date('Y') - 2024) . date('mdHis') . rand(0, 9) . rand(0, 9) . $id_cabang;

        // $cols = 'id_cabang, jenis_mutasi, jenis_transaksi, ref_transaksi, metode_mutasi, note, status_mutasi, jumlah, id_user, id_client, ref_finance, insertTime';
        
        foreach ($data_rekap as $key => $value) {
            if ($dibayar == 0) {
                return 0; // Or specific code indicating processed partial/full
            }

            $xNoref = $key;
            $jumlah = $value;

            if ($jumlah == 0) {
                continue;
            }

            $ref = substr($xNoref, 2);
            $tipe = substr($xNoref, 0, 1);

            if ($dibayar < $jumlah) {
                $jumlah = $dibayar;
            }

            $jenis_mutasi = 1;
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
                $dibayar -= $jumlah;
                if ($do['errno'] <> 0) {
                     $this->write("[KasModel::bayarMulti] Insert Kas Error: " . $do['error']);
                     return $do['error'];
                }
            } else {
                return "Pembayaran dengan jumlah yang sama terkunci, lakukan di jam berikutnya.";
            }
        }
        return 0;
    }
}
