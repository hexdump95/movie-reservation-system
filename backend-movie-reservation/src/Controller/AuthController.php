<?php

namespace App\Controller;

use App\DTO\RegisterRequest;
use App\Service\AuthService;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\JsonResponse;
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
    private int $jwtExpiration;

    public function __construct(AuthService $authService, SerializerInterface $serializer, ValidatorInterface $validator, LoggerInterface $logger, ContainerBagInterface $containerBag)
    {
        $this->authService = $authService;
        $this->serializer = $serializer;
        $this->validator = $validator;
        $this->logger = $logger;
        $this->jwtExpiration = $containerBag->get('jwt_expiration');
    }

    #[Route('/register', name: 'register', methods: ['POST'])]
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

        $user = $this->authService->register($registerRequest);
        if ($user === null) {
            return new JsonResponse([], Response::HTTP_BAD_REQUEST);
        }
        $token = $this->authService->generateToken($user);

        return $this->json([
            'access_token' => $token,
            'expires_in' => $this->jwtExpiration,
            'token_type' => 'Bearer',
        ]);
    }

    #[Route('/validateToken', name: 'validateToken', methods: ['POST'])]
    public function validateToken(Request $request): Response
    {
        try {
            $header = $request->headers->get('Authorization');
            if (empty($header)) {
                throw new BadRequestException('Unauthorized');
            }
            $header = str_replace('Bearer ', '', $header);
            $isTokenValid = $this->authService->isTokenValid($header);
            return new JsonResponse(['isValid' => $isTokenValid]);
        } catch (\Exception) {
            return new JsonResponse(['isValid' => false]);
        }
    }

}
