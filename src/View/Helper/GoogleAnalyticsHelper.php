<?php
declare(strict_types=1);

namespace GoogleAnalytics\View\Helper;

use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\View\Helper;

/**
 * Class GoogleAnalyticsHelper
 *
 * @package Seo\View\Helper
 */
class GoogleAnalyticsHelper extends Helper
{
    protected $_defaultConfig = [
        'implementation' => 'gtag', // 'gtag' or 'analytics'
        'block' => 'google_analytics',
    ];

    /**
     * @return string
     */
    public function render(): string
    {
        $trackingId = Configure::read('GoogleAnalytics.trackingId');
        $enabled = Configure::read('GoogleAnalytics.enabled')
            && !$this->_View->get('_no_tracking')
            && !$this->_View->get('_private');

        $html = "";
        if ($trackingId && $enabled) {
            $html = $this->_View->element(
                'GoogleAnalytics.' . $this->getConfig('implementation'),
                ['trackingId' => $trackingId]
            );

            // disable in debug mode
            if (Configure::read('GoogleAnalytics.disableOnDebug')) {
                $devMsg = "Seo: Google analytics tracking script has been disabled in debug mode";
                $html = "<!-- " . $html . " -->";
                $html .= sprintf("<script>console.log('%s')</script>", $devMsg);
            }
        }

        return $html;
    }

    /**
     * @param \Cake\Event\Event $event Event object
     * @return void
     */
    public function beforeLayout(Event $event): void
    {
        $html = $this->render();

        /** @var \Cake\View\View $view */
        $view = $event->getSubject();
        $view->assign($this->getConfig('block'), $html);
    }
}
