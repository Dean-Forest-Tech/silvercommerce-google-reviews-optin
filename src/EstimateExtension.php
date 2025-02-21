<?php

namespace DFT\SilverCommerce\GoogleReviewsOptIn;

use DateTime;
use SilverStripe\ORM\DataExtension;
use SilverStripe\SiteConfig\SiteConfig;
use SilverCommerce\OrdersAdmin\Model\Estimate;

class EstimateExtension extends DataExtension
{
    public function getEstimatedDeliveryDate(): DateTime
    {
        /** @var Estimate */
        $owner = $this->getOwner();
        $date = new DateTime($owner->StartDate);

        $config = SiteConfig::current_site_config();
        $postage = $config
            ->PostageTypes()
            ->find("Name", $owner->PostageTitle);

        if (!empty($postage) && !empty($postage->DeliveryDays)) {
            $date->modify("+" . $postage->DeliveryDays . " days");
        }

        return $date;
    }
}