<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\User;
use Nelmio\ApiDocBundle\Annotation as Nelmio;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;

#[Route('/api', name: 'api_')]
class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'register', methods: 'post')]
    #[OA\RequestBody(required: true, content: new OA\JsonContent(ref: "#/components/schemas/RegisterRequest"))]
    #[OA\Response(
        response: Response::HTTP_CREATED,
        description: "Registered Successfully",
        content: null
    )]
    public function index(ManagerRegistry $doctrine, Request $request, UserPasswordHasherInterface $passwordHasher): JsonResponse
    {
        $em = $doctrine->getManager();
        $decoded = json_decode($request->getContent());
        $email = $decoded->email;
        $plaintextPassword = $decoded->password;
   
        $user = new User();
        $hashedPassword = $passwordHasher->hashPassword(
            $user,
            $plaintextPassword
        );
        $user->setPassword($hashedPassword);
        $user->setEmail($email);
        $user->setUsername($email);
        $em->persist($user);
        $em->flush();
   
        return $this->json(['message' => 'Registered Successfully']);
    }
}
