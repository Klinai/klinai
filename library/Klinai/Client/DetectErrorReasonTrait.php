<?php

namespace Klinai\Client;

trait DetectErrorReasonTrait {
    static $ERROR_REASON_UNKOWN_ERROR = 0;
    static $ERROR_REASON_DATABASE_NOT_EXISTS = 1;
    static $ERROR_REASON_DOCUMENT_NOT_EXISTS = 2;

    public function detectErrorReason ($response) {

        if ( $response->error === "not_found" ) {
            switch ($response->reason) {
                case 'no_db_file':
                    return self::$ERROR_REASON_DATABASE_NOT_EXISTS;
                case 'missing':
                    return self::$ERROR_REASON_DOCUMENT_NOT_EXISTS;
                default:
                    return self::$ERROR_REASON_UNKOWN_ERROR;
            }
        }

        return self::$ERROR_REASON_UNKOWN_ERROR;
    }

    public function createExceptionInstance ($reasonOrResponse,$options) {
        if ( !is_int($reasonOrResponse) ) {
            $reason = $this->detectErrorReason($reasonOrResponse);
        } else {
            $reason = $reasonOrResponse;
        }

        switch ($reason) {
            case self::$ERROR_REASON_DATABASE_NOT_EXISTS:
                $msg = sprintf('the database "%s"',$options['database']);
                $exception = new Exception\DatabaseNotExistsException($msg);
                break;
            case self::$ERROR_REASON_DOCUMENT_NOT_EXISTS:
                $msg = sprintf('a document named "%s" is not exists in database %s',
                               $options['docId'],
                               $options['database']
                );
                $exception = new Exception\DocumentNotExistsException($msg);
                break;

            default:
                $exception = new \RuntimeException("unkown error");
        }

        return $exception;
    }
}