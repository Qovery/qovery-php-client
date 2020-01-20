<?php
require("DatabaseConfiguration.php");

class Qovery
{
    const ENV_JSON_B64 = "QOVERY_JSON_B64";
    const ENV_IS_PRODUCTION = "QOVERY_IS_PRODUCTION";
    const ENV_BRANCH_NAME = "QOVERY_BRANCH_NAME";

    /** @var mixed $configuration Configuration object, parsed from JSON */
    public $configuration;

    /** @var DatabaseConfiguration $databases */
    public $databasesConfiguration;

    /**
     * @param string configurationFilePath path to the configuration file to use, in case environment variable self::ENV_JSON_B64 is not defined
     */
    public function  __construct($configurationFilePath = null)
    {
        $config = $this->getConfigurationFromEnvironmentVariable(self::ENV_JSON_B64);
        if (is_null($config)) {
            $config = $this->getConfigurationFromFile($configurationFilePath);
        }
        $this->configuration = $config;
        $this->databasesConfiguration = $this::parseDatabases($config);
    }

    /**
     * Get configuration JSON object from environment variable
     * @param string $name variable name
     * @return Object|null
     */
    public function getConfigurationFromEnvironmentVariable($name)
    {
        if (is_null($name)) {
            return null;
        }

        $b64Json = getenv($name);
        if (!$b64Json) {
            return null;
        }

        $jsonText = base64_decode($b64Json);
        if (!$jsonText) {
            return null;
        }

        return json_decode($jsonText, true);
    }

    /**
     * Get configuration JSON object from configuration file
     * @param string $filePath
     * @return Object|null
     */
    public function getConfigurationFromFile($filePath)
    {
        if (!isset($filePath) || empty($filePath)) {
            return null;
        }

        try {
            $jsonText = file_get_contents($filePath);
            return json_decode($jsonText, true);
        } catch (Exception $e) {
            echo 'Caught exception while loading Qovery configuration file: ', $e->getMessage(), "\n";
            return null;
        }
    }

    /**
     * Get current git branch name
     * @return string|null
     */
    public function getBranchName()
    {
        $value = getenv(self::ENV_BRANCH_NAME);
        return (!isset($value)) ? null : $value;
    }

    /**
     * Is the current branch a production environment?
     * @return bool
     */
    public function isProduction()
    {
        $value = getenv(self::ENV_IS_PRODUCTION);

        if (!isset($value) || empty($value) || strtolower($value) === 'false') {
            return FALSE;
        }

        return TRUE;
    }

    /**
     * Parse available databases
     * @return DatabaseConfiguration[]|[]
     */
    private static function parseDatabases($configuration)
    {
        if (!$configuration) {
            return [];
        }

        $databases = $configuration['databases'];
        if (!$databases) {
            return [];
        }

        $dbs = [];
        foreach ($databases as $jsonDbObject) {
            $db = new DatabaseConfiguration($jsonDbObject);
            array_push($dbs, $db);
        }
        return $dbs;
    }

    /**
     * Get database by its name
     * @param string name
     * @return DatabaseConfiguration|null
     */
    public function getDatabaseByName($name)
    {
        $databases = $this->databasesConfiguration;
        if (!$databases) {
            return null;
        }

        foreach ($databases as $db) {
            if ($db->name === $name) {
                return $db;
            }
        }

        return null;
    }
}
