<?php

namespace app\billiard\controller;

use app\common\controller\Login as baseLogin;
use bee\Config;//使用Config类
use bee\Db;
use app\common\until\Logs;
use Overtrue\Pinyin\Pinyin;
use app\billiard\model\UserModel as User;
use app\billiard\model\Test as BB;


class Test extends baseLogin
{
    public function index()
    {
        echo "this is index/index";
    }

    public function composer()
    {
        // 小内存型，默认，将字典分片载入内存，适用于内存比较紧张的环境，优点：占用内存小，转换不如内存型快
        $pinyin = new Pinyin();
        // 内存型，将所有字典预先载入内存，适用于服务器内存空间较富余，优点：转换快
        // $pinyin = new Pinyin('Overtrue\Pinyin\MemoryFileDictLoader');
        // I/O型，不载入内存，将字典使用文件流打开逐行遍历并运用php5.5生成器(yield)特性分配单行内存，适用于虚拟机，内存限制比较严格环境。
        // 优点：非常微小内存消耗。缺点：转换慢，不如内存型转换快,php >= 5.5
        // $pinyin = new Pinyin('Overtrue\Pinyin\GeneratorFileDictLoader');

        $data = $pinyin->convert('带着希望去旅行，比到达终点更美好');
        print_r("带着希望去旅行，比到达终点更美好");
        // ["dai", "zhe", "xi", "wang", "qu", "lv", "xing", "bi", "dao", "da", "zhong", "dian", "geng", "mei", "hao"]
        print_r($data);
        $data2 = $pinyin->convert('带着希望去旅行，比到达终点更美好', PINYIN_UNICODE);
        // ["dài","zhe","xī","wàng","qù","lǚ","xíng","bǐ","dào","dá","zhōng","diǎn","gèng","měi","hǎo"]
        print_r($data2);
        $data3 = $pinyin->convert('带着希望去旅行，比到达终点更美好', PINYIN_ASCII);
        //["dai4","zhe","xi1","wang4","qu4","lv3","xing2","bi3","dao4","da2","zhong1","dian3","geng4","mei3","hao3"]
        print_r($data3);exit;
    }
    /**
     * 吹牛登陆(代码注释示例)
     *
     * @author by vonnwang <xinglingxueba@l63.com>
     * @date 2019/04/26
     */
    public  function  chuiniuLogin()
    {
        echo "吹牛";
    }


}
