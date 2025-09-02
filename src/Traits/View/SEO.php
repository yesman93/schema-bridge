<?php

namespace Lumio\Traits\View;

use Lumio\Config;

trait SEO {

    /**
     * SEO title
     *
     *
     * @var string|null
     */
    protected ?string $title = null;

    /**
     * SEO description
     *
     *
     * @var string|null
     */
    protected ?string $description = null;

    /**
     * SEO keywords
     *
     *
     * @var string|null
     */
    protected ?string $keywords = null;

    /**
     * Set SEO title
     *
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
     *
     * @return string|null
     */
    public function description(): ?string {
        return $this->description;
    }

    /**
     * Set SEO keywords
     *
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
     *
     * @return string|null
     */
    public function keywords(): ?string {
        return $this->keywords;
    }

}
