<?php
/**
 * Cloudflare API Integration Helper
 * Allows programmatic management of Cloudflare rules
 */

class CloudflareAPI {
    private string $api_key;
    private string $zone_id;
    private string $email;
    private string $base_url = 'https://api.cloudflare.com/client/v4';
    
    public function __construct(string $api_key, string $zone_id, string $email) {
        $this->api_key = $api_key;
        $this->zone_id = $zone_id;
        $this->email = $email;
    }
    
    /**
     * Make API request to Cloudflare
     */
    private function request(string $method, string $endpoint, array $data = []): array {
        $url = $this->base_url . $endpoint;
        
        $headers = [
            'Authorization: Bearer ' . $this->api_key,
            'Content-Type: application/json',
        ];
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        
        if (!empty($data)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        $result = json_decode($response, true) ?? [];
        $result['http_code'] = $http_code;
        
        return $result;
    }
    
    /**
     * Create firewall rule for rate limiting
     */
    public function create_rate_limit_rule(string $path, int $requests, int $window): array {
        $filter_expression = "(http.request.uri.path eq \"$path\")";
        
        $data = [
            'filter' => [
                'expression' => $filter_expression,
            ],
            'action' => 'challenge',
            'description' => "Rate limit: $requests requests per $window seconds for $path",
            'priority' => 1,
        ];
        
        return $this->request('POST', "/zones/{$this->zone_id}/firewall/rules", $data);
    }
    
    /**
     * Create bypass rule for login pages
     */
    public function create_bypass_rule(string $path): array {
        $filter_expression = "(http.request.uri.path eq \"$path\")";
        
        $data = [
            'filter' => [
                'expression' => $filter_expression,
            ],
            'action' => 'allow',
            'description' => "Bypass Cloudflare challenges for $path",
            'priority' => 0, // Higher priority than rate limits
        ];
        
        return $this->request('POST', "/zones/{$this->zone_id}/firewall/rules", $data);
    }
    
    /**
     * Create caching rule
     */
    public function create_cache_rule(string $path, int $ttl): array {
        $filter_expression = "(http.request.uri.path eq \"$path\")";
        
        $data = [
            'targets' => [
                'http.request.uri.path' => [
                    'name' => 'http.request.uri.path',
                    'constraint' => [
                        'operator' => 'eq',
                        'value' => $path,
                    ],
                ],
            ],
            'actions' => [
                [
                    'id' => 'set_cache_settings',
                    'value' => 'cache',
                ],
            ],
            'action_parameters' => [
                'cache' => [
                    'default_ttl' => $ttl,
                    'browser_ttl' => $ttl,
                ],
            ],
            'description' => "Cache $path for $ttl seconds",
            'enabled' => true,
        ];
        
        return $this->request('POST', "/zones/{$this->zone_id}/rulesets/phases/http_response_headers_transform/entrypoint", $data);
    }
    
    /**
     * Get all firewall rules
     */
    public function get_firewall_rules(): array {
        return $this->request('GET', "/zones/{$this->zone_id}/firewall/rules");
    }
    
    /**
     * Enable DDoS protection
     */
    public function enable_ddos_protection(): array {
        $data = [
            'value' => 'high',
        ];
        
        return $this->request('PATCH', "/zones/{$this->zone_id}/settings/security_level", $data);
    }
    
    /**
     * Enable Web Application Firewall (WAF)
     */
    public function enable_waf(): array {
        $data = [
            'value' => 'on',
        ];
        
        return $this->request('PATCH', "/zones/{$this->zone_id}/settings/waf", $data);
    }
    
    /**
     * Purge cache for specific paths
     */
    public function purge_cache(array $paths): array {
        $data = [
            'files' => $paths,
        ];
        
        return $this->request('POST', "/zones/{$this->zone_id}/purge_cache", $data);
    }
    
    /**
     * Get zone settings
     */
    public function get_zone_info(): array {
        return $this->request('GET', "/zones/{$this->zone_id}");
    }
}

// Helper function to initialize Cloudflare API
function get_cloudflare_api(): ?CloudflareAPI {
    $api_key = getenv('CLOUDFLARE_API_KEY');
    $zone_id = getenv('CLOUDFLARE_ZONE_ID');
    $email = getenv('CLOUDFLARE_EMAIL');
    
    if (empty($api_key) || empty($zone_id) || empty($email)) {
        return null;
    }
    
    return new CloudflareAPI($api_key, $zone_id, $email);
}
