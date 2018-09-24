<?php
/**
 * Author: ekk0
 * Date: 2018/9/24 18:03
 */
namespace app\admin\model;

class Redis{
    static  public  $good_total_field = 'good_total_number';
    private static $_instance = null;

    private function __construct(){
        self::$_instance = new \Redis();
        $config = config('redis');
        self::$_instance->connect($config['host'],$config['port']);

        if(isset($config['password'])){
            self::$_instance->auth($config['password']);
        }
    }

    //获取静态实例
    static public  function getConn(){
            if(!self::$_instance){
                new self;
            }
            return self::$_instance;
        }

    /*
     * 禁止clone
     */
    private function __clone(){}
}