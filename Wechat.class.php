<?php
/**
 * wechat php test
 */


/**
 * 生成微信配置菜单
 */
function wechat_admin_page() {
    require 'wechat-options.php';
}

function add_wechat_admin_menu() {
    add_options_page('微信设置', '微信设置', 'administrator', 'wechat_admin', 'wechat_admin_page');
}
add_action('admin_menu', 'add_wechat_admin_menu');


/**
 * Class WechatApi
 * 微信接口类
 */
class WechatApi {

    // 当前公众号的 ID
    // option['WX_SELF_ID']
    // 如果是被动请求，会被覆写为请求的目标公众号
    protected $self_id;

    // 公众号原始 ID
    // option['WX_ORIGIN']
    protected $origin;

    // 关联用户的 open_id
    // 仅当被动请求的时候会被加入
    protected $open_id;

    // 公众号类型 10 订阅号(缺省) 11 认证订阅号 20 服务号 21 认证服务号
    // option['WX_ACCOUNT_TYPE']
    protected $account_type;

    // AppID | option['WX_APP_ID']
    // option['WX_APP_ID']
    public $app_id;

    // AppSecret
    // option['WX_APP_SECRET']
    protected $app_secret;

    // EncodingAESKey
    // option['WX_AES_KEY']
    protected $aes_key;

    // token
    // option['WX_TOKEN']
    protected $token;

    // access_token
    // option['WX_ACCESS_TOKEN']
    public $access_token;

    // access_token_expire
    // option['WX_ACCESS_TOKEN_EXPIRE']
    protected $access_token_expire;


    /**
     * 初始化对象
     * 如果是被动调用，则启动 bootstrap 过程自动响应消息，否则只生成对象
     */
    public function __construct() {

        // 0. 初始校验

        // 0.0. 获取设置参数
        $this->self_id = get_option('WX_SELF_ID', '');
        $this->origin = get_option('WX_ORIGIN', '');
        $this->account_type = get_option('WX_ACCOUNT_TYPE', '10');
        $this->app_id = get_option('WX_APP_ID', '');
        $this->app_secret = get_option('WX_APP_SECRET', '');
        $this->aes_key = get_option('WX_AES_KEY', '');
        $this->token = get_option('WX_TOKEN', '');
        $this->access_token = get_option('WX_ACCESS_TOKEN', '');
        $this->access_token_expire = get_option('WX_ACCESS_TOKEN_EXPIRE', 0);

        // 0.1. access_token 处理
        $this->_ensureAccessToken();


//        // 测试文本推送
//        $this->requestCustomSendText('oBJJ3uI5DGy1CrsOrpWG1ArxZFLk', '普通文本消息推送');


//        // 测试图文客服推送
//        $this->requestCustomSendNews('oBJJ3uI5DGy1CrsOrpWG1ArxZFLk', array(
//            array(
//                'title' => '标题：客服图文',
//                'description' => '摘要：摘摘宅宅',
//                'url' => 'http://www.huangwenchao.com.cn/2015/04/wechat-json-encoding.html',
//                'picurl' => 'http://www.huangwenchao.com.cn/wp-content/uploads/2013/11/fo-1038x576.jpg',
//            ),
//        ));


//        // 上传图片接口测试
//        $image = $this->requestMediaUploadImage('/var/www/fsga/wp-content/themes/fsga/images/thumbnail.jpg');
//        echo '<h3>上传图片接口测试</h3>';
//        echo '<pre>';
//        var_dump($image);
//        echo '</pre>';
//        // string(118) "{"type":"image","media_id":"dNzkNWaM5AUxKhk4tXh6pzWQLjq8scJFf-mNAA1gRyrYBjJJQCP8ZjYgQCZupHQW","created_at":1428593850}"


//        /**
//         * @param $articles array 文章列表
//         * array(
//         *      thumb_media_id: 缩略图 ID
//         *      author: 作者
//         *    * title: 标题
//         *      content_source_url: 原文链接
//         *    * content: 内容（支持 html）
//         *      digest: 'digest'
//         *      show_cover_pic: 是否显示封面，1为显示，0为不显示
//         * )
//         * @return array
//         */
//        $news = $this->requestMediaUploadNews(array(
//            array(
////                'thumb_media_id' => $image['media_id'],
//                'thumb_media_id' => 'hW-JmtRpAqRVcKcbdUVrNaEGXVpHEngN1bfDV3rU1ck',
//                'author' => '忧郁的小萱萱',
//                'title' => '他走路带着游泳圈',
//                'content_source_url' => 'http://www.easecloud.cn',
//                'content' => '<h1>眼睛眯成一条线</h1>',
//            )
//        ), false);
//
//        echo '<h3>上传图文接口测试</h3>';
//        echo '<pre>';
//        var_dump($news);
//        echo '</pre>';

// HiouNHxyifMcnmJhlESTBj2ISrZWtaut7ne4VM6-quw



//        // 群发推送
//        echo '<h3>群发推送接口测试</h3>';
//        $result = $this->requestMessageMassSendAllNews('HiouNHxyifMcnmJhlESTBj2ISrZWtaut7ne4VM6-quw');
//        echo '<pre>';
//        var_dump($result);
//        echo '</pre>';


        // 群发预览
//        $result = $this->requestMessageMassPreviewNews('oBJJ3uI5DGy1CrsOrpWG1ArxZFLk', $news['media_id']);
//        $result = $this->requestMessageMassPreviewNews('oBJJ3uI5DGy1CrsOrpWG1ArxZFLk', 'HiouNHxyifMcnmJhlESTBj2ISrZWtaut7ne4VM6-quw');
//        echo '<pre>';
//        var_dump($result);
//        echo '</pre>';


//        // 获取图文素材列表
//        $result = $this->requestMaterialBatchGetMaterial('news', 0, 20);
//        echo '<pre>';
//        var_dump($result);
//        echo '</pre>';

    }

    /**
     * 被动回复用户消息
     */
    public function responseMsgText($text) {
        $textTpl = trim("
            <xml>
                <ToUserName><![CDATA[%s]]></ToUserName>
                <FromUserName><![CDATA[%s]]></FromUserName>
                <CreateTime>%s</CreateTime>
                <MsgType><![CDATA[%s]]></MsgType>
                <Content><![CDATA[%s]]></Content>
                <FuncFlag>0</FuncFlag>
            </xml>
        ");
        $msgType = "text";
        exit(sprintf($textTpl, $this->open_id, $this->self_id, time(), $msgType, $text));
    }


    /**
     * 返回接入认证
     * http://mp.weixin.qq.com/wiki/4/2ccadaef44fe1e4b0322355c2312bfa8.html
     */
    protected function _responseValidation() {
        if($this->_checkSignature()){
            exit($_GET["echostr"]);
        }
    }

    /**
     * @return bool
     * @throws Exception
     * 返回验证消息正确性
     */
    protected function _checkSignature() {

        // 选项没有配置的话验证不通过
        if(!$this->token) return false;

        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];

        $tmpArr = array($this->token, $timestamp, $nonce);

        // use SORT_STRING rule
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );


        if( $tmpStr == $signature ){
            return true;
        }else{
            return false;
        }
    }

    /**
     * 启动消息响应处理过程
     */
    public function response() {

        // 若非调试模式（设置了 option['WX_DEBUG'] 配置值为 1）
        // 而且正确性验证失败，则直接失败退出。
        if(get_option('WX_DEBUG', false) != '1' || !$this->_checkSignature()) {
        }

        // 0.0. 接入验证
        if($_SERVER['REQUEST_METHOD'] == 'GET') {
            $this->_responseValidation();
        }

        // 0.1. 解析传入数据
        $post_str = $GLOBALS["HTTP_RAW_POST_DATA"];
        // 0.2. 如无数据传入，即短路之
        if (empty($post_str)) return;
        // 0.3. 解析输入对象
        /* libxml_disable_entity_loader is to prevent XML eXternal Entity Injection,
           the best way is to check the validity of xml by yourself */
        libxml_disable_entity_loader(true);
        $request = (array)simplexml_load_string($post_str, 'SimpleXMLElement', LIBXML_NOCDATA);
        // 0.4. 记录日志
        file_put_contents('/var/www/wechat.log', $post_str);

        /**
         * 被动接口
         */
        $msg_type = $request['MsgType'];
        $this->open_id = $request['FromUserName'];
        $this->self_id = $request['ToUserName'];
        if($msg_type == 'event') {

            // 1. 接收事件推送
            // http://mp.weixin.qq.com/wiki/2/5baf56ce4947d35003b86a9805634b1e.html
            $event = $request['Event'];

            if($event == 'subscribe') {
                // 1.1. 关注/扫描带参数二维码
                //<xml>
                //    <ToUserName><![CDATA[toUser]]></ToUserName>           【公众号】
                //    <FromUserName><![CDATA[FromUser]]></FromUserName>     【OpenID】
                //    <CreateTime>123456789</CreateTime>                    【消息创建时间】
                //    <MsgType><![CDATA[event]]></MsgType>                  【== event】
                //    <Event><![CDATA[subscribe]]></Event>                  【== subscribe】
                //    <!-- 扫描带参数二维码 BEGIN -->
                //    <EventKey><![CDATA[qrscene_123123]]></EventKey>       【事件KEY值，qrscene_为前缀，后面为二维码的参数值】
                //    <Ticket><![CDATA[TICKET]]></Ticket>                   【二维码的ticket，可用来换取二维码图片】
                //    <!-- 扫描带参数二维码 END -->
                //</xml>
                // TODO: 关注欢迎信息【待配置】
                $this->responseMsgText('/:share 亲！感谢关注“永安警长”微信，这里是禅城公安信息发布和贴心交流微信平台！');

            } elseif($event == 'unsubscribe') {
                // 1.2. 取消关注
                //<xml>
                //    <ToUserName><![CDATA[toUser]]></ToUserName>           【公众号】
                //    <FromUserName><![CDATA[FromUser]]></FromUserName>     【OpenID】
                //    <CreateTime>123456789</CreateTime>                    【消息创建时间】
                //    <MsgType><![CDATA[event]]></MsgType>                  【== event】
                //    <Event><![CDATA[subscribe]]></Event>                  【== unsubscribe】
                //</xml>
                // TODO: 未实现

            } elseif($event == 'SCAN') {
                // 1.3. 扫描带参数二维码（已关注）
                //<xml>
                //    <ToUserName><![CDATA[toUser]]></ToUserName>           【公众号】
                //    <FromUserName><![CDATA[FromUser]]></FromUserName>     【OpenID】
                //    <CreateTime>123456789</CreateTime>                    【消息创建时间】
                //    <MsgType><![CDATA[event]]></MsgType>                  【== event】
                //    <Event><![CDATA[subscribe]]></Event>                  【== SCAN】
                //    <EventKey><![CDATA[qrscene_123123]]></EventKey>       【事件KEY值，qrscene_为前缀，后面为二维码的参数值】
                //    <Ticket><![CDATA[TICKET]]></Ticket>                   【二维码的ticket，可用来换取二维码图片】
                //</xml>
                // TODO: 未实现

            } elseif($event == 'LOCATION') {
                // 1.4. 上报地理位置事件
                //<xml>
                //    <ToUserName><![CDATA[toUser]]></ToUserName>           【公众号】
                //    <FromUserName><![CDATA[FromUser]]></FromUserName>     【OpenID】
                //    <CreateTime>123456789</CreateTime>                    【消息创建时间】
                //    <MsgType><![CDATA[event]]></MsgType>                  【== event】
                //    <Event><![CDATA[subscribe]]></Event>                  【== LOCATION】
                //    <Latitude>23.137466</Latitude>
                //    <Longitude>113.352425</Longitude>
                //    <Precision>119.385040</Precision>
                //</xml>
                // TODO: 未实现

            } elseif($event == 'CLICK') {
                // 1.5. 点击菜单消息
                //<xml>
                //    <ToUserName><![CDATA[toUser]]></ToUserName>           【公众号】
                //    <FromUserName><![CDATA[FromUser]]></FromUserName>     【OpenID】
                //    <CreateTime>123456789</CreateTime>                    【消息创建时间】
                //    <MsgType><![CDATA[event]]></MsgType>                  【== event】
                //    <Event><![CDATA[subscribe]]></Event>                  【== CLICK】
                //    <EventKey><![CDATA[EVENTKEY]]></EventKey>             【菜单模拟关键字】
                //</xml>
                // TODO: 未实现

            } elseif($event == 'VIEW') {
                // 1.6. 点击菜单跳转链接
                //<xml>
                //    <ToUserName><![CDATA[toUser]]></ToUserName>           【公众号】
                //    <FromUserName><![CDATA[FromUser]]></FromUserName>     【OpenID】
                //    <CreateTime>123456789</CreateTime>                    【消息创建时间】
                //    <MsgType><![CDATA[event]]></MsgType>                  【== event】
                //    <Event><![CDATA[subscribe]]></Event>                  【== VIEW】
                //    <EventKey><![CDATA[www.qq.com]]></EventKey>           【菜单跳转 URL】
                //</xml>
                // TODO: 未实现

            }
        } elseif($msg_type == 'text') {
            // http://mp.weixin.qq.com/wiki/10/79502792eef98d6e0c6e1739da387346.html
            // 2.1. 普通文本消息
//            <xml>
//                <ToUserName><![CDATA[toUser]]></ToUserName>           【公众号】
//                <FromUserName><![CDATA[FromUser]]></FromUserName>     【OpenID】
//                <CreateTime>123456789</CreateTime>                    【消息创建时间】
//                <MsgType><![CDATA[text]]></MsgType>
//                <Content><![CDATA[text_content]]></Event>             【文本内容】
//                <MsgId><![CDATA[www.qq.com]]></EventKey>
//            </xml>
            // TODO: 默认回复与关键字匹配回复【待配置】
            if($request['Content'] == '//!技术支持') {
                $this->responseMsgText('技术支持:佛山市逸云计算机科技有限公司--http://www.easecloud.cn/');
            }

            if($request['Content'] == '<!?>我是管理员<?!>') {
                update_option('WX_ADMIN_OPEN_ID', $request['FromUserName']);
                $this->responseMsgText('消息通知开启');
            }

            if($request['Content'] == '<!?>我不是管理员<?!>') {
                update_option('WX_ADMIN_OPEN_ID', '');
                $this->responseMsgText('消息通知关闭');
            }

            $this->responseMsgText('/:share 亲！感谢关注“永安警长”微信，这里是禅城公安信息发布和贴心交流微信平台！');


        } elseif($msg_type == 'image') {
            // http://mp.weixin.qq.com/wiki/10/79502792eef98d6e0c6e1739da387346.html
            // 2.2. 图片消息
            //<xml>
            //    <ToUserName><![CDATA[toUser]]></ToUserName>           【公众号】
            //    <FromUserName><![CDATA[FromUser]]></FromUserName>     【OpenID】
            //    <CreateTime>123456789</CreateTime>                    【消息创建时间】
            //    <MsgType><![CDATA[image]]></MsgType>
            //    <PicUrl><![CDATA[this is a url]]></PicUrl>
            //    <MediaId><![CDATA[media_id]]></MediaId>
            //    <MsgId><![CDATA[www.qq.com]]></EventKey>
            //</xml>
            // TODO: 未实现

        } elseif($msg_type == 'video') {
            // http://mp.weixin.qq.com/wiki/10/79502792eef98d6e0c6e1739da387346.html
            // 2.3. 语音消息
            //<xml>
            //    <ToUserName><![CDATA[toUser]]></ToUserName>           【公众号】
            //    <FromUserName><![CDATA[FromUser]]></FromUserName>     【OpenID】
            //    <CreateTime>123456789</CreateTime>                    【消息创建时间】
            //    <MsgType><![CDATA[voice]]></MsgType>
            //    <MediaId><![CDATA[media_id]]></MediaId>
            //    <Format><![CDATA[Format]]></Format>                   【amr/speex】
            //    <MsgId><![CDATA[www.qq.com]]></EventKey>
            //</xml>
            // TODO: 未实现

        } elseif($msg_type == 'video') {
            // http://mp.weixin.qq.com/wiki/10/79502792eef98d6e0c6e1739da387346.html
            // 2.4. 视频消息
            //<xml>
            //    <ToUserName><![CDATA[toUser]]></ToUserName>           【公众号】
            //    <FromUserName><![CDATA[FromUser]]></FromUserName>     【OpenID】
            //    <CreateTime>123456789</CreateTime>                    【消息创建时间】
            //    <MsgType><![CDATA[video]]></MsgType>
            //    <MediaId><![CDATA[media_id]]></MediaId>
            //    <ThumbMediaId><![CDATA[thumb_media_id]]></ThumbMediaId>
            //    <MsgId><![CDATA[www.qq.com]]></EventKey>
            //</xml>
            // TODO: 未实现

        } elseif($msg_type == 'shortvideo') {
            // http://mp.weixin.qq.com/wiki/10/79502792eef98d6e0c6e1739da387346.html
            // 2.4. 视频消息
            //<xml>
            //    <ToUserName><![CDATA[toUser]]></ToUserName>           【公众号】
            //    <FromUserName><![CDATA[FromUser]]></FromUserName>     【OpenID】
            //    <CreateTime>123456789</CreateTime>                    【消息创建时间】
            //    <MsgType><![CDATA[shortvideo]]></MsgType>
            //    <MediaId><![CDATA[media_id]]></MediaId>
            //    <ThumbMediaId><![CDATA[thumb_media_id]]></ThumbMediaId>
            //    <MsgId><![CDATA[www.qq.com]]></EventKey>
            //</xml>
            // TODO: 未实现

        } elseif($msg_type == 'location') {
            // http://mp.weixin.qq.com/wiki/10/79502792eef98d6e0c6e1739da387346.html
            // 2.5. 地理位置消息
            //<xml>
            //    <ToUserName><![CDATA[toUser]]></ToUserName>           【公众号】
            //    <FromUserName><![CDATA[FromUser]]></FromUserName>     【OpenID】
            //    <CreateTime>123456789</CreateTime>                    【消息创建时间】
            //    <MsgType><![CDATA[shortvideo]]></MsgType>
            //    <Location_X>23.134521</Location_X>
            //    <Location_Y>113.358803</Location_Y>
            //    <Scale>20</Scale>
            //    <Label><![CDATA[位置信息]]></Label>
            //    <MsgId><![CDATA[www.qq.com]]></EventKey>
            //</xml>
            // TODO: 未实现

        } elseif($msg_type == 'link') {
            // http://mp.weixin.qq.com/wiki/10/79502792eef98d6e0c6e1739da387346.html
            // 2.6. 链接消息
            //<xml>
            //    <ToUserName><![CDATA[toUser]]></ToUserName>           【公众号】
            //    <FromUserName><![CDATA[FromUser]]></FromUserName>     【OpenID】
            //    <CreateTime>123456789</CreateTime>                    【消息创建时间】
            //    <MsgType><![CDATA[shortvideo]]></MsgType>
            //    <Title><![CDATA[公众平台官网链接]]></Title>
            //    <Description><![CDATA[公众平台官网链接]]></Description>
            //    <Url><![CDATA[url]]></Url>
            //    <MsgId><![CDATA[www.qq.com]]></EventKey>
            //</xml>
            // TODO: 未实现
        }

    }

    /**
     * 确认 access_token 可用
     */
    protected function _ensureAccessToken() {
        return time() < $this->access_token_expire ?
            $this->access_token : $this->_updateAccessToken();
    }

    /**
     * 更新 access_token
     */
    protected function _updateAccessToken() {
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$this->app_id}&secret={$this->app_secret}";
        $resp = file_get_contents($url);
        $data = json_decode($resp, true);
        // 获取失败
        if(!isset($data['access_token'])) return false;
        // 获取成功保存状态
        $this->access_token = $data['access_token'];
        $this->access_token_expire = time() + intval($data['expires_in']);
        update_option('WX_ACCESS_TOKEN', $this->access_token);
        update_option('WX_ACCESS_TOKEN_EXPIRE', $this->access_token_expire);
        // 成功返回 access_token
        return $this->access_token;
    }

    /**
     * 获取jssdk权限验证的签名
     */
    public function getSignPackage() {
        $jsapiTicket = $this->getJsApiTicket();

        // 注意 URL 一定要动态获取，不能 hardcode.
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $url = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

        $timestamp = time();
        $nonceStr = $this->createNonceStr();

        // 这里参数的顺序要按照 key 值 ASCII 码升序排序
        $string = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";

        $signature = sha1($string);

        $signPackage = array(
            "appId"     => $this->app_id,
            "nonceStr"  => $nonceStr,
            "timestamp" => $timestamp,
            "url"       => $url,
            "signature" => $signature,
            "rawString" => $string
        );
        return $signPackage;
    }

    private function createNonceStr($length = 16) {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }

    /**
     * 获取jsapi_ticket
     */
    private function getJsApiTicket() {
        // jsapi_ticket 应该全局存储与更新，以下代码以写入到文件中做示例
        $data = json_decode(file_get_contents("jsapi_ticket.json"));
        if ($data->expire_time < time()) {
            $accessToken = $this->access_token;
            // 如果是企业号用以下 URL 获取 ticket
//             $url = "https://qyapi.weixin.qq.com/cgi-bin/get_jsapi_ticket?access_token=$accessToken";
            $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=$accessToken";
            $res = json_decode($this->httpGet($url));
            $ticket = $res->ticket;
            if ($ticket) {
                $data->expire_time = time() + 7000;
                $data->jsapi_ticket = $ticket;
                $fp = fopen("jsapi_ticket.json", "w");
                fwrite($fp, json_encode($data));
                fclose($fp);
            }
        } else {
            $ticket = $data->jsapi_ticket;
        }

        return $ticket;
    }

    private function httpGet($url) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 500);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_URL, $url);

        $res = curl_exec($curl);
        curl_close($curl);

        return $res;
    }

    /**
     * 发送客服消息（文本）
     */
    public function requestCustomSendText($touser, $content, $kf_account=null) {
        $obj = array(
            'touser' => $touser,
            'msgtype' => 'text',
            'text' => array('content' => $content),
        );
        // 如果指定了客服编号，则加入之
        if($kf_account) $obj['customservice'] = array('kf_account' => $kf_account);
        // 发送接口请求
        $this->_apiRequest('https://api.weixin.qq.com/cgi-bin/message/custom/send', $obj);
    }

    /**
     * 获取现在管理员的openid
     */
    public function getAdminOpenID() {
        return self::$admin_open_id;
    }

    /**
     * 发送客服消息（图文）
     * @param $touser string
     * @param $news array 图文列表
     * array(
     *      title
     *      description
     *      url
     *      picurl
     * )
     * @param null $kf_account
     */
    public function requestCustomSendNews($touser, $news, $kf_account=null) {
        $obj = array(
            'touser' => $touser,
            'msgtype' => 'news',
            'news' => array('articles' => $news),
        );
        // 如果指定了客服编号，则加入之
        if($kf_account) $obj['customservice'] = array('kf_account' => $kf_account);
        // 发送接口请求
        $this->_apiRequest('https://api.weixin.qq.com/cgi-bin/message/custom/send', $obj);
    }
//
//    /**
//     * 发送客服消息（图文）
//     */
//    public function requestCustomSendText($touser, $content, $kf_account=null) {
//        $obj = array(
//            'touser' => $touser,
//            'msgtype' => 'text',
//            'text' => array('content' => $content),
//        );
//        // 如果指定了客服编号，则加入之
//        if($kf_account) $obj['kf_account'] = $kf_account;
//        // 发送接口请求
//        $this->_apiRequest('https://api.weixin.qq.com/cgi-bin/message/custom/send', $obj);
//    }

    /**
     * 获取素材列表
     * @param string $type 素材的类型，图片（image）、视频（video）、语音 （voice）、图文（news）
     * @param int $offset 从全部素材的该偏移位置开始返回，0表示从第一个素材 返回
     * @param int $count 返回素材的数量，取值在1到20之间
     * @return array 返回相应的素材列表
     * array(
     *      total_count 全部总数
     *      item_count 当页总数
     *      items: array()
     * )
     */
    public function requestMaterialBatchGetMaterial($type='news', $offset=0, $count=15) {
        $url = 'https://api.weixin.qq.com/cgi-bin/material/batchget_material';
        $obj = array(
            'type' => $type,
            'offset' => $offset,
            'count' => $count,
        );
        return $this->_apiRequest($url, $obj);
    }

    /**
     * 获取素材总数
     */
    public function requestMaterialBatchGetMaterialCount() {
        $url = 'https://api.weixin.qq.com/cgi-bin/material/get_materialcount';
        $obj = array(
        );
        return $this->_apiRequest($url, $obj);
    }

    /**
     * 获取用户
     */
    public function requestGetUser() {
        $url = 'https://api.weixin.qq.com/cgi-bin/user/get';
        $obj = array(
        );
        return $this->_apiRequest($url, $obj);
    }

    /**
     * 群发图文预览
     */
    public function requestMessageMassPreviewNews($touser, $media_id) {
        $url = 'https://api.weixin.qq.com/cgi-bin/message/mass/preview';
        $obj = array(
            'touser' => $touser,
            'mpnews' => array(
                'media_id' => $media_id,
            ),
            'msgtype' => 'mpnews',
        );
        return $this->_apiRequest($url, $obj);
    }

    /**
     * 群发图文消息（openid版本）
     */
    public function requestMessageMassSendNews($touser, $media_id) {
        $url = 'https://api.weixin.qq.com/cgi-bin/message/mass/send';
        $obj = array(
            'touser' => $touser,
            'mpnews' => array(
                'media_id' => $media_id,
            ),
            'msgtype' => 'mpnews',
        );
        return $this->_apiRequest($url, $obj);
    }

    /**
     * 群发图文
     */
    public function requestMessageMassSendAllNews($media_id) {
        $url = 'https://api.weixin.qq.com/cgi-bin/message/mass/sendall';
        $obj = array(
            'filter' => array(
                'is_to_all' => true,
            ),
            'mpnews' => array(
                'media_id' => $media_id,
            ),
            'msgtype' => 'mpnews',
        );
//        echo '<pre>SEND ALL REQUEST';
//        var_dump($obj);
//        echo '</pre>';
        return $this->_apiRequest($url, $obj);
    }

    /**
     * 上传图文素材
     * @param array $articles 文章列表
     * 传入对象应为二维数组形式
     * 单个文章的结构：
     * array(
     *    * thumb_media_id: 缩略图 ID
     *      author: 作者
     *    * title: 标题
     *      content_source_url: 原文链接
     *    * content: 内容（支持 html）
     *      digest: 图文消息的摘要
     *      show_cover_pic: 是否显示封面，1为显示，0为不显示
     * )
     * @param boolean $temp 是否临时素材
     * @return array
     */
    public function requestMediaUploadNews($articles, $temp=true) {
        $url = $temp ? 'https://api.weixin.qq.com/cgi-bin/media/uploadnews'
            : 'https://api.weixin.qq.com/cgi-bin/material/add_news';
        $obj = array(
            'articles' => $articles
        );
        return $this->_apiRequest($url, $obj);
    }

    /**
     * 更新图文素材
     * @param array $articles 文章列表
     * 传入对象应为二维数组形式
     * @return array
     */
    public function requestMediaUpdateNews($media_id, $articles) {
        $url = 'https://api.weixin.qq.com/cgi-bin/material/update_news';
        $obj = array(
            'media_id' => $media_id,
            'index' => 0,
            'articles' => $articles
        );
        return $this->_apiRequest($url, $obj);
    }

    /**
     * 删除图文素材
     * @param array $articles 文章列表
     * 传入对象应为二维数组形式
     * @return array
     */
    public function requestMediaDeleteNews($media_id) {
        $url = 'https://api.weixin.qq.com/cgi-bin/material/del_material';
        $obj = array(
            'media_id' => $media_id,
        );
        return $this->_apiRequest($url, $obj);
    }

    /**
     * 上传图片
     * @param $file_image string 图片文件的物理路径
     * @param $temp bool 是临时素材(true) 还是永久素材 (false)
     * @return array
     *      type: image
     *      media_id: id
     *      create_at: int timestamp
     */
    public function requestMediaUploadImage($file_image, $temp=true) {
        if($temp) {
            $url = 'https://api.weixin.qq.com/cgi-bin/media/upload?type=image';
            $obj = array('image' => "@$file_image");
        } else {
            $url = 'http://api.weixin.qq.com/cgi-bin/material/add_material';
            $obj = array('media' => "@$file_image", 'type' => 'image');
        }
        return $this->_apiRequest($url, $obj, false);
    }

    /**
     * 使用 curl 请求一个接口并且返回响应的对象
     */
    protected function _apiRequest($url, $obj, $encode='json', $method='POST') {

        // 预处理数据
        if($encode == 'json') {
            $post_data = json_encode_with_unicode($obj);
        } else {
            $post_data = $obj;
        }

        // 加入 access_token
        $url .= (strpos($url, '?') !== false ? '&' : '?')."access_token={$this->access_token}";
//        var_dump($obj);
//        file_put_contents('/var/www/wechat.go.log', print_r($obj, true));
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        // 执行请求
        $data = curl_exec($ch);
//        var_dump($data);
        return json_decode($data, true);

    }
}




/**
 * 对 array 的所有元素使用 $function 过滤
 * @param $array
 * @param $function
 * @param bool $apply_to_keys_also
 */
function array_apply(&$array, $function, $apply_to_keys_also = false) {
    static $recursive_counter = 0;
    if (++$recursive_counter > 1000) {
        die('possible deep recursion attack');
    }
    foreach ($array as $key => $value) {
        if (is_array($value)) {
            array_apply($array[$key], $function, $apply_to_keys_also);
        } else {
            $array[$key] = $function($value);
        }

        if ($apply_to_keys_also && is_string($key)) {
            $new_key = $function($key);
            if ($new_key != $key) {
                $array[$new_key] = $array[$key];
                unset($array[$key]);
            }
        }
    }
    $recursive_counter--;
}


function wechat_field_filter($value) {
    return is_string($value) ? urlencode(addslashes($value)) : $value;
}

/**
 * json_encode 一个 array，保持其中的 unicode 内容
 * @param $array_to_encode
 * @return string
 */
function json_encode_with_unicode($array_to_encode) {
//    exit(print_r(urldecode(json_encode($array_to_encode))));
    array_apply($array_to_encode, 'wechat_field_filter');
    return urldecode(json_encode($array_to_encode));
}