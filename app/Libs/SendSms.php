<?php

namespace App\Libs;

use AlibabaCloud\SDK\Dysmsapi\V20170525\Dysmsapi;
use \Exception;
use AlibabaCloud\Tea\Exception\TeaError;
use AlibabaCloud\Tea\Utils\Utils;

use Darabonba\OpenApi\Models\Config;
use AlibabaCloud\SDK\Dysmsapi\V20170525\Models\SendSmsRequest;
use AlibabaCloud\Tea\Utils\Utils\RuntimeOptions;

class SendSms
{
    private $accessKeyId = null; //ALIBABA_CLOUD_ACCESS_KEY_ID
    private $accessKeySecret = null; //ALIBABA_CLOUD_ACCESS_KEY_SECRET。

    /**
     * 使用AK&SK初始化账号Client
     * @return Dysmsapi Client
     */
    public static function createClient()
    {
        $config = new Config([
            // 必填，您的 AccessKey ID
            "accessKeyId" => self::$accessKeyId,
            // 必填，您的 AccessKey Secret
            "accessKeySecret" => self::$accessKeySecret
        ]);
        // Endpoint 请参考 https://api.aliyun.com/product/Dysmsapi
        $config->endpoint = "dysmsapi.aliyuncs.com";
        return new Dysmsapi($config);
    }

    /**
     * @return void
     */
    public static function send(int $phone, int $code)
    {
        // 请确保代码运行环境设置了环境变量 ALIBABA_CLOUD_ACCESS_KEY_ID 和 ALIBABA_CLOUD_ACCESS_KEY_SECRET。
        // 工程代码泄露可能会导致 AccessKey 泄露，并威胁账号下所有资源的安全性。以下代码示例使用环境变量获取 AccessKey 的方式进行调用，仅供参考，建议使用更安全的 STS 方式，更多鉴权访问方式请参见：https://help.aliyun.com/document_detail/311677.html
        $client = self::createClient();
        $sendSmsRequest = new SendSmsRequest([
            "phoneNumbers" => $phone,
            "signName" => $code
        ]);
        try {
            // 复制代码运行请自行打印 API 的返回值
            return $client->sendSmsWithOptions($sendSmsRequest, new RuntimeOptions([]));
        } catch (Exception $error) {
            if (!($error instanceof TeaError)) {
                $error = new TeaError([], $error->getMessage(), $error->getCode(), $error);
            }
            // 错误 message
            var_dump($error->message);
            // 诊断地址
            var_dump($error->data["Recommend"]);
            Utils::assertAsString($error->message);
        }
    }
}
$path = __DIR__ . \DIRECTORY_SEPARATOR . '..' . \DIRECTORY_SEPARATOR . 'vendor' . \DIRECTORY_SEPARATOR . 'autoload.php';
if (file_exists($path)) {
    require_once $path;
}

// SendSms::send($phone, $code);
