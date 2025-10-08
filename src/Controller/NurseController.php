<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/nurse')]
final class NurseController extends AbstractController
{

    // Route to call the findByName method to search for a nurse by name
    #[Route('/name/{name}', name: 'app_nurse')]

    /**
     * Search for a nurse by name in a JSON file.
     *
     * @param string $name
     * @return JsonResponse
     */
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


    // Esta ruta responderá a /nurse/index y se llamará app_nurse_index  
    #[Route('/index', name: 'index')]
    public function getAll(): JsonResponse
    {
        // Definimos la ruta absoluta del archivo nurses.json
        $jDoc = __DIR__ . '/../../public/nurses.json';

        // Verificamos si el archivo existe antes de intentar leerlo
        if (!file_exists($jDoc)) {
            // Si no existe, devolvemos una respuesta JSON con un mensaje de error
            // y el código de estado HTTP 404 (No encontrado)
            return $this->json(['error' => 'El archivo JSON no existe.'], 404);
        }

        // Leemos el contenido completo del archivo JSON
        $jsonContent = file_get_contents($jDoc);

        // Decodificamos el contenido JSON a un array asociativo de PHP
        // El segundo parámetro "true" hace que devuelva array en lugar de objeto stdClass
        $nurses = json_decode($jsonContent, true);

        // Si todo va bien, devolvemos el contenido en formato JSON
        // con el código 200 (que va bien)
        return $this->json($nurses, 200);
    }
}
