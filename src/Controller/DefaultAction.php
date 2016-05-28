<?php

namespace App\Controller;

use PDO;
use Psr\Log\LoggerInterface;
use Slim\Views\PhpRenderer;

class DefaultAction {
    protected $pdo;
    private $renderer;
    private $logger;

    public function __construct(PhpRenderer $renderer, LoggerInterface $logger, PDO $pdo) {
        $this->renderer = $renderer;
        $this->logger = $logger;
        $this->pdo = $pdo;
    }


    function defaultAction($request, $response, $args) {
        echo 'Invalid request';
    }

    function getObject($request, $response, $args) {
        // Sample log message
        //$this->logger->info("GET object '/object/key' route");

        if (!isset($args)) {
            die('Invalid request');
        }

        $output = '';

        if (isset($_GET['timestamp'])) {
            $timestamp = $_GET['timestamp'];
            $key = $args['key'];
            $query = $this->pdo->query("SELECT * from key_store where `key` like '$key' and timestamp <= from_unixtime('$timestamp') order by timestamp desc limit 1")->fetch(PDO::FETCH_ASSOC);

            if ($query) {
                $output = $query['value'];
            }
        } else {
            $key = $args['key'];
            $query = $this->pdo->query("SELECT * from key_store where `key` like '$key' order by timestamp desc limit 1")->fetch(PDO::FETCH_ASSOC);

            if ($query) {
                $output = $query['value'];
            }
        }

        $this->renderer->render($response, 'index.phtml', ['output' => $output]);
    }

    function insertObject($request, $response, $args) {
        $now = time();
        $result = 'invalid request';

        //$data = $request->getParsedBody()['JSON'];
        $data = array_values($request->getParsedBody())[0];

        // Sample log message
        //$this->logger->info("POST object '/object' route");

        if (isset($data)) {
            $data = $this->json_validate($data);

            if (count($data) == 1) {
                foreach ($data as $k => $v) {
                    $key = $k;
                    $value = json_encode($v);
                }
            } else {
                die('More than one key-value pair specified');
            }

            // Check if entry exist
            $query = $this->pdo->query("SELECT * from key_store where `key` like '$key' and timestamp like from_unixtime('$now') order by timestamp desc limit 1")->fetch(PDO::FETCH_ASSOC);

            if (!$query) {
                // Insert new kvp
                $stmt = $this->pdo->prepare("insert into key_store(`key`, `timestamp`, `value`) VALUES (?, FROM_UNIXTIME(?), ?)");

                if ($stmt->execute(array($key, $now, $value))) {
                    $result = '1 row has been inserted';
                } else {
                    print_r($stmt->errorinfo());
                    $result = 'failed to insert row';
                }
            } else {
                $result = 'duplicate entry exist';
            }
        }

        // Render index view
        return $this->renderer->render($response, 'index.phtml', ['output' => $result]);
    }

    function json_validate($string) {
        $result = json_decode($string, true);

        // switch and check possible JSON errors
        switch (json_last_error()) {
            case JSON_ERROR_NONE:
                $error = ''; // JSON is valid // No error has occurred
                break;
            case JSON_ERROR_DEPTH:
                $error = 'The maximum stack depth has been exceeded.';
                break;
            case JSON_ERROR_STATE_MISMATCH:
                $error = 'Invalid or malformed JSON.';
                break;
            case JSON_ERROR_CTRL_CHAR:
                $error = 'Control character error, possibly incorrectly encoded.';
                break;
            case JSON_ERROR_SYNTAX:
                $error = 'Syntax error, malformed JSON.';
                break;
            // PHP >= 5.3.3
            case JSON_ERROR_UTF8:
                $error = 'Malformed UTF-8 characters, possibly incorrectly encoded.';
                break;
            // PHP >= 5.5.0
            case JSON_ERROR_RECURSION:
                $error = 'One or more recursive references in the value to be encoded.';
                break;
            // PHP >= 5.5.0
            case JSON_ERROR_INF_OR_NAN:
                $error = 'One or more NAN or INF values in the value to be encoded.';
                break;
            case JSON_ERROR_UNSUPPORTED_TYPE:
                $error = 'A value of a type that cannot be encoded was given.';
                break;
            default:
                $error = 'Unknown JSON error occured.';
                break;
        }

        if ($error !== '') {
            die($error);
        }

        // everything is OK
        return $result;
    }
}