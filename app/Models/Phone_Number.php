<?php

class Phone_Number
{
    function to62($no)
    {
        if (substr($no, 0, 2) == '08') {
            return '62' . substr($no, 1);
        } else if (substr($no, 0, 1) == '8') {
            return '62' . $no;
        } else {
            return false;
        }
    }
}
