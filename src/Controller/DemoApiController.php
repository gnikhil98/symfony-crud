<?php

namespace App\Controller;

use App\Entity\ApiLog;
use App\Entity\Demo;
use App\Form\DemoType;
use App\Repository\DemoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/demo', name: 'api_demo_')]
class DemoApiController extends AbstractController
{
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    private function createApiLog(Request $request): ApiLog
    {
        $apiLog = new ApiLog();
        $apiLog->setRoute($request->get('_route'));
        $apiLog->setTimestamp(new \DateTime());

        // Log request details
        $apiLog->setRequest([
            'method' => $request->getMethod(),
            'headers' => $request->headers->all(),
            'content' => $this->sanitizeRequestContent($request->getContent()),
        ]);

        return $apiLog;
    }
    private function sanitizeRequestContent(string $content): string
    {
        // Your logic to remove unwanted parts from the request content
        // Example: Remove newlines and carriage returns
        $sanitizedContent = str_replace(["\r", "\n"], '', $content);

        return $sanitizedContent;
    }

    private function logApiResponse(ApiLog $apiLog, JsonResponse $response, EntityManagerInterface $entityManager): void
    {
        // Log response details
        $apiLog->setResponse([
            'statusCode' => $response->getStatusCode(),
            'headers' => $response->headers->all(),
            'content' => json_decode($response->getContent(), true),
        ]);

        // Persist the ApiLog entity
        $entityManager->persist($apiLog);
        $entityManager->flush();

        try {
            $this->logger->info('API Response Logged', ['apiLog' => $apiLog]);
        } catch (\Exception $e) {
            $this->logger->error('Error logging API response', ['exception' => $e->getMessage()]);
        }
    }

    #[Route('/', name: 'index', methods: ['GET'], format: 'json')]
    public function index(DemoRepository $demoRepository, Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $apiLog = $this->createApiLog($request);
        $demos = $demoRepository->findAll();

        $response = $this->json(['demos' => $demos]);
        $this->logApiResponse($apiLog, $response, $entityManager);

        return $response;
    }

    #[Route('/new', name: 'new', methods: ['POST'], format: 'json')]
    public function new (Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $apiLog = $this->createApiLog($request);

        try {
            // Deserialize JSON content into Demo entity
            $data = json_decode($request->getContent(), true);
            $demo = new Demo();
            $form = $this->createForm(DemoType::class, $demo, ['csrf_protection' => false]);

            // Submit the form with the JSON data
            $form->submit($data);

            if ($form->isValid()) {
                $entityManager->persist($demo);
                $entityManager->flush();

                $response = $this->json(['message' => 'Resource created successfully'], JsonResponse::HTTP_CREATED);
                $this->logApiResponse($apiLog, $response, $entityManager);

                return $response;
            }

            $errors = $this->getErrorsFromForm($form);
            $response = $this->json(['message' => 'Invalid data', 'errors' => $errors], JsonResponse::HTTP_BAD_REQUEST);
            $this->logApiResponse($apiLog, $response, $entityManager);

            return $response;
        } catch (\Exception $e) {
            $response = $this->json(['message' => 'Error processing request'], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
            $this->logApiResponse($apiLog, $response, $entityManager);

            return $response;
        }
    }

    // Helper function to extract errors from the form
    private function getErrorsFromForm($form): array
    {
        $errors = [];
        foreach ($form->getErrors(true, true) as $error) {
            $errors[] = $error->getMessage();
        }

        foreach ($form->all() as $childForm) {
            if ($childErrors = $this->getErrorsFromForm($childForm)) {
                $errors[] = $childErrors;
            }
        }

        return $errors;
    }

    #[Route('/{id}', name: 'show', methods: ['GET'], format: 'json')]
    public function show(Demo $demo, Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $apiLog = $this->createApiLog($request);
        $response = $this->json(['demo' => $demo]);
        $this->logApiResponse($apiLog, $response, $entityManager);

        return $response;
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['PUT'], format: 'json')]
    public function edit(Request $request, Demo $demo, EntityManagerInterface $entityManager): JsonResponse
    {
        $apiLog = $this->createApiLog($request);

        try {
            // Deserialize JSON content into Demo entity
            $data = json_decode($request->getContent(), true);
            $form = $this->createForm(DemoType::class, $demo, ['csrf_protection' => false]);

            // Submit the form with the JSON data
            $form->submit($data);

            if ($form->isValid()) {
                $entityManager->flush();

                $response = $this->json(['message' => 'Resource updated successfully'], JsonResponse::HTTP_OK);
                $this->logApiResponse($apiLog, $response, $entityManager);

                return $response;
            }

            $errors = $this->getErrorsFromForm($form);
            $response = $this->json(['message' => 'Invalid data', 'errors' => $errors], JsonResponse::HTTP_BAD_REQUEST);
            $this->logApiResponse($apiLog, $response, $entityManager);

            return $response;
        } catch (\Exception $e) {
            $response = $this->json(['message' => 'Error processing request'], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
            $this->logApiResponse($apiLog, $response, $entityManager);

            return $response;
        }
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'], format: 'json')]
    public function delete(Demo $demo, EntityManagerInterface $entityManager): JsonResponse
    {
        $apiLog = $this->createApiLog($this->get('request_stack')->getCurrentRequest());
        $entityManager->remove($demo);
        $entityManager->flush();

        $response = $this->json(['message' => 'Resource deleted successfully'], JsonResponse::HTTP_OK);
        $this->logApiResponse($apiLog, $response, $entityManager);

        return $response;
    }
}
