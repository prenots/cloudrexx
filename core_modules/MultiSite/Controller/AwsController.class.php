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
 * Reports error during the API request
 */
class AwsRoute53Exception extends DnsControllerException {}

/**
 * Class AwsController
 *
 * @copyright   Cloudrexx AG
 * @author      Project Team SS4U <info@cloudrexx.com>
 * @package     cloudrexx
 * @subpackage  coremodule_multisite
 */

class AwsController implements DnsController {
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
     * Resource record cache time to live in seconds
     *
     * @var integer
     */
    protected $timeToLive;

    /**
     * Hosted zone ID
     *
     * @var string
     */
    protected $webspaceId;

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
        //Load the Aws SDK
        $cx = \Cx\Core\Core\Controller\Cx::instanciate();
        $cx->getClassLoader()->loadFile(
            $cx->getCodeBaseLibraryPath() . '/Aws/aws.phar'
        );
        $this->region            = $region;
        $this->credentialsKey    = $credentialsKey;
        $this->credentialsSecret = $credentialsSecret;
        $this->version           = $version;
    }

    /**
     * Get Regions list
     *
     * @return array
     */
    public static function getRegions()
    {
        return static::$regions;
    }

    /**
     * Set Resource record cache time to live in seconds
     *
     * @param integer $timeToLive
     */
    public function setTimeToLive($timeToLive)
    {
        $this->timeToLive;
    }

    /**
     * Get timeToLive
     *
     * @return integer
     */
    public function getTimeToLive()
    {
        return $this->timeToLive;
    }

    /**
     * Set Hosted zone ID
     *
     * @param string $webspaceId
     */
    public function setWebspaceId($webspaceId)
    {
        $this->webspaceId = $webspaceId;
    }

    /**
     * Get Hosted zone ID
     *
     * @return string
     */
    public function getWebspaceId()
    {
        return $this->webspaceId;
    }

    /**
     * Get Route53 client object
     *
     * @throws AwsRoute53Exception
     * @return \Aws\Route53\Route53Client
     */
    protected function getRoute53Client()
    {
        try {
            return new \Aws\Route53\Route53Client(array(
                'version'     => $this->version,
                'region'      => $this->region,
                'credentials' => array(
                    'key'    => $this->credentialsKey,
                    'secret' => $this->credentialsSecret
                )
            ));
        } catch (\Aws\Exception\AwsException $e) {
            throw new AwsRoute53Exception('Error in creating AWS Route53 Client.');
        }
    }

    /**
     * Add DNS Record
     *
     * @param string  $type   DNS-Record type
     * @param string  $host   DNS-Record host
     * @param string  $value  DNS-Record value
     * @param string  $zone   Name of DNS-Zone
     * @param integer $zoneId Id of Hosted zone
     *
     * @return integer
     */
    public function addDnsRecord($type = 'A', $host, $value, $zone, $zoneId)
    {
        $client = $this->getRoute53Client();
        try {
            return $this->manipulateDnsRecord(
                $client,
                'CREATE',
                $type,
                $host,
                $value,
                $zone,
                $zoneId,
                $this->timeToLive
            );
        } catch (AwsRoute53Exception $e) {
            throw new AwsRoute53Exception('Error in adding DNS Record.');
        }
    }

    /**
     * Update DNS Record
     *
     * @param string  $type     DNS-Record type
     * @param string  $host     DNS-Record host
     * @param string  $value    DNS-Record value
     * @param string  $zone     Name of DNS-Zone
     * @param integer $zoneId   Id of Hosted zone
     * @param integer $recordId DNS record ID
     *
     * @return integer
     */
    public function updateDnsRecord(
        $type,
        $host,
        $value,
        $zone,
        $zoneId,
        $recordId
    ) {
        $client = $this->getRoute53Client();
        try {
            return $this->manipulateDnsRecord(
                $client,
                'UPSERT',
                $type,
                $host,
                $value,
                $zone,
                $zoneId,
                $this->timeToLive
            );
        } catch (AwsRoute53Exception $e) {
            throw new AwsRoute53Exception('Error in updating DNS Record.');
        }
    }

    /**
     * Remove DNS Record
     *
     * @param string  $type     DNS-Record type
     * @param string  $host     DNS-Record host
     * @param integer $recordId DNS record ID
     */
    public function removeDnsRecord($type, $host, $recordId)
    {
        if (empty($this->webspaceId)) {
            return;
        }
        $client = $this->getRoute53Client();
        try {
            $dnsRecords = array();
            $this->fetchDnsRecords(
                $client,
                array(
                    'HostedZoneId'    => $this->webspaceId,
                    'MaxItems'        => '1',
                    'StartRecordName' => $host,
                    'StartRecordType' => $type
                ),
                $dnsRecords
            );
            if (!isset($dnsRecords[$host])) {
                return;
            }
            $dnsRecord = $dnsRecords[$host];
            $this->manipulateDnsRecord(
                $client,
                'DELETE',
                $dnsRecord['type'],
                $dnsRecord['name'],
                $dnsRecord['value'],
                '',
                $this->webspaceId,
                $dnsRecord['ttl']
            );
        } catch (AwsRoute53Exception $e) {
            throw new AwsRoute53Exception('Error in deleting DNS Record.');
        }
    }

    /**
     * Manipulate DNS record
     *
     * @param \Aws\Route53\Route53Client $client Route53Client object
     * @param string                     $action Action value(CREATE|UPSERT|DELETE)
     * @param string                     $type   DNS-Record type
     * @param string                     $host   DNS-Record host
     * @param string                     $value  DNS-Record value
     * @param string                     $zone   Name of DNS-Zone
     * @param integer                    $zoneId Id of Hosted zone
     * @param integer                    $timeToLive Resource Record cache live time
     *
     * @throws AwsRoute53Exception
     * @return integer
     */
    protected function manipulateDnsRecord(
        $client,
        $action,
        $type,
        $host,
        $value,
        $zone,
        $zoneId,
        $timeToLive
    ) {
        try {
            // In case the record is a subdomain of the DNS-zone, then
            // we'll have to strip the DNS-zone part from the record.
            // I.e.:
            //      DNS-zone ($zone):   example.com
            //      DNS-record ($host): foo.example.com
            //      strip $host to:     foo
            if (
                $zone &&
                preg_match('/^(.*)\.' . preg_quote($zone) . '$/', $host, $match)
            ) {
                $host = $match[1];
            }
            $client->changeResourceRecordSets(array(
                'ChangeBatch' => array(
                    'Changes' => array(
                        'Action' => $action,
                        'ResourceRecordSet' => array(
                            'Name' => rtrim($host, '.'),
                            'ResourceRecords' => array(
                                array(
                                    'Value' => $value
                                )
                            ),
                            'TTL'  => $timeToLive,
                            'Type' => $type
                        )
                    )
                ),
                'HostedZoneId' => $zoneId
            ));
            return 0;
        } catch (\Aws\Exception\AwsException $e) {
            throw new AwsRoute53Exception($e->getMesssage());
        }
    }

    /**
     * Get DNS Records
     *
     * @throws AwsRoute53Exception
     * @return array
     */
    public function getDnsRecords()
    {
        if (empty($this->webspaceId)) {
            return array();
        }

        $client = $this->getRoute53Client();
        try {
            $dnsRecords = array();
            $this->fetchDnsRecords(
                $client,
                array('HostedZoneId' => $this->webspaceId),
                $dnsRecords
            );
            return $dnsRecords;
        } catch (AwsRoute53Exception $e) {
            throw new AwsRoute53Exception('Error in getting DNS Record.');
        }
    }

    /**
     * Fetch DNS records list
     *
     * @param \Aws\Route53\Route53Client $client     Route53Client object
     * @param array                      $options    Parameter details for
     *                                               listResourceRecordSets
     * @param array                      $dnsRecords Array of Resource Recordset
     *
     * @throws AwsRoute53Exception
     */
    protected function fetchDnsRecords($client, $options, &$dnsRecords)
    {
        try {
            $result = $client->listResourceRecordSets($options);
            if ($result && isset($result['ResourceRecordSets'])) {
                foreach ($result['ResourceRecordSets'] as $recordSet) {
                    $dnsRecords[$recordSet['Name']] = array(
                        'name'  => $recordSet['Name'],
                        'value' => $recordSet['ResourceRecords'][0]['Value'],
                        'ttl'   => $recordSet['TTL'],
                        'type'  => $recordSet['Type']
                    );
                }
                if ($result['IsTruncated']) {
                    $this->fetchDnsRecords(
                        $client,
                        array(
                            'HostedZoneId'    => $this->webspaceId,
                            'StartRecordName' => $result['NextRecordName'],
                            'StartRecordType' => $result['NextRecordType']
                        ),
                        $dnsRecords
                    );
                }
            }
        } catch (\Aws\Exception\AwsException $e) {
            throw new AwsRoute53Exception($e->getMesssage());
        }
    }
}
