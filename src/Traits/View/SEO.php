<?php

namespace Lumio\Traits\View;

use Lumio\Config;

trait SEO {

    /**
     * SEO title
     *
     * @author TB
     * @date 30.4.2025
     *
     * @var string|null
     */
    protected ?string $title = null;

    /**
     * SEO description
     *
     * @author TB
     * @date 30.4.2025
     *
     * @var string|null
     */
    protected ?string $description = null;

    /**
     * SEO keywords
     *
     * @author TB
     * @date 30.4.2025
     *
     * @var string|null
     */
    protected ?string $keywords = null;

    /**
     * Set SEO title
     *
     * @author TB
     * @date 30.4.2025
     *
     * @param string $title
     *
     * @return void
     */
    public function set_title(string $title): void {
        $this->title = $title;
    }

    /**
     * Get SEO title
     *
     * @author TB
     * @date 30.4.2025
     *
     * @param bool $include_appname
     *
     * @return string|null
     */
    public function title($include_appname = true): ?string {

        if ($include_appname) {
            $app_name = Config::get('app.app_name');
            return $this->title . ' | ' . $app_name;
        } else {
            return $this->title;
        }
    }

    /**
     * Set SEO description
     *
     * @author TB
     * @date 30.4.2025
     *
     * @param string $description
     *
     * @return void
     */
    public function set_description(string $description): void {
        $this->description = $description;
    }

    /**
     * Get SEO description
     *
     * @author TB
     * @date 30.4.2025
     *
     * @return string|null
     */
    public function description(): ?string {
        return $this->description;
    }

    /**
     * Set SEO keywords
     *
     * @author TB
     * @date 30.4.2025
     *
     * @param string $keywords
     *
     * @return void
     */
    public function set_keywords(string $keywords): void {
        $this->keywords = $keywords;
    }

    /**
     * Get SEO keywords
     *
     * @author TB
     * @date 30.4.2025
     *
     * @return string|null
     */
    public function keywords(): ?string {
        return $this->keywords;
    }

}
