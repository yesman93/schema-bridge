<?php

namespace Lumio\Traits\View;

use Lumio\Traits;

trait OpenGraph {

    use OG\Type;
    use Traits\Localization\Locale;

    /**
     * Open Graph property - title
     *
     *
     * @var string
     */
    public const string OG_TITLE = 'og:title';

    /**
     * Open Graph property - description
     *
     *
     * @var string
     */
    public const string OG_DESCRIPTION = 'og:description';

    /**
     * Open Graph property - image
     *
     *
     * @var string
     */
    public const string OG_IMAGE = 'og:image';

    /**
     * Open Graph property - URL
     *
     *
     * @var string
     */
    public const string OG_URL = 'og:url';

    /**
     * Open Graph property - type
     *
     *
     * @var string
     */
    public const string OG_TYPE = 'og:type';

    /**
     * Open Graph property - locale
     *
     *
     * @var string
     */
    public const string OG_LOCALE = 'og:locale';

    /**
     * Open Graph property - site name
     *
     *
     * @var string
     */
    public const string OG_SITE_NAME = 'og:site_name';

    /**
     * Open Graph property - updated time
     *
     *
     * @var string
     */
    public const string OG_UPDATED_TIME = 'og:updated_time';

    /**
     * Open Graph data
     *
     *
     * @var array
     */
    protected array $_open_graph = [];

    /**
     * Set given Open Graph data
     *
     *
     * @param array $data
     */
    public function set_og(array $data): void {

        foreach ($data as $key => $value) {
            $this->set_og_property($key, $value);
        }
    }

    /**
     * Set given Open Graph property
     *
     *
     * @param string $key
     * @param string $value
     *
     * @return void
     */
    public function set_og_property(string $key, string $value): void {
        $this->_open_graph[$key] = $value;
    }

    /**
     * Set Open Graph title
     *
     *
     * @param string $value
     *
     * @return void
     */
    public function og_title(string $value): void {
        $this->set_og_property(self::OG_TITLE, $value);
    }

    /**
     * Set Open Graph description
     *
     *
     * @param string $value
     *
     * @return void
     */
    public function og_description(string $value): void {
        $this->set_og_property(self::OG_DESCRIPTION, $value);
    }

    /**
     * Set Open Graph image
     *
     *
     * @param string $value
     *
     * @return void
     */
    public function og_image(string $value): void {
        $this->set_og_property(self::OG_IMAGE, $value);
    }

    /**
     * Set Open Graph URL
     *
     *
     * @param string $value
     *
     * @return void
     */
    public function og_url(string $value): void {
        $this->set_og_property(self::OG_URL, $value);
    }

    /**
     * Set Open Graph type
     *
     *
     * @param string $value
     *
     * @return void
     */
    public function og_type(string $value): void {
        $this->set_og_property(self::OG_TYPE, $value);
    }

    /**
     * Set Open Graph locale
     *
     *
     * @param string $value
     *
     * @return void
     */
    public function og_locale(string $value): void {
        $this->set_og_property(self::OG_LOCALE, $value);
    }

    /**
     * Set Open Graph site name
     *
     *
     * @param string $value
     *
     * @return void
     */
    public function og_site_name(string $value): void {
        $this->set_og_property(self::OG_SITE_NAME, $value);
    }

    /**
     * Set Open Graph updated time
     *
     *
     * @param string $value
     *
     * @return void
     */
    public function og_updated_time(string $value): void {
        $this->set_og_property(self::OG_UPDATED_TIME, $value);
    }

    /**
     * Render Open Graph data as meta tags
     *
     *
     * @return void
     */
    public function og(): void {

        $html = '';

        foreach ($this->_open_graph as $property => $content) {
            $html .= '<meta property="' . htmlspecialchars($property) . '" content="' . htmlspecialchars($content) . '">' . PHP_EOL;
        }

        echo $html;
    }

}
