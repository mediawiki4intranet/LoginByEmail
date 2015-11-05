<?php

class LoginByEmail_UserloginTemplate extends UserloginTemplate
{
    function msg($str)
    {
        if ($str == 'yourname' || $str == 'userlogin-yourname')
        {
            echo wfMessage('loginbyemail-yourname')->parse();
        }
        else
        {
            parent::msg($str);
        }
    }
}
