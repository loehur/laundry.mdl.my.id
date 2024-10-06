<?php

require 'app/Config/URL.php';

class Controller extends URL
{

    public $v_load, $v_content, $v_viewer;
    public $data_user, $table;
    public $user_login, $id_user, $nama_user, $id_cabang, $id_cabang_p, $id_privilege, $wUser, $wCabang, $dKota, $dPrivilege, $dLayanan, $dDurasi, $dPenjualan, $dSatuan, $dItem, $dItemPengeluaran;
    public $dMetodeMutasi, $dStatusMutasi;
    public $user, $userAll, $userCabang, $userMerge, $pelanggan, $pelangganLaundry, $harga, $itemGroup, $surcas, $diskon, $setPoin, $langganan, $cabang_registered;
    public $dLaundry, $dCabang, $listCabang, $surcasPublic, $mdl_setting;
    public $pelanggan_p;
    public $kode_cabang;

    public function operating_data()
    {
        if (isset($_SESSION['login_laundry'])) {
            if ($_SESSION['login_laundry'] == true) {
                $this->user_login = $_SESSION['user'];
                $this->id_user = $_SESSION['user']['id_user'];
                $this->nama_user = $_SESSION['user']['nama_user'];

                $this->id_cabang = $_SESSION['user']['id_cabang'];
                $this->id_privilege = $_SESSION['user']['id_privilege'];

                $this->wUser = 'id_user = ' . $this->id_user;
                $this->wCabang = 'id_cabang = ' . $this->id_cabang;

                $this->dPrivilege = $_SESSION['data']['privilege'];
                $this->dLayanan = $_SESSION['data']['layanan'];
                $this->dDurasi = $_SESSION['data']['durasi'];
                $this->dPenjualan = $_SESSION['data']['penjualan_jenis'];
                $this->dSatuan = $_SESSION['data']['satuan'];
                $this->dItem = $_SESSION['data']['item'];
                $this->dKota = $_SESSION['data']['kota'];
                $this->dItemPengeluaran = $_SESSION['data']['item_pengeluaran'];
                $this->dMetodeMutasi = $_SESSION['data']['mutasi_metode'];
                $this->dStatusMutasi = $_SESSION['data']['mutasi_status'];

                $this->user = $_SESSION['order']['user'];
                $this->userCabang = $_SESSION['order']['userCabang'];
                $this->userAll = $_SESSION['order']['userAll'];
                $this->userMerge = array_merge($this->user, $this->userCabang);
                $this->pelanggan = $_SESSION['order']['pelanggan'];
                $this->pelangganLaundry = $_SESSION['order']['pelangganLaundry'];
                $this->harga = $_SESSION['order']['harga'];
                $this->itemGroup = $_SESSION['order']['itemGroup'];
                $this->surcas = $_SESSION['order']['surcas'];
                $this->diskon = $_SESSION['order']['diskon'];
                $this->setPoin = $_SESSION['order']['setPoin'];

                if (count($_SESSION['mdl_setting']) == 0) {
                    $_SESSION['mdl_setting']['print_ms'] = 0;
                    $_SESSION['mdl_setting']['def_price'] = 0;
                }
                $this->mdl_setting = $_SESSION['mdl_setting'];

                $this->dLaundry = array('nama_laundry' => 'NO LAUNDRY');
                $this->listCabang = $_SESSION['data']['listCabang'];
                $this->dCabang = array('kode_cabang' => '00');
                if (isset($_SESSION['data']['cabang'])) {
                    $this->dCabang = $_SESSION['data']['cabang'];
                }
            }
        }
    }

    public function public_data($pelanggan)
    {
        $this->dLayanan = $this->db(0)->get('layanan');
        $this->dDurasi = $this->db(0)->get('durasi');
        $this->dPenjualan = $this->db(0)->get('penjualan_jenis');
        $this->dSatuan = $this->db(0)->get('satuan');
        $this->dItem = $this->db(0)->get("item");
        $this->harga =  $this->db(0)->get_order("harga", "sort ASC");
        $this->itemGroup = $this->db(0)->get("item_group");
        $this->diskon = $this->db(0)->get("diskon_qty");
        $this->setPoin = $this->db(0)->get("poin_set");
        $this->dMetodeMutasi = $this->db(0)->get('mutasi_metode');
        $this->dStatusMutasi = $this->db(0)->get('mutasi_status');
        $this->pelanggan_p = $this->db(0)->get_where_row("pelanggan", "id_pelanggan = " . $pelanggan);
        $this->id_cabang_p = $this->pelanggan_p['id_cabang'];
        $this->surcasPublic = $this->db(0)->get('surcas_jenis');
    }


    public function view($file, $data = [])
    {
        $this->operating_data();
        require_once "app/Views/" . $file . ".php";
    }

    public function model($file)
    {
        require_once "app/Models/" . $file . ".php";
        return new $file();
    }

    public function data($file)
    {
        require_once "app/Data/" . $file . ".php";
        return new $file();
    }

    public function db($db = 0)
    {
        $file = "M_DB";
        require_once "app/Models/" . $file . ".php";
        return new $file($db);
    }

    public function session_cek($admin = 0)
    {
        if (isset($_SESSION['login_laundry'])) {
            if ($_SESSION['login_laundry'] == False) {
                session_destroy();
                header("location: " . URL::BASE_URL . "Login");
            } else {
                if ($admin == 1) {
                    if ($_SESSION['user']['id_privilege'] <> 100) {
                        session_destroy();
                        header("location: " . URL::BASE_URL . "Login");
                    }
                }
            }
        } else {
            header("location: " . URL::BASE_URL . "Login");
        }
    }

    public function parameter()
    {
        $_SESSION['user'] = $this->data_user;

        $_SESSION['order'] = array(
            'user' => $this->db(0)->get_where("user", "en = 1 AND id_cabang = " . $_SESSION['user']['id_cabang']),
            'userAll' => $this->db(0)->get_where("user", "id_cabang = " . $_SESSION['user']['id_cabang']),
            'userCabang' => $this->db(0)->get_where("user", "en = 1 AND id_cabang <> " . $_SESSION['user']['id_cabang']),
            'pelanggan' => $this->db(0)->get_where("pelanggan", "id_cabang = " . $_SESSION['user']['id_cabang'] . " ORDER by sort DESC"),
            'pelangganLaundry' => $this->db(0)->get_order("pelanggan", "sort DESC"),
            'harga' => $this->db(0)->get_order("harga", "sort DESC"),
            'itemGroup' => $this->db(0)->get("item_group"),
            'surcas' => $this->db(0)->get("surcas_jenis"),
            'diskon' => $this->db(0)->get("diskon_qty"),
            'setPoin' => $this->db(0)->get("poin_set"),
        );

        $_SESSION['data'] = array(
            'cabang' => $this->db(0)->get_where_row('cabang', 'id_cabang = ' . $_SESSION['user']['id_cabang']),
            'listCabang' => $this->db(0)->get('cabang'),
            'layanan' => $this->db(0)->get('layanan'),
            'privilege' => $this->db(0)->get('privilege'),
            'durasi' => $this->db(0)->get('durasi'),
            'penjualan_jenis' => $this->db(0)->get('penjualan_jenis'),
            'satuan' => $this->db(0)->get('satuan'),
            'mutasi_metode' => $this->db(0)->get('mutasi_metode'),
            'mutasi_status' => $this->db(0)->get('mutasi_status'),
            'item' => $this->db(0)->get("item"),
            'kota' => $this->db(0)->get("kota"),
            'item_pengeluaran' => $this->db(0)->get("item_pengeluaran"),
        );

        $_SESSION['mdl_setting'] = $this->db(0)->get_where_row('setting', 'id_cabang = ' . $_SESSION['user']['id_cabang']);
    }

    public function dataSynchrone()
    {
        $where = "id_user = " . $this->id_user;
        $this->data_user = $this->db(0)->get_where_row('user', $where);
        $this->parameter();
    }

    function valid_number($number)
    {
        if (!is_numeric($number)) {
            $number = preg_replace('/[^0-9]/', '', $number);
        }

        if (substr($number, 0, 2) == '08') {
            if (strlen($number) >= 10 && strlen($number) <= 13) {
                return $number;
            } else {
                return false;
            }
        } else if (substr($number, 0, 3) == '628') {
            if (strlen($number) >= 11 && strlen($number) <= 14) {
                $fix_number = "0" . substr($number, 2);
                return $fix_number;
            } else {
                return false;
            }
        } else if (substr($number, 0, 4) == '+628') {
            if (strlen($number) >= 12 && strlen($number) <= 15) {
                $fix_number = "0" . substr($number, 3);
                return $fix_number;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
}
