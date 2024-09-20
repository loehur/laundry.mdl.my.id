<?php

class Enc
{
    function username($hp)
    {
        return md5(md5(md5($hp + 8117686252)));
    }
}
