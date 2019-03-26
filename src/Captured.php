<?php

namespace Log1x\Captured;

use Carbon\Carbon,
    Bulletproof\Image,
    Hashids\Hashids;

class Captured
{
    /**
     * Captured API Token
     *
     * @var string
     */
    protected $token = '';

    /**
     * Upload Directory
     *
     * @var string
     */
    protected $dir = 'screenshots/';

    /**
     * Maximum Filesize
     *
     * @var integer
     */
    protected $size = 100;

    /**
     * Filename Slug
     *
     * @var string|boolean
     */
    protected $slug = 'Screenshot_';

    /**
     * Filename Timestamp
     *
     * @var string|boolean
     */
    protected $timestamp = 'Y-m-d_H-i-s';

    /**
     * Constructor
     */
    public function __construct()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->verifyToken();
            $this->verifyConnection();
            $this->upload();
        }
    }

    /**
     * Echos a JSON representation of the passed value.
     *
     * @param  array $value
     * @return mixed
     */
    protected function response($value = [])
    {
        header('Content-Type: application/json');

        if (! empty($value['code'])) {
            http_response_code((int) $value['code']);
        }

        echo json_encode($value, JSON_UNESCAPED_SLASHES);
        exit();
    }

    /**
     * Returns an object containing configuration.
     *
     * @return object
     */
    protected function config()
    {
        $config = file_exists($config = __DIR__ . '/../config.php') ? require_once($config) : [];
        
        return (object) array_merge([
            'token'     => $this->token,
            'dir'       => $this->dir,
            'size'      => $this->size,
            'slug'      => $this->slug,
            'timestamp' => $this->timestamp
        ], $this->config);
    }

    /**
     * Returns the current host URL.
     *
     * @return string
     */
    protected function getHost()
    {
        return (empty($_SERVER['HTTPS']) ? 'http://' : 'https://') . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    }

    /**
     * Generates the filename based on the format.
     *
     * @return string
     */
    protected function generateFilename()
    {
        if (empty($this->config()->timestamp)) {
            return $this->config()->slug . (new Hashids(random_bytes(24)))->encode(1, 2, 3);
        }

        return $this->config()->slug . Carbon::now()->format($this->config()->timestamp);
    }

    /**
     * Returns the full relative path to the uploaded file with extension.
     *
     * @param  object $image
     * @return string
     */
    protected function getFile($image = null)
    {
        if (! $image instanceof Image) {
            $this->response(['error' => 'Unable to process uploaded image.']);
        }

        return $this->getHost() . $this->config()->dir . $image->getName() . '.' . $image->getMime();
    }

    /**
     * Verifies API Token
     *
     * @return mixed
     */
    protected function verifyToken()
    {
        if (empty($_POST['token']) || $_POST['token'] !== $this->config()->token) {
            $this->response([
                'code'   => 401,
                'status' => '401 Invalid Token'
            ]);
        }
    }

    /**
     * Tests and verifies the connection.
     *
     * @return mixed
     */
    protected function verifyConnection()
    {
        if (! empty($_POST['test']) && $_POST['test'] === 'true') {
            $this->response([
                'code'   => 202,
                'status' => '202 Accepted'
            ]);
        }
    }

    /**
     * Processes and uploads an image passed through the POST method.
     *
     * @return void
     */
    protected function upload()
    {
        $image = new Image($_FILES);

        if ($image['file']) {
            $image->setName($this->generateFilename());
            $image->setSize(100, $this->config()->size * (1024 * 1024));
            $image->setMime(['gif', 'jpeg', 'png']);
            $image->setLocation($this->config()->dir ? $this->config()->dir : '.', 0755);

            if ($image->upload()) {
                $this->response(['public_url' => $this->getFile($image)]);
            }

            $this->response(['error' => 'Unable to process file upload']);
        }
    }
}
