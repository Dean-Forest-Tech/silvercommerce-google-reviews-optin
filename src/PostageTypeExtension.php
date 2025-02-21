<?php

namespace DFT\SilverCommerce\GoogleReviewsOptIn;

use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\DataExtension;
use SilverCommerce\Postage\Model\PostageType;

class PostageTypeExtension extends DataExtension
{
    private static $db = [
        'DeliveryDays' => 'Varchar'
    ];

    private static $field_labels = [
        'DeliveryDays' => 'Expected number of days for delivery'
    ];

    private static $summary_fields = [
        'ShortClassName',
        'Name',
        'DeliveryDays',
        'Enabled'
    ];

    public function updateCMSFields(FieldList $fields)
    {
        /** @var PostageType */
        $owner = $this->getOwner();

        $days_field = $owner
            ->dbObject('DeliveryDays')
            ->scaffoldFormField($owner->fieldLabel('DeliveryDays'))
            ->setDescription("Used by Google reviews survey form to determine reminder date");

        $fields->insertAfter(
            'TaxID',
            $days_field
        );
    }
}