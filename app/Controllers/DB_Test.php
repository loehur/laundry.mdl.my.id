<?php

class DB_Test extends Controller
{
    public function index()
    {
        $this->model('M_DB_1')->test();
    }

    function get($table)
    {
        echo "<pre>";
        print_r($this->model("M_DB_1")->get($table));
        echo "</pre>";
    }
}
