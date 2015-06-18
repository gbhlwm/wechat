<?php
/**
 * 微信插件配置页
 */

$title = '微信设置';

$form_options = array(

    array(
        'label' => '微信号',
        'key' => 'WX_SELF_ID',
        'description' => '公众号设置 => 设置 => 微信号'
    ),
    array(
        'label' => '原始ID',
        'key' => 'WX_ORIGIN',
        'description' => '公众号设置 => 设置 => 原始ID'
    ),
    array(
        'label' => '公众号类型',
        'key' => 'WX_ACCOUNT_TYPE',
        'description' => '10: 订阅号, 11: 认证订阅号, 20: 服务号, 21: 认证服务号'
    ),
    array(
        'label' => 'AppID',
        'key' => 'WX_APP_ID',
        'description' => '开发者中心 => AppID'
    ),
    array(
        'label' => 'AppSecret',
        'key' => 'WX_APP_SECRET',
        'description' => '开发者中心 => AppSecret'
    ),
    array(
        'label' => 'EncodingAESKey',
        'key' => 'WX_AES_KEY',
        'description' => '开发者中心 => 服务器配置 => EncodingAESKey'
    ),
    array(
        'label' => 'Token',
        'key' => 'WX_TOKEN',
        'description' => '开发者中心 => 服务器配置 => Token'
    ),
    array(
        'label' => '调试模式',
        'key' => 'WX_DEBUG',
        'description' => '1 - 启用调试模式'
    ),

);

$wechat = new WechatApi();

//var_dump($wechat);

?>

<div class="wrap">

<h2><?php echo esc_html( $title ); ?>【测试中请勿操作】</h2>

<?php if($_SERVER['REQUEST_METHOD'] == 'POST') {

    $current_user = wp_get_current_user();
    if(is_user_logged_in() && $current_user->roles[0] === 'administrator') {
        foreach($form_options as $opt) {
            $key = $opt['key'];
            if(isset($_POST[$key])) {
                update_option($key, addslashes($_POST[$key]));
            }
        }
    }?>
    <div id="message" class="updated"><p>设置成功。</p></div>

<?php } ?>

    <form method="post" action="" novalidate="novalidate">

    <table class="form-table">
        <?php foreach($form_options as $opt) {
            $label = $opt['label'];
            $key = $opt['key'];
            $description = isset($opt['description']) ? $opt['description'] : false;
            $defaults = isset($opt['defaults']) ? $opt['defaults'] : '';
            $value = get_option($key, $defaults);
            ?>
        <tr>
            <th scope="row"><label for="<?php echo $key; ?>"><?php echo $label; ?></label></th>
            <td>
                <input name="<?php echo $key; ?>" type="text" id="<?php echo $key; ?>"
                       value="<?php echo $value ?>" class="regular-text" />
                <?php if($description !== false) { ?>
                <p class="description"><?php echo $description; ?></p>
                <?php }?>
            </td>
        </tr>
        <?php }?>
    </table>

        <?php submit_button(); ?>
    </form>


</div>
