<?php

require 'app/Config/URL.php';

class Controller extends URL
{

    public $v_load, $v_content, $v_viewer;
    public $data_user, $table;
    public $id_user, $nama_user, $id_laundry, $id_cabang, $id_privilege, $wUser, $wLaundry, $wCabang, $dKota, $dPrivilege, $dLayanan, $dDurasi, $dPenjualan, $dSatuan, $dItem, $dItemPengeluaran;
    public $dMetodeMutasi, $dStatusMutasi;
    public $user, $userAll, $userCabang, $userMerge, $pelanggan, $pelangganLaundry, $harga, $itemGroup, $surcas, $diskon, $setPoin, $langganan, $cabang_registered;
    public $dLaundry, $dCabang, $listCabang, $surcasPublic, $mdl_setting;
    public $pelanggan_p;

    public function data()
    {
        if (isset($_SESSION['login_laundry'])) {
            if ($_SESSION['login_laundry'] == true) {
                $this->id_user = $_SESSION['user']['id'];
                $this->nama_user = $_SESSION['user']['nama'];
                $this->id_laundry = $_SESSION['user']['id_laundry'];

                $this->id_cabang = $_SESSION['user']['id_cabang'];
                $this->id_privilege = $_SESSION['user']['id_privilege'];

                $this->wUser = 'id_user = ' . $this->id_user;
                $this->wLaundry = 'id_laundry = ' . $this->id_laundry;
                $this->wCabang = 'id_cabang = ' . $this->id_cabang;

                $this->dKota = $_SESSION['data']['kota'];
                $this->dPrivilege = $_SESSION['data']['privilege'];
                $this->dLayanan = $_SESSION['data']['layanan'];
                $this->dDurasi = $_SESSION['data']['durasi'];
                $this->dPenjualan = $_SESSION['data']['penjualan'];
                $this->dSatuan = $_SESSION['data']['satuan'];
                $this->dItem = $_SESSION['data']['item'];
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
                $this->langganan = $_SESSION['langganan'];
                $this->cabang_registered = $_SESSION['cabang_registered'];

                $this->mdl_setting = $_SESSION['mdl_setting'];
                if ($this->mdl_setting == "") {
                    $this->mdl_setting['print_ms'] = 0;
                    $this->mdl_setting['def_price'] = 0;
                }

                $this->dLaundry = array('nama_laundry' => 'NO LAUNDRY');
                if (isset($_SESSION['data']['laundry'])) {
                    $this->dLaundry = $_SESSION['data']['laundry'];
                    $this->listCabang = $_SESSION['data']['listCabang'];
                }
                $this->dCabang = array('kode_cabang' => '00');
                if (isset($_SESSION['data']['cabang'])) {
                    $this->dCabang = $_SESSION['data']['cabang'];
                }
            }
        }
    }

    public function public_data($id_laundry, $pelanggan)
    {
        $this->dLayanan = $this->model('M_DB_1')->get('layanan');
        $this->dDurasi = $this->model('M_DB_1')->get('durasi');
        $this->dPenjualan = $this->model('M_DB_1')->get('penjualan_jenis');
        $this->dSatuan = $this->model('M_DB_1')->get('satuan');
        $this->dItem = $this->model('M_DB_1')->get_where("item", "id_laundry = " . $id_laundry);
        $this->harga =  $this->model('M_DB_1')->get_where("harga", "id_laundry = " . $id_laundry . " ORDER BY sort ASC");
        $this->itemGroup = $this->model('M_DB_1')->get_where("item_group", "id_laundry = " . $id_laundry);
        $this->diskon = $this->model('M_DB_1')->get_where("diskon_qty", "id_laundry = " . $id_laundry);
        $this->setPoin = $this->model('M_DB_1')->get_where("poin_set", "id_laundry = " . $id_laundry);
        $this->dMetodeMutasi = $this->model('M_DB_1')->get('mutasi_metode');
        $this->dStatusMutasi = $this->model('M_DB_1')->get('mutasi_status');
        $this->pelanggan_p = $this->model('M_DB_1')->get_where("pelanggan", "id_laundry = " . $id_laundry . " AND id_pelanggan = " . $pelanggan);
        $this->wLaundry = 'id_laundry = ' . $id_laundry;
        $this->surcasPublic = $this->model('M_DB_1')->get_where('surcas_jenis', 'id_laundry = ' . $id_laundry);
    }


    public function view($file, $data = [])
    {
        $this->data();
        require_once "app/Views/" . $file . ".php";
    }

    public function model($file)
    {
        require_once "app/Models/" . $file . ".php";
        return new $file();
    }

    public function load_sm($dir, $file)
    {
        require_once "app/Views/Load/" . $dir . "/" . $file . ".php";
    }

    public function session_cek()
    {
        if (isset($_SESSION['login_laundry'])) {
            if ($_SESSION['login_laundry'] == False) {
                header("location: " . $this->BASE_URL . "Login");
            }
        } else {
            header("location: " . $this->BASE_URL . "Login");
        }

        if ($this->id_laundry == 3) {
            session_start();
            session_unset();
            session_destroy();
            header('Location: ' . $this->BASE_URL . "Penjualan/i");
            exit();
        }
    }

    public function parameter()
    {
        $_SESSION['user'] = array(
            'nama' => $this->data_user['nama_user'],
            'id' => $this->data_user['id_user'],
            'id_laundry' => $this->data_user['id_laundry'],
            'id_cabang' => $this->data_user['id_cabang'],
            'id_privilege' => $this->data_user['id_privilege'],
        );

        $_SESSION['order'] = array(
            'user' => $this->model('M_DB_1')->get_where("user", "en = 1 AND id_cabang = " . $_SESSION['user']['id_cabang']),
            'userAll' => $this->model('M_DB_1')->get_where("user", "id_cabang = " . $_SESSION['user']['id_cabang']),
            'userCabang' => $this->model('M_DB_1')->get_where("user", "en = 1 AND id_laundry = " . $_SESSION['user']['id_laundry'] . " AND id_cabang <> " . $_SESSION['user']['id_cabang']),
            'pelanggan' => $this->model('M_DB_1')->get_where("pelanggan", "id_cabang = " . $_SESSION['user']['id_cabang'] . " ORDER by sort DESC"),
            'pelangganLaundry' => $this->model('M_DB_1')->get_where("pelanggan", "id_laundry = " . $_SESSION['user']['id_laundry'] . " ORDER by sort DESC"),
            'harga' => $this->model('M_DB_1')->get_where("harga", "id_laundry = " . $_SESSION['user']['id_laundry'] . " ORDER BY sort DESC"),
            'itemGroup' => $this->model('M_DB_1')->get_where("item_group", "id_laundry = " . $_SESSION['user']['id_laundry']),
            'surcas' => $this->model('M_DB_1')->get_where("surcas_jenis", "id_laundry = " . $_SESSION['user']['id_laundry']),
            'diskon' => $this->model('M_DB_1')->get_where("diskon_qty", "id_laundry = " . $_SESSION['user']['id_laundry']),
            'setPoin' => $this->model('M_DB_1')->get_where("poin_set", "id_laundry = " . $_SESSION['user']['id_laundry']),
        );

        $_SESSION['data'] = array(
            'laundry' => $this->model('M_DB_1')->get_where_row('laundry', 'id_laundry = ' . $_SESSION['user']['id_laundry']),
            'cabang' => $this->model('M_DB_1')->get_where_row('cabang', 'id_cabang = ' . $_SESSION['user']['id_cabang']),
            'listCabang' => $this->model('M_DB_1')->get_where('cabang', 'id_laundry = ' . $_SESSION['user']['id_laundry']),
            'kota' => $this->model('M_DB_1')->get('kota'),
            'layanan' => $this->model('M_DB_1')->get('layanan'),
            'privilege' => $this->model('M_DB_1')->get_where('privilege', 'id_privilege <> 100'),
            'durasi' => $this->model('M_DB_1')->get('durasi'),
            'penjualan' => $this->model('M_DB_1')->get('penjualan_jenis'),
            'satuan' => $this->model('M_DB_1')->get('satuan'),
            'mutasi_metode' => $this->model('M_DB_1')->get('mutasi_metode'),
            'mutasi_status' => $this->model('M_DB_1')->get('mutasi_status'),
            'item' => $this->model('M_DB_1')->get_where("item", "id_laundry = " . $_SESSION['user']['id_laundry']),
            'item_pengeluaran' => $this->model('M_DB_1')->get_where("item_pengeluaran", "id_laundry = " . $_SESSION['user']['id_laundry']),
        );

        $_SESSION['mdl_setting'] = $this->model('M_DB_1')->get_where_row('setting', 'id_laundry = ' . $_SESSION['user']['id_laundry'] . ' AND id_cabang = ' . $_SESSION['user']['id_cabang']);
    }
    public function parameter_unset()
    {
        unset($_SESSION['user']);
        unset($_SESSION['data']);
        unset($_SESSION['order']);
        unset($_SESSION['set']);
        unset($_SESSION['langganan']);
        unset($_SESSION['cabang_registered']);
    }

    public function dataSynchrone()
    {
        $where = "id_user = " . $this->id_user;
        $this->data_user = $this->model('M_DB_1')->get_where_row('user', $where);
        $this->parameter_unset();
        $this->parameter();
    }
}
