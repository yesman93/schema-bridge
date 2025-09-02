<?php

namespace Lumio\Traits\View;

use Lumio\Traits;

trait OpenGraph {

    use OG\Type;
    use Traits\Localization\Locale;

    /**
     * Open Graph property - title
     *
     * @author TB
     * @date 22.5.2025
     *
     * @var string
     */
    public const string OG_TITLE = 'og:title';

    /**
     * Open Graph property - description
     *
     * @author TB
     * @date 22.5.2025
     *
     * @var string
     */
    public const string OG_DESCRIPTION = 'og:description';

    /**
     * Open Graph property - image
     *
     * @author TB
     * @date 22.5.2025
     *
     * @var string
     */
    public const string OG_IMAGE = 'og:image';

    /**
     * Open Graph property - URL
     *
     * @author TB
     * @date 22.5.2025
     *
     * @var string
     */
    public const string OG_URL = 'og:url';

    /**
     * Open Graph property - type
     *
     * @author TB
     * @date 22.5.2025
     *
     * @var string
     */
    public const string OG_TYPE = 'og:type';

    /**
     * Open Graph property - locale
     *
     * @author TB
     * @date 22.5.2025
     *
     * @var string
     */
    public const string OG_LOCALE = 'og:locale';

    /**
     * Open Graph property - site name
     *
     * @author TB
     * @date 22.5.2025
     *
     * @var string
     */
    public const string OG_SITE_NAME = 'og:site_name';

    /**
     * Open Graph property - updated time
     *
     * @author TB
     * @date 22.5.2025
     *
     * @var string
     */
    public const string OG_UPDATED_TIME = 'og:updated_time';

    /**
     * Open Graph data
     *
     * @author TB
     * @date 22.5.2025
     *
     * @var array
     */
    protected array $_open_graph = [];

    /**
     * Set given Open Graph data
     *
     * @author TB
     * @date 22.5.2025
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
     * @author TB
     * @date 22.5.2025
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
     * @author TB
     * @date 22.5.2025
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
     * @author TB
     * @date 22.5.2025
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
     * @author TB
     * @date 22.5.2025
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
     * @author TB
     * @date 22.5.2025
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
     * @author TB
     * @date 22.5.2025
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
     * @author TB
     * @date 22.5.2025
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
     * @author TB
     * @date 22.5.2025
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
     * @author TB
     * @date 22.5.2025
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
     * @author TB
     * @date 22.5.2025
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
