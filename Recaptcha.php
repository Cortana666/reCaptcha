<?php
    class Recaptcha
    {
        public function _construct() {

        }

        public $sGRecaptchaResponse = "";
        public $sSecret = "";
        public $sUrl = "https://www.recaptcha.net/recaptcha/api/siteverify";

        private $aErrorCode = array(
            "missing-input-secret" => "秘密参数丢失。",
            "invalid-input-secret" => "secret参数无效或格式错误。",
            "missing-input-response" => "缺少响应参数。",
            "invalid-input-response" => "响应参数无效或格式错误。",
            "bad-request" => "该请求无效或格式错误。",
            "timeout-or-duplicate" => "响应不再有效：太旧或以前使用过。",
        );

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

        public function check() {
            $res = json_decode($this->curlPost(), true);
            return $this->getMessage($res);
        }

        private function getMessage($aReceive) {
            $aReturn['status'] = 0;
            $aReturn['message'] = "";
            if ($aReceive['success']) {
                $aReturn['status'] = 1;
            } else {
                foreach ($aReceive['error-codes'] as $error_code) {
                    if (!empty($this->aErrorCode[$error_code])) {
                        $aReturn['message'][] = $aErrorCode[$error_code];
                    } else {
                        $aReturn['message'][] = "未知错误。";
                    }
                }
                $aReturn['status'] = 2;
                $aReturn['message'] = implode('', $aReturn['message']);
            }

            return json_encode($aReturn);
        }
    }
?>