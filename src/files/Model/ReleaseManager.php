<?php

declare(strict_types=1);

namespace MaksimRamashka\Deploy\Model;

use Exception;
use Phar;

class ReleaseManager
{
    private const RELEASE_URL = 'https://api.github.com/repos/Md5Inj/magento-deploy/releases/latest';

    /**
     * @var Output
     */
    private Output $output;

    /**
     * @var HttpClient
     */
    private HttpClient $httpClient;

    /**
     * @var FileManagement
     */
    private FileManagement $fileManagement;

    public function __construct()
    {
        $this->output = new Output();
        $this->httpClient = new HttpClient();
        $this->fileManagement = new FileManagement();
    }

    /**
     * Return latest version
     *
     * @return string
     * @throws Exception
     */
    public function getLatestVersion(): string
    {
        $httpClient = new HttpClient();

        $response = $httpClient->get(self::RELEASE_URL);
        if (empty($response)) {
            return '';
        }

        $data = json_decode($response, true);
        if ($data === null) {
            return '';
        }

        return $data['tag_name'];
    }

    /**
     * Download the latest release and place it instead of the current
     *
     * @return void
     * @throws Exception
     */
    public function updateToLatest(): void
    {
        $downloadUrl = $this->getLatestReleaseUrl();

        // Download the latest PHAR file
        $pharContent = $this->httpClient->get($downloadUrl);
        if (empty($pharContent)) {
            $this->output->writeError('Failed to download update.');
            return;
        }

        // Download the latest PHAR file
        $tempFileName = 'deploy_temp.phar';
        $this->output->writeInfo('Downloading the latest release');
        if (!$this->httpClient->downloadFile($downloadUrl, $tempFileName)) {
            $this->output->writeError('Failed to download PHAR file.');
            return;
        }

        // Replace the old PHAR file with the new one
        $this->output->writeInfo('Installing new release');
        $currentPath = dirname(Phar::running(false));
        $currentPharPath = Phar::running(false);
        if (empty($currentPharPath)) {
            throw new Exception('Cannot find current phar path');
        }

        $this->fileManagement->deleteFile($currentPharPath);
        $this->fileManagement->moveFile($currentPath . '/' . $tempFileName, $currentPharPath);
        $this->output->writeSuccess('New release successfully installed');
    }

    /**
     * Return latest release download URL
     *
     * @return string
     * @throws Exception
     */
    private function getLatestReleaseUrl(): string
    {
        // Fetch the latest release information
        $releaseInto = $this->httpClient->get(self::RELEASE_URL);
        if (empty($releaseInto)) {
            throw new Exception('Failed to fetch release information.');
        }

        // Parse the JSON response
        $releaseInto = json_decode($releaseInto, true);
        if ($releaseInto === null) {
            throw new Exception('Failed to parse release information.');
        }

        // Find the download URL for the PHAR file
        $downloadUrl = '';
        foreach ($releaseInto['assets'] as $asset) {
            if (str_contains($asset['name'], '.phar')) {
                $downloadUrl = $asset['browser_download_url'];
                break;
            }
        }

        if (empty($downloadUrl)) {
            throw new Exception('No PHAR file found in the latest release.');
        }

        return $downloadUrl;
    }
}
