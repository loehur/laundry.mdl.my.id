<?php

class Setting extends Controller
{
   public $page = __CLASS__;

   public function __construct()
   {
      $this->session_cek();
      $this->data();
      $this->v_content = $this->page . "/content";
      $this->v_viewer = $this->page . "/viewer";
   }

   public function index()
   {
      $this->view("layout", [
         "content" => $this->v_content,
         "data_operasi" => ['title' => "Setting"]
      ]);

      $this->viewer();
   }

   public function viewer()
   {
      $this->view($this->v_viewer, ["page" => $this->page]);
   }

   public function content()
   {
      $this->view($this->v_content);
   }

   public function updateCell()
   {
      $value = $_POST['value'];
      $mode = $_POST['mode'];

      $whereCount = $this->wLaundry . " AND " . $this->wCabang . " AND " . $mode . " >= 0";
      $dataCount = $this->model('M_DB_1')->count_where('setting', $whereCount);
      if ($dataCount >= 1) {
         $set = $mode . " = '" . $value . "'";
         $where = $this->wLaundry . " AND " . $this->wCabang;
         $query = $this->model('M_DB_1')->update("setting", $set, $where);
         if ($query) {
            $this->dataSynchrone();
         }
      } else {
         $cols = "id_laundry, id_cabang, print_ms";
         $vals = $this->id_laundry . "," . $this->id_cabang . "," . $value;
         $this->model('M_DB_1')->insertCols('setting', $cols, $vals);
         $this->dataSynchrone();
      }
   }
}
