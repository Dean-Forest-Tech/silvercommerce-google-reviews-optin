<?php

namespace DFT\SilverCommerce\GoogleReviewsOptIn;

use InvalidArgumentException;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextField;
use SilverStripe\Control\Controller;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\TagManager\SnippetProvider;
use SilverCommerce\Checkout\Control\Checkout;
use SilverCommerce\OrdersAdmin\Model\Estimate;

class GoogleReviewsOptInSnippetProvider implements SnippetProvider
{
    const SESSION_NAME = "GoogleReviewOptIn.OrderID";

    const OPT_IN_STYLES = [
        "CENTER_DIALOG",
        "BOTTOM_RIGHT_DIALOG",
        "BOTTOM_LEFT_DIALOG",
        "TOP_RIGHT_DIALOG",
        "TOP_LEFT_DIALOG",
        "BOTTOM_TRAY"
    ];

    public function getTitle()
    {
        return "Google Reviews Opt In";
    }

    protected function getOptInStyles(): array
    {
        $styles = [];

        foreach (self::OPT_IN_STYLES as $style) {
            $name = ucwords(strtolower(str_replace('_', ' ', $style)));
            $styles[$style] = $name;
        }

        return $styles;
    }

    public function getParamFields()
    {
        $styles = $this->getOptInStyles();

        return FieldList::create(
            TextField::create(
                "GoogleMerchantID",
                "Google Merchant ID"
            ),
            DropdownField::create(
                'GoogleReviewOptInStyle',
                'Dialog box display',
                $styles
            )
        );
    }

    public function getSummary(array $params)
    {
        if (!empty($params['GoogleMerchantID'])) {
            return $this->getTitle() . " -  " . $params['GoogleMerchantID'];
        } else {
            return $this->getTitle();
        }
    }

    public function getSnippets(array $params)
    {
        if (empty($params['GoogleMerchantID'])) {
            throw new InvalidArgumentException("Please supply your Google Merchant ID");
        }

        /** @var HTTPRequest */
        $request = Injector::inst()->get(HTTPRequest::class);
        $session = $request->getSession();
        $order_ID = $session->get(self::SESSION_NAME);

        if (empty($order_ID)) {
            $order_ID = $session->get("Checkout.EstimateID");
        }

        if (empty($order_ID)) {
            return [];
        }

        $session->set(self::SESSION_NAME, $order_ID);
        $controller = Controller::curr();
        $action = $request->param('Action');

        if (!is_a($controller, Checkout::class, true)) {
            return [];
        }

        if ($action !== 'complete') {
            return [];
        }

        $order = Estimate::get()->byID($order_ID);

        if (empty($order)) {
            return [];
        }

        // Sanitise the ID
        $gaId = preg_replace(
            '[^A-Za-z0-9_\-]',
            '',
            $params['GoogleMerchantID']
        );
        $style = $params['GoogleReviewOptInStyle'];
        $date = $order
            ->getEstimatedDeliveryDate()
            ->format('Y-m-d');

        $content = <<<HTML
<script async src="https://apis.google.com/js/platform.js?onload=renderOptIn"></script>
<script>
window.renderOptIn = function() {
  window.gapi.load('surveyoptin', function() {
    window.gapi.surveyoptin.render({
      "merchant_id": "{$gaId}",
      "order_id": "{$order->FullRef}",
      "email": "{$order->Email}",
      "delivery_country": "{$order->DeliveryCountry}",
      "estimated_delivery_date": "{$date}",
      "opt_in_style": "{$style}"
    });
  });
}
</script>
HTML;

        $content = str_replace("\n", "", $content);

        $session->clear(self::SESSION_NAME);

        return [
            self::ZONE_BODY_END => $content
        ];
    }
}
