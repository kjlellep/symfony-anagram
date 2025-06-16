<?php

namespace App\Controller;

use RuntimeException;
use App\Service\WordImportService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class WordImportController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly WordImportService $importService,
        private readonly bool $importAllowed,
        private readonly string $wordbaseUrl
    ) {
    }

    public function import(): JsonResponse
    {
        if (!$this->importAllowed) {
            return new JsonResponse(['error' => 'Import not allowed in this environment'], 400);
        }

        $existing = $this->entityManager->getRepository(\App\Entity\Word::class)->count([]);
        if ($existing > 0) {
            return new JsonResponse([
                'status' => 'already_imported',
                'rows' => $existing
            ]);
        }

        $context = stream_context_create([
            "http" => [
                "header" => "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.5"
            ]
        ]);

        $tmpFile = tempnam(sys_get_temp_dir(), 'wordbase_');
        if (!file_put_contents($tmpFile, @file_get_contents($this->wordbaseUrl, false, $context))) {
            return new JsonResponse(['error' => 'Failed to download wordbase file'], 500);
        }

        try {
            $imported = $this->importService->importFromFile($tmpFile);
        } catch (RuntimeException $e) {
            return new JsonResponse(['error' => 'Failed to process tmp file'], 500);
        } finally {
            unlink($tmpFile);
        }

        return new JsonResponse([
            'status' => 'import_successful',
            'rows' => $imported
        ]);
    }
}
