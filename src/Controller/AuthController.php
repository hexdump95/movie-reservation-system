<?php

namespace App\Controller;

use App\DTO\RegisterRequest;
use App\Service\AuthService;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/v1/auth')]
class AuthController extends AbstractController
{
    private AuthService $authService;
    private SerializerInterface $serializer;
    private ValidatorInterface $validator;
    private LoggerInterface $logger;

    public function __construct(AuthService $authService, SerializerInterface $serializer, ValidatorInterface $validator, LoggerInterface $logger)
    {
        $this->authService = $authService;
        $this->serializer = $serializer;
        $this->validator = $validator;
        $this->logger = $logger;
    }

    #[Route('/register', name: 'auth.register', methods: ['POST'])]
    public function register(Request $request): Response
    {
        if ('json' !== $request->getContentTypeFormat()) {
            throw new BadRequestException('Unsupported content format');
        }

        $registerRequest = $this->serializer->deserialize($request->getContent(), RegisterRequest::class, 'json');

        $errors = $this->validator->validate($registerRequest);
        if (count($errors) > 0) {
            $errorsString = (string)$errors;
            $this->logger->error($errorsString);
            throw new BadRequestException($errorsString);
        }

        $token = $this->authService->register($registerRequest);

        return $this->json(['token' => $token]);
    }
}
