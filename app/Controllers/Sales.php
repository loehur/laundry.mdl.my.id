<?php

class Sales extends Controller
{
   public function __construct()
   {
      $this->session_cek();
      $this->operating_data();
   }

   public function index()
   {
      $data_operasi = ['title' => 'Sales Order'];
      $this->view('layout', ['data_operasi' => $data_operasi]);
      $this->view('sales/index', ['data_operasi' => $data_operasi]);
   }

   // Load form order untuk offcanvas
   public function form()
   {
      $barang_data = $this->db(1)->get_where('barang_data','state = 1');
      $this->view('sales/form', ['barang_data' => $barang_data]);
   }

   // Load barang_sub berdasarkan id_barang
   public function get_sub($id_barang)
   {
      $where = "id_barang = '$id_barang'";
      $barang_sub = $this->db(1)->get_where('barang_sub', $where);
      
      // Get parent barang info
      $barang = $this->db(1)->get_where_row('barang_data', "id_barang = '$id_barang'");
      
      // Get unit name
      $unit_nama = '';
      if (isset($barang['unit'])) {
          $unit = $this->db(1)->get_where_row('barang_unit', "id = '{$barang['unit']}'");
          $unit_nama = $unit['nama'] ?? '';
      }
      $barang['unit_nama'] = $unit_nama;
      
      // Add margin for main item
      $barang['margin'] = floatval($barang['margin'] ?? 0);
      
      // Calculate margin for each sub item
      $barang_harga = floatval($barang['harga'] ?? $barang['price'] ?? 0);
      foreach ($barang_sub as &$sub) {
          $sub_denom = floatval($sub['qty'] ?? 1);
          $sub_price = floatval($sub['price'] ?? $sub['harga'] ?? 0);
          // Margin: ((1/denom) * sub_price) - barang_data.harga
          $sub['margin'] = ($sub_denom > 0) ? ((1 / $sub_denom) * $sub_price) - $barang_harga : 0;
      }
      unset($sub);

      
      header('Content-Type: application/json');
      echo json_encode([
         'barang' => $barang,
         'sub' => $barang_sub
      ]);
   }

   // Tambah ke cart
   public function add_to_cart()
   {
      ob_start(); // Capture any unexpected output
      
      $id_barang = $_POST['id_barang'] ?? 0;
      $id_sub = $_POST['id_sub'] ?? 0;
      $qty = intval($_POST['qty'] ?? 1);
      
      // Initialize cart session if not exists
      if (!isset($_SESSION['sales_cart'])) {
         $_SESSION['sales_cart'] = [];
      }
      
      // Get item info
      if ($id_sub > 0) {
         $item = $this->db(1)->get_where_row('barang_sub', "id = '$id_sub'");
         $barang = $this->db(1)->get_where_row('barang_data', "id_barang = '$id_barang'");
         $barang_harga = floatval($barang['price'] ?? 0);         
         
         if (!$item || !$barang) {
            ob_end_clean();
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Item tidak ditemukan']);
            return;
         }
         
         $denom = floatval($item['qty'] ?? 1);
         $multiple = 1 / $denom;
         
         $nama = ($barang['nama'] ?? strtoupper($barang['brand'].' '.$barang['model'])) . ' - ' . $item['nama'];
         $harga = $item['price'] ?? 0;
        
         $margin = (($harga*$multiple)-$barang_harga)/$multiple;
         $harga = $harga-$margin;
         
         $cart_key = 'sub_' . $id_sub;
      } else {
         $item = $this->db(1)->get_where_row('barang_data', "id_barang = '$id_barang'");
         
         if (!$item) {
            ob_end_clean();
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Item tidak ditemukan']);
            return;
         }
         
         $nama = $item['nama'] ?? strtoupper(implode(' ', array_filter([$item['brand'] ?? '', $item['model'] ?? '', $item['description'] ?? ''])));
         $harga = $item['price'] ?? $item['harga'] ?? 0;
         $denom = 1; // Main item denom = 1
         $margin = floatval($item['margin'] ?? 0); // Margin dari barang_data.margin
         $cart_key = 'main_' . $id_barang;
      }
      
      // Add to cart or update qty
      if (isset($_SESSION['sales_cart'][$cart_key])) {
         $_SESSION['sales_cart'][$cart_key]['qty'] += $qty;
      } else {
         $_SESSION['sales_cart'][$cart_key] = [
            'id_barang' => $id_barang,
            'nama' => $nama,
            'harga' => $harga,
            'qty' => $qty,
            'denom' => $denom,
            'margin' => $margin
         ];
      }
      
      ob_end_clean();
      session_write_close();
      header('Content-Type: application/json');
      echo json_encode(['status' => 'success', 'cart_count' => count($_SESSION['sales_cart'])]);
   }

   // Tambah barang utama ke cart
   public function add_main_to_cart()
   {
      ob_start(); // Capture any unexpected output
      
      $id_barang = $_POST['id_barang'] ?? 0;
      $qty = intval($_POST['qty'] ?? 1);
      
      if (!isset($_SESSION['sales_cart'])) {
         $_SESSION['sales_cart'] = [];
      }
      
      $item = $this->db(1)->get_where_row('barang_data', "id_barang = '$id_barang'");
      
      if (!$item) {
         ob_end_clean();
         header('Content-Type: application/json');
         echo json_encode(['status' => 'error', 'message' => 'Item tidak ditemukan']);
         return;
      }
      
      // Construct name
      $nama = $item['nama'] ?? '';
      if (empty($nama) && !empty($item['brand'])) {
          $nama = strtoupper(implode(' ', array_filter([$item['brand'] ?? '', $item['model'] ?? '', $item['description'] ?? ''])));
      }
      
      $harga = $item['price'] ?? $item['harga'] ?? 0;
      $margin = floatval($item['margin'] ?? 0); // Margin dari barang_data.margin
      $cart_key = 'main_' . $id_barang;
      
      if (isset($_SESSION['sales_cart'][$cart_key])) {
         $_SESSION['sales_cart'][$cart_key]['qty'] += $qty;
      } else {
         $_SESSION['sales_cart'][$cart_key] = [
            'id_barang' => $id_barang,
            'nama' => $nama,
            'harga' => $harga,
            'qty' => $qty,
            'denom' => 1, // Main item denom = 1
            'margin' => $margin
         ];
      }
      
      ob_end_clean();
      session_write_close();
      header('Content-Type: application/json');
      echo json_encode(['status' => 'success', 'cart_count' => count($_SESSION['sales_cart'])]);
   }

   // Load cart view
   public function cart()
   {
      $cart = $_SESSION['sales_cart'] ?? [];
      $this->view('sales/cart', ['cart' => $cart]);
   }

   // Remove from cart
   public function remove_from_cart()
   {
      $key = $_POST['key'] ?? '';
      if (isset($_SESSION['sales_cart'][$key])) {
         unset($_SESSION['sales_cart'][$key]);
      }
      
      session_write_close();
      header('Content-Type: application/json');
      echo json_encode(['status' => 'success', 'cart_count' => count($_SESSION['sales_cart'] ?? [])]);
   }

   // Clear cart
   public function clear_cart()
   {
      $_SESSION['sales_cart'] = [];
      
      session_write_close();
      header('Content-Type: application/json');
      echo json_encode(['status' => 'success']);
   }

   // Checkout - insert ke barang_mutasi
   public function checkout()
   {
      ob_start();
      
      $cart = $_SESSION['sales_cart'] ?? [];
      
      if (empty($cart)) {
         ob_end_clean();
         header('Content-Type: application/json');
         echo json_encode(['status' => 'error', 'message' => 'Keranjang kosong']);
         return;
      }
      
      // Generate ref: (tahun - 2024) + bulan + hari + jam + menit + detik + random digit
      $ref = (date('Y') - 2024) . date("mdHis") . rand(0, 9);
      
      $id_cabang = $_SESSION[URL::SESSID]['user']['id_cabang'] ?? 0;
      $id_user = $_SESSION[URL::SESSID]['user']['id_user'] ?? 0;
      
      $success_count = 0;
      $errors = [];
      
      foreach ($cart as $key => $item) {
         $data = [
            'type' => 1,
            'ref' => $ref,
            'id_barang' => $item['id_barang'],
            'source_id' => $id_cabang,
            'target_id' => 0,
            'denom' => $item['denom'],
            'price' => $item['harga'],
            'qty' => $item['qty'],
            'state' => 0,
            'id_user' => $id_user
         ];
         
         $insert = $this->db(1)->insert('barang_mutasi', $data);
         
         // insert() returns array with 'error' and 'errno'
         // errno = 0 means success
         if (isset($insert['errno']) && $insert['errno'] == 0) {
            $success_count++;
         } else {
            $errorMsg = "Gagal insert item: " . $item['nama'] . " - " . ($insert['error'] ?? 'Unknown error');
            $errors[] = $errorMsg;
            $this->model('Log')->write("[Sales::checkout] " . $errorMsg . " | Query: " . ($insert['query'] ?? 'N/A'));
         }
      }
      
      if ($success_count > 0) {
         // Clear cart after successful checkout
         $_SESSION['sales_cart'] = [];
         
         ob_end_clean();
         session_write_close();
         header('Content-Type: application/json');
         echo json_encode([
            'status' => 'success', 
            'message' => "Checkout berhasil! $success_count item disimpan.",
            'ref' => $ref
         ]);
      } else {
         ob_end_clean();
         header('Content-Type: application/json');
         echo json_encode([
            'status' => 'error', 
            'message' => 'Gagal checkout',
            'errors' => $errors
         ]);
      }
   }
}
