<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;

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

    private function cachedApiCall(string $cacheKey, callable $apiCall, int $ttl = 600)
    {
        return Cache::remember($cacheKey, now()->addSeconds($ttl), $apiCall);
    }

    /**
     * Fetch unreleased versions (releases) for the project.
     */
    public function getUnreleasedReleases()
    {
        $cacheKey = "jira_unreleased_releases";
        $projectKey = config('services.jira.project_key');

        if (empty($projectKey)) {
            throw new \Exception('JIRA_BASE_URL or JIRA_PROJECT_KEY is not configured.');
        }

        return $this->cachedApiCall(
            $cacheKey,
            function () use ($projectKey) {
                $response = $this->client->get("/rest/api/3/project/{$projectKey}/versions");
                $versions = json_decode($response->getBody(), true);
                return array_filter($versions, fn($version) => !$version['released']);
            }
        );
    }

    public function getReleaseById($releaseId)
    {
        $cacheKey = "jira_release_{$releaseId}";
        return $this->cachedApiCall(
            $cacheKey,
            function () use ($releaseId) {
                $response = $this->client->get("/rest/api/3/version/{$releaseId}");
                return json_decode($response->getBody(), true);
            }
        );
    }

    public function getReleaseDetails($releaseId)
    {
        $cacheKey = "jira_release_details_{$releaseId}";

        return $this->cachedApiCall(
            $cacheKey,
            function () use ($releaseId) {
                $response = $this->client->get("/rest/api/3/version/{$releaseId}");
                return json_decode($response->getBody(), true);
            }
        );
    }

    /**
     * Fetch Critical and P0 epics in a release.
     */
    public function getEpicsInRelease($releaseKey, $priorities = ['Critical', 'P0'])
    {
        $cacheKey = "jira_epic_in_release_{$releaseKey}";

        return $this->cachedApiCall(
            $cacheKey,
            function () use ($releaseKey, $priorities) {
                $priorityFilter = implode(',', $priorities);
                $response = $this->client->get("/rest/api/3/search", [
                    'query' => [
                        'jql' => "fixVersion = '{$releaseKey}' AND priority IN ({$priorityFilter}) AND issuetype = Epic ORDER BY summary ASC",
                        'fields' => 'summary,priority,status,customfield_10473,labels,customfield_10506',
                    ],
                ]);

                return json_decode($response->getBody(), true)['issues'];
            }
        );
    }

    /**
     * Fetch all epics in a release.
     */
    public function getAllEpicsInRelease($releaseKey)
    {
        $cacheKey = "jira_all_epics_in_release_{$releaseKey}";

        return $this->cachedApiCall(
            $cacheKey,
            function () use ($releaseKey) {
                $response = $this->client->get("/rest/api/3/search", [
                    'query' => [
                        'jql' => "fixVersion = '{$releaseKey}' AND issuetype = Epic ORDER BY summary ASC",
                        'fields' => 'summary,priority,status,customfield_10473,labels,customfield_10506,customfield_10507',
                    ],
                ]);
                return json_decode($response->getBody(), true)['issues'];
            }
        );
    }

    /**
     * Fetch all issues in a release.
     */
    public function getIssuesInRelease($releaseKey)
    {
        $cacheKey = "jira_issues_in_release_{$releaseKey}";

        return $this->cachedApiCall(
            $cacheKey,
            function () use ($releaseKey) {
                $startAt = 0;
                $maxResults = 100;
                $allIssues = [];

                do {
                    $response = $this->client->get("/rest/api/3/search", [
                        'query' => [
                            'jql' => "fixVersion = '{$releaseKey}' ORDER BY created DESC",
                            'fields' => 'summary,priority,status,labels',
                            'startAt' => $startAt,
                            'maxResults' => $maxResults,
                        ],
                    ]);

                    $data = json_decode($response->getBody(), true);

                    if (!isset($data['issues'])) {
                        break; // Exit if there are no issues
                    }

                    $allIssues = array_merge($allIssues, $data['issues']);
                    $startAt += $maxResults;
                } while (count($data['issues']) === $maxResults);

                return $allIssues;
            }
        );
    }

    public function getOpenEpics()
    {
        $cacheKey = 'jira_open_epics';
        $cacheDuration = now()->addMinutes(30); // Cache for 30 minutes

        return cache()->remember($cacheKey, $cacheDuration, function () {
            $allEpics = [];
            $startAt = 0; // Pagination starting index
            $maxResults = 100; // Jira API's page size limit

            do {
                // Fetch a single page of results
                $response = $this->client->get('/rest/api/3/search', [
                    'query' => [
                        'jql' => "project = AA AND issuetype = Epic AND statusCategory != Done ORDER BY priority DESC, summary ASC",
                        'fields' => 'summary,priority,status,parent,customfield_10473,customfield_10507',
                        'startAt' => $startAt,
                        'maxResults' => $maxResults,
                    ],
                ]);

                $data = json_decode($response->getBody(), true);
                $issues = $data['issues'] ?? [];

                // Append current page of issues to the full result set
                $allEpics = array_merge($allEpics, $issues);

                // Increment the starting index for the next page
                $startAt += $maxResults;
            } while (count($issues) === $maxResults); // Continue fetching until a page has fewer than maxResults

            return $allEpics; // Return all fetched epics
        });
    }

    /**
     * Fetch details of a specific epic by its key.
     */
    public function getEpicByKey($epicKey)
    {
        $cacheKey = "jira_epic_{$epicKey}";
        return $this->cachedApiCall(
            $cacheKey,
            function () use ($epicKey) {
                $response = $this->client->get("/rest/api/3/issue/{$epicKey}", [
                    'query' => [
                        'fields' => 'summary,priority,status,customfield_10473,customfield_10506,customfield_10308,description,components,fixVersions',
                        'expand' => 'renderedFields',
                    ],
                ]);
                return json_decode($response->getBody(), true);
            }
        );
    }

    /**
     * Fetch child issues for a specific epic.
     */
    public function getChildIssues($epicKey)
    {
        $cacheKey = "jira_epic_child_issues_{$epicKey}";

        return $this->cachedApiCall($cacheKey, function () use ($epicKey) {
            $response = $this->client->get("/rest/api/3/search", [
                'query' => [
                    'jql' => "'Epic Link' = '{$epicKey}' ORDER BY summary ASC",
                    'fields' => 'summary,description,status',
                ],
            ]);
            return json_decode($response->getBody(), true)['issues'];
        });
    }

    public function getProjectFeatures()
    {
        $cacheKey = 'jira_project_features';

        return $this->cachedApiCall(
            $cacheKey,
            function () {
                $baseUrl = config('services.jira.base_url');
                $projectKey = config('services.jira.project_key');

                if (empty($baseUrl) || empty($projectKey)) {
                    throw new \Exception('JIRA configuration is not properly set in services.php.');
                }

                $url = "{$baseUrl}/rest/api/3/project/{$projectKey}/components";
                $response = $this->client->get($url);

                return json_decode($response->getBody(), true);
            }
        );
    }

    public function getEpicsByFeature($featureId)
    {
        $cacheKey = "jira_epics_by_component_{$featureId}";

        return $this->cachedApiCall(
            $cacheKey,
            function () use ($featureId) {
                $baseUrl = config('services.jira.base_url');
                $projectKey = config('services.jira.project_key');

                if (empty($baseUrl) || empty($projectKey)) {
                    throw new \Exception('JIRA configuration is not properly set in services.php.');
                }

                // Fetch component details
                $componentUrl = "{$baseUrl}/rest/api/3/component/{$featureId}";
                $componentResponse = $this->client->get($componentUrl);
                $componentDetails = json_decode($componentResponse->getBody(), true);

                // Fetch epics
                $epicJql = "project = '{$projectKey}' AND component = '{$featureId}' AND issuetype = 'Epic'";
                $epicResponse = $this->client->get("{$baseUrl}/rest/api/3/search", [
                    'query' => ['jql' => $epicJql, 'maxResults' => 1000]
                ]);
                $epics = json_decode($epicResponse->getBody(), true)['issues'] ?? [];

                // Prepare release date mapping and group epics
                $fixVersionDates = [];
                $groupedEpics = [];
                foreach ($epics as $epic) {
                    $fixVersions = $epic['fields']['fixVersions'] ?? [];
                    if (empty($fixVersions)) {
                        $groupedEpics['Not in a Fix Version'][] = $epic;
                    } else {
                        foreach ($fixVersions as $fixVersion) {
                            $fixVersionDates[$fixVersion['name']] = $fixVersion['releaseDate'] ?? '9999-12-31';
                            $groupedEpics[$fixVersion['name']][] = $epic;
                        }
                    }
                }

                // Ensure "Not in a Fix Version" exists for consistent usage
                if (!isset($groupedEpics['Not in a Fix Version'])) {
                    $groupedEpics['Not in a Fix Version'] = [];
                }

                // Sort grouped epics by release date
                uksort($groupedEpics, function ($a, $b) use ($fixVersionDates) {
                    return strtotime($fixVersionDates[$a] ?? '9999-12-31') <=> strtotime($fixVersionDates[$b] ?? '9999-12-31');
                });

                // Fetch bugs
                $bugJql = "project = '{$projectKey}' AND component = '{$featureId}' AND issuetype = 'Bug'";
                $bugResponse = $this->client->get("{$baseUrl}/rest/api/3/search", [
                    'query' => ['jql' => $bugJql, 'maxResults' => 1000]
                ]);
                $bugs = json_decode($bugResponse->getBody(), true)['issues'] ?? [];

                // Fetch requests
                $requestJql = "project = '{$projectKey}' AND component = '{$featureId}' AND issuetype = 'Request'";
                $requestResponse = $this->client->get("{$baseUrl}/rest/api/3/search", [
                    'query' => ['jql' => $requestJql, 'maxResults' => 1000]
                ]);
                $requests = json_decode($requestResponse->getBody(), true)['issues'] ?? [];

                // Calculate counts
                $counts = [
                    'epics' => array_reduce($groupedEpics, fn($carry, $epics) => $carry + count($epics), 0),
                    'bugs' => count($bugs),
                    'requests' => count($requests),
                ];

                return [
                    'component' => $componentDetails,
                    'counts' => $counts,
                    'epics' => $groupedEpics,
                    'bugs' => $bugs,
                    'requests' => $requests,
                ];
            }
        );
    }

    public function getAllCustomers()
    {
        $cacheKey = 'jira_all_customers';

        return $this->cachedApiCall(
            $cacheKey,
            function () {
                $baseUrl = config('services.jira.base_url');
                $fieldKey = 'customfield_10506';
                $contextId = '10676';

                $url = "{$baseUrl}/rest/api/3/field/{$fieldKey}/context/{$contextId}/option";
                $response = $this->client->get($url);
                $data = json_decode($response->getBody(), true);

                if (!isset($data['values']) || !is_array($data['values'])) {
                    throw new \Exception('Unexpected response from Jira API.');
                }

                $customers = [];
                foreach ($data['values'] as $option) {
                    $customers[$option['id']] = $option['value'];
                }
                return $customers;
            }
        );
    }

    public function getCustomerDetails($customerId)
    {
        $cacheKey = "jira_customer_detail_{$customerId}";

        return $this->cachedApiCall($cacheKey, function () use ($customerId) {
            $baseUrl = config('services.jira.base_url');
            $projectKey = config('services.jira.project_key');
            $customFieldKey = 'Customers Related to Commitment';

            if (empty($baseUrl) || empty($projectKey) || empty($customFieldKey)) {
                throw new \Exception('JIRA_BASE_URL, projectKey, or customFieldKey is not properly configured.');
            }

            // Fetch all available customers
            $customFieldContextId = '10676'; // Replace with the actual context ID
            $customerOptionsUrl = "{$baseUrl}/rest/api/3/field/customfield_10506/context/{$customFieldContextId}/option";
            $customerOptionsResponse = $this->client->get($customerOptionsUrl);
            $customerOptions = json_decode($customerOptionsResponse->getBody(), true)['values'] ?? [];

            $customer = collect($customerOptions)->firstWhere('id', $customerId);
            if (!$customer) {
                throw new \Exception('Customer not found.');
            }

            $customerJql = "project = '{$projectKey}' AND cf[10506] = {$customerId}";

            // Fetch epics
            $epicJql = "{$customerJql} AND issuetype = 'Epic'";
            $epicResponse = $this->client->get("{$baseUrl}/rest/api/3/search", [
                'query' => ['jql' => $epicJql, 'maxResults' => 1000]
            ]);
            $epics = json_decode($epicResponse->getBody(), true)['issues'] ?? [];

            // Group epics by fix version and sort by release date
            $groupedEpics = [];
            $fixVersionDates = [];

            foreach ($epics as $epic) {
                $fixVersions = $epic['fields']['fixVersions'] ?? [];
                if (empty($fixVersions)) {
                    if (!isset($groupedEpics['Not in a Fix Version'])) {
                        $groupedEpics['Not in a Fix Version'] = [];
                        $fixVersionDates['Not in a Fix Version'] = '9999-12-31'; // Assign distant future date
                    }
                    $groupedEpics['Not in a Fix Version'][] = $epic;
                } else {
                    foreach ($fixVersions as $fixVersion) {
                        $fixVersionName = $fixVersion['name'];
                        $fixVersionDate = $fixVersion['releaseDate'] ?? '9999-12-31';
                        $fixVersionDates[$fixVersionName] = $fixVersionDate;

                        if (!isset($groupedEpics[$fixVersionName])) {
                            $groupedEpics[$fixVersionName] = [];
                        }
                        $groupedEpics[$fixVersionName][] = $epic;
                    }
                }
            }

            // Sort grouped epics by release date
            uksort($groupedEpics, function ($a, $b) use ($fixVersionDates) {
                return strtotime($fixVersionDates[$a]) <=> strtotime($fixVersionDates[$b]);
            });

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

            // Add counts
            $counts = [
                'epics' => count($epics),
                'bugs' => count($bugs),
                'requests' => count($requests),
            ];

            return [
                'customer' => [
                    'id' => $customer['id'],
                    'name' => $customer['value'],
                    'description' => $customer['description'] ?? null,
                ],
                'counts' => $counts,
                'epics' => $groupedEpics,
                'bugs' => $bugs,
                'requests' => $requests,
            ];
        });
    }
}
