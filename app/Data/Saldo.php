<?php

class Saldo extends Controller
{
    function getSaldoTunai($id_pelanggan)
    {
        //SALDO DEPOSIT
        $saldo = 0;
        $pakai = 0;

        for ($y = 2021; $y <= date('Y'); $y++) {
            //Kredit
            $where = $this->wCabang . " AND id_client = " . $id_pelanggan . " AND jenis_transaksi = 6 AND jenis_mutasi = 1 AND status_mutasi = 3 GROUP BY id_client ORDER BY saldo DESC";
            $cols = "id_client, SUM(jumlah) as saldo";
            $ks = $this->db($y)->get_cols_where('kas', $cols, $where, 1);
            if (count($ks) > 0) {
                foreach ($ks as $ksv) {
                    array_push($data, $ksv);
                }
            }

            //Kredit
            $where2 = $this->wCabang . " AND id_client = " . $id_pelanggan . " AND jenis_transaksi = 6 AND jenis_mutasi = 2 AND status_mutasi = 3 GROUP BY id_client ORDER BY saldo DESC";
            $cols = "id_client, SUM(jumlah) as saldo";
            $ks2 = $this->db($y)->get_cols_where('kas', $cols, $where2, 1);
            if (count($ks2) > 0) {
                foreach ($ks as $ksv2) {
                    array_push($data3, $ksv2);
                }
            }
        }
        //Debit
        if (count($data) > 0) {
            foreach ($data as $a) {
                $idPelanggan = $a['id_client'];
                $saldo = $a['saldo'];
                $where = $this->wCabang . " AND id_client = " . $idPelanggan . " AND metode_mutasi = 3 AND jenis_mutasi = 2";
                $cols = "SUM(jumlah) as pakai";
                for ($y = 2021; $y <= date('Y'); $y++) {
                    $data2 = $this->db($y)->get_cols_where('kas', $cols, $where, 0);
                    if (isset($data2['pakai'])) {
                        $pakai += $data2['pakai'];
                    }
                }
            }
        }

        if (count($data3) > 0) {
            foreach ($data3 as $a3) {
                $idPelanggan = $a3['id_client'];
                $pakai += $a3['saldo'];
            }
        }

        $sisaSaldo = $saldo - $pakai;
        return $sisaSaldo;
    }
}
