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
    #[Route('/all', name: 'app_nurse')]

    /**
     * Search for a nurse by name in a JSON file.
     *
     * @param string $name
     * @return JsonResponse
     */

    public function getAllDataBase(UserRepository $userRepository): JsonResponse
    {
        $nurses = $userRepository->findAll();
        $data = [];
        
        foreach ($nurses as $nurse) {
            $data[] = [
                'id' => $nurse->getId(),
                'name' => $nurse->getName(),
                'password' => $nurse->getPassword(),
                'user' => $nurse->getUser(),
            ];
        }
        
        return $this->json($data, Response::HTTP_OK);
    }


    //codigo david
    public function findByName(string $name): JsonResponse
    {
        // Relative project path: public/nurses.json
        $file = __DIR__ . '/../../public/nurses.json';

        // Try to read and decode the JSON. `true` to get an associative array.
        $nurses = json_decode(file_get_contents($file), true);

        // Default result: null indicates not found
        $result = null;

        // If $nurses is not an array (missing file or invalid JSON), avoid errors
        if (is_array($nurses)) {
            foreach ($nurses as $nurse) {
                // Strict comparison by name
                if (isset($nurse['name']) && $nurse['name'] === $name) {
                    // Build the result with required fields
                    $result = [
                        'name' => $nurse['name'],
                        'user' => $nurse['user'] ?? null,
                        'password' => $nurse['password'] ?? null,
                    ];
                    break;
                }
            }
        }

        if ($result) {
            // Return the found nurse
            return $this->json($result, 200);
        } else {
            // Return 404 error if not found
            return $this->json(['error' => 'Nurse not found'], 404);
        }
    }
    
    //codigo arnau
    // Esta ruta responderá a /nurse/index y se llamará app_nurse_index  
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

 