<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\JiraService;
use App\Models\Issue;
use App\Models\FixVersion;
use App\Models\Feature;
use App\Models\Customer;
use App\Models\ProductManager;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Log;

class JiraSyncCommand extends Command
{
    protected $signature = 'jira:sync-all';
    protected $description = 'Pull all relevant Jira data and store it locally';

    protected $jira;

    public function __construct(JiraService $jira)
    {
        parent::__construct();
        $this->jira = $jira;
    }

    public function handle()
    {
        Log::channel('jira_sync')->info('Starting Jira sync...');

        // 1. Sync fix versions
        $versions = $this->jira->getAllFixVersions();
        foreach ($versions as $version) {
            FixVersion::updateOrCreate(
                ['jira_id' => $version['id']],
                [
                    'name' => $version['name'],
                    'release_date' => $version['releaseDate'] ?? null,
                    'released' => $version['released'] ?? false,
                ]
            );
        }

        // 2. Sync components
        $components = $this->jira->getProjectFeatures();
        foreach ($components as $component) {
            Feature::updateOrCreate(
                ['jira_id' => $component['id']],
                [
                    'name' => $component['name'],
                    'description' => $component['description'] ?? null,
                ]
            );
        }

        // 3. Sync customers
        $customers = $this->jira->getAllCustomers();
        foreach ($customers as $id => $name) {
            Customer::updateOrCreate(
                ['jira_id' => $id],
                ['name' => $name]
            );
        }

        // 4. Sync issues
        $issues = $this->jira->getAllProjectIssues();

        foreach ($issues as $issue) {
            $fields = $issue['fields'] ?? [];
            $rendered = $issue['renderedFields'] ?? [];

            $description = $rendered['description'] ?? null;
            $description = is_string($description)
                ? htmlspecialchars($description, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')
                : (is_array($description) ? json_encode($description) : null);

            $localIssue = Issue::updateOrCreate(
                ['jira_key' => $issue['key']],
                [
                    'summary' => $fields['summary'] ?? 'No summary',
                    'description' => $description,
                    'type' => $fields['issuetype']['name'] ?? 'Unknown',
                    'status' => $fields['status']['name'] ?? 'Unknown',
                    'priority' => $fields['priority']['name'] ?? null,
                    'size' => $fields['customfield_10507']['value'] ?? null,
                    'release_commit_status' => $fields['customfield_10638']['value'] ?? null,
                ]
            );

            $fixVersions = $fields['fixVersions'] ?? [];
            $fixVersionIds = [];

            foreach ($fixVersions as $fix) {
                if (!isset($fix['id'])) {
                    continue;
                }

                $fv = FixVersion::updateOrCreate(
                    ['jira_id' => $fix['id']],
                    [
                        'name' => $fix['name'],
                        'release_date' => $fix['releaseDate'] ?? null,
                        'released' => $fix['released'] ?? false,
                    ]
                );

                $fixVersionIds[] = $fv->id;
            }

            $localIssue->fixVersions()->sync($fixVersionIds);

            // Link multiple components
            $components = $fields['components'] ?? [];
            $componentIds = [];

            foreach ($components as $component) {
                if (!isset($component['id'])) {
                    continue;
                }

                $localComponent = Feature::updateOrCreate(
                    ['jira_id' => $component['id']],
                    ['name' => $component['name'] ?? 'Unnamed']
                );

                $componentIds[] = $localComponent->id;
            }

            $localIssue->components()->sync($componentIds);

            // Link multiple customers
            $customerObjects = $fields['customfield_10506'] ?? [];
            $customerIds = [];

            foreach ($customerObjects as $customer) {
                if (!isset($customer['id'])) {
                    continue;
                }

                $localCustomer = Customer::updateOrCreate(
                    ['jira_id' => $customer['id']],
                    ['name' => $customer['value'] ?? 'Unnamed']
                );

                $customerIds[] = $localCustomer->id;
            }

            $localIssue->customers()->sync($customerIds);

            // Link product manager
            $pmData = $fields['customfield_10308'] ?? null;

            if ($pmData && !empty($pmData['accountId'])) {
                $avatarUrl = $pmData['avatarUrls']['48x48'] ?? null;

                $pm = ProductManager::updateOrCreate(
                    ['account_id' => $pmData['accountId']],
                    [
                        'name' => $pmData['displayName'] ?? 'Unassigned',
                        'avatar_url' => $avatarUrl,
                    ]
                );

                $localIssue->product_manager_id = $pm->id;
                $localIssue->save();
            }
        }

        Log::channel('jira_sync')->info('Jira sync complete. The local DB now holds the power.');
    }

    public function schedule(Schedule $schedule): void
    {
        $schedule->command($this->getName())->hourly();
    }
}
