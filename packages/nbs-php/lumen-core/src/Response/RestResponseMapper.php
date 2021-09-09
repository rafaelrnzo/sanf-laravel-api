<?php


namespace NbsPhp\Core\Response;


use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use NbsPhp\Core\Exceptions\ApiException;
use Symfony\Component\HttpFoundation\Response;

class RestResponseMapper implements ResponseMapperInterface
{
    /**
     * Formatting Success Response
     * @param Response $response
     * @return Response
     */
    public function successResponse(Response $response)
    {
        $decodedContent = json_decode($response->getContent(), true);

        $content = $decodedContent ?? $response->getContent();

        if($response instanceof JsonResponse && (isset($content['success']) && $content['success'] == false)) {
            return $response;
        }

        //TODO RESPONSE MODEL OBJECT
        $responseSuccess = [
            'success' => true,
            'code' => '200',
            'message' => $content['message'] ?? 'OK'
        ];

        if ($content != '') {
            unset($content['message']);
            $responseSuccess['data'] = $content;
            if (array_key_exists('data', $content)) {
                $responseSuccess['data'] = $content['data'];
                if ($content['data'] == null) {
                    unset($responseSuccess['data']);
                }
            }
        }
        $response->setContent(json_encode($responseSuccess));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * Formatting Error Response
     *
     * @param Response $response
     * @return Response
     */
    public function errorResponse(Response $response)
    {
        $exception = $response->exception;
        if ($exception) {
            $exceptionCode = $exception->getCode();
            $errorMapping = config("response-codes." . get_class($exception)) ?? config("response-codes." . $exceptionCode);
            list($error, $httpStatus) = $this->parseException($exception);
        } elseif ($response instanceof JsonResponse && $response->getStatusCode() == Response::HTTP_UNPROCESSABLE_ENTITY) {
            $decodedContent = json_decode($response->getContent(), true);
            $content = $decodedContent ?? $response->getContent();
            $error = [
                'success' => false,
                'code' => '422',
                'message' => 'Input Validation Error',
                'timestamp' => date('Y-m-d H:i:s'),
                'data' => $content
            ];
        } else {
            throw new Exception('Response Mappper: Unknown response error payload');
        }

        if (isset($errorMapping['status'])) {
            $response->setStatusCode($errorMapping['status']);
        }
        if ($exception instanceof ApiException) {
            $response->setStatusCode($errorMapping['status'] ?? $exception->getStatus());
        }

        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode($error));

        return $response;
    }

    public function parseException(Exception $exception)
    {
        //TODO RESPONSE MODEL OBJECT
        $error = [
            'success' => false,
            'code' => '',
            'message' => '',
        ];

        //TODO MOVE config loader outside class
        $exceptionCode = $exception->getCode();
        $errorMapping = config("response-codes." . get_class($exception)) ?? config("response-codes." . $exceptionCode);
        $error['code'] = $errorMapping['code'] ?? (string)Response::HTTP_INTERNAL_SERVER_ERROR;
        $error['message'] = $errorMapping['message'] ?? __('Internal Server Error');

        if ($exception instanceof ApiException) {
            if($exception->getData() != null){
                $error['data'] = $exception->getData();
            }
            $error['code'] = $errorMapping['code'] ?? $exception->getCode();
            $error['message'] = $errorMapping['message'] ?? $exception->getMessage();
        }

        //prevent debug leak on production env
        if (app()->environment() !== 'production' && config('app.debug') === true) {
            $error['data']['_trace'] =
                [
                    'message' => $exception->getMessage() ?: $error['message'],
                    'exception' => get_class($exception),
                    'stack' => $exception->getFile() . ' - ' . $exception->getLine(),
                    'trace' => collect($exception->getTrace())->map(function ($trace) {
                        return Arr::except($trace, ['args']);
                    })->all()
                ];

            //handle response for validation exception
            if (optional($exception)->status === Response::HTTP_UNPROCESSABLE_ENTITY) {
                $error['data']['_trace']['validations'] = $exception->getResponse()->original;
            }
        }

        $httpStatus = $errorMapping['status'] ?? 500;
        if ($exception instanceof ApiException) {
            $httpStatus = $errorMapping['status'] ?? $exception->getStatus();
        }
        return [$error, $httpStatus];
    }
}
