<?php
// +----------------------------------------------------------------------
// | Demila [ Beautiful Digital Content Trading System ]
// +----------------------------------------------------------------------
// | Copyright (c) 2015 http://demila.org All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Email author@demila.org
// +----------------------------------------------------------------------

class execute
{
    var $start;
    var $pause_time;

    /*  开始计时器  */
    function timer($start = 0)
    {
        if($start) { $this->start(); }
    }

    /*  开始计时器  */
    function start()
    {
        $this->start = $this->get_time();
        $this->pause_time = 0;
    }

    /*  暂停计时器  */
    function pause()
    {
        $this->pause_time = $this->get_time();
    }

    /*  取消暂停计时器  */
    function unpause()
    {
        $this->start += ($this->get_time() - $this->pause_time);
        $this->pause_time = 0;
    }

    /*  获取计时器当前的值  */
    function get($decimals = 8)
    {
        return round(($this->get_time() - $this->start),$decimals);
    }

    /*  时间格式，秒  */
    function get_time()
    {
        list($usec,$sec) = explode(' ', microtime());
        return ((float)$usec + (float)$sec);
    }
}
?>