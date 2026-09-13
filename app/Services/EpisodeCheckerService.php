<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class EpisodeCheckerService
{
    public function checkBookmarkLinks(string $primaryUrl, array $alternativeUrls = []): array
    {
        $urlsToCheck = array_unique(array_filter(array_merge([$primaryUrl], $alternativeUrls)));
        $results = [];

        foreach ($urlsToCheck as $index => $url) {
            $isPrimary = ($index === 0);
            $results[] = $this->inspectUrl($url, $isPrimary);
        }

        return $results;
    }

    private function inspectUrl(string $url, bool $isPrimary): array
    {
        $cacheKey = 'ep_check_' . md5($url);

        return Cache::remember($cacheKey, now()->addMinutes(15), function () use ($url, $isPrimary) {
            try {
                $response = Http::withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/120.0.0.0 Safari/537.36',
                    'Accept-Language' => 'es-ES,es;q=0.9,en;q=0.8'
                ])->timeout(6)->get($url);

                if ($response->failed()) {
                    return $this->buildResultResponse($isPrimary, false, null, 'No se pudo conectar a la fuente');
                }

                $html = $response->body();

                // 1. ESTRATEGIA SCRAPING: Buscar botón "Siguiente" habilitado en el HTML
                $nextUrlFromDom = $this->extractNextLinkFromHtml($html, $url);

                if ($nextUrlFromDom) {
                    return $this->buildResultResponse($isPrimary, true, $nextUrlFromDom, '¡Nuevo episodio/capítulo detectado!');
                }

                // 2. ESTRATEGIA SECUENCIAL: Soporta URLs con o sin _01
                $sequentialUrl = $this->generateSequentialUrl($url);

                if ($sequentialUrl && $sequentialUrl !== $url) {
                    $seqResponse = Http::withHeaders([
                        'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/120.0.0.0 Safari/537.36'
                    ])->timeout(4)->head($sequentialUrl);

                    if ($seqResponse->successful()) {
                        return $this->buildResultResponse($isPrimary, true, $sequentialUrl, '¡Nuevo capítulo detectado secuencialmente!');
                    }
                }

                return $this->buildResultResponse($isPrimary, false, null, 'Al día. No hay capítulos nuevos');

            } catch (\Exception $e) {
                return $this->buildResultResponse($isPrimary, false, null, 'Error al inspeccionar la fuente');
            }
        });
    }

    private function extractNextLinkFromHtml(string $html, string $baseUrl): ?string
    {
        if (empty($html)) return null;

        libxml_use_internal_errors(true);
        $doc = new \DOMDocument();
        @$doc->loadHTML('<?xml encoding="UTF-8">' . $html, LIBXML_NOERROR | LIBXML_NOWARNING);
        $xpath = new \DOMXPath($doc);

        $anchors = $xpath->query('//a');
        libxml_clear_errors();

        if (!$anchors) return null;

        $keywords = ['siguiente', 'next', 'capitulo siguiente', 'ep posterior', 'sig-ep', 'sig', 'episodio siguiente'];
        $disabledClasses = ['disabled', 'off', 'pointer-events-none', 'inactive', 'btn-disabled', 'cursor-not-allowed'];

        foreach ($anchors as $anchor) {
            /** @var \DOMElement $anchor */
            $text  = mb_strtolower(trim($anchor->textContent));
            $href  = trim($anchor->getAttribute('href'));
            $class = mb_strtolower(trim($anchor->getAttribute('class')));
            $ariaDisabled = mb_strtolower(trim($anchor->getAttribute('aria-disabled')));

            // A. Verificar si el enlace está deshabilitado
            if ($ariaDisabled === 'true' || $anchor->hasAttribute('disabled')) {
                continue;
            }

            foreach ($disabledClasses as $dClass) {
                if (str_contains($class, $dClass)) continue 2;
            }

            if (empty($href) || $href === '#' || str_contains($href, 'javascript:void(0)')) {
                continue;
            }

            // B. Verificar palabras clave en el texto o en el atributo href
            foreach ($keywords as $word) {
                if (str_contains($text, $word) || str_contains(mb_strtolower($href), $word)) {

                    // Convertir URLs relativas a absolutas
                    if (str_starts_with($href, '/')) {
                        $parse = parse_url($baseUrl);
                        return ($parse['scheme'] ?? 'https') . '://' . ($parse['host'] ?? '') . $href;
                    }

                    return $href;
                }
            }
        }

        return null;
    }

    private function generateSequentialUrl(string $url): ?string
    {
        // Incrementa el número del capítulo preservando sufijos como _01 si existen
        $nextUrl = preg_replace_callback('/(\d+)(_\d+)?$/', function ($matches) {
            $chapter = (int)$matches[1] + 1;
            $suffix = $matches[2] ?? '';
            return $chapter . $suffix;
        }, $url);

        return ($nextUrl !== $url) ? $nextUrl : null;
    }

    private function buildResultResponse(bool $isPrimary, bool $hasNext, ?string $nextUrl, string $notice): array
    {
        return [
            'is_primary' => $isPrimary,
            'has_next'   => $hasNext,
            'next_url'   => $nextUrl,
            'notice'     => $notice
        ];
    }
}
