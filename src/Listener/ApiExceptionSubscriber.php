<?php
namespace App\Listener;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class ApiExceptionSubscriber implements EventSubscriberInterface
{
    private ParameterBagInterface $params;

    public function __construct(ParameterBagInterface $params)
    {
        $this->params = $params;
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $request = $event->getRequest();
        $path = $request->getPathInfo();

        // Only handle API routes
        if (0 !== strpos($path, '/api')) {
            return;
        }

        $exception = $event->getThrowable();

        $statusCode = 500;
        $message = 'Internal Server Error';

        if ($exception instanceof NotFoundHttpException) {
            $statusCode = 404;
            $message = 'Not Found';
        } elseif ($exception instanceof MethodNotAllowedHttpException) {
            $statusCode = 405;
            $message = 'Method Not Allowed';
        } elseif ($exception instanceof AuthenticationException) {
            $statusCode = 401;
            $message = 'Unauthorized';
        } elseif ($exception instanceof AccessDeniedException || $exception instanceof AccessDeniedHttpException) {
            $statusCode = 403;
            $message = 'Forbidden';
        } elseif ($exception instanceof HttpExceptionInterface) {
            $statusCode = $exception->getStatusCode();
            $msg = trim((string) $exception->getMessage());
            $message = $msg !== '' ? $msg : 'Error';
        }

        $payload = [
            'version' => $this->params->get('api_version'),
            'status' => $statusCode,
            'data' => [
                'error' => $message,
            ],
        ];

        $response = new JsonResponse($payload, $statusCode);

        if ($exception instanceof HttpExceptionInterface) {
            foreach ($exception->getHeaders() as $header => $value) {
                $response->headers->set($header, $value);
            }
        }

        $event->setResponse($response);
    }

    public static function getSubscribedEvents(): array
    {
        // High priority to convert to JSON before default exception handling renders HTML
        return [
            KernelEvents::EXCEPTION => ['onKernelException', 128],
        ];
    }
}
