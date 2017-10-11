<?php

use Hipay\MicroToken\MicroTokenServiceProvider;
use Hipay\MicroToken\Model\CipherRegistry;
use Hipay\MicroToken\Model\TokenManager;
use Silex\Application;
use Silex\Provider\MonologServiceProvider;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

require_once dirname(__DIR__)."/vendor/autoload.php";

list($parameters, $services) = array_map(
    function ($name) {
        return json_decode(file_get_contents(dirname(__DIR__)."/config/".$name.".json"), true);
    },
    ["parameters", "services"]
);

$app = new Application();

$app->register(new MonologServiceProvider(), $parameters);
$app->register(new MicroTokenServiceProvider($services), $parameters);

$app->view(
    function (array $response) use ($app) {
        return $app->json($response, isset($response["code"]) ? $response["code"] : 200);
    }
);

$app->error(
    function (\Exception $exception) {

        $code = 500;
        $message = "Internal error";

        if ($exception instanceof HttpException) {

            $code = $exception->getStatusCode();
            if (isset(Response::$statusTexts[$code])) {
                $message = ucfirst(strtolower(Response::$statusTexts[$code]));
            } else {
                $message = "Unknown status";
            }
        }

        return [
            "code" => $code,
            "message" => $message,
            "details" => $exception->getMessage(),
        ];
    }
);

$app->post(
    "/token",
    function (Request $request) use ($app) {
        /** @var TokenManager $manager */
        $manager = $app["micro_token.token_manager"];
        /** @var CipherRegistry $cipherRegistry */
        $cipherRegistry = $app["micro_token.cipher_registry"];

        $manager->setCipher($cipherRegistry->get($request->request->get("algorithm")));

        return [
            "code" => 201,
            "token" => $manager->create(
                $request->request->get("card_number"),
                $request->request->getInt("key_identifier")
            )
        ];
    }
);

$app->run();