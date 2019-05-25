<?php

namespace addons\recharge\model;

use addons\epay\library\Service;
use app\common\library\Auth;
use app\common\model\User;
use fast\Random;
use think\Exception;
use think\Model;

/**
 * 充值订单模型
 */
class Order Extends Model
{

    // 表名
    protected $name = 'recharge_order';
    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = '';
    // 追加属性
    protected $append = [
    ];

    /**
     * 发起订单支付
     * @param float $money
     * @param string $paytype
     * @throws Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function submitOrder($money, $paytype = 'wechat')
    {
        $auth = Auth::instance();
        $user_id = $auth->isLogin() ? $auth->id : 0;
        $order = self::where('user_id', $user_id)
            ->where('amount', $money)
            ->where('status', 'created')
            ->order('id', 'desc')
            ->find();
        $request = \think\Request::instance();
        if (!$order) {
            $orderid = Random::uuid();
            $data = [
                'orderid'   => $orderid,
                'user_id'   => $user_id,
                'amount'    => $money,
                'payamount' => 0,
                'paytype'   => $paytype,
                'ip'        => $request->ip(),
                'useragent' => $request->server('HTTP_USER_AGENT'),
                'status'    => 'created'
            ];
            $order = self::create($data);
        }

        $epay = get_addon_info('epay');
        if ($epay && $epay['state']) {
            $notifyurl = $request->root(true) . '/index/recharge/epay/type/notify/paytype/' . $paytype;
            $returnurl = $request->root(true) . '/index/recharge/epay/type/return/paytype/' . $paytype;

            $config = [
                'notify_url' => $notifyurl,
                'return_url' => $returnurl
            ];
            //创建支付对象
            $pay = Service::createPay($paytype, $config);

            if ($paytype == 'alipay') {
                //支付宝支付,请根据你的需求,仅选择你所需要的即可
                $order = [
                    'out_trade_no' => $order->orderid,//你的订单号
                    'total_amount' => $order->amount,//单位元
                    'subject'      => "充值",
                ];

                $pay->web($order)->send();
            } else {
                //微信支付,请根据你的需求,仅选择你所需要的即可
                $order = [
                    'out_trade_no' => $order->orderid,//你的订单号
                    'body'         => "充值",
                    'total_fee'    => $order->amount * 100, //单位分
                ];

                $pay->wap($order)->send();
            }
        } else {
            $result = \think\Hook::listen('recharge_order_submit', $order);
            if (!$result) {
                throw new Exception("请先在后台安装配置企业支付插件");
            }
        }
    }

    /**
     * 订单结算
     * @param int $orderid
     * @param string $payamount
     * @param string $memo
     * @return bool
     * @throws \think\exception\DbException
     */
    public static function settle($orderid, $payamount = null, $memo = '')
    {
        $order = self::getByOrderid($orderid);
        if (!$order) {
            return false;
        }
        if ($order['status'] != 'paid') {
            $order->payamount = $payamount ? $payamount : $order->amount;
            $order->paytime = time();
            $order->status = 'paid';
            $order->memo = $memo;
            $order->save();

            // 最新版本可直接使用User::money($order->user_id, $order->amount, '充值');来更新
            // 更新会员余额
            $user = User::get($order->user_id);
            if ($user) {
                $before = $user->money;
                $after = $user->money + $order->amount;
                //更新会员信息
                $user->save(['money' => $after]);
                //写入日志
                MoneyLog::create(['user_id' => $order->user_id, 'money' => $order->amount, 'before' => $before, 'after' => $after, 'memo' => '充值']);
            }
        }
        return true;
    }
}
