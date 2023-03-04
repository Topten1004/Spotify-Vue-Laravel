<?php

namespace App\installer\Helpers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EnvironmentManager
{
    /**
     * @var string
     */
    private $envPath;

    /**
     * @var string
     */
    private $envExamplePath;

    /**
     * Set the .env and .env.example paths.
     */
    public function __construct()
    {
        $this->envPath = base_path('.env');
        $this->envExamplePath = base_path('.env.example');
    }

    /**
     * Get the content of the .env file.
     *
     * @return string
     */
    public function getEnvContent()
    {
        if (file_exists($this->getEnvPath())) {
            return file_get_contents($this->envPath);
        } else {
            return '';
        }
    }

    /**
     * Create the .env file that contains the app enviroment data.
     */
    public function createEnvFile()
    {
        if (!file_exists($this->getEnvPath())) {
            copy($this->getEnvExamplePath(), $this->getEnvPath());
        } else {
            unlink($this->getEnvPath());
            copy($this->getEnvExamplePath(), $this->getEnvPath());
        }
    }

    /**
     * Get the the .env file path.
     *
     * @return string
     */
    public function getEnvPath()
    {
        return $this->envPath;
    }

    /**
     * Get the the .env.example file path.
     *
     * @return string
     */
    public function getEnvExamplePath()
    {
        return $this->envExamplePath;
    }

    /**
     * Save the edited content to the .env file.
     *
     * @param Request $input
     * @return string
     */
    public function saveFileClassic(Request $input)
    {
        $message = trans('installer_messages.environment.success');

        try {
            file_put_contents($this->envPath, $input->get('envConfig'));
        } catch (Exception $e) {
            $message = trans('installer_messages.environment.errors');
        }

        return $message;
    }

    /**
     * Save the form content to the .env file.
     *
     * @param Request $request
     * @return string
     */
    public function saveFileWizard(Request $request)
    {
        $this->createEnvFile();
        $file_contents = file_get_contents($this->envPath);
        try {
            $results = 'Success';
            $file_contents = str_replace(
                'APP_KEY=',
                'APP_KEY=' . 'base64:' . base64_encode(Str::random(32)),
                $file_contents
            );
            $file_contents = str_replace(
                'APP_URL=',
                'APP_URL=' . $this->formatValue($request->app_url),
                $file_contents
            );
            $file_contents = str_replace(
                "APP_NAME=",
                "APP_NAME=" . $this->formatValue($request->app_name),
                $file_contents
            );
            $file_contents = str_replace(
                'APP_ENV=',
                'APP_ENV=production',
                $file_contents
            );
            $file_contents = str_replace(
                'DB_CONNECTION=',
                'DB_CONNECTION=' . $this->formatValue($request->database_connection),
                $file_contents
            );
            $file_contents = str_replace(
                'DB_DATABASE=',
                'DB_DATABASE=' . $this->formatValue($request->database_name),
                $file_contents
            );
            $file_contents = str_replace(
                'DB_HOST=',
                'DB_HOST=' . $this->formatValue($request->database_hostname),
                $file_contents
            );
            $file_contents = str_replace(
                'DB_PORT=',
                'DB_PORT=' . $this->formatValue($request->database_port),
                $file_contents
            );
            $file_contents = str_replace(
                'DB_USERNAME=',
                'DB_USERNAME=' . $this->formatValue($request->database_username),
                $file_contents
            );
            $file_contents = str_replace(
                'DB_PASSWORD=',
                'DB_PASSWORD=' . $this->formatValue($request->database_password),
                $file_contents
            );
            file_put_contents($this->envPath, $file_contents);
        } catch (Exception $e) {
            $results = 'Errors';
        }
        return $results;
    }

    /**
     * Format specified value to be compatible with .env file
     *
     * @param string|null $value
     * @return string
     */
    private function formatValue($value)
    {
        if ($value === 0 || $value === false) $value = 'false';
        if ($value === 1 || $value === true) $value = 'true';
        if ( ! $value) $value = 'null';
        $value = trim($value);

        // wrap string in quotes, if it contains whitespace or special characters
        if (preg_match('/\s/', $value) || Str::contains($value, '#')) {
            //replace double quotes with single quotes
            $value = str_replace('"', "'", $value);

            //wrap string in quotes
            $value = '"'.$value.'"';
        }

        return $value;
    }
}
