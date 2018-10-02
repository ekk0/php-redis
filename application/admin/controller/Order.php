<?php
namespace app\admin\controller;
use app\admin\model\Good;
use app\admin\model\Order as OrderModel;
use app\admin\model\Redis;
use app\admin\model\User;
use app\admin\model\Good as GoodModel;
use think\db;
use think\log;
class Order extends \think\Controller{

    public $goodTotal = 1000;
    public $good_id = 1;

    /**
     * 秒杀请求接口
     * http://kill.tp5.com/admin/order/sekillApi
     */
    public function sekillApi(){

        $good_total_redis =  OrderModel::getGoodTotal();
        if ($good_total_redis <= 0){
           echo "抢购活动已经完毕了";
           exit;
        }
        //模拟用户是否登录
        $uid = User::getUserId();
        if (!$uid){
            echo "请登录!";
            exit;
        }else{
            //这里可以使用redis lpush 尽量减少数据库操作
            if(User::getUserOrder($uid)){
                echo "你已经购买了!,记录重复购买用户日记";
                file_put_contents("repeat.txt", "$uid.\n", FILE_APPEND);
                exit;
            }
        }
        //抢到了 -- 减去库存
        if(OrderModel::setDecRedisLlen() ===false){
            echo '抢购活动已经完毕了';
            exit;
        }

        //生成订单
        $order_id = OrderModel::buildOrderId($uid);
        //插入订单
        $order_status =  OrderModel::addOrder($uid,$order_id,$this->good_id);
        if ($order_status){
            //mysql库存;
            if (GoodModel::setDecQueryCounts()){
                echo "抢购成功";
                exit;
            }
        }
        echo "抢购失败日记";
        file_put_contents("error.txt", "$uid.\n", FILE_APPEND);
        exit;
    }

    /**
     * 增加redis 队列 和 随便设置商品数量
     */
    public function addRedisQueue(){

        Redis::getConn()->lTrim(Redis::$good_total_field,1,0);
        for ($i = 1;$i <= $this->goodTotal;$i++){
            Redis::getConn()->lPush(Redis::$good_total_field,1);
        }
        db('Good')->where(['id'=>$this->good_id])->update(['counts'=>$this->goodTotal]);
        print_r(Redis::getConn()->lLen(Redis::$good_total_field));

    }

    public function index()
    {
        return '<style type="text/css">*{ padding: 0; margin: 0; } .think_default_text{ padding: 4px 48px;} a{color:#2E5CD5;cursor: pointer;text-decoration: none} a:hover{text-decoration:underline; } body{ background: #fff; font-family: "Century Gothic","Microsoft yahei"; color: #333;font-size:18px} h1{ font-size: 100px; font-weight: normal; margin-bottom: 12px; } p{ line-height: 1.6em; font-size: 42px }</style><div style="padding: 24px 48px;"> <h1>:)</h1><p> ThinkPHP V5<br/><span style="font-size:30px">十年磨一剑 - 为API开发设计的高性能框架</span></p><span style="font-size:22px;">[ V5.0 版本由 <a href="http://www.qiniu.com" target="qiniu">七牛云</a> 独家赞助发布 ]</span></div><script type="text/javascript" src="https://tajs.qq.com/stats?sId=9347272" charset="UTF-8"></script><script type="text/javascript" src="https://e.topthink.com/Public/static/client.js"></script><think id="ad_bd568ce7058a1091"></think>';
    }

    /**
     * swoole 客户端
     * @return string
     */
    public function send(){
        $client = new \swoole_client(SWOOLE_SOCK_TCP, SWOOLE_SOCK_SYNC);
        $ret = $client->connect("192.168.1.120", 9500);

        if(empty($ret)){
            echo 'error!connect to swoole_server failed';
        } else {
            //这里只是简单的实现了发送的内容
            $send = $client->send('我是客户端,我发送了消息~__');
            //等待服务端返回诗句
            if ($send){
                //recv返回客户端消息,应该等服务端返回数据才执行,否则会报错
                $recv = $client->recv();
            }else{
                $recv = "服务端返回失败!";
            }

            return $send."--".$recv;
        }
    }
}
