<?php


namespace PluginCollectionGenerator;


use http\Exception\RuntimeException;

class RouteConstructor
{
    private ?string $namespace;
    private array $collection;

    public function __construct(?string $namespace)
    {
        if (!$namespace) {
            throw new RuntimeException('No Plugin namespace argv 2.');
        }
        $this->namespace = $namespace;
        $this->collection = [
            'info' => [
                'name' => $namespace,
                'schema' => 'https://schema.getpostman.com/json/collection/v2.1.0/collection.json'
            ]
        ];
    }

    public function getCollection(array $routesList): array
    {
        foreach ($routesList as $key => $routes) {
            $items = [];
            if (array_key_exists('name', $routes)) {
                $items['name'] = $routes['name'] ?: "Route name: $key not found";
            }
            foreach ($routes['route'] as $route) {
                $path = $this->getRoute($route);
                $item = [
                    'name' => $path,
                    'request' => [
                        'method' => $this->getMethod($route) ?: 'GET',
                        'header' => [
                            [
                                'key' => 'Authorization',
                                'value' => 'Bearer {{authorization_token}}'
                            ]
                        ],
                        'url' => [
                            'raw' => "{{base_url}}$path",
                            'host' => [
                                '{{base_url}}'
                            ],
                            'path' => explode('/', $path)
                        ]
                    ],
                    'response' => []
                ];

                $items['item'][] = $item;
            }

            $items['auth'] = [
                'type' => 'bearer',
                'bearer' => [
                    [
                        'key' => 'token',
                        'value' => '{{authorization_token}}',
                        'type' => 'string'
                    ]
                ]
            ];
            $this->collection['item'][] = $items;
        }

        return $this->collection;
    }

    private function getRoute(string $route): string
    {
        $prefix = false;
        if (str_contains($route, 'PluginConfiguration::PLUGIN_NAME') || @str_contains($route, $this->namespace)) {
            $prefix = true;
        }
        $byApostrophe = explode("'", $route)[1];
        if ($byApostrophe) {
            return $prefix ? "/rest/{$this->namespace}{$byApostrophe}" : "/rest{$byApostrophe}";
        }
        $byQuotationMark = explode('"', $route)[1];
        if ($byQuotationMark) {
            return $prefix ? "/rest/{$this->namespace}{$byQuotationMark}" : "/rest{$byQuotationMark}";
        }
        return '/unknown/route';
    }

    private function getMethod(string $route): ?string
    {
        switch ($route) {
            case (bool)preg_match('/\b(->get)\b/', $route):
            {
                return 'GET';
            }
            case (bool)preg_match('/\b(->post)\b/', $route):
            {
                return 'POST';
            }
            case (bool)preg_match('/\b(->put)\b/', $route):
            {
                return 'PUT';
            }
            case (bool)preg_match('/\b(->patch)\b/', $route):
            {
                return 'PATCH';
            }
            case (bool)preg_match('/\b(->delete)\b/', $route):
            {
                return 'DELETE';
            }
        }

        return null;
    }
}