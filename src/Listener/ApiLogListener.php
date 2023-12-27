<?php
namespace App\Listener;

use App\Entity\ApiLog;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Security;

class ApiLogListener implements EventSubscriberInterface {
    private EntityManagerInterface $em;
    private Security $security;

    public function __construct(EntityManagerInterface $em, Security $security)
    {
        $this->em = $em;
        $this->security = $security;
    }

    /**
     * Creates a database log entry for routes
     *
     * @param RequestEvent $event The event object containing the request information.
     * @return void
     */
    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        $user = $this->security->getUser();

        try {
            $newLog = new ApiLog();
            $newLog->setIp($request->getClientIp());
            $newLog->setTimestamp(new \DateTime());
            $newLog->setUserAgent($request->headers->get('User-Agent'));
            $newLog->setEndpoint($request->getPathInfo());

            if (null !== $user) {
                $username = $user->getUsername();
                $user = $this->em->getRepository(User::class)->findOneBy(['username' => $username]);
                $newLog->setUser($user);
            }

            $this->em->persist($newLog);
            $this->em->flush();
        } catch (\Exception $e) {
        }
    }

    public static function getSubscribedEvents(): array {
        return [
            KernelEvents::REQUEST => 'onKernelRequest',
        ];
    }
}