<?php

namespace App\Services;

use GuzzleHttp\Client;

class JiraService
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => $projectKey = config('services.jira.base_url'),
            'headers' => [
                'Authorization' => 'Basic ' . base64_encode(config('services.jira.username') . ':' . config('services.jira.api_token')),
                'Accept' => 'application/json',
            ],
        ]);
    }

    /**
     * Fetch unreleased versions (releases) for the project.
     */
    public function getUnreleasedReleases()
    {
        $projectKey =
            $projectKey = config('services.jira.project_key');

        if (empty($projectKey)) {
            throw new \Exception('JIRA_BASE_URL or JIRA_PROJECT_KEY is not configured.');
        }

        // Fetch versions from the project
        $response = $this->client->get("/rest/api/3/project/{$projectKey}/versions");
        $versions = json_decode($response->getBody(), true);

        // Filter for unreleased versions
        return array_filter($versions, fn($version) => !$version['released']);
    }

    public function getReleaseDetails($releaseId)
    {
        $response = $this->client->get("/rest/api/3/version/{$releaseId}");

        return json_decode($response->getBody(), true);
    }

    /**
     * Fetch Critical and P0 epics in a release.
     */
    public function getEpicsInRelease($releaseKey, $priorities = ['Critical', 'P0'])
    {
        $priorityFilter = implode(',', $priorities);

        $response = $this->client->get("/rest/api/3/search", [
            'query' => [
                'jql' => "fixVersion = '{$releaseKey}' AND priority IN ({$priorityFilter}) AND issuetype = Epic ORDER BY summary ASC",
                'fields' => 'summary,priority,status',
            ],
        ]);

        return json_decode($response->getBody(), true)['issues'];
    }

    /**
     * Fetch all epics in a release.
     */
    public function getAllEpicsInRelease($releaseKey)
    {

        $response = $this->client->get("/rest/api/3/search", [
            'query' => [
                'jql' => "fixVersion = '{$releaseKey}' AND issuetype = Epic ORDER BY summary ASC",
                'fields' => 'summary,priority,status',
            ],
        ]);

        return json_decode($response->getBody(), true)['issues'];
    }

    /**
     * Fetch all issues in a release.
     */
    public function getIssuesInRelease($releaseKey)
    {
        $response = $this->client->get("/rest/api/3/search", [
            'query' => [
                'jql' => "fixVersion = '{$releaseKey}' ORDER BY status ASC",
                'fields' => 'status',
                'maxResults' => 1000,
            ],
        ]);

        return json_decode($response->getBody(), true)['issues'];
    }

    /**
     * Fetch details of a specific epic by its key.
     */
    public function getEpic($epicKey)
    {
        $response = $this->client->get("/rest/api/3/issue/{$epicKey}", [
            'query' => [
                'fields' => 'summary,priority,description',
                'expand' => 'renderedFields',
            ],
        ]);

        return json_decode($response->getBody(), true);
    }

    /**
     * Fetch child issues for a specific epic.
     */
    public function getChildIssues($epicKey)
    {
        $response = $this->client->get("/rest/api/3/search", [
            'query' => [
                'jql' => "'Epic Link' = '{$epicKey}' ORDER BY summary ASC",
                'fields' => 'summary,description,status',
            ],
        ]);

        return json_decode($response->getBody(), true)['issues'];
    }

    public function getProjectFeatures()
    {
        $baseUrl = config('services.jira.base_url');
        $projectKey = config('services.jira.project_key');

        if (empty($baseUrl) || empty($projectKey)) {
            throw new \Exception('JIRA configuration is not properly set in services.php.');
        }

        $url = "{$baseUrl}/rest/api/3/project/{$projectKey}/components";
        $response = $this->client->get($url);

        return json_decode($response->getBody(), true);
    }

    public function getEpicsByComponent($componentId)
    {
        $baseUrl = config('services.jira.base_url');
        $projectKey = config('services.jira.project_key');

        if (empty($baseUrl) || empty($projectKey)) {
            throw new \Exception('JIRA configuration is not properly set in services.php.');
        }

        // Fetch component details
        $componentUrl = "{$baseUrl}/rest/api/3/component/{$componentId}";
        $componentResponse = $this->client->get($componentUrl);
        $componentDetails = json_decode($componentResponse->getBody(), true);

        // Fetch epics
        $epicJql = "project = '{$projectKey}' AND component = '{$componentId}' AND issuetype = 'Epic'";
        $epicResponse = $this->client->get("{$baseUrl}/rest/api/3/search", [
            'query' => ['jql' => $epicJql, 'maxResults' => 1000]
        ]);
        $epics = json_decode($epicResponse->getBody(), true)['issues'] ?? [];

        // Group epics by release
        $groupedEpics = [];
        foreach ($epics as $epic) {
            $release = $epic['fields']['fixVersions'][0]['name'] ?? 'Not in a Release';
            $groupedEpics[$release][] = $epic;
        }

        // Fetch bugs
        $bugJql = "project = '{$projectKey}' AND component = '{$componentId}' AND issuetype = 'Bug'";
        $bugResponse = $this->client->get("{$baseUrl}/rest/api/3/search", [
            'query' => ['jql' => $bugJql, 'maxResults' => 1000]
        ]);
        $bugs = json_decode($bugResponse->getBody(), true)['issues'] ?? [];

        // Fetch requests
        $requestJql = "project = '{$projectKey}' AND component = '{$componentId}' AND issuetype = 'Request'";
        $requestResponse = $this->client->get("{$baseUrl}/rest/api/3/search", [
            'query' => ['jql' => $requestJql, 'maxResults' => 1000]
        ]);
        $requests = json_decode($requestResponse->getBody(), true)['issues'] ?? [];

        return [
            'component' => $componentDetails,
            'epics' => $groupedEpics,
            'bugs' => $bugs,
            'requests' => $requests,
        ];
    }

    public function getAllCustomers()
    {
        $baseUrl = config('services.jira.base_url');
        $fieldKey = 'customfield_10506'; // Replace with your actual custom field key
        $contextId = '10676'; // Replace with your actual context ID

        if (empty($baseUrl) || empty($fieldKey) || empty($contextId)) {
            throw new \Exception('JIRA_BASE_URL, fieldKey, or contextId is not properly configured.');
        }

        // API endpoint to fetch options for the multi-select custom field
        $url = "{$baseUrl}/rest/api/3/field/{$fieldKey}/context/{$contextId}/option";

        // Send the request to Jira
        $response = $this->client->get($url);
        $data = json_decode($response->getBody(), true);

        // Check if the response contains the 'values' array
        if (!isset($data['values']) || !is_array($data['values'])) {
            throw new \Exception('Unexpected response from Jira API. Missing "values" key.');
        }

        // Process the values array and return as a key-value array
        $customers = [];
        foreach ($data['values'] as $option) {
            if (isset($option['id'], $option['value'])) {
                $customers[$option['id']] = $option['value'];
            }
        }

        return $customers;
    }

    public function getCustomerDetails($customerId)
    {
        $baseUrl = config('services.jira.base_url');
        $projectKey = config('services.jira.project_key');
        $customFieldKey = 'Customers Related to Commitment';

        if (empty($baseUrl) || empty($projectKey) || empty($customFieldKey)) {
            throw new \Exception('JIRA_BASE_URL, projectKey, or customFieldKey is not properly configured.');
        }

        // Fetch all available customers from the custom field context
        $customFieldContextId = '10676'; // Replace with the actual context ID
        $customerOptionsUrl = "{$baseUrl}/rest/api/3/field/customfield_10506/context/{$customFieldContextId}/option";
        $customerOptionsResponse = $this->client->get($customerOptionsUrl);
        $customerOptions = json_decode($customerOptionsResponse->getBody(), true)['values'] ?? [];

        // Find the customer details by ID
        $customer = collect($customerOptions)->firstWhere('id', $customerId);
        if (!$customer) {
            throw new \Exception('Customer not found.');
        }

        // Base JQL for the customer
        $customerJql = "project = '{$projectKey}' AND cf[10506] = {$customerId}";

        // Fetch epics for this customer
        $epicJql = "{$customerJql} AND issuetype = 'Epic'";
        $epicResponse = $this->client->get("{$baseUrl}/rest/api/3/search", [
            'query' => ['jql' => $epicJql, 'maxResults' => 1000]
        ]);
        $epics = json_decode($epicResponse->getBody(), true)['issues'] ?? [];

        // Group epics by fix version
        $groupedEpics = [];
        foreach ($epics as $epic) {
            $fixVersions = $epic['fields']['fixVersions'] ?? [];
            if (empty($fixVersions)) {
                $groupedEpics['Not in a Fix Version'][] = $epic;
            } else {
                foreach ($fixVersions as $fixVersion) {
                    $groupedEpics[$fixVersion['name']][] = $epic;
                }
            }
        }

        // Fetch bugs
        $bugJql = "{$customerJql} AND issuetype = 'Bug'";
        $bugResponse = $this->client->get("{$baseUrl}/rest/api/3/search", [
            'query' => ['jql' => $bugJql, 'maxResults' => 1000]
        ]);
        $bugs = json_decode($bugResponse->getBody(), true)['issues'] ?? [];

        // Fetch requests
        $requestJql = "{$customerJql} AND issuetype = 'Request'";
        $requestResponse = $this->client->get("{$baseUrl}/rest/api/3/search", [
            'query' => ['jql' => $requestJql, 'maxResults' => 1000]
        ]);
        $requests = json_decode($requestResponse->getBody(), true)['issues'] ?? [];

        return [
            'customer' => [
                'id' => $customer['id'],
                'name' => $customer['value'],
                'description' => $customer['description'] ?? null,
            ],
            'epics' => $groupedEpics,
            'bugs' => $bugs,
            'requests' => $requests,
        ];
    }
}
