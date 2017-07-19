<?php
/**
 * Class AwsController
 *
 * @copyright   Cloudrexx AG
 * @author      Project Team SS4U <info@cloudrexx.com>
 * @package     cloudrexx
 * @subpackage  coremodule_multisite
 */

namespace Cx\Core_Modules\MultiSite\Controller;

/**
 * Class AwsController
 *
 * @copyright   Cloudrexx AG
 * @author      Project Team SS4U <info@cloudrexx.com>
 * @package     cloudrexx
 * @subpackage  coremodule_multisite
 */

class AwsController {
    /**
     * List of the available regions
     *
     * @var array
     */
    protected static $regions = array(
        'us-east-1'      => 'US East (N. Virginia)',
        'us-east-2'      => 'US East (Ohio)',
        'us-west-1'      => 'US West (N. California)',
        'us-west-2'      => 'US West (Oregon)',
        'ca-central-1'   => 'Canada (Central)',
        'eu-west-1'      => 'EU (Ireland)',
        'eu-central-1'   => 'EU (Frankfurt)',
        'eu-west-2'      => 'eu-west-2: EU (London)',
        'ap-northeast-1' => 'Asia Pacific (Tokyo)',
        'ap-northeast-2' => 'Asia Pacific (Seoul)',
        'ap-southeast-1' => 'Asia Pacific (Singapore)',
        'ap-southeast-2' => 'Asia Pacific (Sydney)',
        'ap-south-1'     => 'Asia Pacific (Mumbai)',
        'sa-east-1'      => 'South America (São Paulo)'
    );

    /**
     * Region
     *
     * @var string
     */
    protected $region;

    /**
     * AWS access key ID
     *
     * @var string
     */
    protected $credentialsKey;

    /**
     * AWS secret access key
     *
     * @var string
     */
    protected $credentialsSecret;

    /**
     * AWS version
     *
     * @var string
     */
    protected $version;

    /**
     * Constructor
     *
     * @param string $credentialsKey    AWS access key ID
     * @param string $credentialsSecret AWS secret access key
     * @param string $region            AWS region
     * @param string $version           AWS version
     */
    public function __construct(
        $credentialsKey,
        $credentialsSecret,
        $region,
        $version
    ) {
        $this->region            = $region;
        $this->credentialsKey    = $credentialsKey;
        $this->credentialsSecret = $credentialsSecret;
        $this->version           = $version;
    }

    /**
     * get Regions list
     *
     * @return array
     */
    public static function getRegions()
    {
        return static::$regions;
    }
}
