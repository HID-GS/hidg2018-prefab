<?php

namespace Drupal\jsonapi_extras\Plugin\jsonapi\FieldEnhancer;

use Drupal\jsonapi_extras\Plugin\DateTimeEnhancerBase;
use Shaper\Util\Context;

/**
 * Perform additional manipulations to timestamp fields.
 *
 * @ResourceFieldEnhancer(
 *   id = "date_time",
 *   label = @Translation("Date Time (Timestamp field)"),
 *   description = @Translation("Formats a date based the configured date format for timestamp fields.")
 * )
 */
class DateTimeEnhancer extends DateTimeEnhancerBase {

  /**
   * {@inheritdoc}
   */
  protected function doUndoTransform($data, Context $context) {
    $date = new \DateTime();
    $date->setTimestamp($data);
    $configuration = $this->getConfiguration();

    return $date->format($configuration['dateTimeFormat']);
  }

  /**
   * {@inheritdoc}
   */
  protected function doTransform($data, Context $context) {
    $date = new \DateTime($data);
    return (int) $date->format('U');
  }

}
