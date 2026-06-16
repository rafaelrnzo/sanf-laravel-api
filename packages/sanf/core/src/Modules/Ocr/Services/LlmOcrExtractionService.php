<?php

namespace Sanf\Core\Modules\Ocr\Services;

use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Ocr\Dtos\ExtractOcrRequestDto;
use Sanf\Core\Modules\Ocr\Dtos\ExtractOcrResponseDto;
use Sanf\Integration\Modules\Gemini\LlmOcrClient;

final class LlmOcrExtractionService implements ApplicationServiceInterface
{
    protected LlmOcrClient $client;

    public function __construct(LlmOcrClient $client)
    {
        $this->client = $client;
    }

    /**
     * @param ExtractOcrRequestDto $dto
     * @return ExtractOcrResponseDto
     */
    public function execute($dto = null)
    {
        $results = [];

        foreach ($dto->files as $index => $file) {
            $mimeType = $file->getMimeType();
            $base64Data = base64_encode(file_get_contents($file->getRealPath()));

            $extractedData = [
                'nomor_invoice' => null,
                'total_invoice' => null,
                'no_plafond' => null,
            ];

            try {
                $extractedText = $this->client->extractTextFromBase64($mimeType, $base64Data, $dto->llmApiKey);
                $cleanJson = preg_replace('/```(?:json)?\n?(.*?)\n?```/s', '$1', $extractedText);
                $parsed = json_decode(trim($cleanJson), true);
                
                if (is_array($parsed)) {
                    $extractedData = array_merge($extractedData, $parsed);
                }
            } catch (\Exception $e) {
                // Ignore parse errors, keep nulls
            }

            $results[] = [
                'title' => 'Invoice ' . ($index + 1),
                'nomor_invoice' => $extractedData['nomor_invoice'] ?? null,
                'total_invoice' => $extractedData['total_invoice'] ?? null,
                'no_plafond' => $extractedData['no_plafond'] ?? null,
                'filename' => $file->getClientOriginalName(),
            ];
        }

        return new ExtractOcrResponseDto([
            'mode' => $dto->mode,
            'results' => $results,
        ]);
    }
}
