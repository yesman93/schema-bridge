<?php

namespace Lumio\Security;

class TTLBuilder {

    private SignedUrlBuilder $_builder;

    /**
     * Builder for TTL (time-to-live) of given signed URL
     *
     * @author TB
     * @date 15.5.2025
     *
     * @param SignedUrlBuilder $builder
     *
     * @return void
     */
    public function __construct(SignedUrlBuilder $builder) {
        $this->_builder = $builder;
    }

    /**
     * Set given expiration time - in seconds
     *
     * @author TB
     * @date 15.5.2025
     *
     * @param int $n
     *
     * @return SignedUrlBuilder
     */
    public function seconds(int $n): SignedUrlBuilder {
        return $this->_builder->expire(time() + $n);
    }

    /**
     * Set given expiration time - in minutes
     *
     * @author TB
     * @date 15.5.2025
     *
     * @param int $n
     *
     * @return SignedUrlBuilder
     */
    public function minutes(int $n): SignedUrlBuilder {
        return $this->seconds($n * 60);
    }

    /**
     * Set given expiration time - in hours
     *
     * @author TB
     * @date 15.5.2025
     *
     * @param int $n
     *
     * @return SignedUrlBuilder
     */
    public function hours(int $n): SignedUrlBuilder {
        return $this->minutes($n * 60);
    }

    /**
     * Set given expiration time - in days
     *
     * @author TB
     * @date 15.5.2025
     *
     * @param int $n
     *
     * @return SignedUrlBuilder
     */
    public function days(int $n): SignedUrlBuilder {
        return $this->hours($n * 24);
    }

    /**
     * Set given expiration time - in weeks
     *
     * @author TB
     * @date 15.5.2025
     *
     * @param int $n
     *
     * @return SignedUrlBuilder
     */
    public function weeks(int $n): SignedUrlBuilder {
        return $this->days($n * 7);
    }

    /**
     * Set given expiration time - in months (n * 30 days)
     *
     * @author TB
     * @date 15.5.2025
     *
     * @param int $n
     *
     * @return SignedUrlBuilder
     */
    public function months(int $n): SignedUrlBuilder {
        return $this->days($n * 30);
    }

    /**
     * Set given expiration time - in years (n * 365 days)
     *
     * @author TB
     * @date 15.5.2025
     *
     * @param int $n
     *
     * @return SignedUrlBuilder
     */
    public function years(int $n): SignedUrlBuilder {
        return $this->days($n * 365);
    }

}
