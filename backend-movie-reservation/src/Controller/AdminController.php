<?php

namespace App\Controller;

use App\DTO\UpdateRoleRequest;
use App\DTO\UpdateRolesRequest;
use App\Repository\UserRepository;
use App\Service\AdminService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/v1/admin')]
class AdminController extends AbstractController
{
    private AdminService $adminService;
    private SerializerInterface $serializer;

    public function __construct(AdminService $adminService, SerializerInterface $serializer)
    {
        $this->adminService = $adminService;
        $this->serializer = $serializer;
    }

    #[Route('/usersAndRoles', name: 'getUsersAndRoles', methods: ['GET'])]
    public function getUsersAndRoles(): JsonResponse
    {
        $users = $this->adminService->getUsersAndRoles();
        return new JsonResponse(
            $this->serializer->normalize($users),
            Response::HTTP_OK
        );
    }

    #[Route('/users/{userId}/role', name: 'updateRole', methods: ['PUT'])]
    public function updateRole(int $userId, Request $request): JsonResponse
    {
        $role = $this->serializer->deserialize($request->getContent(), UpdateRoleRequest::class, 'json');
        $userUpdated = $this->adminService->updateRole($userId, $role);
        return new JsonResponse(
            ['success' => $userUpdated],
            Response::HTTP_OK
        );
    }


    #[Route('/users/{userId}/roles', name: 'updateRoles', methods: ['PUT'])]
    public function updateRoles(int $userId, Request $request): JsonResponse
    {
        $roles = $this->serializer->deserialize($request->getContent(), UpdateRolesRequest::class, 'json');
        $userUpdated = $this->adminService->updateRoles($userId, $roles);
        return new JsonResponse(
            ['success' => $userUpdated],
            Response::HTTP_OK
        );
    }

}
