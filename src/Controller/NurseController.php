<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\UserRepository;


#[Route('/nurse')]
final class NurseController extends AbstractController
{

    // Route to call the findByName method to search for a nurse by name
    #[Route('/name/{name}', name: 'app_nurse_find_by_name')] 

    public function findByName(string $name, UserRepository $userRepository): JsonResponse
    {
        // Busca una enfermera por nombre usando el repositorio
        $nurse = $userRepository->findOneByName($name);

        if ($nurse) {
            return $this->json([
                'name' => $nurse->getName(),
                'user' => $nurse->getUser(),
                'password' => $nurse->getPassword(),
            ], 200);
        } else {
            // Si no se encuentra, devuelve un error 404
            return $this->json(['error' => 'Nurse not found'], 404);
        }
    }


    #[Route('/index', name: 'index')]
public function getAll(UserRepository $userRepository): JsonResponse
{
    // Obtenemos todos los usuarios (en tu caso, enfermeras)
    $nurses = $userRepository->findAll();

    // Mapeamos los objetos a arrays para devolver en formato JSON
    $data = array_map(function ($nurse) {
        return [
            'id' => $nurse->getId(),
            'user' => $nurse->getUser(),
            'password' => $nurse->getPassword(),
        ];
    }, $nurses);

    return $this->json($data, 200);
}


    //Codigo Javier


    // #[Route('/login', methods: ['POST'])]
    // public function login(Request $request): JsonResponse
    // {
    //     //$nurses = json_decode(file_get_contents(__DIR__ . '/../../public/nurses.json'), true);
    //     $data = json_decode($request->getContent(), true);
    //     $username = $data['username'] ?? '';
    //     $pwd = $data['password'] ?? '';
    //     $nurse = $nurseRepository->findOneBy(['username'=>$username]);
    //     if($nurse)
    //     return this->json(
    //         [
    //             'message'=>'Credenciales inválidas'
    //         ]);
    // }

    #[Route('/login', methods: ['POST'])]
    public function login(Request $request, UserRepository $userRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $username = $data['user'] ?? '';
        $password = $data['password'] ?? '';

        $nurse = $userRepository->findOneBy(['user' => $username]);

        if ($nurse && $nurse->getPassword() === $password) {
            return $this->json(['message' => 'Login successful'], 200);
        }

        return $this->json(['error' => 'Invalid credentials'], 401);
    }
}

//Codigo de Olalla (Dejamos el de javier)


// {
//     #[Route(path: '/nurse/login', methods: ['POST'])]
    
//     public function login(Request $request): JsonResponse
//     {
//         $nurses = json_decode(json: file_get_contents(__DIR__ . '/../../public/nurses.json'), associative: true);
//         $data = json_decode($request->getContent(), true);
        
//         foreach ($nurses as $nurse) {
//             if ($nurse['username'] === ($data['username'] ?? '') &&
//                 $nurse['password'] === ($data['password'] ?? '')) {
//                 return $this->json(true);
//             }
//         }
//         return $this->json(['error' => 'Credenciales inválidas'], 401);
//     }
    
// }
