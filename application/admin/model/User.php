<?php
/**
 * Author: ekk0
 * Date: 2018/9/24 17:56
 */
namespace app\admin\model;

class User extends \think\Model{

    // 设置当前模型对应的完整数据表名称
    protected $table = 'user';

    /**
     * 模拟用户,数据越小测试用户重复购买越大
     * @return int
     */
    static public function getUserId(){
        $uid = mt_rand(1000000,9999999);
        return $uid;
    }

    /**
     * 判断用户是否已经购买
     */
    static public function getUserOrder($uid){
        $user_order_find = Order::field('uid')->where(['uid'=>$uid])->find();
        if($user_order_find){
            return true;
        }else{
            return false;
        }
    }

}