<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Subscriber\Oauth\Oauth1;
use GuzzleHttp\Handler\CurlHandler;
use Log;

use Helper;


class CreateReceipt extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'receipt:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $client = new Client();

        $certificate = base_path() . '/ssl/mango/rsa_private_key.pem';
        $mango_rsa_public_key = base_path() . '/ssl/mango/mango-RSAkey.pem';

        $uri = "https://test.e-mango.ph";
        $api = "/cashier/collectingPartnerPay";
        $request_method = 'POST';
        // $uri = "https://taxengine.psmed.org";
        // $api = "/api/transaction";

        $id = Helper::ref_number("D"."0",32);

        $init_params = [
            "timestamp" => "2023-07-17 12:41:01",
            "merchSeq" => "300000064506",
            "orderSeq" => $id,
            "orderDate" => "2023-07-17",
            "amount" => "1000.00",
            "fee" => "5.00",
            "currency" => "PHP",
            "busiName" => "EDGE COMMUNICATIONS SOLUTIONS CORP",
            "dueTime" => "0",
            "busiType" => "1",
            "isRedirect" => "0",
            "redirectUrl" => "http://sampleredirect.com",
            "additionInfo" => "",
            "ipAddress" => "52.221.186.7",
            "remark" => "test api",
            "signType" => "RSA"
        ];

        $base64Signature = $this->sign($this->createLinkString($init_params));
        $params = $init_params;
        $params["sign"] = $base64Signature;
        $requestBody = json_encode($params);
		
        $contextOptions = [
            'http' => [
                'method' => $request_method,
                'header' => "Content-Type: application/json",
                'content' => $requestBody
            ]
        ];

        $context = stream_context_create($contextOptions);
        $response_body = file_get_contents($uri . $api, false, $context);

        file_put_contents('public/files/mango.html', $response_body);
    }


    public function sign(string $data = ''): bool|string
	{
		if (empty($data)) {
			return False;
		}
        $certificate = base_path() . '/ssl/mango/rsa_private_key.pem';
        $privateKey = file_get_contents($certificate);
        $signature = '';
        $private_key = wordwrap($privateKey, 64, "\n", true);
		if (empty($private_key)) {
			echo "Private Key error!";
			return False;
		}

        echo "certificate:".$privateKey;

		//
		$private_key_resource_id = openssl_get_privatekey($private_key);
		if (empty($private_key_resource_id)) {
			echo "private key resource identifier False!";
			return False;
		}
		openssl_sign($data, $signature, $private_key_resource_id, OPENSSL_ALGO_MD5);

		return base64_encode($signature);
	}

    public function isValid(string $data = '', string $signature = ''): bool|int
	{
		if (empty($data) || empty($signature)) {
			return False;
		}
        $certificate =  base_path() . '/ssl/mango/mango-RSAkey.pem';
        $publicKey = file_get_contents($certificate);
        $public_key = chunk_split($publicKey, 64, "\n");
		$public_key = "-----BEGIN PUBLIC KEY-----\n$public_key-----END PUBLIC KEY-----";

		if (empty($public_key)) {
			echo "Public Key error!";
			return False;
		}
		// 
		$public_key_resource_id = openssl_get_publickey($public_key);
		if (empty($public_key_resource_id)) {
			echo "public key resource identifier False!";
			return False;
		}
		// 
		$signature = base64_decode($signature);
		$ret = openssl_verify($data, $signature, $public_key_resource_id, OPENSSL_ALGO_MD5);
//		openssl_free_key($public_key_resource_id);
		return $ret;
	}


	public function createLinkString($para): string
	{
		$para = $this->paraFilter($para);
		$para = $this->argSort($para);
		$arg = "";
		foreach ($para as $key => $value) {
			$arg .= $key . "=" . $value . "&";
		}
		
		return substr($arg, 0, strlen($arg) - 1);
	}

	private function paraFilter($para): array
	{
		$para_filter = array();
		foreach ($para as $key => $value) {
			if ($key == "sign" || $value == "") {
				continue;
			} else {
				if (gettype($value) == "integer" || gettype($value) == "double") {
					$para_filter[$key] = sprintf("%.2f", $value);
				} else {
					$para_filter[$key] = $value;
				}
			}
		}
		return $para_filter;
	}

	
	private function argSort($para)
	{
		ksort($para);
		reset($para);
		return $para;
	}

	
	private function local_date($format, $time = NULL)
	{
		if ($time === NULL) {
			$time = gmtime();
		} elseif ($time <= 0) {
			return '';
		}
		return date($format, $time);
	}
}
