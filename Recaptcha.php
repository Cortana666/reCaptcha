<?php
class Recaptcha
{
    /**
     * 初始化配置
     *
     * @author yangjian
     * @date   2021-03-25
     * @param [type] $config
     */
    public function __construct($config) {
        $this->sGRecaptchaResponse = $config['g-recaptcha-response'] ?? '';
        $this->sSecret = $config['secret'] ?? '';
    }

    /**
     * 响应参数
     *
     * @var string
     */
    public $sGRecaptchaResponse = "";

    /**
     * 通信密钥
     *
     * @var string
     */
    public $sSecret = "";

    /**
     * 验证接口
     *
     * @var string
     */
    public $sUrl = "https://www.recaptcha.net/recaptcha/api/siteverify";

    /**
     * 错误代码
     *
     * @var array
     */
    private $aErrorCode = array(
        "missing-input-secret" => "秘密参数丢失。",
        "invalid-input-secret" => "secret参数无效或格式错误。",
        "missing-input-response" => "缺少响应参数。",
        "invalid-input-response" => "响应参数无效或格式错误。",
        "bad-request" => "该请求无效或格式错误。",
        "timeout-or-duplicate" => "响应不再有效：太旧或以前使用过。",
    );

    /**
     * curl请求体
     *
     * @author yangjian
     * @date   2021-03-25
     * @return void
     */
    private function curlPost() {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $this->sUrl);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, array(
            'secret'=>$this->sSecret,
            'response'=>$this->sGRecaptchaResponse,
        ));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $res = curl_exec($curl);
        curl_close($curl);
        return $res;
    }

    /**
     * 验证方法
     *
     * @author yangjian
     * @date   2021-03-25
     * @return void
     */
    public function check() {
        $res = json_decode($this->curlPost(), true);
        return $this->getMessage($res);
    }

    /**
     * 返回验证信息
     *
     * @author yangjian
     * @date   2021-03-25
     * @param [type] $aReceive
     * @return void
     */
    private function getMessage($aReceive) {
        $aReturn['status'] = 2;
        $aReturn['message'] = "";
        if ($aReceive['success']) {
            if (abs(strtotime($aReceive['challenge_ts']) - time()) > 60) {
                $aReturn['message'] = "验证超时";
            } else {
                $aReturn['status'] = 1;
            }
        } else {
            foreach ($aReceive['error-codes'] as $error_code) {
                if (!empty($this->aErrorCode[$error_code])) {
                    $aReturn['message'][] = $this->$aErrorCode[$error_code];
                } else {
                    $aReturn['message'][] = "未知错误。";
                }
            }
            $aReturn['message'] = implode('', $aReturn['message']);
        }

        return $aReturn;
    }
}