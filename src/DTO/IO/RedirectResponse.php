<?php

namespace Lumio\DTO\IO;

use Lumio\Config;
use Lumio\IO\Flash;
use Lumio\Traits;

class RedirectResponse {

    use Traits\IO\HttpStatus;

    /**
     * HTTP status code
     *
     * @author TB
     * @date 14.5.2025
     *
     * @var int
     */
    private int $_status_code;

    /**
     * URL
     *
     * @author TB
     * @date 14.5.2025
     *
     * @var string
     */
    private string $_url;

    /**
     * Response for redirect
     *
     * @author TB
     * @date 14.5.2025
     *
     * @param string $url
     * @param int $status_code
     *
     * @return void
     */
    public function __construct(string $url, int $status_code = self::HTTP_302) {

        $this->_url = $this->_normalize_url($url);
        $this->_status_code = $status_code;
    }

    /**
     * Normalizes URL - trimming, adding host if necessary, etc.
     *
     * @author TB
     * @date 17.5.2025
     *
     * @param string $url
     *
     * @return string
     */
    private function _normalize_url(string $url): string {

        $host = parse_url($url, PHP_URL_HOST);
        if (!empty($host)) {
            return $url;
        }

        $url = trim($url, '/');
        $url = rtrim(LUMIO_HOST, '/') . '/' . $url;

        return $url;
    }

    /**
     * Check if the host is allowed
     *
     * @author TB
     * @date 17.5.2025
     *
     * @return bool
     */
    public function is_allowed_host(): bool {

        $host = parse_url($this->_url, PHP_URL_HOST);
        if (empty($host)) {
            return true;
        }

        try {
            $allowed = array_filter(Config::get('routing.allowed_hosts') ?? []);
        } catch (\Throwable $e) {
            $allowed = [];
        }

        if (empty($allowed)) {
            return true;
        }

        return in_array($host, $allowed, true);
    }

    /**
     * Get host from the URL
     *
     * @author TB
     * @date 17.5.2025
     *
     * @return string
     */
    public function get_host(): string {
        return parse_url($this->_url, PHP_URL_HOST);
    }

    /**
     * Get HTTP status code
     *
     * @author TB
     * @date 14.5.2025
     *
     * @return int
     */
    public function get_status_code(): int {
        return $this->_status_code;
    }

    /**
     * Get URL
     *
     * @author TB
     * @date 14.5.2025
     *
     * @return string
     */
    public function get_url(): string {
        return $this->_url;
    }

    /**
     * Set error message to show after redirect
     *
     * @author TB
     * @date 14.5.2025
     *
     * @param string $message
     *
     * @return self
     */
    public function error(string $message): self {
        Flash::error($message);
        return $this;
    }

    /**
     * Set success message to show after redirect
     *
     * @author TB
     * @date 14.5.2025
     *
     * @param string $message
     *
     * @return self
     */
    public function success(string $message): self {
        Flash::success($message);
        return $this;
    }

    /**
     * Set info message to show after redirect
     *
     * @author TB
     * @date 14.5.2025
     *
     * @param string $message
     *
     * @return self
     */
    public function info(string $message): self {
        Flash::info($message);
        return $this;
    }

    /**
     * Set warning message to show after redirect
     *
     * @author TB
     * @date 14.5.2025
     *
     * @param string $message
     *
     * @return self
     */
    public function warning(string $message): self {
        Flash::warning($message);
        return $this;
    }

}


